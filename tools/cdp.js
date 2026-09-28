/**
 * Klien Chrome DevTools Protocol untuk audit tema.
 *
 * Kenapa file JavaScript dan bukan Python:
 *
 *   Audit ini butuh `Emulation.setDeviceMetricsOverride`, karena
 *   Chrome di Windows memberlakukan jendela minimum 500px.
 *   `--window-size=375,900` diam-diam dirender pada 500px, sehingga
 *   audit "375px" lama sebenarnya mengukur 500px.
 *
 *   CDP itu hanya bisa dialing dari sisi Windows: WSL tidak bisa
 *   menjangkau loopback Windows (`curl 127.0.0.1:<port>` dari WSL
 *   selalu ditolak), dan `node.exe` adalah proses Windows, jadi
 *   loopback-nya sama dengan yang dipakai Chrome. WebSocket-nya
 *   memakai `WebSocket` bawaan Node 22+ tanpa dependensi.
 *
 * Pemakaian: job masuk lewat stdin sebagai larik JSON, hasil keluar
 * di stdout sebagai larik JSON dengan panjang yang sama.
 *
 *   [
 *     {
 *       "url": "http://127.0.0.1:8731/...html",
 *       "width": 375, "height": 900,
 *       "settle": 400,
 *       "waitFor": "document.getElementById('x') ? 1 : ''",
 *       "eval": "document.title",
 *       "screenshot": "C:\\...\\out.png",
 *       "fullPage": true
 *     }
 *   ]
 */
import { spawn } from 'node:child_process';
import { mkdtempSync, rmSync, writeFileSync, mkdirSync } from 'node:fs';
import { tmpdir } from 'node:os';
import { dirname, join } from 'node:path';
import { createServer } from 'node:net';

const CHROME = 'C:/Program Files/Google/Chrome/Application/chrome.exe';

function readStdin() {
    return new Promise((resolve, reject) => {
        let buf = '';
        process.stdin.setEncoding('utf8');
        process.stdin.on('data', (chunk) => (buf += chunk));
        process.stdin.on('end', () => resolve(buf));
        process.stdin.on('error', reject);
    });
}

function freePort() {
    return new Promise((resolve, reject) => {
        const srv = createServer();
        srv.on('error', reject);
        srv.listen(0, '127.0.0.1', () => {
            const { port } = srv.address();
            srv.close(() => resolve(port));
        });
    });
}

const sleep = (ms) => new Promise((r) => setTimeout(r, ms));

async function waitForDevtools(port, timeoutMs) {
    const deadline = Date.now() + timeoutMs;
    let last = 'tidak mencoba';

    while (Date.now() < deadline) {
        try {
            const res = await fetch(`http://127.0.0.1:${port}/json/version`);
            const info = await res.json();

            if (info.webSocketDebuggerUrl) {
                return info.webSocketDebuggerUrl;
            }
            last = JSON.stringify(info).slice(0, 200);
        } catch (e) {
            last = String(e && e.message ? e.message : e).slice(0, 200);
        }
        await sleep(200);
    }
    throw new Error(`DevTools tidak listen di port ${port}: ${last}`);
}

class Cdp {
    constructor(ws) {
        this.ws = ws;
        this.id = 0;
        this.pending = new Map();
        this.session = null;

        ws.onmessage = (ev) => {
            let msg;
            try {
                msg = JSON.parse(ev.data);
            } catch {
                return;
            }
            if (msg.id && this.pending.has(msg.id)) {
                const { resolve, reject } = this.pending.get(msg.id);
                this.pending.delete(msg.id);

                if (msg.error) {
                    reject(new Error(`${msg.error.message} (${msg.error.code})`));
                } else {
                    resolve(msg.result || {});
                }
            }
        };
    }

    send(method, params = {}, sessionId = null) {
        const id = ++this.id;
        const payload = { id, method, params };
        if (sessionId) {
            payload.sessionId = sessionId;
        }

        return new Promise((resolve, reject) => {
            this.pending.set(id, { resolve, reject });
            this.ws.send(JSON.stringify(payload));
            setTimeout(() => {
                if (this.pending.has(id)) {
                    this.pending.delete(id);
                    reject(new Error(`${method} timeout`));
                }
            }, 60000);
        });
    }

    async openPage() {
        const target = await this.send('Target.createTarget', { url: 'about:blank' });
        const attached = await this.send('Target.attachToTarget', {
            targetId: target.targetId,
            flatten: true,
        });
        this.session = attached.sessionId;
        await this.send('Page.enable', {}, this.session);
        await this.send('Runtime.enable', {}, this.session);
    }

    async viewport(width, height) {
        await this.send('Emulation.setDeviceMetricsOverride', {
            width,
            height,
            deviceScaleFactor: 1,
            mobile: false,
            screenWidth: width,
            screenHeight: height,
        }, this.session);
    }

