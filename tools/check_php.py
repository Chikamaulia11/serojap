#!/usr/bin/env python3
"""
Pemeriksa ringan untuk file PHP.

`php -l` adalah alat yang benar, tapi PHP tidak terpasang di
lingkungan ini (WSL tanpa php), dan sintaks yang rusak di controller
akan langsung 500 di halaman, bukan gagal diam-diam.

Yang diperiksa:
  1. Kurung kurawal, kurung, kurung siku seimbang.
  2. String PHP, heredoc, dan nowdoc tidak diperlakukan sebagai kode.
  3. Komentar harus ditutup.
  4. Setiap `class`/`function` punya `{` pembuka dan `}` penutup di
     tingkat yang benar (deteksi kasar, bukan parser penuh).
  5. Impor `use` yang tidak pernah dipakai di file yang sama.

Pakai:
    python3 tools/check_php.py                    # semua file php
    python3 tools/check_php.py app/Models/User.php
"""

import re
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent


def line_of(text: str, index: int) -> int:
    return text.count("\n", 0, index) + 1


def scan(text: str) -> list[str]:
    problems: list[str] = []
    stack: list[tuple[str, int]] = []
    used_names: set[str] = set()

    i = 0
    n = len(text)
    line = 1

    while i < n:
        ch = text[i]
        nxt = text[i + 1] if i + 1 < n else ""

        # --- komentar & tag php ---
        if ch == "/" and nxt == "*":
            j = text.find("*/", i + 2)
            if j == -1:
                problems.append(f"  baris {line}: komentar /* tidak ditutup")
                break
            line += text.count("\n", i, j)
            i = j + 2
            continue

        if ch == "/" and nxt == "/":
            j = text.find("\n", i)
            i = n if j == -1 else j
            continue

        # --- string PHP ---
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
                j += 1
            if not closed:
                problems.append(f"  baris {line}: string {quote} tidak ditutup")
                break
            line += text.count("\n", i, j)
            i = j + 1
            continue

        # --- heredoc / nowdoc ---
        m = re.match(r"<<<\s*(['\"]?)([A-Za-z_][A-Za-z0-9_]*)\1\r?\n", text[i:])
        if m:
            label = m.group(2)
            end = re.search(rf"^\s*{label}\b", text[i + m.end():], re.M)
            if not end:
                problems.append(f"  baris {line}: heredoc {label} tidak ditutup")
                break
            line += text.count("\n", i, i + m.end() + end.end())
            i = i + m.end() + end.end()
            continue

        # --- label goto / case colon: lewati satu ':' ---
        if ch in "({[":
            stack.append((ch, line))
        elif ch in ")}]":
            pairs = {")": "(", "}": "{", "]": "["}
            if not stack:
                problems.append(f"  baris {line}: {ch} tanpa pembuka")
            elif stack[-1][0] != pairs[ch]:
                opener, oline = stack[-1]
                problems.append(f"  baris {line}: {ch} menutup {opener} dari baris {oline}")
                stack.pop()
            else:
                stack.pop()
        elif ch == "\n":
            line += 1

        i += 1

    for opener, oline in stack:
        problems.append(f"  baris {oline}: {opener} tidak pernah ditutup")

    # --- impor tidak terpakai ---
    #
    # Hanya `use` di kolom 0 yang bisa jadi import. `use` yang
    # menjorok ke dalam adalah trait yang dipakai di dalam class
    # (mis. `    use RefreshDatabase;`) dan TIDAK boleh dihitung.
    #
    # Nama yang dicek adalah nama LOKAL, yaitu segmen terakhir
    # (`use App\Models\User;` -> `User`, bukan `App`).
    lines = text.split("\n")
    import_lines: list[tuple[int, str]] = []
    in_class_body = False
    for idx, ln in enumerate(lines):
        if re.match(r"^(?:final\s+|abstract\s+)?class\b", ln):
            in_class_body = True
        if re.match(r"^\}", ln):
            in_class_body = False
        m = re.match(r"^use\s+([^;]+);", ln)
        if m and not in_class_body:
            import_lines.append((idx, m.group(1).strip()))

    body = re.sub(r"^use\s+[^;]+;", "", text, flags=re.M)
    for idx, spec in import_lines:
        if spec.startswith("function ") or spec.startswith("const "):
            spec = spec.split(" ", 1)[1]
        if " as " in spec:
            local = spec.split(" as ")[1].strip()
        else:
            local = spec.rsplit("\\", 1)[-1].strip()
        if not local or not re.match(r"^[A-Za-z_][A-Za-z0-9_]*$", local):
            continue
        pattern = rf"(?<![A-Za-z0-9_\\$]){re.escape(local)}(?![A-Za-z0-9_])"
        if not re.search(pattern, body):
            problems.append(f"  baris {idx + 1}: import `{local}` tidak terpakai")

    return problems


def main() -> int:
    if len(sys.argv) > 1:
        targets = [Path(a) for a in sys.argv[1:]]
    else:
        targets = sorted(
            p for p in (ROOT / "app").rglob("*.php")
            if "vendor" not in p.parts
        ) + sorted((ROOT / "database" / "migrations").rglob("*.php")) + \
            sorted((ROOT / "database" / "seeders").rglob("*.php")) + \
            sorted((ROOT / "routes").rglob("*.php")) + \
            sorted((ROOT / "config").rglob("*.php")) + \
            sorted((ROOT / "lang").rglob("*.php")) + \
            sorted((ROOT / "tests").rglob("*.php"))

    failed = 0
    for path in targets:
        if not path.exists():
            print(f"X {path}: file tidak ditemukan")
            failed += 1
            continue
        problems = scan(path.read_text(encoding="utf-8", errors="replace"))
        if problems:
            failed += 1
            rel = path.relative_to(ROOT) if path.is_relative_to(ROOT) else path
            print(f"X {rel}")
            for p in problems:
                print(p)

    print()
    print(f"{len(targets) - failed}/{len(targets)} file PHP lolos pemeriksaan.")
    return 1 if failed else 0


if __name__ == "__main__":
    sys.exit(main())
