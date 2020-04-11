window.onscroll = function() { myFunction() };
var navbar = document.getElementById("nav-bar");
var offset = navbar.offsetTop;
function myFunction() {
    if(window.pageYOffset >= offset) {
        navbar.classList.add("stick-on-top")
    }
    else {
        navbar.classList.remove("stick-on-top");
    }
}