    async scrollbars(hidden) {
        /* Scrollbar klasik atau disembunyikan.
         *
         * Ini bukan detail kecil, dan default-nya sengaja TIDAK
         * menyembunyikan scrollbar.
         *
         * Di emulasi CDP, scrollbar vertikal klasik tetap mengambil
         * 15px: `clientWidth` = lebar yang diminta - 15. Trik deteksi
         * overflow justru bergantung pada itu:
         * `width: 100vw` + `margin-left: -50vw` menghitung scrollbar
         * juga, jadi lebarnya jadi 15px lebih dari area yang boleh
         * dipakai dan memunculkan scrollbar horizontal. Kalau
         * scrollbar disembunyikan, bug itu hilang dari pengukuran
         * dan auditing jadi melaporkan "bersih" untuk halaman yang
         * sebenarnya bergeser di desktop pengguna.
         *
         * Screenshot justru perlu disembunyikan: HP asli memakai
         * scrollbar overlay, jadi foto 375px dari halaman dengan
         * scrollbar klasik hanya 360px dan tidak mewakili apa yang
         * dilihat pengguna.
         */
        await this.send('Emulation.setScrollbarsHidden', { hidden }, this.session);
    }

    async evaluate(expression) {
        const res = await this.send('Runtime.evaluate', {
            expression,
            returnByValue: true,
            awaitPromise: true,
        }, this.session);

        if (res.exceptionDetails) {
            throw new Error(
                'exception: ' + JSON.stringify(res.exceptionDetails).slice(0, 300));
        }
        return res.result ? res.result.value : undefined;
    }

    async navigate(url, settle = 400) {
        await this.send('Page.navigate', { url }, this.session);

        const deadline = Date.now() + 30000;
        while (Date.now() < deadline) {
            const state = await this.evaluate('document.readyState');
            if (state === 'complete') {
                break;
            }
            await sleep(100);
        }
        if (settle) {
            await sleep(settle);
        }
    }

    async waitFor(expression, timeoutMs = 20000) {
        const deadline = Date.now() + timeoutMs;
        while (Date.now() < deadline) {
            const value = await this.evaluate(expression);
            if (value) {
                return true;
            }
            await sleep(150);
        }
        return false;
    }

    async screenshot(out, fullPage = true) {
        const params = { format: 'png' };
        if (fullPage) {
            params.captureBeyondViewport = true;
        }
        const data = await this.send('Page.captureScreenshot', params, this.session);
        mkdirSync(dirname(out), { recursive: true });
        writeFileSync(out, Buffer.from(data.data, 'base64'));
    }
}

async function main() {
    const raw = await readStdin();
    const jobs = raw.trim() ? JSON.parse(raw) : [];

    if (!jobs.length) {
        process.stdout.write('[]');
        return 0;
    }

    const profile = mkdtempSync(join(tmpdir(), 'serojap-cdp-'));
    const port = await freePort();

    const child = spawn(CHROME, [
        '--headless=new',
        '--disable-gpu',
        '--no-sandbox',
        '--no-first-run',
        '--no-default-browser-check',
        '--disable-extensions',
        '--disable-background-networking',
        '--disable-component-update',
        '--disable-sync',
        '--disable-default-apps',
        '--disable-translate',
        '--disable-features=Translate',
        '--metrics-recording-only',
        '--mute-audio',
        '--password-store=basic',
        `--remote-debugging-port=${port}`,
        `--user-data-dir=${profile}`,
        'about:blank',
    ], { stdio: 'ignore' });

    let ws = null;
    const results = [];

    try {
        const url = await waitForDevtools(port, 40000);
        ws = new WebSocket(url);

        await new Promise((resolve, reject) => {
            ws.onopen = resolve;
            ws.onerror = () => reject(new Error('WebSocket DevTools gagal dibuka'));
        });

        const cdp = new Cdp(ws);
        await cdp.openPage();

        for (const job of jobs) {
            try {
                await cdp.viewport(job.width || 1280, job.height || 900);
                await cdp.scrollbars(job.hideScrollbars === true);
                await cdp.navigate(job.url, job.settle ?? 400);

                if (job.waitFor) {
                    const ok = await cdp.waitFor(job.waitFor, job.timeout ?? 20000);
                    if (!ok) {
                        throw new Error('waitFor tidak terpenuhi: ' + job.waitFor);
                    }
                }

                if (job.screenshot) {
                    await cdp.screenshot(job.screenshot, job.fullPage !== false);
                }

                const value = job.eval ? await cdp.evaluate(job.eval) : null;
                results.push({ ok: true, value });
            } catch (e) {
                results.push({ ok: false, error: String(e && e.message ? e.message : e) });
            }
        }

        try {
            await cdp.send('Browser.close');
        } catch {
            /* browser bisa sudah menutup diri */
        }
    } catch (e) {
        for (let i = results.length; i < jobs.length; i += 1) {
            results.push({ ok: false, error: String(e && e.message ? e.message : e) });
        }
    } finally {
        if (ws) {
            try {
                ws.close();
            } catch { /* abaikan */ }
        }
        child.kill();
        await sleep(300);
        rmSync(profile, { recursive: true, force: true });
    }

    process.stdout.write(JSON.stringify(results));
    return 0;
}

main().then((code) => process.exit(code)).catch((e) => {
    process.stdout.write(JSON.stringify([{ ok: false, error: String(e) }]));
    process.exit(1);
});
