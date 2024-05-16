document.addEventListener("DOMContentLoaded", function() {
    var navbarProfil = document.getElementById("navbar-profil");
    var itemMenu = document.getElementById("item-menu");
    var itemSideMenu = document.getElementById('article-side-list');
    var secondSideMenu = document.querySelector('.second-side-menu');
    var arrowIcon = itemSideMenu.querySelector('.left');
    var btnHamburger = document.getElementById('btn-hamburger');
    var sideMenu = document.getElementById('side-menu');
    var sideContent = document.querySelector('.side-content');

    navbarProfil.addEventListener("click", function() {
        itemMenu.classList.toggle("show");
    });

    itemSideMenu.addEventListener('click', function() {
        secondSideMenu.classList.toggle('show');
        itemSideMenu.classList.toggle('show');
        arrowIcon.classList.toggle('show');
    });

    btnHamburger.addEventListener('click', function() {
        sideMenu.classList.toggle('hidden');
        sideContent.classList.toggle('visible');
    });
});
