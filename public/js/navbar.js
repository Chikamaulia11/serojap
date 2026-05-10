document.querySelectorAll('.nav-item').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();

        const target = document.querySelector(this.getAttribute('href'));

        if(target){
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }

        document.querySelectorAll('.nav-item')
            .forEach(el => el.classList.remove('active'));

        this.classList.add('active');
    });
});

function toggleMenu() {
    document.getElementById("mobileMenu").classList.toggle("show");
}