// ============================
// POPUP
// ============================
var popup = document.getElementById("popup");
var titel = document.getElementById("popup-title");
var tekst = document.getElementById("popup-text");
var afbeelding = document.getElementById("popup-image");
var sluiten = document.getElementById("popup-kruis");

document.body.onclick = function (event) {
  // Klik op hotspot → popup openen
  if (event.target.classList.contains("hotspot")) {
    popup.style.display = "flex";
    titel.innerText = event.target.dataset.title;
    tekst.innerText = event.target.dataset.text;
    afbeelding.src = event.target.dataset.image;
  }
};

// Alleen sluiten met het kruisje
sluiten.onclick = function () {
  popup.style.display = "none";
};

// ============================
// MINIMAP
// ============================

var panorama = document.querySelector(".panorama");
var minimap = document.getElementById("panoramaMinimap");
var track = document.getElementById("minimapTrack");

minimap.onclick = function (e) {
  var klikX = e.clientX - track.getBoundingClientRect().left;
  var procent = klikX / track.scrollWidth;

  if (procent > 0.1) procent = 0.1; // voorkomt te ver naar rechts

  panorama.scrollLeft = procent * (panorama.scrollWidth - panorama.clientWidth);
};

// ============================
// VERGROOTGLAS (ALLEEN OP DE AFBEELDING)
// ============================

var zoom = 0.5;
var grootte = 150;

var vergrootglas = document.getElementById("vergrootglas");
var inhoud = document.getElementById("vergrootInhoud");

vergrootglas.style.width = grootte + "px";
vergrootglas.style.height = grootte + "px";
vergrootglas.style.display = "none";

var afbeeldingen = document.querySelectorAll(".panorama img");

afbeeldingen.forEach(function (img) {
  img.onmouseenter = function () {
    vergrootglas.style.display = "block";
    inhoud.src = img.src;
  };

  img.onmouseleave = function () {
    vergrootglas.style.display = "none";
  };

  img.onmousemove = function (muis) {
    vergrootglas.style.left = muis.clientX + "px";
    vergrootglas.style.top = muis.clientY + "px";

    inhoud.style.left = -muis.offsetX + "px";
    inhoud.style.top = -muis.offsetY + "px";
  };
});
