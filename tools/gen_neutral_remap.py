#!/usr/bin/env python3
"""
Buat `resources/css/neutral-remap.css` dari utility Tailwind netral yang
benar-benar dipakai di template Blade.

Alasan: area admin/superadmin dibangun dengan kelas netral (slate/gray/
white), bukan hex, jadi kelas itu tidak ikut berubah saat aksen atau mode
diganti. Menyalinutility satu per satu di banyak file rawan salah, dan
kelas yang terlewat akan tampak terang di mode dark. Lebih baik memetakan
utility yang benar-benar muncul ke token tema lewat satu file CSS.

Dua jebakan yang harus dijaga di file hasil:

  1. Pemilih harus BERKOMA HIDUP. `bg-slate-100` adalah nama kelas, jadi
     selektornya `.bg-slate-100`. Tanpa titik, aturan itu menjadi
     selektor elemen `<bg-slate-100>` yang tidak pernah ada, dan
     browser mengabaikannya tanpa error. `:` pada variant (mis.
     `hover:bg-slate-100`) dan `/` pada modifier opasitas (mis.
     `bg-slate-50/70`) harus di-escape dengan backslash.

  2. URUTAN SUMBER tidak bisa diandalkan. Build ini tidak menghasilkan
     `@layer utilities` sama sekali, jadi tidak ada cascade layer yang
     membuat remap otomatis berada di atas utility; urutan sumber yang
     menentukan, dan utility Tailwind selalu ditulis belakangan.

     Dua percobaan gagal di sini dan keduanya sudah dicoba:
       a. Impor remap SEBELUM `@tailwind utilities` -> utility asli
          menang karena ditulis lebih akhir.
       b. Impor remap SESUDAH `@tailwind utilities` -> Vite membuang
          `@import` yang muncul setelah aturan lain, jadi seluruh remap
          hilang dari bundle tanpa error.

     Karena itu yang menang adalah SPESIFISITAS: tiap selector remap
     diawali `html[data-accent]`, yang menambah satu atribut ke
     spesifisitas utility Tailwind (`.bg-slate-100`). `data-accent`
     selalu ada karena script anti-FOUC selalu menyetelnya sebelum
     CSS, jadi penambahannya tidak pernah menggantungkan pematch.

Pakai:
    python3 tools/gen_neutral_remap.py
"""

import re
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
VIEWS = ROOT / "resources" / "views"
OUT = ROOT / "resources" / "css" / "neutral-remap.css"

# Family netral Tailwind yang diremap.
FAMILIES = ("slate", "gray", "zinc", "neutral", "stone")

# Skala angka -> token tema.
#
# `text` dipisah dari yang lain karena tingkat perbedaan kontrasnya
# berarti: nomor halaman `text-slate-200` sengaja dibuat nyaris tak
# terlihat, jadi harus jatuh ke `--ink-mute`, bukan ke `--line`
# (yang dipakai untuk border) yang jauh lebih gelap.
SCALE_TEXT = {
    200: "ink-mute", 300: "ink-mute", 400: "ink-mute",
    500: "ink-soft", 600: "ink-soft", 700: "ink-soft",
    800: "ink", 900: "ink", 950: "ink",
}
SCALE_SURFACE = {
    50: "bg", 100: "surface-2", 200: "line", 300: "line",
    400: "ink-mute", 500: "ink-soft", 600: "ink-soft",
}

# Utilitas non-angka: `bg-white` polos dan `text-black`.
SPECIAL = {
    ("bg", "white", None): ("background-color", "surface", None),
    ("text", "black", None): ("color", "ink", None),
}

VARIANTS = r"(?:[a-z-]+:)?"

TOKEN_RE = re.compile(
    rf"{VARIANTS}(bg|text|border|ring|divide|placeholder|from|to|via)-"
    rf"(white|black|{'|'.join(FAMILIES)})"
    r"(?:-(\d{2,3}))?(?:/(\d{1,3}))?"
)

# `text-white` sengaja TIDAK ikut diremap. Ia dipakai di atas
# `bg-slate-500`, `bg-red-500`, tombol aksen, avatar, dan gradien --
# konteks yang beragam. Memetakannya ke `--on-accent` akan membuat teks
# putih di atas badge abu-abu jadi hampir hitam pada mode dark. Kasus
# `text-white` di atas aksen ditangani eksplisit di Blade.


