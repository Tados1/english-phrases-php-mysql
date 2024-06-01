window.onload = function() {
    let params = new URLSearchParams(window.location.search);
    if (params.has('scroll')) {
        let elementId = params.get('scroll');
        let element = document.getElementById(elementId);
        if (element) {
            element.scrollIntoView({ behavior: 'smooth' });
        }
    }
}