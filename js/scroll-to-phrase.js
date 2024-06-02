window.onload = function() {
    let params = new URLSearchParams(window.location.search);
    if (params.has('scroll')) {
        let elementId = params.get('scroll');
        let element = document.getElementById(elementId);
        if (element) {
            let headerHeight = document.querySelector('header').offsetHeight;
            let elementPosition = element.getBoundingClientRect().top;
            let offsetPosition = elementPosition - headerHeight;
            window.scrollTo({
                top: offsetPosition,
                behavior: 'smooth'
            });
        }
    }
}