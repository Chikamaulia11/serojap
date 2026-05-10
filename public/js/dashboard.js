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

window.addEventListener("scroll", () => {

    let current = "";

    sections.forEach(section => {

        const sectionTop = section.offsetTop - 220;
        const sectionHeight = section.offsetHeight;

        if (window.scrollY >= sectionTop &&
            window.scrollY < sectionTop + sectionHeight) {

            current = section.getAttribute("id");
        }
    });

    navLinks.forEach(link => {

        link.classList.remove("active");

        if(link.getAttribute("href") === `#${current}`){
            link.classList.add("active");
        }

    });

});