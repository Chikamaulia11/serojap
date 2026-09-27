#!/usr/bin/env python3
"""
Audit referensi lintas file: controller -> view, view -> route,
view -> komponen, view -> layout.

`php artisan route:list` dan `php artisan view:cache` adalah alat yang
benar, tapi keduanya butuh PHP. Yang paling sering membuat halaman
berkode 500 -- dan sama sekali tidak terlihat di diff kecil -- adalah
salah satu dari empat hal ini:

  1. Controller me-return view yang filenya tidak pernah dibuat.
  2. View memanggil route() yang tidak terdaftar.
  3. View memakai <x-komponen> yang tidak ada.
  4. View @extends layout yang tidak ada.

Semuanya bisa dicek dari isi file, jadi tidak perlu PHP.

Pakai:
    python3 tools/audit_refs.py
"""

import re
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
VIEWS = ROOT / "resources" / "views"

problems: list[str] = []


def rel(p: Path) -> str:
    try:
        return str(p.relative_to(ROOT))
    except ValueError:
        return str(p)


def blade_files() -> list[Path]:
    return sorted(VIEWS.rglob("*.blade.php"))


def php_files() -> list[Path]:
    return sorted((ROOT / "app").rglob("*.php"))


# ---------------------------------------------------------------- routes

RESOURCE_ACTIONS = (
    "index", "create", "store", "show", "edit", "update", "destroy",
)


def _find_group_blocks(text: str) -> list[tuple[str, int, int]]:
    """
    Kembalikan (name_prefix, start, end) untuk setiap
    `->name('x')->group(function ... { ... })`.

    `name_prefix` adalah apa yang ditambahkan di depan setiap nama route
    di dalam group, termasuk titik di akhir kalau ada.
    """
    blocks: list[tuple[str, int, int]] = []

    for m in re.finditer(r"->name\(\s*['\"]([A-Za-z0-9_.\-]*)['\"]\s*\)\s*->group\s*\(", text):
        prefix = m.group(1)
        brace_at = text.find("{", m.end())
        if brace_at == -1:
            continue

        depth = 0
        i = brace_at
        while i < len(text):
            if text[i] == "{":
                depth += 1
            elif text[i] == "}":
                depth -= 1
                if depth == 0:
                    break
            i += 1

        blocks.append((prefix, m.end(), i))

    return blocks


def _resource_names(
    text: str, lo: int, hi: int, prefix: str
) -> set[str]:
    """Nama route yang dihasilkan Route::resource di rentang [lo, hi)."""
    out: set[str] = set()

    for m in re.finditer(
        r"Route::(?:api)?resource\(\s*['\"]([A-Za-z0-9_\-]+)['\"]", text[lo:hi]
    ):
        resource = m.group(1)
        # ambil statement resource ini saja, sampai ';' pertama
        semi = text.find(";", lo + m.end())
        end = semi if semi != -1 and semi <= hi else hi
        chunk = text[lo + m.end(): end - lo]

        only = re.search(r"->only\(\s*\[([^\]]*)\]", chunk)
        exc = re.search(r"->except\(\s*\[([^\]]*)\]", chunk)

        picked = list(RESOURCE_ACTIONS)
        if only:
            picked = re.findall(r"['\"]([a-z\-]+)['\"]", only.group(1))
        elif exc:
            dropped = set(re.findall(r"['\"]([a-z\-]+)['\"]", exc.group(1)))
            picked = [a for a in picked if a not in dropped]

        for action in picked:
            out.add(f"{prefix}{resource}.{action}")

        # Route::resource(...)->names([...]) menimpa nama bawaan
        names = re.search(r"->names\(\s*\[([^\]]*)\]", chunk)
        if names:
            for key, val in re.findall(
                r"['\"]([a-z\-]+)['\"]\s*=>\s*['\"]([A-Za-z0-9_\-]+)['\"]",
                names.group(1),
            ):
                out.add(f"{prefix}{val}")

    return out


def collect_route_names() -> set[str]:
    """Kumpulkan nama route dari routes/*.php, termasuk Route::resource."""
    names: set[str] = set()

    for f in sorted((ROOT / "routes").glob("*.php")):
        text = f.read_text(encoding="utf-8", errors="replace")

        blocks = _find_group_blocks(text)

        # nama di dalam group ber-prefix
        for prefix, lo, hi in blocks:
            names |= _resource_names(text, lo, hi, prefix)
            for m in re.finditer(r"->name\(\s*['\"]([A-Za-z0-9_.\-]+)['\"]", text[lo:hi]):
                names.add(prefix + m.group(1))

        # nama di luar group apa pun: everything, lalu buang yang
        # sudah tertangani group supaya tidak dobel salah
        covered: list[tuple[int, int]] = []
        for _prefix, lo, hi in blocks:
            covered.append((lo, hi))

        def inside_any_group(pos: int) -> bool:
            return any(lo <= pos <= hi for lo, hi in covered)

        for m in re.finditer(r"->name\(\s*['\"]([A-Za-z0-9_.\-]+)['\"]", text):
            if inside_any_group(m.start()):
                continue
            names.add(m.group(1))

        # resource di luar group
        for m in re.finditer(r"Route::(?:api)?resource\(\s*['\"]([A-Za-z0-9_\-]+)['\"]", text):
            if inside_any_group(m.start()):
                continue
            names |= _resource_names(text, m.start(), len(text), "")

    return names