def prop_for(family):
    return {
        "bg": "background-color",
        "text": "color",
        "border": "border-color",
        "placeholder": "color",
        "ring": "--tw-ring-color",
        # `divide-*` mewarnai border anak bertetangga, bukan elemen
        # induknya; selektornya ditangani terpisah di `render()`.
        "divide": "border-color",
        "from": "--tw-gradient-from",
        "via": "--tw-gradient-via",
        "to": "--tw-gradient-to",
    }[family]


def exclusion_reason(family, name, shade, alpha):
    """Utility yang SENGAJA tidak themable, beserta alasannya.

    Dua kelompok, keduanya karena nilainya bukan "permukaan" melainkan
    lapisan atas sesuatu yang sudah berwarna:

    1. `bg-white/N` -- veil dekoratif di atas gradien banner (lingkaran
       dan ikon transparan). Kalau dipetakan ke `var(--surface)`
       warnanya jadi pekat dan lingkaran itu berubah menjadi gumpalan
       solid. Untuk N kecil, `color-mix()` pun tetap salah: veil itu
       harus tetap putih, bukan ikut gelap. Jadi dibiarkan apa adanya;
       nilai opasitasnya tidak berubah sama sekali.

    2. `bg-slate-700` ke atas -- tombol gelap, pill aktif, dan tirai
       navigasi seluler. Semuanya harus tetap gelap di kedua mode.
       Kalau `bg-slate-900` dipetakan ke `var(--ink)`, mode dark
       akan membuat tombol berlatar terang dengan teks putih, dan
       tirai jadi putih tembus pandang. Keduanya tidak punya makna
       tema, jadi dibiarkan.
    """
    if family == "bg" and name == "white" and alpha is not None:
        return "veil dekoratif di atas gradien, harus tetap putih"
    if family == "bg" and name in FAMILIES and shade is not None and int(shade) >= 700:
        return "chrome gelap (tombol/pill/tirai), harus tetap gelap"
    return None


def resolve(family, name, shade, alpha):
    if (family, name, shade) in SPECIAL:
        return SPECIAL[(family, name, shade)]
    if name not in FAMILIES or shade is None:
        return None
    table = SCALE_TEXT if family == "text" else SCALE_SURFACE
    key = table.get(int(shade))
    return None if key is None else (prop_for(family), key, alpha)


def collect():
    found = {}
    skipped = {}
    for path in VIEWS.rglob("*.blade.php"):
        text = path.read_text(encoding="utf-8", errors="replace")
        for m in TOKEN_RE.finditer(text):
            utility = m.group(0)
            family = m.group(1)
            name = m.group(2)
            shade = m.group(3)
            alpha = m.group(4)
            if family == "text" and name == "white":
                skipped.setdefault(utility, "di atas badge/aksen/avatar, konteks beragam")
                continue
            reason = exclusion_reason(family, name, shade, alpha)
            if reason:
                skipped.setdefault(utility, reason)
                continue
            resolved = resolve(family, name, shade, alpha)
            if resolved is None:
                continue
            found.setdefault(utility, resolved)
    return found, skipped


def value_for(prop, token, alpha):
    """Nilai CSS untuk satu utility.

    Modifier opasitas (`/70`) tidak boleh dibuang. `bg-slate-50/70`
    sengaja tembus pandang supaya warna kartu di bawahnya terlihat, dan
    `border-slate-200/70` juga begitu. Menulis `var(--bg)` polos akan
    menjadikannya pekat dan mengubah tampilan. `color-mix()` ke
    `transparent` mempertahankan opasitas sekaligus tetap themable.
    """
    ref = f"var(--{token})"
    if alpha is None:
        return ref
    return f"color-mix(in srgb, {ref} {alpha}%, transparent)"


