document.addEventListener("DOMContentLoaded", function() {
    var signInBtn = document.getElementById("signInBtn");
    var loginOverlay = document.getElementById("login");

    if (signInBtn && loginOverlay) {
        signInBtn.addEventListener("click", function(event) {
            event.preventDefault();
            loginOverlay.classList.add("show");
        });

        // オーバーレイの外をクリックしたら非表示にする
        loginOverlay.addEventListener("click", function(event) {
            if (event.target === loginOverlay) {
                loginOverlay.classList.remove("show");
            }
        });
    } 
});