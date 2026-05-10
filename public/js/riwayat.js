function openPopup(alamat, status, keterangan) {
    document.getElementById("popup").style.display = "flex";

    document.getElementById("pop-alamat").innerText = alamat;
    document.getElementById("pop-status").innerText = status;
    document.getElementById("pop-keterangan").innerText = keterangan;
}

function closePopup() {
    document.getElementById("popup").style.display = "none";
}