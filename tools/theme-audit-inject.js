/* Audit tema di browser sungguhan, bukan di halaman tiruan.
 *
 * Dipakai lewat Chrome DevTools Protocol (tools/cdp.js). Untuk setiap
 * elemen yang punya text node terlihat, hitung kontras warnanya terhadap
 * background EFEKTIF -- hasilnya dicari ke atas sampai ketemu latar
 * yang tidak transparan, karena `background-color` sebuah kartu sering
 * `transparent` sementara warnanya benar-benar datang dari elemen induk.
 *
 * Yang diukur per kombinasi tema:
 *   - kontras setiap text node (ambang 4.5 teks normal, 3.0 teks besar)
 *   - overflow horizontal
 *   - elemen yang meluap dari lebar viewport
 *
 * Kombinasi tema dipasang lewat atribut yang sama dengan stratified
 * production (`data-accent` + `data-mode-resolved`), jadi yang diuji
 * benar-benar cascade CSS aplikasi, bukan tebakan.
 */
function __audit() {
    var ACCENTS = ['teal', 'blue', 'green', 'purple', 'amber', 'rose'];
    var MODES = ['light', 'dark'];
      var root = document.documentElement;

      // Transisi dimatikan dulu. Tanpa ini `getComputedStyle` yang
      // dipanggil seperseketika setelah `data-mode-resolved` diganti
      // masih melaporkan warna LAMA yang sedang dianimasikan, sehingga
      // mode light ikut terukur memakai tinta mode dark (dan sebaliknya).
      // Yang diukur adalah warna akhir, bukan tengah animasi.
      var stopMotion = document.createElement('style');
      stopMotion.textContent = '*,*::before,*::after{transition:none !important;'
          + 'animation:none !important;scroll-behavior:auto !important}';
      document.head.appendChild(stopMotion);


    function srgb(c) {
        // Array 0-1 (hasil komposit) langsung dipakai apa adanya.
        if (Object.prototype.toString.call(c) === '[object Array]') return c;
        // Normalisasi apa pun yang dikembalikan browser (rgb(), rgba(),
        // color(srgb ...), oklch) menjadi angka 0-1 per kanal.
        var m = String(c).match(/[-\d.]+/g);
        if (!m) return null;
        if (/^color\(/.test(String(c).trim())) {
            return [parseFloat(m[0]), parseFloat(m[1]), parseFloat(m[2])];
        }
        return [
            parseFloat(m[0]) / 255,
            parseFloat(m[1]) / 255,
            parseFloat(m[2]) / 255
        ];
    }

    function lum(c) {
        var v = srgb(c);
        if (!v) return null;
        var f = v.map(function (x) {
            return x <= 0.03928 ? x / 12.92 : Math.pow((x + 0.055) / 1.055, 2.4);
        });
        return 0.2126 * f[0] + 0.7152 * f[1] + 0.0722 * f[2];
    }

    function alpha(c) {
        if (!c) return 1;
        if (c === 'transparent') return 0;
        var m = String(c).match(/rgba?\(([^)]+)\)/);
        if (m) {
            var parts = m[1].split(',');
            if (parts.length >= 4) return parseFloat(parts[3]);
        }
        if (/^color\(/.test(String(c).trim())) return 1;
        return 1;
    }

    function over(fg, bg) {
        // Komposit foreground di atas latar (warna canvas tidak transparan).
        var f = srgb(fg), b = srgb(bg);
        if (!f || !b) return null;
        var a = alpha(fg);
        var m = [f[0] * a + b[0] * (1 - a),
                 f[1] * a + b[1] * (1 - a),
                 f[2] * a + b[2] * (1 - a)];
        return 'rgb(' + m.map(function (x) { return Math.round(x * 255); }).join(',') + ')';
    }

    function lumOf(c) { return lum(c); }

    function ratio(fg, bg) {
        var L1 = lumOf(fg), L2 = lumOf(bg);
        if (L1 === null || L2 === null) return null;
        var hi = Math.max(L1, L2), lo = Math.min(L1, L2);
        return (hi + 0.05) / (lo + 0.05);
    }

    function css(el, prop) {
        return getComputedStyle(el).getPropertyValue(prop);
    }

    // Latar efektif: naik sampai ketemu yang tidak transparan. Kalau
    // tidak ada, pakai warna canvas browser (putih kalau tidak ada
    // yang diset di body).
// Warna-warna stop dari string gradient hasil hitungan browser.
// Dipakai sebagai kandidat latar: teks di atas gradien bisa saja
// mendarat di antara dua stop, jadi semua stop harus diuji.
function gradientStops(bi) {
    var out = [], re = /rgba?\([^)]+\)/g, m;
    while ((m = re.exec(bi)) !== null) out.push(m[0]);
    return out;
}

function mixColor(a, b, t) {
    var A = srgb(a), B = srgb(b);
    if (!A || !B) return b;
    var av = alpha(a) * (1 - t) + alpha(b) * t;
    var ch = [0, 1, 2].map(function (i) {
        return Math.round((A[i] * (1 - t) + B[i] * t) * 255);
    });
    return 'rgba(' + ch[0] + ',' + ch[1] + ',' + ch[2] + ',' +
           (Math.round(av * 1000) / 1000) + ')';
}

// Titik sampel: setiap stop plus titik tengah di antaranya, supaya
// warna interpolasi di tengah gradien ikut terukur.
function sampleGradient(stops) {
    if (stops.length < 2) return stops;
    var out = [stops[0]];
    for (var i = 1; i < stops.length; i++) {
        out.push(mixColor(stops[i - 1], stops[i], 0.5));
        out.push(stops[i]);
    }
    return out;
}

// Mengembalikan daftar kandidat latar, bukan satu warna. Untuk
// latar polos daftarnya satu; untuk gradien, satu per titik sampel.
function effectiveBg(el) {
    var stack = [], image = null;

    // Lapisan diambil dari `elementsFromPoint`, bukan dengan naik ke
    // ancestor. Alasannya: hero dan peta menaruh foto serta overlay
    // gelapnya sebagai SAUDARA di dalam container, jadi nenek moyang
    // elemen teks tidak pernah punya background-nya. Naik ke ancestor
    // akan menganggap teks itu berdiri di atas `--bg` dan salah
    // melaporkan teks putih di atas foto sebagai kontras 1:1.
    var r = el.getBoundingClientRect();
    var pts = null;
    if (r.width > 0 && r.height > 0) {
        try {
            pts = document.elementsFromPoint(
                r.left + r.width / 2, r.top + r.height / 2);
        } catch (e) { pts = null; }
    }
    if (pts && pts.length) {
        for (var i = 0; i < pts.length; i++) {
            var n = pts[i];
            if (n.nodeType !== 1) continue;
            // Elemen teks itu sendiri dan anaknya tidak melapisi.
            if (n === el || el.contains(n)) continue;
            var c = css(n, 'background-color');
            var a = alpha(c);
            if (a > 0) {
                stack.push(c);
                if (a >= 1) break;
            }
            var bi = css(n, 'background-image');
            if (bi && bi !== 'none') { image = bi; break; }
        }
    }
    // Kalau titik pengujiannya jatuh di luar viewport, turun ke
    // penelusuran ancestor seperti biasa.
    if (!stack.length && !image) {
        var node = el;
        while (node && node.nodeType === 1) {
            var c2 = css(node, 'background-color');
            var a2 = alpha(c2);
            if (a2 > 0) {
                stack.push(c2);
                if (a2 >= 1) break;
            }
            var bi2 = css(node, 'background-image');
            if (bi2 && bi2 !== 'none') { image = bi2; break; }
            node = node.parentElement;
        }
    }

    function comp(under) {
        var acc = under;
        for (var i = stack.length - 1; i >= 0; i--) acc = over(stack[i], acc) || acc;
        return acc;
    }

    // Canvas: warna yang benar-benar tampil kalau tidak ada
    // elemen yang menimpanya.
    var base = getComputedStyle(document.body).backgroundColor;
    if (alpha(base) < 1) base = getComputedStyle(document.documentElement).backgroundColor;
    if (alpha(base) < 1) base = 'rgb(255,255,255)';

    if (image) {
        var stops = sampleGradient(gradientStops(image));
        if (!stops.length) {
            return { candidates: stack.length ? [comp(base)] : [], overImage: true,
                     why: 'latar berupa gambar raster, tidak bisa dihitung' };
        }
        return { candidates: stops.map(function (s) { return comp(s); }),
                 overImage: false, why: '', onGradient: true };
    }
    return { candidates: [comp(base)], overImage: false, why: '' };
}

// Hanya elemen yang punya node teks sendiri yang diukur; elemen
// yang hanya membungkus anak (kartu, section) tidak dihitung
// supaya tidak dobel.
function hasVisibleText(el) {
    for (var i = 0; i < el.childNodes.length; i++) {
        var n = el.childNodes[i];
        if (n.nodeType === 3 && n.textContent.trim().length) return true;
    }
    return false;
}

// Rantai selector singkat untuk melacak elemen yang gagal.
function pathOf(el) {
    var out = [], n = el;
    while (n && n.nodeType === 1 && n.tagName !== 'HTML') {
        var s = n.tagName.toLowerCase();
        if (n.id) s += '#' + n.id;
        else if (typeof n.className === 'string' && n.className.trim()) {
            s += '.' + n.className.trim().split(/\s+/).slice(0, 2).join('.');
        }
        out.unshift(s);
        n = n.parentElement;
    }
    return out.slice(-6).join(' > ');
}

// Ambang WCAG: teks besar (>=24px, atau >=18.66px tebal) cukup 3:1.
function isLarge(el) {
    var cs = getComputedStyle(el);
    var px = parseFloat(cs.fontSize) || 0;
    var bold = parseInt(cs.fontWeight, 10) || 400;
    return px >= 24 || (px >= 18.66 && bold >= 700);
}

function isDisabled(el) {
    return el.hasAttribute('disabled') ||
           el.getAttribute('aria-disabled') === 'true';
}

    function scanContrast() {
        var fails = [];
        var skipped = [];
        var all = document.body.querySelectorAll('*');
        for (var i = 0; i < all.length; i++) {
            var el = all[i];
            if (!hasVisibleText(el)) continue;
            var r = el.getBoundingClientRect();
            if (r.width <= 0 || r.height <= 0) continue;
            var cs = getComputedStyle(el);
            if (cs.visibility === 'hidden' || cs.display === 'none') continue;
            if (parseFloat(cs.opacity) < 0.15) continue;

            var fg = cs.color;
            var bgInfo = effectiveBg(el);
            if (bgInfo.overImage || !bgInfo.candidates.length) {
                skipped.push({ text: el.textContent.trim().slice(0, 40), why: bgInfo.why || 'latar tidak ditemukan' });
                continue;
            }

            // over() sudah mengembalikan string warna CSS, jadi tidak
            // perlu dibungkus lagi. Untuk beberapa kandidat latar
            // (gradien) yang dipakai adalah hasil TERBURUK: teks
            // dianggap gagal kalau gagal di salah satu titik gradien.
            var worst = null;
            for (var ci = 0; ci < bgInfo.candidates.length; ci++) {
                var bgc = bgInfo.candidates[ci];
                var fgc = alpha(fg) < 1 ? (over(fg, bgc) || fg) : fg;
                var r2 = ratio(fgc, bgc);
                if (r2 === null) continue;
                if (worst === null || r2 < worst.ratio) {
                    worst = { fg: fgc, bg: bgc, ratio: r2 };
                }
            }
            if (worst === null) { skipped.push({ text: el.textContent.trim().slice(0, 40), why: 'warna tidak bisa diurai' }); continue; }

            var large = isLarge(el);
            var min = large ? 3.0 : 4.5;
            if (worst.ratio < min) {
                fails.push({
                    text: el.textContent.trim().slice(0, 60),
                    fg: worst.fg, bg: worst.bg,
                    ratio: Math.round(worst.ratio * 100) / 100,
                    need: min, large: large, disabled: isDisabled(el),
                    onGradient: !!bgInfo.onGradient,
                    path: pathOf(el)
                });
            }
        }
        return { fails: fails, skipped: skipped };
    }

    function vwWidth() {
        /* Lebar nyata dari `100vw`, diukur, bukan diasumsikan. */
        var probe = document.createElement('div');
        probe.style.cssText = 'position:fixed;top:0;left:0;height:0;'
            + 'visibility:hidden;pointer-events:none;width:100vw';
        document.body.appendChild(probe);
        var w = probe.getBoundingClientRect().width;
        document.body.removeChild(probe);
        return w;
    }

    function scanOverflow() {
        var de = document.documentElement;
        var res = {
            scrollWidth: de.scrollWidth,
            clientWidth: de.clientWidth,
            overflow: de.scrollWidth > de.clientWidth,
            culprits: [],
            vwWidth: vwWidth(),
            vwSlack: 0,
            vwCulprits: []
        };

        /*
         * `scrollWidth > clientWidth` saja belum cukup.
         *
         * Emulasi CDP (dan headless Chrome pada umumnya) memakai
         * scrollbar overlay, jadi `clientWidth == innerWidth` dan
         * elemen selebar `100vw` terlihat PASC. Di browser sungguhan
         * dengan scrollbar klasik, `100vw` menghitung scrollbar
         * vertikal juga -- lebarnya jadi `clientWidth + 15px`, dan
         * itulah yang memunculkan scrollbar horizontal. Bug itu
         * karena itu tidak terlihat di sini dan harus dihitung
         * sendiri: `vwSlack` adalah selisih yang akan muncul di
         * browser sungguhan, dan setiap elemen yang lebarnya sama
         * dengan `100vw` akan melebar sebesar itu.
         *
         * Inilah yang membuat `.footer-section` dengan
         * `width: 100vw` + `margin-left: -50vw` auditing bersih di
         * sini tapi tetap menghasilkan scrollbar horizontal di
         * desktop pengguna.
         */
        res.vwSlack = Math.max(0, Math.round((res.vwWidth - de.clientWidth) * 10) / 10);

        if (res.overflow) {
            var limit = de.clientWidth;
            var all = document.body.querySelectorAll('*');
            for (var i = 0; i < all.length; i++) {
                var el = all[i];
                var r = el.getBoundingClientRect();
                if (r.right > limit + 1 || r.left < -1) {
                    res.culprits.push({
                        path: pathOf(el),
                        left: Math.round(r.left), right: Math.round(r.right),
                        w: Math.round(r.width)
                    });
                }
                if (res.culprits.length >= 8) break;
            }
        }

        if (res.vwSlack > 0) {
            var body = document.body.querySelectorAll('*');
            for (var j = 0; j < body.length; j++) {
                var e2 = body[j];
                var r2 = e2.getBoundingClientRect();
                if (r2.width > 0 && Math.abs(r2.width - res.vwWidth) < 1) {
                    res.vwCulprits.push({
                        path: pathOf(e2),
                        w: Math.round(r2.width),
                        lebih: res.vwSlack
                    });
                    if (res.vwCulprits.length >= 8) break;
                }
            }
        }

        return res;
    }

    var results = [];

    /*
     * Lebar TIDAK lagi diputar di dalam skrip ini.
     *
     * Dulu ada loop `for (w of WIDTHS)` yang tidak pernah mengubah
     * viewport sungguhan -- tidak ada `resize`, tidak ada
     * `setDeviceMetricsOverride` -- jadi setiap halaman diukur sekali
     * pada satu lebar, lalu hasilnya ditulis tiga kali dengan label
     * 1280/768/375. Angka "per lebar" di laporan lama karena itu
     * sebenarnya angka yang sama berulang.
     *
     * Sekarang lebar datang dari luar (`tools/audit_theme.py` lewat
     * CDP) dan yang dicatat adalah `window.innerWidth` yang benar.
     * Satu eksekusi = satu lebar.
     */
    var actualWidth = window.innerWidth;

    for (var a = 0; a < ACCENTS.length; a++) {
        for (var m = 0; m < MODES.length; m++) {
            root.setAttribute('data-accent', ACCENTS[a]);
            root.setAttribute('data-mode', MODES[m]);
            root.setAttribute('data-mode-resolved', MODES[m]);
            var c = scanContrast();
            var o = scanOverflow();
            results.push({
                width: actualWidth,
                combo: ACCENTS[a] + '/' + MODES[m],
                fails: c.fails.filter(function (f) { return !f.disabled; }),
                disabledFails: c.fails.filter(function (f) { return f.disabled; }),
                skipped: c.skipped,
                overflow: o.overflow, ow: o.scrollWidth, cw: o.clientWidth,
                culprits: o.culprits,
                vwWidth: o.vwWidth, vwSlack: o.vwSlack, vwCulprits: o.vwCulprits
            });
        }
    }

    var out = document.createElement('script');
    out.type = 'application/json';
    out.id = 'theme-audit-result';
    out.textContent = JSON.stringify({
        page: document.body.getAttribute('data-theme-audit-page') || document.title,
        results: results
    });
    document.body.appendChild(out);
    document.title = 'AUDIT-DONE';
}

try {
    __audit();
} catch (e) {
    var err = document.createElement('script');
    err.type = 'application/json';
    err.id = 'theme-audit-result';
    err.textContent = JSON.stringify({ fatal: String(e && e.stack || e) });
    document.body.appendChild(err);
}
