/**
 * Sistem tema Serojap.
 *
 * Dipanggil dari `app.js`, jadi ia berjalan sebagai module yang
 * `defer` -- dokumen sudah selesai di-parse ketika kode ini jalan, dan
 * karena itu DOM picker (yang ada di `<body>`) sudah bisa dicari.
 *
 * Script anti-FOUC di `<head>` sudah memasang `data-accent` dan
 * `data-mode-resolved` SEBELUM CSS pertama kali di-apply, jadi tidak
 * ada kedipan di sini. Modul ini yang menambah kemampuan: tombol
 * picker, penyimpanan, dan `themeColor()` untuk canvas.
 *
 * Implementasi ini port dari draft referensi (Bagian 2), dengan tiga
 * penyimpangan yang disengaja:
 *
 * 1. Semua pencarian DOM dijaga. Draft memanggil
 *    `document.getElementById('themeDot')` dan memasang listener ke
 *    `#swatches` tanpa padahal halaman tertentu (mis. `welcome.blade.php`)
 *    tidak memasang picker sama sekali. Tanpa penjaga, satu
 *    `TypeError` menghentikan seluruh script dan temanya tidak pernah
 *    aktif. Mode default tetap dipasang, hanya interaktifnya yang dilewati.
 *
 * 2. `color:#fff` pada tombol mode aktif diganti `var(--on-accent)`.
 *    Sama seperti di `theme.css`: di mode dark aksen justru terang,
 *    jadi teks putih di atasnya tidak terbaca.
 *
 * 3. Ditambah `themeColor()` dan `onThemeChange()`. Chart.js dan
 *    SweetAlert2 menulis ke canvas / memberi warna lewat JavaScript,
 *    dan `var(--x)` tidak bisa dipakai di sana. `themeColor()`
 *    meresolvasinya ke warna nyata.
 *
 * 4. Picker boleh dirender lebih dari sekali. Halaman pelapor
 *    menaruhnya di navbar (>= 640px) DAN di menu mobile (< 640px),
 *    jadi `mountPicker()` memindai `.theme-dd` dan memasang listener
 *    ke setiap turunya. `placePanel()` di dalamnya menjaga agar
 *    panel tidak pernah keluar viewport: panel dibalik ke atas kalau
 *    tidak ada ruang di bawah (kasus sidebar yang menempel di dasar
 *    layar), dan digeser dengan `--dd-shift` kalau tepi kirinya
 *    keluar layar (lebar panel 230px di sidebar 240px).
 *
 * Font TIDAK disentuh. Draft memakai Figtree hanya untuk preview-nya.
 */

const ACCENTS = ['teal', 'blue', 'green', 'purple', 'amber', 'rose'];
const MODES = ['light', 'dark', 'system'];

const KEY_ACCENT = 'serojap_theme_accent';
const KEY_MODE = 'serojap_theme_mode';

const DEFAULT_ACCENT = 'teal';
const DEFAULT_MODE = 'system';

const html = document.documentElement;

function safeGet(key) {
    try {
        return localStorage.getItem(key);
    } catch (e) {
        return null;
    }
}

function safeSet(key, value) {
    try {
        localStorage.setItem(key, value);
    } catch (e) {
        /* mode privat / storage penuh: tema tetap jalan untuk sesi ini */
    }
}

function resolveMode(mode) {
    if (mode === 'system') {
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }
    return mode;
}

function readStored() {
    const accent = safeGet(KEY_ACCENT);
    const mode = safeGet(KEY_MODE);
    return {
        accent: ACCENTS.includes(accent) ? accent : DEFAULT_ACCENT,
        mode: MODES.includes(mode) ? mode : DEFAULT_MODE,
    };
}

let state = readStored();
const listeners = [];

/**
 * Menerapkan tema ke `<html>` dan menyegarkan picker bila ada.
 */
