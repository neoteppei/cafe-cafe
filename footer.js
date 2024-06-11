document.addEventListener("DOMContentLoaded", function() {
    var jumpButton = document.querySelector(".jump");

    window.addEventListener("scroll", function() {
        if (window.scrollY > 300) { // Show button after scrolling 300px
            jumpButton.style.display = "block";
        } else {
            jumpButton.style.display = "none";
        }
    });

    document.getElementById("scrollToTop").addEventListener("click", function(event) {
        event.preventDefault();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
});