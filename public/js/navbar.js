function toggleMenu() {
    const menu = document.getElementById("mobileMenu");

    if (menu) {
        menu.classList.toggle("show");
    }
}

document.addEventListener("DOMContentLoaded", function () {
    const navItems = document.querySelectorAll(".nav-item");

    function setActiveByHash(hash) {
        navItems.forEach(item => {
            item.classList.remove("active");

            if (item.getAttribute("href")?.endsWith(hash)) {
                item.classList.add("active");
            }
        });
    }

    navItems.forEach(item => {
        item.addEventListener("click", function () {
            const url = new URL(this.href);
            setActiveByHash(url.hash);
        });
    });

    const sections = ["dashboard", "prosedur", "laporan", "riwayat", "faq"]
        .map(id => document.getElementById(id))
        .filter(Boolean);

    window.addEventListener("scroll", function () {
        let current = "";

        sections.forEach(section => {
            const sectionTop = section.offsetTop - 120;

            if (window.scrollY >= sectionTop) {
                current = "#" + section.id;
            }
        });

        if (current) {
            setActiveByHash(current);
        }
    });

    if (window.location.hash) {
        setTimeout(() => {
            const target = document.querySelector(window.location.hash);

            if (target) {
                target.scrollIntoView({
                    behavior: "smooth",
                    block: "start"
                });

                setActiveByHash(window.location.hash);
            }
        }, 100);
    } else {
        setActiveByHash("#dashboard");
    }
});