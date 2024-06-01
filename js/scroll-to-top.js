let button = document.querySelector(".scroll-to-top")
let windowHeight = window.innerHeight;

window.addEventListener('scroll', function() {
    let scrollPosition = window.scrollY;
    if(scrollPosition > windowHeight) {
        button.style.display = 'block'
    } else {
        button.style.display = 'none'
    }
});

button.addEventListener('click', function() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth' 
    });
});