ROUTE_NAMES = collect_route_names()


def audit_view_routes() -> None:
    # route('a.b.c') dan route('a.b.c', [...])
    for f in blade_files():
        text = f.read_text(encoding="utf-8", errors="replace")
        # buang komentar blade
        text = re.sub(r"\{\{--.*?--\}\}", "", text, flags=re.S)

        for m in re.finditer(r"\broute\(\s*['\"]([A-Za-z0-9_.\-]+)['\"]", text):
            name = m.group(1)

            # nama route dinamis: route($x, ...) -- tidak bisa dicek
            if name in ROUTE_NAMES:
                continue

            # resource tanpa nama eksplisit menghasilkan nama default
            if "." not in name and name in {
                "index", "create", "store", "show", "edit", "update", "destroy",
            }:
                continue

            ln = text.count("\n", 0, m.start()) + 1
            problems.append(
                f"{rel(f)}:{ln}  route('{name}') tidak terdaftar di routes/"
            )


# ------------------------------------------------------------ components

# `x-slot` bukan komponen: itu fitur bawaan Blade untuk isi slot.
# Menghitungnya sebagai komponen yang hilang menghasilkan 100+ temuan
# palsu.
BUILTIN_COMPONENTS = {"slot", "slot:title", "slot:head", "errors"}


def component_names() -> set[str]:
    d = VIEWS / "components"
    if not d.exists():
        return set()
    # `p.stem` hanya membuang ".php", jadi hasilnya "status-badge.blade".
    # Yang benar adalah buang ".blade.php" utuh.
    return {
        p.name[: -len(".blade.php")]
        for p in d.glob("*.blade.php")
        if p.name.endswith(".blade.php")
    }


COMPONENTS = component_names()


def audit_view_components() -> None:
    for f in blade_files():
        text = f.read_text(encoding="utf-8", errors="replace")
        text = re.sub(r"\{\{--.*?--\}\}", "", text, flags=re.S)

        # <x-nama  ; <x-nama> ; <x-nama/>  -- bukan <x-slot>, bukan tag html
        for m in re.finditer(r"<x-([a-z0-9\-\.]+)", text):
            comp = m.group(1)

            if comp in COMPONENTS:
                continue

            if comp in BUILTIN_COMPONENTS:
                continue

            # Komponen dari package pihak ketiga tidak bisa dicek dari
            # isi repo.
            if comp.startswith("jet-"):
                continue

            ln = text.count("\n", 0, m.start()) + 1
            problems.append(
                f"{rel(f)}:{ln}  komponen <x-{comp}> tidak ada di resources/views/components"
            )


# ---------------------------------------------------------------- layout

LAYOUTS = {p.stem for p in VIEWS.glob("layouts/*.blade.php")}
LAYOUTS |= {p.stem for p in (VIEWS / "components").glob("*.blade.php")}


def audit_layouts() -> None:
    for f in blade_files():
        text = f.read_text(encoding="utf-8", errors="replace")

        m = re.search(r"@extends\(\s*['\"]([^'\"]+)['\"]", text)
        if not m:
            continue

        target = m.group(1)
        # 'layouts.admin' -> components/layouts/admin.blade.php ATAU
        # resources/views/layouts/admin.blade.php
        name = target.rsplit(".", 1)[-1]

        if f"layouts/{name}.blade.php" in str(f.relative_to(ROOT)):
            continue
        if (VIEWS / "layouts" / f"{name}.blade.php").exists():
            continue
        if name in LAYOUTS:
            continue

        ln = text.count("\n", 0, m.start()) + 1
        problems.append(
            f"{rel(f)}:{ln}  @extends('{target}') -- layout tidak ada"
        )


# --------------------------------------------------------- controller view

def audit_controller_views() -> None:
    for f in php_files():
        text = f.read_text(encoding="utf-8", errors="replace")

        # view('a.b.c', [...]) dan ->view('a.b.c') dan view()->with
        for m in re.finditer(
            r"\bview\(\s*['\"]([A-Za-z0-9_.\-]+)['\"]", text
        ):
            v = m.group(1).replace(".", "/")
            path = VIEWS / f"{v}.blade.php"
            if path.exists():
                continue

            ln = text.count("\n", 0, m.start()) + 1
            problems.append(
                f"{rel(f)}:{ln}  view('{m.group(1)}') -> file tidak ada"
            )

        # ->view('a.b.c')
        for m in re.finditer(
            r"->view\(\s*['\"]([A-Za-z0-9_.\-]+)['\"]", text
        ):
            v = m.group(1).replace(".", "/")
            path = VIEWS / f"{v}.blade.php"
            if path.exists():
                continue

            ln = text.count("\n", 0, m.start()) + 1
            problems.append(
                f"{rel(f)}:{ln}  ->view('{m.group(1)}') -> file tidak ada"
            )


# ------------------------------------------------------------------ main

def main() -> int:
    audit_controller_views()
    audit_view_routes()
    audit_view_components()
    audit_layouts()

    if problems:
        for p in problems:
            print(f"  {p}")
        print()
        print(f"{len(problems)} temuan.")
        return 1

    n_blade = len(blade_files())
    n_php = len(php_files())
    print(
        f"Bersih: {n_php} controller, {n_blade} view, "
        f"{len(COMPONENTS)} komponen, {len(ROUTE_NAMES)} nama route."
    )
    return 0


if __name__ == "__main__":
    sys.exit(main())
