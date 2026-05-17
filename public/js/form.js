var map;
var mapLoaded = false;

window.toggleMap = function() {
    const container = document.getElementById("mapContainer");

    if (container.style.display === "none" || container.style.display === "") {
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

function initMap() {
    map = L.map('map').setView([-6.556, 107.443], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    var marker;

    map.on('click', function(e) {
        if (marker) map.removeLayer(marker);

        marker = L.marker(e.latlng).addTo(map);

        document.getElementById('lat').value = e.latlng.lat;
        document.getElementById('lng').value = e.latlng.lng;

        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${e.latlng.lat}&lon=${e.latlng.lng}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('alamat').value = data.display_name;
            });
    });
}

/* REDIRECT */
function goToRiwayat() {
    window.location.href = "/my-report";
}

/* RESET FORM */
function resetForm() {
    window.location.href = "/report";
}