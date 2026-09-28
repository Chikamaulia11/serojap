#!/usr/bin/env python3
"""Screenshot halaman audit tema pada kombinasi warna tertentu.

Halaman di `public/theme-audit/` sudah HTML statis hasil render. Untuk
memaksa tema tanpa bergantung pada localStorage, atribut `data-accent` dan
`data-mode-resolved` disuntik langsung ke tag `<html>` -- sama seperti
yang dilakukan script anti-FOUC di produksi, nilainya ditetapkan lebih
dahulu, sehingga yang ter-render persis tema yang diminta.

Server menyajikan `public/` supaya path absolut `/build/assets/...`
tersedia, meniru kondisi halaman asli.

Pakai:
    python3 tools/shot_theme.py pelapor-dashboard teal dark
    python3 tools/shot_theme.py admin-statistik rose dark --panel --w=375
"""
import json
import subprocess
import sys
import time
from http.server import SimpleHTTPRequestHandler, ThreadingHTTPServer
from pathlib import Path
from threading import Thread

ROOT = Path(__file__).resolve().parent.parent
SRC = ROOT / "public" / "theme-audit"
RUN = ROOT / "public" / "theme-audit-run"
SHOT = ROOT / "storage" / "app" / "theme-shots"


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


def main():
    args = sys.argv[1:]
    flags = [a for a in args if a.startswith("--")]
    pos = [a for a in args if not a.startswith("--")]
    if len(pos) != 3:
        sys.exit(__doc__)
    page, accent, mode = pos
    open_panel = "--panel" in flags
    width = 1280
    height = 1200
    for f in flags:
        if f.startswith("--w="):
            width = int(f[4:])
        if f.startswith("--h="):
            height = int(f[4:])

    src = SRC / (page + ".html")
    if not src.exists():
        matches = sorted(p.stem for p in SRC.glob("*.html") if page in p.stem)
        sys.exit(f"  Halaman tidak ada: {page}\n  tersedia: {matches}")

    RUN.mkdir(parents=True, exist_ok=True)
    SHOT.mkdir(parents=True, exist_ok=True)

    tag = f"{page}-{accent}-{mode}{'-panel' if open_panel else ''}"
    out_html = RUN / f"shot-{tag}.html"
    out_png = SHOT / f"{tag}.png"

    html = src.read_text(encoding="utf-8")
    html = html.replace(
        "<html", f'<html data-accent="{accent}" data-mode="{mode}" '
                 f'data-mode-resolved="{mode}"', 1)
    if open_panel:
        html = html.replace('data-open="false"', 'data-open="true"', 1)
        html = html.replace('aria-expanded="false"', 'aria-expanded="true"', 1)
    out_html.write_text(html, encoding="utf-8")

    port = 8741
    httpd = serve(ROOT / "public", port)
    time.sleep(0.4)
    try:
        url = f"http://127.0.0.1:{port}/theme-audit-run/{out_html.name}"
        job = {
            "url": url,
            "width": width,
            "height": height,
            "settle": 700,
            # HP asli memakai scrollbar overlay, jadi untuk foto
            # scrollbar disembunyikan. Tanpa ini panel 375px hanya
            # jadi 360px dan tidak mewakili yang dilihat pengguna.
            "hideScrollbars": True,
            "screenshot": win(out_png),
            "eval": "String(window.innerWidth)",
        }
        # Lebar dipaksakan lewat `Emulation.setDeviceMetricsOverride`, bukan
        # `--window-size`: Chrome di Windows tidak bisa membuat jendela
        # lebih sempit dari 500px, jadi screenshot "375px" versi lama
        # sebenarnya 500px dan menipu di setiap laporan.
        # `node.exe` adalah proses Windows, jadi path-nya harus versi Windows.
        proc = subprocess.run(
            ["node.exe", win(ROOT / "tools" / "cdp.js")],
            input=json.dumps([job]), capture_output=True, text=True, timeout=300,
        )
        try:
            res = json.loads(proc.stdout)[0]
        except (json.JSONDecodeError, IndexError):
            sys.exit(f"  tools/cdp.js tidak mengembalikan JSON.\n"
                     f"  stdout: {proc.stdout[-400:]}\n"
                     f"  stderr: {proc.stderr[-400:]}")
        if not res.get("ok"):
            sys.exit(f"  Screenshot gagal: {res.get('error')}")
        if res.get("value") != str(width):
            print(f"  PERINGATAN: lebar diminta {width}, "
                  f"tapi browser memakai {res.get('value')}")
    finally:
        httpd.shutdown()

    if not out_png.exists():
        sys.exit("  Screenshot gagal: file PNG tidak terbentuk")
    print(f"  {out_png.relative_to(ROOT)}  ({out_png.stat().st_size:,} bytes)  "
          f"lebar nyata {res.get('value')}px")
    return 0


if __name__ == "__main__":
    sys.exit(main())
