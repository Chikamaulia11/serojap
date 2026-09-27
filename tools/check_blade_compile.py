#!/usr/bin/env python3
"""
Memastikan setiap view Blade benar-benar bisa dikompilasi.

`check_blade_tags.py` menghitung tag HTML, dan `view:cache` dilaporkan
sukses even views yang rusak. Kedua hal itu sudah terbukti tidak cukup:

  - `{{-- ... --}}` yang penutupnya ditulis `--}` (satu kurawal) membuat Blade
    menganggap seluruh HTML di bawahnya masih bagian komentar, sehingga
    tag jadi tidak seimbang padahal sumbernya "terlihat benar".
  - `@endelse` bukan directive Blade, jadi dia dibiarkan sebagai teks
    literal. `<?php else: ?>` yang dibuka `@else` tidak pernah ditutup,
    dan PHP baru protes saat view itu di-render -- bukan saat
    `view:cache` dijalankan.

Keduanya lolos `check_blade_tags.py` DAN `php artisan view:cache`.

Skrip ini menutup celah itu dengan cara yang tidak menebak: view
dikompilasi oleh Blade compiler milik aplikasi sendiri, hasilnya
ditulis ke disk, lalu setiap file dilint dengan `php -l`. Syntax error
tidak mungkin lolos karena yang diperiksa adalah PHP hasil kompilasi,
bukan teks Blade.

Di luar itu, pasangan komentar `{{--` / `--}}` dicek langsung di
sumber, karena penutup yang salah kurawal tidak selalu menghasilkan
error PHP -- kadang hanya menghapus HTML secara diam-diam.

Butuh PHP. Berbeda dengan tiga pemeriksa lain yang murni Python, ini
memakai compiler milik Laravel, jadi tidak ada yang bisa ditiru
dengan regex.

Pakai:
    python3 tools/check_blade_compile.py
    python3 tools/check_blade_compile.py resources/views/errors/
    PHP_BIN=/path/ke/php python3 tools/check_blade_compile.py
"""

import os
import re
import shutil
import subprocess
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
VIEWS = ROOT / "resources" / "views"

# Dipakai untuk memindai pasangan komentar Blade di sumber.
KOMENTAR_BUKA = re.compile(r"\{\{--")
KOMENTAR_TUTUP = re.compile(r"--\}\}")


def cari_php() -> str | None:
    """Temukan PHP: env PHP_BIN dulu, lalu php.exe/php di PATH."""
    dari_env = os.environ.get("PHP_BIN")
    if dari_env and (shutil.which(dari_env) or Path(dari_env).exists()):
        return dari_env
    for nama in ("php.exe", "php"):
        found = shutil.which(nama)
        if found:
            return found
    return None


def ke_path_php(path) -> str:
    """
    Windows PHP tidak bisa membuka path WSL seperti `/mnt/c/...`.

    Kalau skrip dijalankan dari WSL tapi PHP-nya `php.exe`, path-nya harus
    diterjemahkan lebih dulu. Di luar itu, path dibiarkan apa adanya.
    """
    p = str(path)
    m = re.match(r"^/mnt/([a-zA-Z])/(.*)$", p)
    if m:
        return f"{m.group(1).upper()}:\\{m.group(2).replace('/', os.sep)}"
    return p


def ke_path_wsl(path) -> str:
    """
    Kebalikan dari `ke_path_php`: "C:\\x\\y" -> "/mnt/c/x/y".

    Driver PHP mengembalikan path Windows. Python di WSL tidak bisa
    `os.path.exists()` path itu, jadi harus dikembalikan dulu ke bentuk
    yang bisa di-stat.
    """
    p = str(path)
    m = re.match(r"^([a-zA-Z]):[\\/](.*)$", p)
    if m:
        sisa = m.group(2).replace("\\", "/")
        return f"/mnt/{m.group(1).lower()}/{sisa}"
    return p


