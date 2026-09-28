#!/usr/bin/env python3
"""Audit tema dengan Chrome DevTools Protocol terhadap halaman asli.

Halaman asli dibuang oleh `tests/Feature/ThemeAuditDumpTest.php` ke
`public/theme-audit/`. Script ini menyajikan folder `public/` lewat HTTP
sederhana (supaya `/build/assets/...` dari `@vite` benar-benar termuat),
menyuntik `theme-audit-inject.js` ke tiap halaman, lalu memuat setiap
halaman pada beberapa lebar layar.

Lebar yang diminta BENAR-BENAR dipakai. Dulu skrip memakai
`--window-size=375`, padahal Chrome di Windows tidak bisa membuat
jendela lebih sempit dari 500px, jadi audit "375px" lama sebenarnya
mengukur 500px. Sekarang lebarnya dipaksakan lewat
`Emulation.setDeviceMetricsOverride` (lihat `tools/cdp.js`), dan
`window.innerWidth` diverifikasi 375 sungguhan.

Yang diukur per halaman x lebar x 12 kombinasi tema:
  - kontras setiap text node yang terlihat vs latar efektif
  - overflow horizontal beserta elemen yang meluap

Keluaran: ringkasan ke stdout + `storage/app/theme-audit.json` untuk
dipakai sebagai bahan commit/laporan.

Pakai:
    python3 tools/audit_theme.py            # semua halaman
    python3 tools/audit_theme.py pelapor    # hanya yang namanya cocok
"""
import json
import shutil
import subprocess
import sys
import time
from http.server import SimpleHTTPRequestHandler, ThreadingHTTPServer
from pathlib import Path
from threading import Thread

ROOT = Path(__file__).resolve().parent.parent
SRC = ROOT / "public" / "theme-audit"
RUN = ROOT / "public" / "theme-audit-run"
INJECT = ROOT / "tools" / "theme-audit-inject.js"
WIDTHS = [1280, 768, 375]
OUT_JSON = ROOT / "storage" / "app" / "theme-audit.json"


def win(p: Path) -> str:
    return subprocess.run(["wslpath", "-w", str(p)], capture_output=True,
                          text=True, check=True).stdout.strip()


def serve(directory: Path, port: int):
    class Quiet(SimpleHTTPRequestHandler):
        def log_message(self, *a):
            pass

    handler = lambda *a, **k: Quiet(*a, directory=str(directory), **k)
    httpd = ThreadingHTTPServer(("127.0.0.1", port), handler)
    Thread(target=httpd.serve_forever, daemon=True).start()
    return httpd


def prepare():
    if not SRC.exists() or not list(SRC.glob("*.html")):
        sys.exit(
            f"  Tidak ada halaman di {SRC}.\n"
            "  Jalankan dulu: php artisan test --filter=ThemeAuditDumpTest"
        )
    if not INJECT.exists():
        sys.exit(f"  Script audit tidak ada: {INJECT}")
    if RUN.exists():
        shutil.rmtree(RUN)
    RUN.mkdir(parents=True)
    script = INJECT.read_text(encoding="utf-8")
    for src in sorted(SRC.glob("*.html")):
        html = src.read_text(encoding="utf-8")
        tag = f'<script src="/theme-audit-run/{INJECT.name}"></script>'
        # Inject sebelum </body> supaya DOM sudah lengkap.
        if "</body>" in html:
            html = html.replace("</body>", tag + "\n</body>", 1)
        else:
            html += tag
        (RUN / src.name).write_text(html, encoding="utf-8")
    shutil.copy(INJECT, RUN / INJECT.name)
    return sorted(SRC.glob("*.html"))


