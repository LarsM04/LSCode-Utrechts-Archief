var popup = document.getElementById("popup");
var popupTitle = document.getElementById("popup-title");
var popupText = document.getElementById("popup-text");
var popupImage = document.getElementById("popup-image");
var popupClose = document.getElementById("popup-close");

// PAK ALLE HOTSPOTS
var hotspots = document.querySelectorAll(".hotspot");

// VOOR ELKE HOTSPOT
hotspots.forEach(function (hotspot) {

    hotspot.onclick = function () {

        popup.style.display = "flex";

        popupTitle.innerText = hotspot.getAttribute("data-title");
        popupText.innerText = hotspot.getAttribute("data-text");
        popupImage.src = hotspot.getAttribute("data-image");
    };

});

// SLUITKNOP
popupClose.onclick = function () {
    popup.style.display = "none";
};

// KLIK BUITEN POPUP = SLUITEN
popup.onclick = function () {
    popup.style.display = "none";
};
