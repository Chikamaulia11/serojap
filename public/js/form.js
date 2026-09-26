var map;
var mapLoaded = false;
var marker = null;

/* =========================
   TOGGLE MAP
========================= */
window.toggleMap = function () {

    const container = document.getElementById("mapContainer");

    if (
        container.style.display === "none" ||
        container.style.display === ""
    ) {

        container.style.display = "block";

        setTimeout(() => {

            if (!mapLoaded) {

                initMap();
                mapLoaded = true;

            } else {

                map.invalidateSize();

            }

        }, 200);

    } else {

        container.style.display = "none";

    }
};

/* =========================
   INIT MAP
========================= */
function initMap() {

    map = L.map('map').setView([-6.556, 107.443], 13);

    L.tileLayer(
        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'
    ).addTo(map);

    map.on('click', function (e) {

        setMarker(e.latlng.lat, e.latlng.lng);

        fetch(
            `https://nominatim.openstreetmap.org/reverse?format=json&lat=${e.latlng.lat}&lon=${e.latlng.lng}`
        )
        .then(res => res.json())
        .then(data => {

            document.getElementById('alamat').value =
                data.display_name;

        });

    });
}

/* =========================
   SET MARKER
========================= */
function setMarker(lat, lng) {

    if (marker) {
        map.removeLayer(marker);
    }

    marker = L.marker([lat, lng]).addTo(map);

    map.setView([lat, lng], 16);

    document.getElementById('lat').value = lat;
    document.getElementById('lng').value = lng;
}

/* =========================
   AUTOCOMPLETE LOKASI
========================= */

const lokasiPurwakarta = [

    "Purwakarta",
    "Plered",
    "Wanayasa",
    "Jatiluhur",
    "Sadang",
    "Pasawahan",
    "Cikopo",
    "Ciganea",
    "Campaka",
    "Bungursari",
    "Cibatu",
    "Darangdan",
    "Kiarapedes",
    "Maniis",
    "Pondoksalam",
    "Sukatani",
    "Tegalwaru",

    "Situ Buleud",
    "Taman Air Mancur Sri Baduga",
    "Stasiun Purwakarta",
    "RSUD Bayu Asih",
    "Alun-Alun Purwakarta",
    "Universitas Pendidikan Indonesia Purwakarta",
    "Gerbang Tol Jatiluhur",
    "Gerbang Tol Sadang",
    "Hotel Harper Purwakarta",
    "Perumahan Bukit Indah",
    "Kawasan Industri Kota Bukit Indah"

];

const alamatInput = document.getElementById('alamat');

const autocompleteList =
    document.getElementById('autocomplete-list');

alamatInput.addEventListener('input', function () {

    const value = this.value.toLowerCase();

    autocompleteList.innerHTML = '';

    if (value.length === 0) {

        autocompleteList.style.display = 'none';
        return;

    }

    const filtered = lokasiPurwakarta.filter(item =>
        item.toLowerCase().includes(value)
    );

    if (filtered.length === 0) {

        autocompleteList.style.display = 'none';
        return;

    }

    autocompleteList.style.display = 'block';

    filtered.forEach(item => {

        const div = document.createElement('div');

        div.classList.add('autocomplete-item');

        div.innerText = item;

        div.addEventListener('click', async function () {

            alamatInput.value = item;

            autocompleteList.style.display = 'none';

            /* buka map otomatis */
            const container =
                document.getElementById("mapContainer");

            container.style.display = "block";

            setTimeout(() => {

                if (!mapLoaded) {

                    initMap();
                    mapLoaded = true;

                } else {

                    map.invalidateSize();

                }

            }, 200);

            /* ambil koordinat lokasi */
            try {

                const response = await fetch(
                    `https://nominatim.openstreetmap.org/search?format=json&q=${item}, Purwakarta, Indonesia`
                );

                const data = await response.json();

                if (data.length > 0) {

                    const lat = parseFloat(data[0].lat);
                    const lng = parseFloat(data[0].lon);

                    setMarker(lat, lng);

                }

            } catch (error) {

                console.log(error);

            }

        });

        autocompleteList.appendChild(div);

    });

});

/* =========================
   CLOSE AUTOCOMPLETE
========================= */

document.addEventListener('click', function (e) {

    if (
        !document.querySelector('.autocomplete-wrapper')
        .contains(e.target)
    ) {

        autocompleteList.style.display = 'none';

    }

});

/* =========================
   REDIRECT
========================= */

function goToRiwayat() {

    window.location.href = "/my-report";

}

function resetForm() {

    window.location.href = "/report";

}