function apply(accent, mode) {
    html.setAttribute('data-accent', accent);
    html.setAttribute('data-mode', mode);
    html.setAttribute('data-mode-resolved', resolveMode(mode));

    document.querySelectorAll('.theme-dd-btn .dot').forEach((el) => {
        el.style.background = 'var(--accent)';
    });

    document.querySelectorAll('.swatch').forEach((b) => {
        const active = b.dataset.accent === accent;
        b.setAttribute('data-active', active ? 'true' : 'false');
        b.setAttribute('aria-pressed', active ? 'true' : 'false');
    });

    document.querySelectorAll('.mode-seg button').forEach((b) => {
        const active = b.dataset.mode === mode;
        b.setAttribute('data-active', active ? 'true' : 'false');
        b.setAttribute('aria-pressed', active ? 'true' : 'false');
    });

    listeners.forEach((fn) => {
        try {
            fn(accent, mode);
        } catch (e) {
            /* satu pendengar yang error tidak boleh mematikan yang lain */
        }
    });
}

function commit(accent, mode) {
    state = { accent, mode };
    safeSet(KEY_ACCENT, accent);
    safeSet(KEY_MODE, mode);
    apply(accent, mode);
}

/**
 * Resolusi CSS variable ke warna yang bisa dipakai canvas.
 *
 * `getPropertyValue('--x')` TIDAK bisa dipakai di sini: untuk custom
 * property yang tidak terdaftar, ia mengembalikan string mentahnya,
 * termasuk `var()` yang belum terselesaikan -- misalnya
 * `--danger-tint` nilainya `color-mix(in srgb, #ef4444 16%, var(--surface))`.
 * Canvas tidak bisa membaca itu.
 *
 * Triknya: pasang nilai itu ke `color` elemen sungguhan, lalu baca
 * hasil hitungannya. Browser sudah menyelesaikan `var()`, `color-mix()`,
 * dan alfa di dalamnya.
 */
const probe = document.createElement('span');
let probeMounted = false;

/**
 * Memasang probe ke DOM hanya saat pertama kali dibutuhkan.
 *
 * Probe TIDAK boleh dipasang di `boot()` saja: `boot()` berjalan pada
 * DOMContentLoaded, sedangkan script inline di `<body>` (Chart.js,
 * SweetAlert2) berjalan lebih dulu saat dokumen masih di-parse. Kalau
 * probe baru ada setelah `boot()`, `themeColor()` yang dipanggil dari
 * sana akan membaca elemen yang belum ada di dokumen, dan
 * `getComputedStyle` mengembalikan string kosong -- bukan error, jadi
 * warnanya jadi `rgba(0, 0, 0, 0)` tanpa ada yang menyadarinya.
 */
function ensureProbe() {
    if (probeMounted) {
        return;
    }
    probe.setAttribute('aria-hidden', 'true');
    probe.style.cssText = 'position:absolute;width:0;height:0;overflow:hidden;pointer-events:none';
    document.documentElement.appendChild(probe);
    probeMounted = true;
}

function themeColor(name) {
    ensureProbe();
    const varName = name.startsWith('--') ? name : `--${name}`;
    probe.style.color = '';
    probe.style.color = `var(${varName})`;
    return window.getComputedStyle(probe).color;
}

/**
 * Dipakai Canvas/Chart.js. Chart dibangun ulang saat tema berubah.
 */
function onThemeChange(fn) {
    listeners.push(fn);
}

/**
 * Memuat SEMUA picker di halaman.
 *
 * Satu halaman bisa punya lebih dari satu: navbar desktop dan menu
 * mobile me-render partial yang sama, dan yang tidak sedang tampil
 * disembunyikan `display: none` di CSS. Karena itu pencarian memakai
 * kelas, bukan `getElementById` -- id-nya sengaja dibuat unik lewat
 * `$variant`, sedangkan listener diikat per elemen.
 */