def cek_komentar(path: Path) -> list[str]:
    """
    Pasangan `{{--` dan `--}}` harus seimbang.

    Penutup yang ditulis `--}` kehilangan satu kurawal tidak ketahuan Blade sebagai
    penutup, jadi regex Bladevp eats markup sesudahnya sebagai bagian
    komentar. Di sini jumlah pembuka dan penutup dihitung langsung.
    """
    text = path.read_text(encoding="utf-8", errors="replace")
    buka = len(KOMENTAR_BUKA.findall(text))
    tutup = len(KOMENTAR_TUTUP.findall(text))
    if buka == tutup:
        return []

    masalah = [
        f"  {path.relative_to(ROOT)}: komentar Blade tidak seimbang "
        f"({buka} pembuka `{{{{--` vs {tutup} penutup `--}}}}`)"
    ]

    # Cari penutup yang salah kurawal: `--}` yang tidak diikuti `}`.
    for m in re.finditer(r"--\}(?!\})", text):
        baris = text.count("\n", 0, m.start()) + 1
        masalah.append(
            f"  {path.relative_to(ROOT)}:{baris}: penutup komentar ditulis "
            f"`--}}`, harus `--}}}}`"
        )
    return masalah


DRIVER = r"""<?php
// Driver: kompilasi setiap view Blade pakai compiler milik aplikasi,
// lalu tulis PHP hasilnya supaya bisa dilint `php -l`.
$root = rtrim(str_replace('\\', '/', $argv[2]), '/');
require $root . '/vendor/autoload.php';
$app = require_once $root . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$views = $app['config']->get('view.paths')[0];
$out = $argv[1];

$rii = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($views, FilesystemIterator::SKIP_DOTS)
);

$files = [];
foreach ($rii as $f) {
    if ($f->isFile() && str_ends_with($f->getFilename(), '.blade.php')) {
        $files[] = $f->getPathname();
    }
}
sort($files);

$blade = app('blade.compiler');
foreach ($files as $i => $path) {
    $rel = 'resources/views/'
    . str_replace('\\', '/', substr($path, strlen(realpath($views)) + 1));
    try {
        $php = $blade->compileString(file_get_contents($path));
    } catch (Throwable $e) {
        echo "COMPILE_ERR\t" . $rel . "\t" . str_replace("\n", ' ', $e->getMessage()) . "\n";
        continue;
    }
    $dest = $out . DIRECTORY_SEPARATOR . $i . '.php';
    file_put_contents($dest, $php);
    echo "OK\t" . $rel . "\t" . $dest . "\n";
}
"""


