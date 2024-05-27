window.onload = function() {
    var params = new URLSearchParams(window.location.search);
    if (params.has('scroll')) {
        var elementId = params.get('scroll');
        var element = document.getElementById(elementId);
        if (element) {
            element.scrollIntoView({ behavior: 'smooth' });
        }
    }
}