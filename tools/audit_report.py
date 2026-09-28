#!/usr/bin/env python3
"""Ringkasan hasil audit tema dari storage/app/theme-audit.json.

Dipakai untuk melihat pola kegagalan, bukan per elemen satu-satu.
Pakai:
    python3 tools/audit_report.py                     # semua kombinasi
    python3 tools/audit_report.py teal/dark           # satu kombinasi
    python3 tools/audit_report.py teal/dark --pages    # Mandatory: per elemen
"""
import json
import sys
from collections import Counter
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
DATA = ROOT / "storage" / "app" / "theme-audit.json"


def load():
    return json.loads(DATA.read_text(encoding="utf-8"))


def combos(data, only):
    out = []
    for run in data:
        for res in run.get("results", []):
            if only and res["combo"] != only:
                continue
            out.append((run["file"], run["width"], res))
    return out


def main():
    args = [a for a in sys.argv[1:] if not a.startswith("--")]
    flags = {a for a in sys.argv[1:] if a.startswith("--")}
    only = args[0] if args else ""
    data = load()
    rows = combos(data, only)
    if not rows:
        sys.exit(f"  tidak ada hasil untuk '{only}'")

    print(f"  data: {len(data)} run, {len(rows)} kombinasi terfilter\n")

    total_f = total_s = 0
    combos_gagal = []
    for name, w, res in rows:
        nf, ns = len(res["fails"]), len(res["skipped"])
        total_f += nf
        total_s += ns
        if nf or res["overflow"]:
            combos_gagal.append((res["combo"], name, w, nf, res["overflow"]))

    if combos_gagal:
        print("  KOMBINASI YANG MASIH GAGAL")
        agg = Counter()
        for combo, name, w, nf, ov in combos_gagal:
            agg[(combo, nf, ov)] += 1
        for (combo, nf, ov), n in sorted(agg.items()):
            print(f"    {combo:<14} gagal={nf:<4} overflow={str(ov):<5} di {n} halaman/lebar")
        print()

    print(f"  TOTAL gagal={total_f}  dilewati={total_s}\n")

    # Pola kegagalan lintas seluruh halaman.
    pola = Counter()
    contoh = {}
    for name, w, res in rows:
        for f in res["fails"]:
            key = (f["fg"], f["bg"], f["need"], bool(f.get("onGradient")))
            pola[key] += 1
            contoh.setdefault(key, f)

    print("  POLA KEGAGALAN (fg di bg, butuh, diAtasGradien) x jumlah")
    for key, n in pola.most_common(24):
        fg, bg, need, grad = key
        c = contoh[key]
        print(f"    {fg:<22} di {bg:<22} butuh={need} grad={str(grad):<5} x{n}")
        print(f"        teks={c['text'][:44]!r}")
        print(f"        {c['path'][:88]}")
    if not pola:
        print("    (tidak ada)")

    if "--skipped" in flags:
        s = Counter()
        for name, w, res in rows:
            for k in res["skipped"]:
                s[k["why"]] += 1
        print("\n  YANG DILEWATI (perlu alasan)")
        for why, n in s.most_common():
            print(f"    {why}: {n}")

    if "--pages" in flags:
        print("\n  PER HALAMAN (halaman yang punya kegagalan)")
        per = Counter()
        for name, w, res in rows:
            n = len(res["fails"])
            if n:
                per[(name, res["combo"])] = n
        for (name, combo), n in sorted(per.items(), key=lambda x: -x[1])[:40]:
            print(f"    {name:<28} {combo:<14} {n}")
    return 0


if __name__ == "__main__":
    sys.exit(main())