def main() -> int:
    if not VIEWS.is_dir():
        print(f"  {VIEWS} tidak ada")
        return 1

    php = cari_php()
    if not php:
        print("  PHP tidak ditemukan. Set PHP_BIN ke path php/php.exe.")
        return 1

    # Target: seluruh view, atau hanya subpath yang diminta.
    if len(sys.argv) > 1:
        relatif = [Path(a).resolve() for a in sys.argv[1:]]
        target = []
        for r in relatif:
            if r.is_dir():
                target.extend(sorted(r.rglob("*.blade.php")))
            elif r.exists():
                target.append(r)
        if not target:
            print(f"  Tidak ada view di: {', '.join(sys.argv[1:])}")
            return 1
    else:
        target = sorted(VIEWS.rglob("*.blade.php"))

    masalah: list[str] = []

    # 1. Pasangan komentar Blade, langsung di sumber.
    for path in target:
        masalah.extend(cek_komentar(path))

    # 2. Kompilasi + php -l, untuk yang benar-benar bisa di-render.
    #
    # Direktori kerja harus terlihat oleh PHP. Kalau skrip jalan di WSL tapi
    # PHP-nya `php.exe`, `/tmp/...` milik WSL tidak bisa dibuka Windows --
    # driver akan gagal menulis dan tidak jadi mengeluarkan baris `OK`.
    workdir = ROOT / "storage" / "framework" / "views" / "_blade_check"
    workdir.mkdir(parents=True, exist_ok=True)
    driver = workdir / "_driver.php"
    outdir = workdir / "compiled"
    outdir.mkdir(exist_ok=True)
    for sisa in outdir.glob("*.php"):
        sisa.unlink()
    driver.write_text(DRIVER, encoding="utf-8")

    try:
        proses = subprocess.run(
            [php, ke_path_php(driver), ke_path_php(outdir), ke_path_php(ROOT)],
            # `cwd` tetap path WSL: subprocess di WSL itu POSIX exec, jadi
            # cwd harus bisa di-chdir oleh kernel Linux, bukan Windows.
            cwd=str(ROOT),
            capture_output=True,
            text=True,
            errors="replace",
        )
    finally:
        driver.unlink(missing_ok=True)

    kompilasi_gagal = 0
    jumlah = 0
    gagal: set[str] = set()
    for ln in proses.stdout.splitlines():
        bagian = ln.split("\t")
        if not bagian or not bagian[0]:
            continue
        if bagian[0] == "COMPILE_ERR":
            kompilasi_gagal += 1
            rel = bagian[1] if len(bagian) > 1 else "?"
            msg = bagian[2] if len(bagian) > 2 else ""
            gagal.add(rel)
            masalah.append(f"  {rel}: kompilasi gagal - {msg}")
            continue
        if bagian[0] != "OK":
            continue

        jumlah += 1
        rel = bagian[1]
        compiled = bagian[2] if len(bagian) > 2 else None
        lokal = ke_path_wsl(compiled) if compiled else ""
        if not lokal or not os.path.exists(lokal):
            gagal.add(rel)
            masalah.append(f"  {rel}: PHP hasil kompilasi tidak tertulis")
            continue

        lint = subprocess.run(
            [php, "-l", ke_path_php(lokal)],
            capture_output=True,
            text=True,
            errors="replace",
        )
        if lint.returncode != 0:
            # `php -l` mencetak judul "Errors parsing <file>" lalu baris
            # yang menyebut penyebabnya. Yang berguna baris "Parse error",
            # jadi ambil itu dan buang judulnya.
            gabungan = (lint.stdout or "") + (lint.stderr or "")
            semua = [g.strip() for g in gabungan.splitlines() if g.strip()]
            sebab = [g for g in semua if "Parse error" in g or "Fatal error" in g]
            detail = sebab[0] if sebab else (semua[0] if semua else "php -l gagal tanpa pesan")
            gagal.add(rel)
            bersih = detail.replace(ke_path_php(outdir), "<php hasil kompilasi>")
            bersih = bersih.replace(str(outdir), "<php hasil kompilasi>")
            masalah.append(f"  {rel}: PHP hasil kompilasi tidak valid - {bersih}")

    # Pengaman: alat yang hijau tanpa memeriksa apa pun lebih berbahaya
    # daripada alat yang merah. Kalau driver tidak mengeluarkan satu baris
    # `OK` pun, itu kegagalan alat, bukan view yang sehat.
    if jumlah == 0:
        masalah.append("  Driver kompilasi tidak mengeluarkan hasil sama sekali.")
        gagal.update(f"<(driver gagal, {len(target)} view tidak diperiksa)>")
        for ln in (proses.stdout + proses.stderr).strip().splitlines()[:6]:
            if ln.strip():
                masalah.append(f"    {ln.strip()}")
    elif jumlah + kompilasi_gagal < len(target):
        masalah.append(
            f"  Hanya {jumlah + kompilasi_gagal} dari {len(target)} view "
            f"yang diproses; sisanya tidak masuk ke driver."
        )

    shutil.rmtree(workdir, ignore_errors=True)

    total = len(target)
    if masalah:
        print()
        for m in masalah:
            print(m)
        print()
        print(f"  {max(0, total - len(gagal))}/{total} view lolos kompilasi.")
        return 1

    print()
    print(f"  {total}/{total} view lolos kompilasi dan php -l.")
    return 0


if __name__ == "__main__":
    sys.exit(main())
