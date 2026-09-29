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
      //
      // Sifat khususnya penting: `*, *::before` (0,0,0) TIDAK cukup.
      // `welcome.blade.php` menulis
      // `.btn-custom-action { transition: all .2s ease-in-out !important }`
      // dan `!important` di spesifisitas (0,1,0) mengalahkan `*` yang
      // juga `!important`. Akibatnya tombol diukur sewaktu animasi --
      // teks dark mode sudah bergerak ke warna light, tapi `getPropertyValue`
      // custom property sudah berubah, jadi laporan menampilkan dua
      // nilai yang saling bertentangan untuk elemen yang sama.
      //
      // `html[data-accent] [class]` bernilai (0,2,1) dan menang. Atribut
      // `data-accent` selalu dipasang audit ini sebelum mengukur, jadi
      // selektor ini tidak pernah gagal match.
      var stopMotion = document.createElement('style');
      stopMotion.textContent = 'html[data-accent] [class],'
          + 'html[data-accent] [class]::before,'
          + 'html[data-accent] [class]::after{'
          + 'transition:none !important;'
          + 'animation:none !important;'
          + 'scroll-behavior:auto !important}';
      document.head.appendChild(stopMotion);


    /* Parser warna yang mengembalikan [r, g, b, a] dengan r/g/b 0-1.
     *
     * Semua bentuk warna yang memang bisa dikembalikan browser harus
     * ditangani:
     * `rgb(1, 2, 3)`, `rgba(1, 2, 3, .5)`, `rgb(1 2 3 / 50%)`,
     * `color(srgb 0.1 0.2 0.3 / 0.4)`, dan `transparent`.
     *
     * Yang paling penting adalah `/ alpha` pada notasi `color()`:
     * `color-mix(in srgb, var(--teal) 10%, transparent)` dikomputasi
     * browser menjadi `color(srgb 0.13 0.42 0.44 / 0.1)`. Versi lama
     * membaca `color(` sebagai "opak" dan mengabaikan alpha-nya, jadi
     * badge 10% itu dihitung seolah warnanya penuh -- teks accent di
     * atasnya jadi kontras 1:1, padahal di layar yang tampil jauh lebih
     * terang. Audit melaporkan kontras salah, bukan temuannya salah.
     */
    function parseColor(c) {
        if (Object.prototype.toString.call(c) === '[object Array]') return c;
        var s = String(c).trim();
        if (!s || s === 'none') return null;
        if (s === 'transparent') return [0, 0, 0, 0];

        var nums = (s.match(/[-\d.]+%?/g) || []).map(function (t) {
            return t.charAt(t.length - 1) === '%' ? parseFloat(t) / 100 : parseFloat(t);
        });
        if (nums.length < 3) return null;

        var a = 1;
        var isColorFn = /^color\(/i.test(s);
        var rgb;

        if (isColorFn) {
            // `color(srgb r g b / a)` -- kanal sudah 0-1.
            rgb = [nums[0], nums[1], nums[2]];
        } else {
            // `rgb()`/rgba() kanal 0-255; `oklab`/`oklch()` juga 0-1
            // tapi tidak pernah muncul sebagai background-color komputasi
            // di browser, jadi tidak ditangani terpisah.
            rgb = [nums[0] / 255, nums[1] / 255, nums[2] / 255];
        }

        // Alpha: baik `%` legacy `rgba(r,g,b,0.35)` maupun `/ a` modern.
        var slash = s.indexOf('/');
        if (slash !== -1) {
            var rest = (s.slice(slash + 1).match(/[-\d.]+%?/) || [])[0];
            if (rest !== undefined) {
                a = rest.charAt(rest.length - 1) === '%' ? parseFloat(rest) / 100 : parseFloat(rest);
            }
        } else if (nums.length >= 4) {
            a = nums[3];
        }

        return [rgb[0], rgb[1], rgb[2], isNaN(a) ? 1 : a];
    }

    function srgb(c) {
        var v = parseColor(c);
        return v ? [v[0], v[1], v[2]] : null;
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
        if (Object.prototype.toString.call(c) === '[object Array]') return c[3];
        var v = parseColor(c);
        return v ? v[3] : 1;
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
//
// Dua perbaikan yang keduanya menutup celah false-positive:
//
// 1. Dulu regex-nya `rgba?\([^)]+\)` dijalankan ke SELURUH string.
//    padahal `background-image` boleh berisi beberapa lapis yang
//    dipisah koma, dan koma itu ARE bagian dari sintaks warna.
//    Contoh nyata di `superadmin/accounts/create.blade.php`:
//
//        linear-gradient(135deg, rgba(255,255,255,.98), rgba(244,249,255,.98)),
//        radial-gradient(circle at 100% 0%, color(srgb .13 .42 .44 / .13), rgba(0,0,0,0) 34%)
//
//    Flattening mengambil semua warna jadi satu deret, lalu
//    `mixColor` menginterpolasi DARI linear-gradient KE radial-gradient
//    -- kombinasi yang tidak pernah ada di layar. Hasilnya
//    `rgb(204,204,204)` dan label gelap dilaporkan 3.08:1, padahal
//   lapisan putih 0.98 di atasnya membuat teksnya 4.77:1 dan LOLOS.
//
// 2. `color(srgb r g b / a)` tidak cocok dengan `rgba?\(`, jadi
//    warna aksen 13% itu hilang dari daftar. Sekarang keduanya
//    dibaca, dan `rgba(0,0,0,0)` diperlakukan sebagai "tembus
//    penuh" -- bukan warna hitam, yang di sampler's lama ikut
//    diinterpolasi dan menarik semua stop ke gelap.
//
// Lapisan dipisah dulu (lihat `splitLayers`), tiap lapis di-sample
// sendiri, lalu dikomposit dari bawah ke atas sesuai urutan CSS:
// lapis PERTAMA yang diwarnai di atas.
function splitLayers(bi) {
    var out = [], depth = 0, start = 0;
    for (var i = 0; i < bi.length; i++) {
        var ch = bi.charAt(i);
        if (ch === '(') depth++;
        else if (ch === ')') depth--;
        else if (ch === ',' && depth === 0) {
            out.push(bi.slice(start, i).trim());
            start = i + 1;
        }
    }
    var last = bi.slice(start).trim();
    if (last) out.push(last);
    return out;
}

var STOP_RE = /rgba?\([^)]*\)|color\(\s*srgb[^)]*\)|#[0-9a-fA-F]{3,8}/g;