def selector_for(utility, family):
    """Bangun pemilih CSS dari nama utility Tailwind.

    Titik diawal wajib, dan karakter yang punya arti khusus di CSS harus
    di-escape:
        hover:bg-slate-100  ->  .hover\\:bg-slate-100
        bg-slate-50/70      ->  .bg-slate-50\\/70
        placeholder:text-400->  .placeholder\\:text-400

    Selektor juga diawali `html[data-accent]`, bukan `:` saja.
    Alasannya bukan sekadar agar menang: tanpa itu utility asli Tailwind
    menulis `background-color` di spesifisitas yang sama dan menang
    lewat urutan sumber, sehingga remap jadi tidak berlaku sama sekali
    -- build tetap hijau, jadi salahnya baru terlihat saat di-browser.
    `data-accent` selalu ada karena script anti-FOUC menyetelnya di
    `<head>` sebelum CSS pertama di-apply, sehingga selektor ini tidak
    pernah menggantungkan pematch.

    Konsekuensinya: aturan di dalam view yang benar-benar perlu
    menimpa remap (mis. `!bg-white`) tetap bisa, karena utility
    `!important` Tailwind menang atas selektor specificity biasa.
    """
    escaped = "html[data-accent] ." + utility.replace(":", "\\:").replace("/", "\\/")
    if family == "divide":
        return f"{escaped} > :not([hidden]) ~ :not([hidden])"
    return escaped


HEADER = """/* =========================================================
   REMAP UTILITAS NETRAL TAILWIND KE TOKEN TEMA
   =========================================================

   DIJASILKAN OTOMATIS oleh tools/gen_neutral_remap.py.
   Jangan disunting tangan -- jalankan ulang scriptnya kalau
   daftar kelas di Blade berubah.

   Area admin/superadmin dibangun dengan kelas netral Tailwind
   (slate/gray/white), bukan dengan hex, jadi kelas itu tidak
   ikut berubah saat aksen atau mode diganti. File ini memetakan
   utility yang benar-benar terpakai ke token tema.

   Ketiadaan `!important` di sini bukan kelalaian: remap ini sengaja
   tidak memakainya supaya `style="..."` inline di view tetap bisa
   mengalahkan remap, dan `!bg-white` versi Tailwind tetap bisa menimpa
   remap bila memang dibutuhkan.

   Yang membuat remap menang adalah SPESIFISITAS: tiap selector remap
   diawali `html[data-accent]`, sehingga spesifisitasnya melampaui
   utility Tailwind yang setara. Build ini tidak menghasilkan
   `@layer utilities`, jadi cascade layer tidak bisa diandalkan.

   Modifier opasitas dipertahankan lewat `color-mix()` ke
   `transparent`.
   ========================================================= */
"""


def render(rules, skipped):
    lines = [HEADER]
    if skipped:
        lines.append("/* Utility yang SENGAJA dibiarkan apa adanya:")
        lines.append("")
        for utility, reason in sorted(skipped.items()):
            lines.append(f"     {utility:<26} {reason}")
        lines.append("")
        lines.append("   `text-white` tidak dipetakan: dipakai di atas badge abu-abu,")
        lines.append("   tombol aksen, avatar, dan gradien. Dipetakan otomatis justru")
        lines.append("   merusak kontras pada mode dark. Yang di atas aksen diubah")
        lines.append("   eksplisit di Blade menjadi `text-[var(--on-accent)]`.")
        lines.append("   ========================================================= */")
        lines.append("")

    buckets = {f: [] for f in ("bg", "text", "border", "placeholder",
                               "ring", "divide", "from", "via", "to")}
    titles = {
        "bg": "Latar", "text": "Teks", "border": "Border", "divide": "Pembatas",
        "ring": "Focus ring", "placeholder": "Placeholder",
        "from": "Gradien", "via": "Gradien", "to": "Gradien",
    }
    order = ["bg", "text", "border", "divide", "ring", "placeholder",
             "from", "via", "to"]
    for utility, (prop, token, alpha) in sorted(rules.items()):
        m = re.match(
            r"((?:[a-z-]+:)*)(bg|text|border|ring|divide|placeholder|from|to|via)-",
            utility,
        )
        buckets[m.group(2)].append((utility, prop, token, alpha))

    for family in order:
        if not buckets[family]:
            continue
        lines.append(f"/* --- {titles[family]} --- */")
        for utility, prop, token, alpha in buckets[family]:
            sel = selector_for(utility, family)
            lines.append(f"{sel} {{ {prop}: {value_for(prop, token, alpha)}; }}")
        lines.append("")
    return "\n".join(lines).rstrip() + "\n"


def main():
    rules, skipped = collect()
    OUT.write_text(render(rules, skipped), encoding="utf-8")
    print(f"  {len(rules)} utility di-remap, {len(skipped)} sengaja dibiarkan")
    print(f"  -> {OUT.relative_to(ROOT)}")
    return 0


if __name__ == "__main__":
    sys.exit(main())
