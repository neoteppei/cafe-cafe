window.addEventListener('scroll', function() {
    const header = document.querySelector('header');
    if (window.scrollY > 0) {
        header.classList.remove('transparent');
        header.classList.add('scrolled');
    } else {
        header.classList.remove('scrolled');
        header.classList.add('transparent');
    }
});

document.addEventListener("DOMContentLoaded", function() {
    function smoothScrollToY(targetY, duration) {
        var startY = window.pageYOffset;
        var startTime = null;

        function animation(currentTime) {
            if (startTime === null) startTime = currentTime;
            var timeElapsed = currentTime - startTime;
            var run = ease(timeElapsed, startY, targetY, duration);
            window.scrollTo(0, run);
            if (timeElapsed < duration) requestAnimationFrame(animation);
        }

        function ease(t, b, c, d) {
            t /= d / 2;
            if (t < 1) return c / 2 * t * t + b;
            t--;
            return -c / 2 * (t * (t - 2) - 1) + b;
        }

        requestAnimationFrame(animation);
    }

    var scrollToStartLink = document.getElementById("scrollToStart");

    scrollToStartLink.addEventListener("click", function(event) {
        event.preventDefault();
        var targetY = window.innerHeight * 1.15; // 115vh
        smoothScrollToY(targetY, 500);
    });
});

document.addEventListener("DOMContentLoaded", function() {
    function smoothScrollToY(targetY, duration) {
        var startY = window.pageYOffset;
        var startTime = null;

        function animation(currentTime) {
            if (startTime === null) startTime = currentTime;
            var timeElapsed = currentTime - startTime;
            var run = ease(timeElapsed, startY, targetY, duration);
            window.scrollTo(0, run);
            if (timeElapsed < duration) requestAnimationFrame(animation);
        }

        function ease(t, b, c, d) {
            t /= d / 2;
            if (t < 1) return c / 2 * t * t + b;
            t--;
            return -c / 2 * (t * (t - 2) - 1) + b;
        }

        requestAnimationFrame(animation);
    }

    var scrollToStartLink = document.getElementById("scrollToStart2");

    scrollToStartLink.addEventListener("click", function(event) {
        event.preventDefault();
        var targetY = window.innerHeight * 2.45;
        smoothScrollToY(targetY, 500);
    });
});