#!/usr/bin/env python3
"""
Pemeriksa keseimbangan kurung JS + deteksi pola berbahaya.

`node --check` adalah alat yang benar, tapi Node tidak tersedia di
lingkungan kerja ini, dan kurung yang tidak seimbang di file publik
berarti 500 di halaman (atau lebih buruk: error JS senyap). Script
ini bukan pengganti `node --check` -- dia hanya menahan kelas
kesalahan yang paling sering muncul saat menyunting file tanpa
menjalankan apa pun.

Yang diperiksa:
  1. Kurung (), [], {} harus seimbang dan tidak bercabang ke luar.
  2. String dan template literal harus tertutup; escaped quote di
     dalamnya tidak boleh dihitung sebagai penutup.
  3. Komentar harus tertutup.
  4. Pola berisiko: `==`/`!=` di dalam if (kecuali `!==`), dan
     `console.log` yang tertinggal.

Pakai:
    python3 tools/check_js.py                    # semua file js
    python3 tools/check_js.py public/js/form.js   # file tertentu
"""

import re
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent

PAIRS = {")": "(", "]": "[", "}": "{"}
OPENERS = set(PAIRS.values())


def line_of(text: str, index: int) -> int:
    return text.count("\n", 0, index) + 1


def scan(text: str, path: Path) -> list[str]:
    problems: list[str] = []
    stack: list[tuple[str, int]] = []

    i = 0
    n = len(text)
    line = 1

    while i < n:
        ch = text[i]
        nxt = text[i + 1] if i + 1 < n else ""

        # --- komentar ---
        if ch == "/" and nxt == "/":
            j = text.find("\n", i)
            i = n if j == -1 else j
            continue

        if ch == "/" and nxt == "*":
            j = text.find("*/", i + 2)
            if j == -1:
                problems.append(f"  baris {line}: komentar /* tidak ditutup")
                break
            line += text.count("\n", i, j)
            i = j + 2
            continue

        # --- string literal ---
        if ch in "'\"":
            quote = ch
            j = i + 1
            closed = False
            while j < n:
                if text[j] == "\\":
                    j += 2
                    continue
                if text[j] == quote:
                    closed = True
                    break
                if text[j] == "\n":
                    break
                j += 1
            if not closed:
                problems.append(f"  baris {line}: string {quote} tidak ditutup")
                break
            line += text.count("\n", i, j)
            i = j + 1
            continue

        # --- template literal ---
        if ch == "`":
            j = i + 1
            closed = False
            depth = 0
            while j < n:
                c = text[j]
                if c == "\\":
                    j += 2
                    continue
                if text.startswith("${", j):
                    depth += 1
                    j += 2
                    continue
                if c == "}" and depth > 0:
                    depth -= 1
                    j += 1
                    continue
                if c == "`" and depth == 0:
                    closed = True
                    break
                j += 1
            if not closed:
                problems.append(f"  baris {line}: template literal ` tidak ditutup")
                break
            line += text.count("\n", i, j)
            i = j + 1
            continue

        # --- kurung ---
        if ch in OPENERS:
            stack.append((ch, line))
        elif ch in PAIRS:
            if not stack:
                problems.append(f"  baris {line}: {ch} tanpa pembuka")
            elif stack[-1][0] != PAIRS[ch]:
                opener, oline = stack[-1]
                problems.append(
                    f"  baris {line}: {ch} menutup {opener} dari baris {oline}"
                )
                stack.pop()
            else:
                stack.pop()
        elif ch == "\n":
            line += 1

        i += 1

    for opener, oline in stack:
        problems.append(f"  baris {oline}: {opener} tidak pernah ditutup")

    # --- heuristik tambahan ---
    for m in re.finditer(r"[^=!<>]==[^=]|[^!]!=[^=]", text):
        ln = line_of(text, m.start())
        context = text[max(0, m.start() - 60): m.start() + 20]
        if re.search(r"\b(if|while)\b[^\n]*$", context):
            problems.append(f"  baris {ln}: pakai == / != di kondisi (pakai === / !==)")

    for m in re.finditer(r"^\s*console\.(log|debug)\(", text, re.M):
        problems.append(f"  baris {line_of(text, m.start())}: console.log tertinggal")

    return problems


def main() -> int:
    if len(sys.argv) > 1:
        targets = [Path(a) for a in sys.argv[1:]]
    else:
        targets = sorted(
            p for p in (ROOT / "public" / "js").rglob("*.js")
        ) + sorted((ROOT / "resources" / "js").rglob("*.js"))

    failed = 0
    for path in targets:
        if not path.exists():
            print(f"X {path}: file tidak ditemukan")
            failed += 1
            continue
        problems = scan(path.read_text(encoding="utf-8", errors="replace"), path)
        if problems:
            failed += 1
            rel = path.relative_to(ROOT) if path.is_relative_to(ROOT) else path
            print(f"X {rel}")
            for p in problems:
                print(p)

    print()
    print(f"{len(targets) - failed}/{len(targets)} file JS lolos pemeriksaan.")
    return 1 if failed else 0


if __name__ == "__main__":
    sys.exit(main())
