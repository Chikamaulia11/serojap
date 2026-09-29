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

    async screenshot(out, fullPage = true, clip) {
        const params = { format: 'png' };
        if (clip) {
            /*
             * Crop per elemen. `Page.captureScreenshot` menerima
             * koordinat CSS, jadi rect harus diukur dari DOM-nya --
             * bukan diteruskan dari luar, karena pemanggil tidak
             * bisa tahu posisi elemen tanpa menjalankan JS juga.
             *
             * `scale: 1` sengaja: untuk memeriksa apakah teks benar
             *ibolata atau tidak, rasio 1:1 dengan pixel device
             * diperlukan. Kalau diskalakan, teks kecil jadi lebih
             * mudah terbaca daripada kenyataannya.
             */
            params.clip = {
                x: Math.max(0, clip.x),
                y: Math.max(0, clip.y),
                width: Math.max(1, clip.width),
                height: Math.max(1, clip.height),
                scale: 1,
            };
            params.captureBeyondViewport = true;
        } else if (fullPage) {
            params.captureBeyondViewport = true;
        }
        const data = await this.send('Page.captureScreenshot', params, this.session);
        mkdirSync(dirname(out), { recursive: true });
        writeFileSync(out, Buffer.from(data.data, 'base64'));
    }

    /**
     * Ukur rect elemen yang pertama cocok dengan `selector`, dalam
     * koordinat viewport CSS. Dipakai bareng `screenshot()` untuk
     * memotong gambar tepat pada elemennya.
     */
    async elementRect(selector) {
        const expr = `(() => {
            const el = document.querySelector(${JSON.stringify(selector)});
            if (!el) return null;
            const r = el.getBoundingClientRect();
            return { x: r.x + scrollX, y: r.y + scrollY, width: r.width, height: r.height };
        })()`;
        return this.evaluate(expr);
    }

    /**
     * Sama seperti `elementRect()`, tapi untuk "path" gaya
     * `pathOf()` dari `theme-audit-inject.js`:
     *
     *     body.bg-white.font-sans > aside#adminSidebar > a.flex.items-center
     *
     * Itu BUKAN selector. Class Tailwind yang memuat titik dua
     * -- `div.md:ml-60.flex` -- bakalan ditolak `querySelector` sebagai
     * pseudo-class, dan path yang memuat `:` atau `>` di dalam nama
     * class akan gagal. Segmen terakhir dipakai sebagai selector,
     * lalu kandidat dicocokkan satu per satu dengan membandingkan
     * rantainya sendiri dan memilih yang prefiks terpanjang cocok.
     */
    async elementRectForPath(desc) {
        const expr = `(() => {
            const want = ${JSON.stringify(desc)};
            const own = (el) => {
                const out = [];
                let n = el;
                while (n && n.nodeType === 1 && n.tagName !== 'HTML') {
                    let t = n.tagName.toLowerCase();
                    if (n.id) t += '#' + n.id;
                    else if (typeof n.className === 'string' && n.className.trim()) {
                        t += '.' + n.className.trim().split(/\\s+/).slice(0, 2).join('.');
                    }
                    out.unshift(t);
                    n = n.parentElement;
                }
                return out.slice(-6).join(' > ');
            };

            // Class Tailwind arbitrary-value -- 'text-[11px]', 'w-[calc(100%-2rem)]' --
            // dan class responsif dengan titik dua -- 'md:ml-60' -- BUKAN
            // selector CSS: yang pertama ditolak sebagai attribut, yang
            // kedua sebagai pseudo-class. Jadi segmen terakhir dicoba
            // apa adanya dulu, lalu class bermasalah itu dibuang.
            const segs = [want.split(' > ').pop()];
            const cleaned = segs[0]
                .split('.')
                .filter((c) => c && !c.includes('[') && !c.includes(':'))
                .join('.');
            if (cleaned && cleaned !== segs[0]) segs.push(cleaned);

            const b = want.split(' > ');
            let cands = [];
            for (const seg of segs) {
                try { cands = [...document.querySelectorAll(seg)]; } catch (e) { cands = []; }
                if (cands.length) break;
            }
            if (!cands.length) return null;

            let best = -1, el = null;
            for (const c of cands) {
                const a = own(c).split(' > ');
                let sc = 0;
                while (sc < Math.min(a.length, b.length) && a[a.length - 1 - sc] === b[b.length - 1 - sc]) sc++;
                if (sc > best) { best = sc; el = c; }
            }
            if (!el) return null;
            const r = el.getBoundingClientRect();
            return { x: r.x + scrollX, y: r.y + scrollY, width: r.width, height: r.height,
                     kandidat: cands.length };
        })()`;
        return this.evaluate(expr);
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

                if (job.screenshot || job.screenshotSelector || job.screenshotPath) {
                    let out = job.screenshot;
                    let clip = null;
                    if (job.screenshotPath) {
                        // `path` dari audit, bukan selector -- lihat
                        // `elementRectForPath()`.
                        clip = await cdp.elementRectForPath(job.screenshotPath);
                        if (!clip) {
                            throw new Error('path tidak bisa diresolve: ' + job.screenshotPath);
                        }
                    } else if (job.screenshotSelector) {
                        clip = await cdp.elementRect(job.screenshotSelector);
                        if (!clip) {
                            throw new Error('selector tidak cocok: ' + job.screenshotSelector);
                        }
                    }
                    if (clip) {
                        // Sisakan konteks visual: crop yang persis
                        // sebesar elemen sering cuma menghasilkan
                        // potongan teks tanpa tepi, sehingga yang
                        // diuji kontrasnya tidak terlihat.
                        const pad = job.screenshotPad ?? 12;
                        clip = {
                            x: clip.x - pad,
                            y: clip.y - pad,
                            width: clip.width + pad * 2,
                            height: clip.height + pad * 2,
                        };
                        if (!out) {
                            out = job.screenshotDir
                                ? job.screenshotDir + '/' + (job.name || 'elemen') + '.png'
                                : (job.screenshotPath || job.screenshotSelector)
                                    .replace(/[^a-z0-9]+/gi, '_') + '.png';
                        }
                    }
                    await cdp.screenshot(out, job.fullPage !== false, clip);
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

        /* Tunggu proses benar-benar berhenti SEBELUM menghapus
         * profilnya.
         *
         * `Browser.close` hanya meminta keluar; Chrome masih memegang
         * berkas profil (leveldb, `SingletonLock`) selama beberapa
         * ratus milidetik. `sleep(300)` yang lama tidak cukup saat
         * audit penuh 75 kombinasi -- `rmSync` lokal soal
         * `EPERM: Permission denied` dan satu job ikut gagal karena
         * cleanup-nya, bukan karena auditnya. */
        if (child.exitCode === null && child.signalCode === null) {
            const berhenti = new Promise((done) => {
                child.once('exit', done);
                child.once('close', done);
            });
            child.kill();
            await Promise.race([berhenti, sleep(5000)]);
        }

        /* Penghapusannya: kalau Windows masih memegang berkas, coba lagi
         * sebentar. Kegagalan membersihkan temp tidak invalidate
         * hasil audit, jadi error-nya ditelan, bukan dilempar. */
        for (let i = 0; i < 10; i += 1) {
            try {
                rmSync(profile, { recursive: true, force: true });
                break;
            } catch {
                if (i === 9) break;
                await sleep(200);
            }
        }
    }

    process.stdout.write(JSON.stringify(results));
    return 0;
}

main().then((code) => process.exit(code)).catch((e) => {
    process.stdout.write(JSON.stringify([{ ok: false, error: String(e) }]));
    process.exit(1);
});
