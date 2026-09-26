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

const sections = document.querySelectorAll("section[id]");
const navLinks = document.querySelectorAll(".nav-item");

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

    navLinks.forEach(link => {

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

window.addEventListener("scroll", handleActiveNav);
window.addEventListener("load", handleActiveNav);


// ================= FAQ BUBBLE =================

function toggleFaqBubble(card) {

    const isOpen = card.classList.contains("open");

    document.querySelectorAll(".faq-bubble-card.open").forEach((item) => {
        item.classList.remove("open");
    });

    if (!isOpen) {
        card.classList.add("open");
    }

}

function showMoreFaq() {

    document.querySelectorAll(".extra-faq").forEach((item) => {
        item.style.display = "";
    });

    const button = document.getElementById("btnMoreFaq");

    if (button) {
        button.style.display = "none";
    }

}

document.addEventListener("DOMContentLoaded", function () {

    const searchInput = document.getElementById("faqSearch");
    const moreButton = document.getElementById("btnMoreFaq");

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

                if (item.classList.contains("extra-faq")) {
                    item.style.display = "none";
                } else {
                    item.style.display = "";
                }

                if (moreButton) {
                    moreButton.style.display = "";
                }

            } else {

                item.style.display = text.includes(keyword) ? "" : "none";

                if (moreButton) {
                    moreButton.style.display = "none";
                }

            }

        });

    });

});