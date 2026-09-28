#!/usr/bin/env python3
"""Ukur geometri panel picker tema dengan `getBoundingClientRect()`.

Halaman di `public/theme-audit/` adalah HTML statis hasil render, jadi
`theme.js` (module di `build/assets`) tetap dimuat dan picker-nya bisa
diklik seperti di halaman asli.

Lebar yang diminta dipaksakan lewat `Emulation.setDeviceMetricsOverride`
(`tools/cdp.js`), bukan `--window-size`: Chrome di Windows tidak bisa
membuat jendela lebih sempit dari 500px, jadi pengukuran "375px" versi
lama sebenarnya 500px.

Untuk tiap lebar, skrip ini:

  1. membuka menu mobile bila ada, supaya picker versi mobile ikut
     diukur (di bawah 640px itu satu-satunya yang tampil);
  2. klik tiap tombol `.theme-dd-btn` yang terlihat, lalu mengukur
     panelnya;
  3. mencatat apakah panel SEPENUHNYA di dalam viewport, apakah ada
     leluhur yang memotongnya (`overflow` bukan `visible`), dan
     nilai `data-drop` yang dipasang `theme.js`.

Hasilnya ditulis ke stdout. Keluarannya bukan "tampak benar" --
sekarang ini angka pengukuran.

Pakai:
    python3 tools/probe_picker.py
    python3 tools/probe_picker.py --pages publik-beranda publik-login
    python3 tools/probe_picker.py --widths 375
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

DEFAULT_PAGES = [
    "publik-beranda",      # welcome, picker di baris tombol hero
    "publik-login",       # guest layout, picker di baris logo
    "pelapor-dashboard",  # navbar + menu mobile
    "pelapor-buat-laporan",
    "admin-dashboard",    # sidebar
    "admin-statistik",
    "super-dashboard",    # sidebar
    "super-akun",
]
DEFAULT_WIDTHS = [1280, 768, 375]

PROBE_JS = r"""
(function () {
    function visible(el) {
        if (!el) return false;
        var r = el.getBoundingClientRect();
        if (r.width === 0 && r.height === 0) return false;
        return getComputedStyle(el).display !== 'none';
    }

    function clipper(node, panel) {
        var out = null;
        for (var p = panel.parentElement; p; p = p.parentElement) {
            var cs = getComputedStyle(p);
            if (cs.overflow !== 'visible' || cs.overflowX !== 'visible' ||
                cs.overflowY !== 'visible') {
                var pr = p.getBoundingClientRect();
                var pl = panel.getBoundingClientRect();
                if (pl.left < pr.left - 0.5 || pl.right > pr.right + 0.5 ||
                    pl.top < pr.top - 0.5 || pl.bottom > pr.bottom + 0.5) {
                    out = p.tagName.toLowerCase() +
                        (p.id ? '#' + p.id : '') +
                        (p.className && typeof p.className === 'string'
                            ? '.' + p.className.trim().split(/\s+/).join('.') : '');
                    break;
                }
            }
        }
        return out;
    }

    function round(n) { return Math.round(n * 10) / 10; }

    function measureAll() {
        var results = [];
        var vw = window.innerWidth;
        var vh = window.innerHeight;

        document.querySelectorAll('.theme-dd').forEach(function (root) {
            var variant = (root.className.match(/theme-dd--(\w+)/) || [])[1] || 'default';
            var btn = root.querySelector('.theme-dd-btn');
            var panel = root.querySelector('.theme-dd-panel');

            if (!btn || !panel) return;

            if (!visible(root)) {
                results.push({ variant: variant, shown: false });
                return;
            }

            var wasOpen = panel.getAttribute('data-open') === 'true';
            if (!wasOpen) btn.click();

            var br = btn.getBoundingClientRect();
            var pr = panel.getBoundingClientRect();
            var cs = getComputedStyle(panel);

            results.push({
                variant: variant,
                shown: true,
                drop: panel.getAttribute('data-drop') || 'down',
                shift: panel.style.getPropertyValue('--dd-shift') || '0px',
                width: round(pr.width),
                trigger: { left: round(br.left), right: round(br.right), top: round(br.top) },
                panel: {
                    left: round(pr.left), right: round(pr.right),
                    top: round(pr.top), bottom: round(pr.bottom),
                },
                insideViewport:
                    pr.left >= 0 && pr.top >= 0 &&
                    pr.right <= vw + 0.5 && pr.bottom <= vh + 0.5,
                panelCss: {
                    position: cs.position,
                    width: cs.width,
                    zIndex: cs.zIndex,
                },
                clippedBy: clipper(root, panel),
            });

            if (!wasOpen) btn.click();
        });

        return { vw: vw, vh: vh, results: results };
    }

    function run() {
        // Tunggu font dulu. Halaman memuat Google Fonts, dan kalau
        // font baru selesai di-load sesudah panel diukur, tinggi
        // baris berubah sehingga keputusan `data-drop="up"` yang
        // sudah ditulis theme.js jadi basi -- hasil pengukuran
        // lalu beda antar-jalannya tanpa ada perubahan CSS pun.
        var go = function () {
            var payload;
            try {
                var menu = document.getElementById('mobileMenu');
                if (menu && window.innerWidth < 640) menu.classList.add('show');
                payload = measureAll();
            } catch (e) {
                payload = { error: String(e && e.stack || e) };
            }
            var pre = document.createElement('pre');
            pre.id = 'probe-out';
            pre.textContent = JSON.stringify(payload);
            document.body.appendChild(pre);
        };

        if (document.fonts && document.fonts.ready) {
            document.fonts.ready.then(go, go);
        } else {
            go();
        }
    }

    if (document.readyState === 'complete') {
        setTimeout(run, 400);
    } else {
        window.addEventListener('load', function () { setTimeout(run, 400); });
    }
})();
"""


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


def probe_all(pages, widths, height, port):
    """Jalankan probe untuk seluruh halaman x lebar dalam satu Chrome.

    Hasil dibaca lewat `textContent` elemennya sendiri, bukan dari penanda
    teks: skrip probe ikut ikutan tercetak di source halaman, jadi pencarian
    string yang sama akan menemukan tag-nya lebih dulu.

    `waitFor` dipakai supaya pengukuran baru diambil setelah skrip probe
    selesai (font siap + 400ms), bukan pada `load` saja.
    """
    RUN.mkdir(parents=True, exist_ok=True)
    jobs, keep = [], []

    for page in pages:
        html = (SRC / (page + ".html")).read_text(encoding="utf-8")
        html = html.replace("</body>", f"<script>{PROBE_JS}</script></body>", 1)
        out = RUN / f"probe-{page}.html"
        out.write_text(html, encoding="utf-8")
        keep.append(out)
        for width in widths:
            jobs.append({
                "url": f"http://127.0.0.1:{port}/theme-audit-run/{out.name}",
                "width": width,
                "height": height,
                "settle": 150,
                "waitFor": "document.getElementById('probe-out') ? 1 : ''",
                "eval": "document.getElementById('probe-out').textContent",
            })

    if not jobs:
        return []

    # `node.exe` adalah proses Windows: path-nya harus versi Windows.
    proc = subprocess.run(
        ["node.exe", win(ROOT / "tools" / "cdp.js")],
        input=json.dumps(jobs), capture_output=True, text=True, timeout=3600,
    )
    try:
        raw = json.loads(proc.stdout)
    except json.JSONDecodeError:
        sys.exit(f"  tools/cdp.js tidak mengembalikan JSON.\n"
                 f"  stderr: {proc.stderr[-500:]}")

    rows = []
    for (job, res) in zip(jobs, raw):
        page = Path(job["url"]).name
        page = page[len("probe-"):-len(".html")]
        row = {"page": page, "width": job["width"]}
        if not res.get("ok"):
            row["error"] = str(res.get("error"))[:300]
        else:
            try:
                row.update(json.loads(res.get("value") or "{}"))
            except json.JSONDecodeError:
                row["error"] = "probe tidak mengembalikan JSON yang valid"
        row["page"], row["width"] = page, job["width"]
        rows.append(row)

    for f in keep:
        f.unlink(missing_ok=True)
    return rows


def main():
    args = sys.argv[1:]
    pages = DEFAULT_PAGES
    widths = DEFAULT_WIDTHS
    rest = []
    i = 0
    while i < len(args):
        if args[i] == "--pages":
            i += 1
            pages = args[i].split(",")
        elif args[i] == "--widths":
            i += 1
            widths = [int(x) for x in args[i].split(",")]
        else:
            rest.append(args[i])
        i += 1

    height = 1200
    for r in rest:
        if r.startswith("--h="):
            height = int(r[4:])

    missing = [p for p in pages if not (SRC / (p + ".html")).exists()]
    if missing:
        sys.exit(f"  Halaman tidak ada: {', '.join(missing)}\n"
                 f"  Jalankan dulu: php artisan test --filter=ThemeAuditDumpTest")

    port = 8743
    httpd = serve(ROOT / "public", port)
    time.sleep(0.4)
    try:
        rows = probe_all(pages, widths, height, port)
    finally:
        httpd.shutdown()

    print()
    print(f"  {'halaman':<20} {'w':>5} {'varian':<9} "
          f"{'lebar':>6} {'drop':>5} {'dalam VP':>9}  panel")
    print("  " + "-" * 96)

    bad = 0
    for row in rows:
        if "error" in row:
            print(f"  {row['page']:<20} {row['width']:>5}  ERROR "
                  f"{row.get('error', '')[:60]}")
            bad += 1
            continue
        for r in row.get("results", []):
            if not r.get("shown"):
                print(f"  {row['page']:<20} {row['width']:>5} "
                      f"{r['variant']:<9} {'-':>6} {'-':>5} {'disembunyi':>9}")
                continue
            p = r["panel"]
            ok = "ya" if r["insideViewport"] else "TIDAK"
            if not r["insideViewport"]:
                bad += 1
            clip = r["clippedBy"]
            if clip:
                bad += 1
            note = ""
            if clip:
                note = f"  TERPOTONG oleh {clip}"
            if not r["insideViewport"]:
                note = (f"  KELUAR viewport "
                        f"(l={p['left']} t={p['top']} r={p['right']} b={p['bottom']})"
                        + note)
            print(f"  {row['page']:<20} {row['width']:>5} {r['variant']:<9} "
                  f"{r['width']:>6} {r['drop']:>5} {ok:>9}{note}")

    print()
    if bad:
        print(f"  {bad} masalah geometri. Perbaiki sebelum commit.")
        return 1
    print(f"  Semua panel di dalam viewport dan tidak terpotong "
          f"({len(rows)} halaman x {len(widths)} lebar).")
    return 0


if __name__ == "__main__":
    sys.exit(main())
