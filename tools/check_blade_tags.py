#!/usr/bin/env python3
"""
Pemeriksa keseimbangan tag HTML untuk view Blade.

Blade bisa saja "seimbang" secara direktif (@if/@endif)
lalu tetap menghasilkan HTML rusak -- misalnya `<span>` yang hilang
penutupnya setelah sebuah blok diganti komponen
`<x-status-badge />`. Tag seperti itu tidak terdeteksi oleh
pemeriksa directive dan tidak terlihat dari diff yang kecil.

Script ini membersihkan yang boleh membersihkan, lalu menghitung
tag yang muncul dalam dokumen harus seimbang. Nol tag tak berpasangan
= aman untuk Denmark.

Pakai:
    python3 tools/check_blade_tags.py            # semua view
    python3 tools/check_blade_tags.py <file> ...  # file tertentu
"""

import re
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent

# Tag yang tidak butuh penutup.
VOID = {
    "area", "base", "br", "col", "embed", "hr", "img", "input",
    "link", "meta", "param", "source", "track", "wbr",
}

# Elemen yang cavity isinya boleh berupa teks mentah / struktur lain.
# Ekspresi Blade di dalamnya diabaikan saat menghitung tag.
RAW_TEXT = {"script", "style", "textarea", "pre"}

# Tag yang tutupannya boleh dielakkan (HTML5 optional end tag).
OPTIONAL_END = {
    "li", "dt", "dd", "option", "thead", "tbody", "tfoot", "tr",
    "td", "th", "p", "colgroup", "rt", "rp",
}

TAG_RE = re.compile(r"<\s*(/?)\s*([a-zA-Z][a-zA-Z0-9:-]*)([^>]*?)(/?)\s*>", re.S)
ATTR_SPREAD_RE = re.compile(r"\{\{\s*\$attributes")


def strip_blade_comments(text: str) -> str:
    """Buang komentar Blade {{-- --}} dan blok PHP @php/@verbatim."""
    text = re.sub(r"\{\{--.*?--\}\}", "", text, flags=re.S)
    text = re.sub(r"@php\b.*?@endphp\b", "", text, flags=re.S)
    text = re.sub(r"@verbatim\b.*?@endverbatim\b", "", text, flags=re.S)
    return text


def strip_blade_expressions(text: str) -> str:
    """
    Ganti ekspresi Blade dengan placeholder agar tag di dalam string
    Blade tidak ikut terhitung.

    Contoh: {{ $attributes->merge(['class' => 'a > b']) }} tidak boleh
    dibaca sebagai tag.
    """
    out = []
    i = 0
    n = len(text)
    while i < n:
        if text.startswith("{!!", i):
            j = text.find("!!}", i)
            if j == -1:
                break
            out.append(" ")
            i = j + 3
        elif text.startswith("{{", i):
            # Temukan penutup }} yang seimbang, hormati kurung kurawal di dalamnya.
            depth = 0
            j = i
            while j < n - 1:
                if text.startswith("{{", j):
                    depth += 1
                    j += 2
                elif text.startswith("}}", j):
                    depth -= 1
                    j += 2
                    if depth == 0:
                        break
                else:
                    j += 1
            out.append(" ")
            i = j
        else:
            out.append(text[i])
            i += 1
    return "".join(out)


def line_of(text: str, index: int) -> int:
    return text.count("\n", 0, index) + 1


def check_file(path: Path) -> list[str]:
    raw = path.read_text(encoding="utf-8", errors="replace")
    text = strip_blade_expressions(strip_blade_comments(raw))

    stack: list[tuple[str, int]] = []
    problems: list[str] = []

    # Kumpulkan dulu semua tag, lalu proses. Isi <script>/<style>/<textarea>
    # diabaikan karena teks/CSS di dalamnya boleh memuat karakter "<"
    # yang bukan tag.
    matches = list(TAG_RE.finditer(text))

    skip_until = 0
    for m in matches:
        if m.start() < skip_until:
            continue

        closing = m.group(1)
        name = m.group(2).lower()
        selfclose = m.group(4)
        ln = line_of(text, m.start())

        # Komponen Blade <x-...> dan direktif @... bukan tag HTML.
        if name.startswith("x-") or name.startswith("@"):
            continue

        if name in VOID or selfclose:
            continue

        if closing:
            if name in OPTIONAL_END and (not stack or stack[-1][0] != name):
                # Penutup opsional (mis. </li> tanpa </li> sebelumnya)
                # tidak dilaporkan sebagai error.
                continue
            if not stack:
                problems.append(f"  baris {ln}: </{name}> tanpa pembuka")
                continue
            if stack[-1][0] == name:
                stack.pop()
                continue
            target = None
            for idx in range(len(stack) - 1, -1, -1):
                if stack[idx][0] == name:
                    target = idx
                    break
            if target is None:
                problems.append(f"  baris {ln}: </{name}> tanpa pembuka yang cocok")
            else:
                for tag, tln in stack[target:]:
                    problems.append(
                        f"  baris {tln}: <{tag}> tidak ditutup sebelum </{name}>"
                    )
                del stack[target:]
            continue

        if name in RAW_TEXT:
            end = re.search(rf"<\/\s*{name}\s*>", text[m.end():], re.I)
            if end:
                skip_until = m.end() + end.end()
            else:
                problems.append(f"  baris {ln}: <{name}> tanpa penutup")
            continue

        stack.append((name, ln))

    for tag, ln in stack:
        problems.append(f"  baris {ln}: <{tag}> tidak pernah ditutup")

    return problems


def main() -> int:
    if len(sys.argv) > 1:
        targets = [Path(a) for a in sys.argv[1:]]
    else:
        targets = sorted((ROOT / "resources" / "views").rglob("*.blade.php"))

    failed = 0
    for path in targets:
        problems = check_file(path)
        if problems:
            failed += 1
            rel = path.relative_to(ROOT) if path.is_relative_to(ROOT) else path
            print(f"X {rel}")
            for p in problems:
                print(p)

    total = len(targets)
    print()
    print(f"{total - failed}/{total} view lolos pemeriksaan tag.")
    return 1 if failed else 0


if __name__ == "__main__":
    sys.exit(main())
