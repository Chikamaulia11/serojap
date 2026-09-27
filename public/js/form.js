/**
 * Form laporan kerusakan jalan.
 *
 * Semua isi koordinat dan lokasi di sini. Fokusnya: jangan pernah
 * meninggalkan pengguna dalam keadaan diam-diam gagal.
 */

var map = null;
var mapLoaded = false;
var marker = null;

/* Default: Kabupaten Purwakarta */
var PURWAKARTA = { lat: -6.556, lng: 107.443 };

var el = {
    tombolPeta: document.getElementById('tombol-peta'),
    tombolGps: document.getElementById('tombol-gps'),
    mapContainer: document.getElementById('mapContainer'),
    alamat: document.getElementById('alamat'),
    autocomplete: document.getElementById('autocomplete-list'),
    lat: document.getElementById('lat'),
    lng: document.getElementById('lng'),
    inputLat: document.getElementById('input-latitude'),
    inputLng: document.getElementById('input-longitude'),
    koordinatManual: document.getElementById('koordinat-manual'),
    foto: document.getElementById('foto'),
    form: document.getElementById('form-laporan'),
    ringkasanError: document.getElementById('ringkasan-error')
};

/* =========================
   UTILITAS
========================= */

function tampilkanPesan(pesan) {
    var box = document.getElementById('pesan-lokasi');

    if (!box) {
        box = document.createElement('p');
        box.id = 'pesan-lokasi';
        box.className = 'field-error';
        box.setAttribute('role', 'status');
        document.getElementById('lokasi-area').appendChild(box);
    }

    box.textContent = pesan;
    box.hidden = false;
}

function sembunyikanPesan() {
    var box = document.getElementById('pesan-lokasi');

    if (box) {
        box.hidden = true;
        box.textContent = '';
    }
}

/*
 * Buka blok "Isi koordinat manual".
 *
 * Halaman menyuruh pengguna "isi koordinat secara manual kalau peta
 * gagal dimuat" -- tapi isinya dua `type="hidden"` yang tidak bisa
 * diketik sama sekali. Setiap pelapor yang peta atau GPS-nya gagal
 * cuma punya satu jalan: submit, ditolak server, lalu mengulang.
 *
 * Semua kegagalan lokasi memanggil fungsi ini supaya fallback-nya
 * benar-benar muncul di depan mata.
 */
function bukaKoordinatManual(alasan) {
    if (!el.koordinatManual) return;

    el.koordinatManual.open = true;

    if (alasan) {
        el.koordinatManual.dataset.alasan = alasan;
    }
}

function tampilkanPeta() {
    if (el.mapContainer.hidden) {
        el.mapContainer.hidden = false;
        el.tombolPeta.setAttribute('aria-expanded', 'true');
    }

    setTimeout(function () {
        if (!mapLoaded) {
            if (typeof L === 'undefined') {
                tampilkanPesan('Peta gagal dimuat. Periksa koneksi internet, atau isi koordinat lokasi secara manual di bawah.');
                bukaKoordinatManual('Peta tidak bisa dimuat');
                return;
            }

            initMap();
            mapLoaded = true;
        } else if (map) {
            map.invalidateSize();
        }
    }, 120);
}

/* =========================
   PETA
========================= */

