// ================= REVEAL LOOP =================

const reveals = document.querySelectorAll(".reveal");

function handleReveal() {

    const windowHeight = window.innerHeight;

    reveals.forEach((el) => {

        const top = el.getBoundingClientRect().top;

        if (top < windowHeight - 120 && top > -250) {

            el.classList.add("active");
            el.classList.remove("out");

        } else {

            el.classList.remove("active");
            el.classList.add("out");

        }

    });
}

window.addEventListener("scroll", handleReveal);
window.addEventListener("load", handleReveal);


// ================= ACTIVE NAV =================

/*
 * Versi lama.feature dari `href` yang berakhiran `#id-section`, jadi
 * ia menambah DAN MENGHAPUS class `active` pada setiap event scroll.
 *
 * Sekarang link navbar menunjuk halaman sungguhan (`/report`,
 * `/my-report`, ...) dan status aktifnya ditentukan server lewat
 * `request()->routeIs()` di Blade. Kalau script ini tetap jalan, setiap
 * kali pengguna menggulir, class `active` di navbar ikut terhapus --
 * indikator halaman yang sedang aktif ikut hilang.
 *
 * Jadi: kalau tidak ada satu pun link navbar berbasis anchor, script
 * ini tidak melakukan apa-apa sama sekali.
 */
const anchorLinks = Array.from(
    document.querySelectorAll(".nav-item[href*='#']")
);

if (anchorLinks.length > 0) {

    const sections = document.querySelectorAll("section[id]");

    function handleActiveNav() {

        let current = "";

        sections.forEach(section => {

            const sectionTop = section.offsetTop - 220;
            const sectionHeight = section.offsetHeight;

            if (
                window.scrollY >= sectionTop &&
                window.scrollY < sectionTop + sectionHeight
            ) {
                current = section.getAttribute("id");
            }

        });

        anchorLinks.forEach(link => {

            link.classList.remove("active");

            const href = link.getAttribute("href");

            if (
                href === `#${current}` ||
                href === `/dashboard#${current}` ||
                href.endsWith(`#${current}`)
            ) {
                link.classList.add("active");
            }

        });

    }

    window.addEventListener("scroll", handleActiveNav, { passive: true });
    window.addEventListener("load", handleActiveNav);
}


// ================= FAQ BUBBLE =================

function toggleFaqBubble(card) {

    const isOpen = card.classList.contains("open");

    document.querySelectorAll(".faq-bubble-card.open").forEach((item) => {
        item.classList.remove("open");
        item.setAttribute("aria-expanded", "false");
    });

    if (!isOpen) {
        card.classList.add("open");
        card.setAttribute("aria-expanded", "true");
    }

}

document.addEventListener("DOMContentLoaded", function () {

    const searchInput = document.getElementById("faqSearch");

    document.querySelectorAll(".faq-bubble-card").forEach((card) => {

        card.addEventListener("keydown", function (event) {

            if (event.key === "Enter" || event.key === " ") {
                event.preventDefault();
                toggleFaqBubble(card);
            }

        });

    });

    if (!searchInput) {
        return;
    }

    searchInput.addEventListener("keyup", function () {

        const keyword = this.value.toLowerCase().trim();
        const items = document.querySelectorAll(".faq-bubble-item");

        items.forEach((item) => {

            const text = item.innerText.toLowerCase();

            if (keyword === "") {

                /* Faq ke-7 dan ke-8 disembunyikan sampai pengguna
                   menekan "Lihat Semua Pertanyaan" (sekarang tautan ke
                   /pusat-bantuan). Kalau sedang mengetik pencarian, SEMUA
                   item yang cocok harus muncul -- termasuk yang tersembunyi
                   itu -- kalau tidak, hasil pencarian jadi tidak lengkap. */
                item.style.display = text.includes(keyword) ? "" : "none";
            }

        });

    });

});