function gradientStops(layer) {
    var out = [], m;
    STOP_RE.lastIndex = 0;
    while ((m = STOP_RE.exec(layer)) !== null) {
        var parsed = parseColor(m[0]);
        if (!parsed) continue;
        // `rgba(0,0,0,0)` = transparan penuh, bukan hitam.
        if (parsed[3] === 0) continue;
        out.push(m[0]);
    }
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

// `background-image` bisa bertumpuk. Urutan CSS: lapis PERTAMA yang
// paling atas.
//
// Menebak warnanya dari teksnya selalu keliru, karena
// `radial-gradient(circle at 100% 0%, ...)` warnanya BERGANTUNG posisi.
// Mengambil semua stop memaksa audit menilai setiap teks seolah
// berdiri di pojok tergelap kartu -- dan `34%`/`px` mustahil
// dihitung tanpa tahu ukuran box.
//
// Jadi gradiennya benar-benar DIWARNAl ke canvas berukuran sama
// dengan padding box elemen, lalu pixel-nya dibaca. `at 100% 0%` dan
// `34%` dihitung browser dengan box yang sama seperti di halaman, jadi
// tidak ada tebakan. Contoh yang dulu salah: kartu `.create-shell`
// punya radial aksen 13% di kanan atas, sementara label form-nya di
// kiri -- audit mengira label itu berdiri di atas aksen 13% dan
// melaporkan 4.08:1, padahal area itu 4.74:1 dan lolos.
//
// Kandidat yang dikembalikan hanya pixel yang mungkin terkena teks:
// pusat, keempat pojok yang di-inset, dan tepi tengah. Rasio monotonik
// terhadap luminansi, jadi titik di antaranya tidak perlu diuji.
//
// Kalau canvas gagal (tanpa 2d context, box berukuran 0), jatuh ke
// `parseGradientFallback` yang mengurai teksnya -- lebih kasar, tapi
// lebih baik daripada tidak mengukur sama sekali.
var _gradCanvas = null;
function _gradCtx(w, h) {
    if (!_gradCanvas) _gradCanvas = document.createElement('canvas');
    if (_gradCanvas.width !== w || _gradCanvas.height !== h) {
        _gradCanvas.width = w;
        _gradCanvas.height = h;
    }
    var cx = _gradCanvas.getContext('2d', { willReadFrequently: true });
    if (!cx) return null;
    cx.clearRect(0, 0, w, h);
    return cx;
}

// Padding box = box tempat `background-origin` (default) menggambar
// gradien. Border tidak termasuk, makanya dikurangkan.
function paddingBoxSize(el) {
    var cs = getComputedStyle(el);
    var w = Math.round(el.offsetWidth -
                       (parseFloat(cs.borderLeftWidth) || 0) -
                       (parseFloat(cs.borderRightWidth) || 0));
    var h = Math.round(el.offsetHeight -
                       (parseFloat(cs.borderTopWidth) || 0) -
                       (parseFloat(cs.borderBottomWidth) || 0));
    return { w: Math.max(0, w), h: Math.max(0, h) };
}

var GRADIENT_MAX_SIDE = 600;

function gradientCandidates(image, base, comp, el, imageNode) {
    var box = paddingBoxSize(el);
    if (box.w > 0 && box.h > 0) {
        // Skala turun kalau boxnya kebesaran; rasio dijaga supaya
        // posisi relatif gradien tidak bergeser.
        var k = Math.min(1, GRADIENT_MAX_SIDE / box.w, GRADIENT_MAX_SIDE / box.h);
        var w = Math.max(1, Math.round(box.w * k));
        var h = Math.max(1, Math.round(box.h * k));
        var cx = _gradCtx(w, h);
        if (cx) {
            try {
                // Canvas tidak mendukung background berlapis, jadi
                // setiap lapis digambar sendiri dari yang paling
                // bawah. Urutan CSS: lapis PERTAMA paling atas, jadi
                // harus diwarnai TERBALIK.
                //
                // `fillStyle` yang tidak valid diabaikan diam-diam --
                // penugasannya tidak melempar error, nilainya tetap yang
                // sebelumnya. Kalau tidak diperiksa, `fillRect` akan
                // mengecat HITAM pekat dan audit melaporkan latar
                // rgb(0,0,0) untuk semua teks di atas gradien. Itu
                // persis yang terjadi sebelum sentinel di bawah
                // ditambahkan: 270 kegagalan palsu dengan rasio 1.35.
                var layers = splitLayers(image).filter(function (l) {
                    return /gradient\(/i.test(l);
                });
                var painted = 0;
                for (var li = layers.length - 1; li >= 0; li--) {
                    cx.fillStyle = 'rgb(1, 2, 3)';
                    cx.fillStyle = layers[li];
                    if (!/gradient/i.test(cx.fillStyle)) continue;
                    cx.fillRect(0, 0, w, h);
                    painted++;
                }
                if (!painted) throw new Error('canvas tidak menerima lapis gradien');

                var under = comp(base);
                var inset = Math.max(1, Math.round(Math.min(w, h) * 0.02));
                var probes = [
                    [w >> 1, h >> 1],
                    [inset, inset], [w - inset - 1, inset],
                    [inset, h - inset - 1], [w - inset - 1, h - inset - 1],
                    [w >> 1, inset], [w >> 1, h - inset - 1],
                    [inset, h >> 1], [w - inset - 1, h >> 1]
                ];
                var lo = null, hi = null, loL = null, hiL = null;
                for (var p = 0; p < probes.length; p++) {
                    var x = Math.min(w - 1, Math.max(0, probes[p][0]));
                    var y = Math.min(h - 1, Math.max(0, probes[p][1]));
                    var d = cx.getImageData(x, y, 1, 1).data;
                    if (d[3] === 0) continue;
                    var c = over('rgba(' + d[0] + ',' + d[1] + ',' + d[2] + ',' +
                                 (Math.round(d[3] / 255 * 1000) / 1000) + ')', under) || under;
                    var L = lum(c);
                    if (L === null) continue;
                    if (loL === null || L < loL) { loL = L; lo = c; }
                    if (hiL === null || L > hiL) { hiL = L; hi = c; }
                }
                if (loL !== null) return lo === hi ? [lo] : [lo, hi];
            } catch (e) { /* jatuh ke fallback di bawah */ }
        }
    }
    return parseGradientFallback(image, base, comp, el, imageNode);
}

// Cadangan untuk saat canvas tidak menerima layer-nya. Chrome
// menolak `radial-gradient` pada `ctx.fillStyle` (linear-gradient
// diterima) -- dicek di browser, bukan asumsi -- jadi layer radial
// harus dihitung sendiri.
//
// Yang dihitung di sini hanya bentuk yang dipakai view: fokus
// eksplisit `at X% Y%` dan satu stop warna aksen yang memudar jadi
// `rgba(0,0,0,0)` pada radius tertentu. Bentuk lain dikembalikan
// null supaya pemanggil jatuh ke sampling semua stop -- kasar tapi
// tidak mengarang posisi.
//
// PENTING: posisinya ikut dihitung. Radial aksen 13% di pojok kanan
// atas kartu TIDAK boleh diperlakukan seolah seluruh kartu kena
// warna itu -- itu membuat label di kiri atas kartu ikut dilaporkan
// 4.08:1 padahal areanya 4.74:1.
function radialAt(layer, box, px, py) {
    var mAt = /at\s+(-?[\d.]+)%\s+(-?[\d.]+)%/.exec(layer);
    if (!mAt) return null;
    var stops = gradientStops(layer);
    if (stops.length !== 1) return null;
    var mEnd = /rgba\(0,\s*0,\s*0,\s*0\)\s*([\d.]+)(%|px)/.exec(layer);
    if (!mEnd) return null;

    var W = box.w, H = box.h;
    var cx = parseFloat(mAt[1]) / 100 * W;
    var cy = parseFloat(mAt[2]) / 100 * H;
    // Panjang persentase pada `circle` dihitung terhadap diagonal
    // Perspektif, sesuai spesifikasi gradient CSS.
    var R = mEnd[2] === '%'
        ? (parseFloat(mEnd[1]) / 100) * Math.sqrt(W * W + H * H) / Math.SQRT2
        : parseFloat(mEnd[1]);
    if (!(R > 0)) return null;

    var d = Math.sqrt(Math.pow(px - cx, 2) + Math.pow(py - cy, 2));
    if (d >= R) return null;   // di luar jangkauan: lapisan tak terlihat

    var v = parseColor(stops[0]);
    if (!v) return null;
    // Radial `A -> transparan pada R%` opasitasnya menurun linear.
    var t = 1 - d / R;
    var a = v[3] * t;
    if (a <= 0) return null;
    return 'rgba(' + Math.round(v[0] * 255) + ',' + Math.round(v[1] * 255) + ',' +
           Math.round(v[2] * 255) + ',' + (Math.round(a * 1000) / 1000) + ')';
}

// Parsa penuh, dipakai kalau `radialAt` tidak bisa,

function parseGradientFallback(image, base, comp, el, node) {
    var layers = splitLayers(image).filter(function (l) { return /gradient\(/i.test(l); });
    if (!layers.length) return null;

    var box = null, px = null, py = null;
    if (node && node.nodeType === 1) {
        box = paddingBoxSize(node);
        var a = node.getBoundingClientRect(), b = el.getBoundingClientRect();
        var cs = getComputedStyle(node);
        var bx = a.left + (parseFloat(cs.borderLeftWidth) || 0);
        var by = a.top + (parseFloat(cs.borderTopWidth) || 0);
        var k = box.w > 0 ? box.w / (a.width - (parseFloat(cs.borderLeftWidth) || 0) -
                                     (parseFloat(cs.borderRightWidth) || 0)) : 1;
        px = (b.left + b.width / 2 - bx) * k;
        py = (b.top + b.height / 2 - by) * k;
    }

    // Komposit dari BAWAH ke atas. Urutan CSS melukis lapis PERTAMA
    // paling atas, jadi iterasi dibalik.
    //
    // `acc` harus terus berjalan antar-lapis. Dulu `acc` di-reset ke
    // `base` tiap iterasi, jadi radial aksen 13% ikut diukur sendirian
    // di atas warna body -- padahal di layar yang mengaturnya ada
    // lapisan putih 0.98 di atasnya. Itu melaporkan 3.51:1 untuk
    // label yang areanya terang.
    var acc = base;
    var results = [];
    for (var i = layers.length - 1; i >= 0; i--) {
        var sampled = null;
        if (box && /radial-gradient/i.test(layers[i])) {
            var rc = radialAt(layers[i], box, px, py);
            if (rc) sampled = [rc];
        }
        if (!sampled) sampled = sampleGradient(gradientStops(layers[i]));
        if (!sampled.length) continue;

        // Satu titik sampel: hasilnya langsung jadi kandidat final.
        // Lebih dari satu: semua diuji, dan yang diteruskan ke lapis
        // berikutnya adalah titik paling gelap -- kalau teksnya gelap
        // itu yang terburuk, dan kalau terang justru titik paling
        // terang. Karena rasio monotonik terhadap luminansi, menguji
        // kedua ujung di setiap lapis menutup semua kasus.
        var composited = [];
        for (var j = 0; j < sampled.length; j++) {
            composited.push(over(sampled[j], acc) || acc);
        }
        for (var q = 0; q < composited.length; q++) results.push(composited[q]);

        var loL = null, hiL = null, lo = null, hi = null;
        for (var m = 0; m < composited.length; m++) {
            var L = lum(composited[m]);
            if (L === null) continue;
            if (loL === null || L < loL) { loL = L; lo = composited[m]; }
            if (hiL === null || L > hiL) { hiL = L; hi = composited[m]; }
        }
        acc = loL === null ? acc : lo;
    }
    if (!results.length) return null;

    var lo = null, hi = null, loL = null, hiL = null;
    for (var q = 0; q < results.length; q++) {
        var L = lum(results[q]);
        if (L === null) continue;
        if (loL === null || L < loL) { loL = L; lo = results[q]; }
        if (hiL === null || L > hiL) { hiL = L; hi = results[q]; }
    }
    if (loL === null) return null;
    return lo === hi ? [lo] : [lo, hi];
}
// Mengembalikan daftar kandidat latar, bukan satu warna. Untuk
// latar polos daftarnya satu; untuk gradien, satu per titik sampel.
function effectiveBg(el) {
    var stack = [], image = null, imageNode = null;

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
            /* Elemen yang diukur sendiri TIDAK dilewati.
             *
             * Latarnya sendiri berada di belakang teksnya. Avatar
             *-sidebar adalah contoh: satu `<div>` yang memuat gradien
             * `from-[var(--accent)]` DAN huruf inisial "A" di
             * dalamnya. Dulu `if (n === el) continue` membuang
             * gradien itu, jadi audit turun ke `<body>` dan mengukur
             * teks putih di atas `--bg` -- melaporkan 1.07:1 padahal
             * warna aslinya 6.01:1.
             *
             * Jalur ancestor di bawah sudah benar (dimulai dari `el`
             * sendiri), jadi keduanya sekarang konsisten. Elemen
             * tanpa background tetap dilewati oleh cek alpha, dan
             * urutan `elementsFromPoint` sudah benar dari atas ke
             * bawah untuk NeedsBehind.
             *
             * HARAP DIJAGA: jangan menambahkan `el.contains(n)`
             * sebagai syarat lewati. Sebuah anak yang punya
             * background sendiri tetap melapisi teks induknya --
             * tombol ber-gradient dengan `<span>` berlatak adalah
             * contoh nyata. Yang dilewati hanya yang alpha-nya 0. */
            var c = css(n, 'background-color');
            var a = alpha(c);
            if (a > 0) {
                stack.push(c);
                if (a >= 1) break;
            }
            var bi = css(n, 'background-image');
            if (bi && bi !== 'none') { image = bi; imageNode = n; break; }
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
            if (bi2 && bi2 !== 'none') { image = bi2; imageNode = node; break; }
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
        var cands = gradientCandidates(image, base, comp, el, imageNode);
        if (!cands || !cands.length) {
            return { candidates: stack.length ? [comp(base)] : [], overImage: true,
                     why: 'latar berupa gambar raster, tidak bisa dihitung' };
        }
        return { candidates: cands, overImage: false, why: '', onGradient: true };
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
                    // Warna mentah apa adanya dari `getComputedStyle`.
                    // `fg`/`bg` di atas sudah berupa hasil komposit, jadi
                    // tanpa ini laporan sering tidak cocok dengan yang
                    // ditunjukkan DevTools dan debugging berhenti
                    // pertanyaan "kenapa audit bilang begini".
                    rawFg: fg,
                    rawClass: typeof el.className === 'string' ? el.className.trim() : '',
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

    /*
     * Matikan semua transisi SEBELUM mengukur.
     *
     * Loop di bawah menukar `data-mode-resolved` lalu langsung
     * membaca `getComputedStyle` di task yang sama. Custom property
     * tidak bisa dianimasikan, jadi nilainya melompat seketika ke
     * mode baru -- tapi `background-color` dan `color` yang ber-
     * `transition` masih menunjukkan nilai mode LAMA.
     *
     * Diuji di `pelapor-buat-laporan` pada `.field-input`, yang punya
     * `transition`:
     *
     *   bacaan sinkron  -> rgb(26, 35, 35)   (mode dark lama)
     *   +50ms           -> rgb(131, 136, 136) ( tengah transisi)
     *   +450ms          -> rgb(255, 255, 255)  (mode light, benar)
     *
     * `--surface` sudah `rgb(255,255,255)` sejak bacaan pertama,
     * karena custom property tidak ikut bertransisi.
     *
     * Efeknya ke laporan: setiap elemen ber-transisi diukur dengan
     * warna kombinasi sebelumnya, sehingga rasio kontras yang
     * tercatat bisa milik mode yang salah. Sebagian besar lonjakan
     * jumlah kegagalan di commit 3 kemungkinan berasal dari ini,
     * bukan dari penemuan masalah yang baru.
     *
     * `!important` dipakai karena aturan transisi di `app.css`
     * dan `navbar.css` specificity-nya lebih tinggi daripada
     * selector universal di sini.
     */
    var kill = document.createElement('style');
    kill.id = 'theme-audit-freeze';
    kill.textContent =
        '*,*::before,*::after{' +
        'transition:none !important;' +
        'animation:none !important;' +
        'animation-duration:0s !important;' +
        'transition-duration:0s !important;' +
        'caret-color:transparent !important;' +
        'scroll-behavior:auto !important' +
        '}';
    document.head.appendChild(kill);

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