function initMap() {
    map = L.map('map', { zoomControl: true }).setView([PURWAKARTA.lat, PURWAKARTA.lng], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    map.on('click', function (e) {
        setMarker(e.latlng.lat, e.latlng.lng);
        ambilAlamat(e.latlng.lat, e.latlng.lng);
    });

    /* Kalau form gagal validasi, kembalikan penanda ke posisi lama. */
    if (el.inputLat.value && el.inputLng.value) {
        var lat = parseFloat(el.inputLat.value);
        var lng = parseFloat(el.inputLng.value);

        if (!isNaN(lat) && !isNaN(lng)) {
            setMarker(lat, lng, false);
        }
    }
}

function setMarker(lat, lng, geserPeta) {
    if (marker && map) {
        map.removeLayer(marker);
    }

    if (map) {
        marker = L.marker([lat, lng], { draggable: true }).addTo(map);

        marker.on('dragend', function () {
            var pos = marker.getLatLng();
            setMarker(pos.lat, pos.lng, false);
            ambilAlamat(pos.lat, pos.lng);
        });

        if (geserPeta !== false) {
            map.setView([lat, lng], 16);
        }
    }

    el.lat.textContent = lat.toFixed(6);
    el.lng.textContent = lng.toFixed(6);
    el.inputLat.value = lat.toFixed(6);
    el.inputLng.value = lng.toFixed(6);
    sembunyikanPesan();
}

/* =========================
   GEOKODING
========================= */

/**
 * Nominatim membatasi 1 permintaan per detik. Permintaan yang gagal
 * DIHAPUS dari UI sebelumnya hanya dicatat ke console, jadi koordinat
 * terisi tapi alamat tetap kosong tanpa penjelasan apa pun.
 */
function ambilAlamat(lat, lng) {
    fetch('https://nominatim.openstreetmap.org/reverse?format=json&zoom=17&lat=' + lat + '&lon=' + lng)
        .then(function (res) {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.json();
        })
        .then(function (data) {
            if (data && data.display_name) {
                el.alamat.value = data.display_name.slice(0, 255);
                sembunyikanPesan();
            } else {
                tampilkanPesan('Lokasi sudah ditandai, tapi alamatnya tidak ditemukan otomatis. Silakan ketik atau lengkapi alamatnya sendiri.');
            }
        })
        .catch(function () {
            tampilkanPesan('Lokasi sudah ditandai di peta, tapi pencarian alamat otomatis gagal (biasanya koneksi sedang lambat). Silakan ketik alamatnya sendiri.');
        });
}

/* =========================
   TOMBOL
========================= */

if (el.tombolPeta) {
    el.tombolPeta.addEventListener('click', function () {
        if (el.mapContainer.hidden) {
            tampilkanPeta();
        } else {
            el.mapContainer.hidden = true;
            el.tombolPeta.setAttribute('aria-expanded', 'false');
        }
    });
}

if (el.tombolGps) {
    el.tombolGps.addEventListener('click', function () {
        if (!navigator.geolocation) {
            tampilkanPesan('HP atau browser ini tidak mendukung pembacaan lokasi. Tandai Manual lewat peta saja.');
            return;
        }

        var catatan = document.getElementById('gps-catatan');
        if (catatan && catatan._x_dataStack) {
            catatan._x_dataStack[0].tampil = true;
        }

        el.tombolGps.disabled = true;

        navigator.geolocation.getCurrentPosition(
            function (posisi) {
                el.tombolGps.disabled = false;

                var lat = posisi.coords.latitude;
                var lng = posisi.coords.longitude;

                tampilkanPeta();
                setTimeout(function () {
                    setMarker(lat, lng);
                    ambilAlamat(lat, lng);
                }, mapLoaded ? 60 : 260);
            },
            function (err) {
                el.tombolGps.disabled = false;

                var pesan = {
                    1: 'Izin lokasi ditolak. Izinkan lokasi lewat pengaturan browser, atau tandai lewat peta.',
                    2: 'Lokasi sedang tidak bisa dibaca. Coba di tempat yang lebih terbuka, atau tandai lewat peta.',
                    3: 'Pencarian lokasi berjalan terlalu lama.'
                };

                tampilkanPesan(
                    (pesan[err.code] || 'Lokasi tidak bisa dibaca otomatis.')
                        + ' Tandai lewat peta, atau isi koordinat manual.'
                );

                /*
                 * Kolom koordinat manual dibuka di sini. Sebelumnya
                 * semua pesan ini hanya redirection ke peta, dan
                 * GPS yang ditolak sementara peta juga gagal dimuat
                 * = halaman buntu total tanpa jalan keluar.
                 */
                bukaKoordinatManual('Gagal membaca lokasi');
            },
            { enableHighAccuracy: true, timeout: 12000, maximumAge: 60000 }
        );
    });
}

/* =========================
   AUTOCOMPLETE LOKASI
========================= */

var lokasiPurwakarta = [
    'Purwakarta',
    'Plered',
    'Wanayasa',
    'Jatiluhur',
    'Sadang',
    'Pasawahan',
    'Cikopo',
    'Ciganea',
    'Campaka',
    'Bungursari',
    'Cibatu',
    'Darangdan',
    'Kiarapedes',
    'Maniis',
    'Pondoksalam',
    'Sukatani',
    'Tegalwaru',
    'Situ Buleud',
    'Taman Air Mancur Sri Baduga',
    'Stasiun Purwakarta',
    'RSUD Bayu Asih',
    'Alun-Alun Purwakarta',
    'Universitas Pendidikan Indonesia Purwakarta',
    'Gerbang Tol Jatiluhur',
    'Gerbang Tol Sadang',
    'Hotel Harper Purwakarta',
    'Perumahan Bukit Indah',
    'Kawasan Industri Kota Bukit Indah'
];

var indeksSorotan = -1;

function tutupAutocomplete() {
    el.autocomplete.innerHTML = '';
    el.autocomplete.hidden = true;
    el.alamat.setAttribute('aria-expanded', 'false');
    indeksSorotan = -1;
}

function isiOpsiAutocomplete(hasil) {
    el.autocomplete.innerHTML = '';

    if (!hasil.length) {
        tutupAutocomplete();
        return;
    }

    hasil.forEach(function (item, i) {
        var li = document.createElement('li');
        li.id = 'opsi-lokasi-' + i;
        li.className = 'autocomplete-item';
        li.setAttribute('role', 'option');
        li.textContent = item;

        li.addEventListener('mousedown', function (e) {
            e.preventDefault();
            pilihLokasi(item);
        });

        el.autocomplete.appendChild(li);
    });

    el.autocomplete.hidden = false;
    el.alamat.setAttribute('aria-expanded', 'true');
}

function tandaiOpsiAktif(i) {
    var opsi = el.autocomplete.querySelectorAll('.autocomplete-item');

    opsi.forEach(function (o) { o.classList.remove('aktif'); });
    indeksSorotan = i;

    if (i < 0 || i >= opsi.length) {
        el.alamat.removeAttribute('aria-activedescendant');
        return;
    }

    opsi[i].classList.add('aktif');
    el.alamat.setAttribute('aria-activedescendant', opsi[i].id);
    opsi[i].scrollIntoView({ block: 'nearest' });
}

function pilihLokasi(item) {
    el.alamat.value = item;
    tutupAutocomplete();

    tampilkanPeta();

    setTimeout(function () {
        cariKoordinat(item);
    }, mapLoaded ? 60 : 260);
}

function cariKoordinat(item) {
    var url = 'https://nominatim.openstreetmap.org/search?format=json&limit=1&zoom=17&q='
        + encodeURIComponent(item + ', Purwakarta, Indonesia');

    fetch(url)
        .then(function (res) {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.json();
        })
        .then(function (data) {
            if (data && data.length > 0) {
                setMarker(parseFloat(data[0].lat), parseFloat(data[0].lon));
            } else {
                tampilkanPesan('"' + item + '" tidak ditemukan di peta. Ketuk lokasi yang lebih tepat secara manual.');
            }
        })
        .catch(function () {
            tampilkanPesan('Pencarian lokasi gagal karena koneksi. Ketuk lokasi yang tepat langsung di peta, atau isi koordinat manual.');
            bukaKoordinatManual('Pencarian alamat gagal');
        });
}

el.alamat.addEventListener('input', function () {
    var value = this.value.trim().toLowerCase();

    if (value.length < 2) {
        tutupAutocomplete();
        return;
    }

    isiOpsiAutocomplete(
        lokasiPurwakarta.filter(function (item) {
            return item.toLowerCase().indexOf(value) !== -1;
        })
    );
});

/* Duarte sebelumnya hanya bisa diklik mouse: tidak ada panah/Enter,
   sehingga pengguna keyboard tidak bisa memilih lokasi sama sekali. */
el.alamat.addEventListener('keydown', function (e) {
    var opsi = el.autocomplete.querySelectorAll('.autocomplete-item');

    if (el.autocomplete.hidden || !opsi.length) {
        return;
    }

    if (e.key === 'ArrowDown') {
        e.preventDefault();
        tandaiOpsiAktif(Math.min(indeksSorotan + 1, opsi.length - 1));
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        tandaiOpsiAktif(Math.max(indeksSorotan - 1, 0));
    } else if (e.key === 'Enter') {
        if (indeksSorotan >= 0 && opsi[indeksSorotan]) {
            e.preventDefault();
            pilihLokasi(opsi[indeksSorotan].textContent);
        }
    } else if (e.key === 'Escape') {
        tutupAutocomplete();
    }
});

document.addEventListener('click', function (e) {
    var wrapper = document.querySelector('.autocomplete-wrapper');

    if (wrapper && !wrapper.contains(e.target)) {
        tutupAutocomplete();
    }
});

/* =========================
   PRATINJAU FOTO
========================= */

if (el.foto) {
    el.foto.addEventListener('change', function () {
        var file = this.files[0];
        var img = document.getElementById('foto-preview-img');
        var nama = document.getElementById('foto-preview-nama');

        if (!file || !img || !nama) return;

        if (img.dataset.objectUrl) {
            URL.revokeObjectURL(img.dataset.objectUrl);
        }

        img.dataset.objectUrl = URL.createObjectURL(file);
        img.src = img.dataset.objectUrl;
        nama.textContent = file.name + ' — ' + (file.size / 1048576).toFixed(1) + ' MB';
    });
}

/* =========================
   VALIDASI SEBELUM KIRIM
========================= */

if (el.form) {
    el.form.addEventListener('submit', function (e) {
        var galat = [];

        if (!el.foto.files.length) {
            galat.push('Foto kerusakan belum dipilih.');
        } else if (el.foto.files[0].size > 5242880) {
            galat.push('Ukuran foto melebihi 5 MB.');
        }

        if (!el.alamat.value.trim()) {
            galat.push('Alamat lokasi belum diisi.');
        }

        if (!el.inputLat.value || !el.inputLng.value) {
            galat.push('Lokasi belum ditandai. Tekan "Tandai di Peta" lalu ketuk lokasi kerusakan, atau "Pakai Lokasi Saya".');
        }

        var keterangan = document.getElementById('keterangan');

        if (keterangan.value.trim().length < 10) {
            galat.push('Keterangan minimal 10 karakter.');
        }

        if (galat.length) {
            e.preventDefault();

            var ringkasan = document.getElementById('ringkasan-error');

            if (!ringkasan) {
                ringkasan = document.createElement('div');
                ringkasan.id = 'ringkasan-error';
                ringkasan.className = 'error-box';
                ringkasan.setAttribute('role', 'alert');
                ringkasan.tabIndex = -1;
                el.form.parentNode.insertBefore(ringkasan, el.form);
            }

            ringkasan.innerHTML = '';

            var judul = document.createElement('p');
            judul.className = 'error-box-judul';
            judul.textContent = 'Laporan belum terkirim:';
            ringkasan.appendChild(judul);

            var list = document.createElement('ul');

            galat.forEach(function (pesan) {
                var li = document.createElement('li');
                li.textContent = pesan;
                list.appendChild(li);
            });

            ringkasan.appendChild(list);
            ringkasan.focus();
            ringkasan.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
}

/* =========================
   FOKUS KE ERROR SETELAH RELOAD
========================= */

if (el.ringkasanError) {
    el.ringkasanError.focus();
}

/* =========================
   KOORDINAT MANUAL
=========================
   Dua arah sinkronisasi, karena form ini punya dua sumber input
   untuk nilai yang sama:

   1. Peta / GPS  -> setMarker() menulis ke input manual.
   2. Ketikan     -> di sini: perbarui label ringkasan, dan kalau
                     peta sudah siap, geser penandanya juga.

   Tanpa (2), mengoreksi koordinat secara manual tidak mengubah apa
   yang terlihat di peta: dua tampilan lokasi yang berbeda untuk satu
   laporan yang sama.
   ========================= */

function terapkanKoordinatManual() {
    if (!el.inputLat || !el.inputLng) return;

    var lat = parseFloat(el.inputLat.value);
    var lng = parseFloat(el.inputLng.value);

    var latValid = !isNaN(lat) && lat >= -90 && lat <= 90;
    var lngValid = !isNaN(lng) && lng >= -180 && lng <= 180;

    if (el.lat) el.lat.textContent = latValid ? lat.toFixed(6) : '-';
    if (el.lng) el.lng.textContent = lngValid ? lng.toFixed(6) : '-';

    if (latValid && lngValid) {
        sembunyikanPesan();

        // Peta mungkin sudah hidup: sinkronkan penandanya.
        if (map && typeof L !== 'undefined') {
            setMarker(lat, lng, false);
        }
    }
}

if (el.inputLat && el.inputLng) {
    ['input', 'change'].forEach(function (evt) {
        el.inputLat.addEventListener(evt, terapkanKoordinatManual);
        el.inputLng.addEventListener(evt, terapkanKoordinatManual);
    });

    // Nilai dari `old()` (form gagal validasi) langsung ditampilkan.
    terapkanKoordinatManual();

    // Error validasi server untuk koordinat harus terlihat, bukan
    // tersembunyi di dalam <details> yang tertutup.
    if (el.koordinatManual) {
        var adaError = el.inputLat.getAttribute('aria-invalid') === 'true'
            || el.inputLng.getAttribute('aria-invalid') === 'true';

        if (adaError) {
            bukaKoordinatManual('Validasi server menolak koordinat');
        }
    }
}