function mountPicker() {
    const roots = document.querySelectorAll('.theme-dd');

    if (!roots.length) {
        return;
    }

    /** Panel yang sedang dibuka, untuk mencegah dua panel terbuka. */
    const panels = [];

    function closeAll(except) {
        panels.forEach(({ panel, btn }) => {
            if (panel === except) {
                return;
            }
            panel.setAttribute('data-open', 'false');
            panel.removeAttribute('data-drop');
            panel.style.setProperty('--dd-shift', '0px');
            btn.setAttribute('aria-expanded', 'false');
        });
    }

    /**
     * Menyesuaikan panel supaya SELURUHNYA di dalam viewport.
     *
     * CSS sudah menangani arah: `top: calc(100% + 8px)` dan
     * `right: 0` adalah bentuk defaultnya. Dua koreksi di sini
     * untuk kasus yang tidak bisa selesai dengan CSS saja:
     *
     *   - `data-drop="up"`. Di sidebar admin/superadmin pemicunya
     *     menempel di dasar sidebar yang setinggi layar, jadi
     *     panel 205px yang membuka ke bawah keluar dari bawah
     *     viewport -- persis yang tidak boleh terjadi.
     *
     *   - `--dd-shift`. Lebar panel 230px di dalam sidebar 240px
     *     membuat tepi kirinya keluar 6px dari layar. Nilainya
     *     diukur setelah panel dirender, jadi angka tetap dari CSS
     *     tidak bisa diandalkan untuk semua lebar.
     */
    function placePanel(panel) {
        const gap = 8;
        const margin = 12;

        panel.removeAttribute('data-drop');
        panel.style.setProperty('--dd-shift', '0px');

        const vh = window.innerHeight;
        const vw = window.innerWidth;

        if (panel.getBoundingClientRect().bottom > vh - gap) {
            panel.setAttribute('data-drop', 'up');
        }

        const rect = panel.getBoundingClientRect();
        let shift = 0;

        if (rect.left < margin) {
            shift = margin - rect.left;
        } else if (rect.right > vw - margin) {
            shift = vw - margin - rect.right;
        }

        if (shift) {
            panel.style.setProperty('--dd-shift', `${shift}px`);
        }
    }

    roots.forEach((root) => {
        const btn = root.querySelector('.theme-dd-btn');
        const panel = root.querySelector('.theme-dd-panel');

        if (!btn || !panel) {
            return;
        }

        root.querySelectorAll('.swatch').forEach((sw) => {
            sw.addEventListener('click', () => {
                commit(sw.dataset.accent, state.mode);
            });
        });

        root.querySelectorAll('.mode-seg button').forEach((mb) => {
            mb.addEventListener('click', () => {
                commit(state.accent, mb.dataset.mode);
            });
        });

        btn.addEventListener('click', (e) => {
            e.stopPropagation();

            const open = panel.getAttribute('data-open') === 'true';

            closeAll(open ? null : panel);
            panel.setAttribute('data-open', open ? 'false' : 'true');
            btn.setAttribute('aria-expanded', open ? 'false' : 'true');

            if (!open) {
                placePanel(panel);
            }
        });

        panels.push({ panel, btn });
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.theme-dd')) {
            closeAll();
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeAll();
        }
    });

    /* Panel yang sudah terbuka harus mengikuti perubahan lebar:
       menyempit ke breakpoint mobile memindahkan pemicunya ke
       menu, dan mode "up" bisa jadi tidak diperlukan lagi. */
    window.addEventListener('resize', () => {
        panels.forEach(({ panel }) => {
            if (panel.getAttribute('data-open') === 'true') {
                placePanel(panel);
            }
        });
    });
}

function boot() {
    ensureProbe();
    apply(state.accent, state.mode);
    mountPicker();

    const mq = window.matchMedia('(prefers-color-scheme: dark)');
    if (mq.addEventListener) {
        mq.addEventListener('change', () => {
            if (state.mode === 'system') {
                apply(state.accent, state.mode);
            }
        });
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
} else {
    boot();
}

window.SerojapTheme = { apply, commit, themeColor, onThemeChange, ACCENTS, MODES };