def node_jobs(pages, port: int):
    """Bangun daftar kerja untuk satu-siapan Chrome lewat `cdp.js`.

    `tools/cdp.js` dijalankan sekali untuk SELURUH halaman x lebar:
    menyalakan Chrome baru butuh ~2 detik, dan 25 halaman x 3 lebar
    jadi 75 kali lipatan. Wait, uji `--window-size` dulu: Chrome di
    Windows tidak bisa membuat jendela lebih sempit dari 500px, jadi
    lebar 375 harus lewat `Emulation.setDeviceMetricsOverride`.
    """
    jobs = []
    for page in pages:
        for w in WIDTHS:
            jobs.append({
                "url": f"http://127.0.0.1:{port}/theme-audit-run/{page.name}",
                "width": w,
                "height": 900,
                "settle": 350,
                "waitFor": "document.getElementById('theme-audit-result') ? 1 : ''",
                "eval": "document.getElementById('theme-audit-result').textContent",
            })
    return jobs


def run_jobs(jobs):
    """Jalankan `cdp.js` dan kembalikan daftar hasil (JSON mentah)."""
    if not jobs:
        return []
    proc = subprocess.run(
        # `node.exe` itu proses Windows, jadi path-nya harus versi
        # Windows. Path POSIX akan berakhir sebagai MODULE_NOT_FOUND.
        ["node.exe", win(ROOT / "tools" / "cdp.js")],
        input=json.dumps(jobs), capture_output=True, text=True, timeout=3600,
    )
    try:
        return json.loads(proc.stdout)
    except json.JSONDecodeError:
        sys.exit(f"  tools/cdp.js tidak mengembalikan JSON.\n"
                 f"  stdout: {proc.stdout[-500:]}\n"
                 f"  stderr: {proc.stderr[-500:]}")


def main():
    only = sys.argv[1] if len(sys.argv) > 1 else ""
    pages = prepare()
    if only:
        pages = [p for p in pages if only in p.name]
        if not pages:
            sys.exit(f"  Tidak ada halaman cocok dengan '{only}'")

    port = 8731
    httpd = serve(ROOT / "public", port)
    time.sleep(0.4)

    all_res = []
    t0 = time.time()
    try:
        jobs = node_jobs(pages, port)
        results = run_jobs(jobs)

        total = len(pages) * len(WIDTHS)
        for idx, (job, res) in enumerate(zip(jobs, results), 1):
            name = Path(job["url"]).name
            w = job["width"]

            if not res.get("ok"):
                print(f"  [{idx}/{total}] {name:<26} @{w:<5} "
                      f"GAGAL: {str(res.get('error'))[:90]}", flush=True)
                continue

            try:
                data = json.loads(res.get("value") or "null")
            except json.JSONDecodeError:
                data = None
            if not data:
                print(f"  [{idx}/{total}] {name:<26} @{w:<5} "
                      f"GAGAL parse", flush=True)
                continue
            if "fatal" in data:
                print(f"  [{idx}/{total}] {name:<26} @{w:<5} "
                      f"ERROR: {data['fatal'][:120]}", flush=True)
                continue

            data["file"] = name
            data["width"] = w
            all_res.append(data)

            nf = sum(len(r["fails"]) for r in data["results"])
            # Overflow dihitung sebagai: `scrollWidth > clientWidth`,
            # ATAU ada elemen yang lebarnya sama dengan `100vw`.
            # `vwSlack` sendiri selalu 15 di sini (emulasi CDP memang
            # menyisakan ruang scrollbar klasik), jadi itu bukan
            # temuan -- yang jadi temuan adalah elemen yang memakainya.
            no = sum(1 for r in data["results"]
                     if r["overflow"] or r.get("vwCulprits"))
            print(f"  [{idx}/{total}] {name:<26} @{w:<5} "
                  f"kontras_gagal={nf:<4} overflow={no}", flush=True)
    finally:
        httpd.shutdown()

    OUT_JSON.parent.mkdir(parents=True, exist_ok=True)
    OUT_JSON.write_text(json.dumps(all_res, indent=1), encoding="utf-8")

    print()
    print(f"  selesai dalam {time.time()-t0:.0f}s -> {OUT_JSON.relative_to(ROOT)}")
    return 0


if __name__ == "__main__":
    sys.exit(main())
