var popup = document.getElementById("popup");
var popupTitle = document.getElementById("popup-title");
var popupText = document.getElementById("popup-text");
var popupImage = document.getElementById("popup-image");
var popupClose = document.getElementById("popup-close");

var hotspots = document.querySelectorAll(".hotspot");

hotspots.forEach(function (hotspot) {
  hotspot.onclick = function () {
    popup.style.display = "flex";

    popupTitle.innerText = hotspot.getAttribute("data-title");
    popupText.innerText = hotspot.getAttribute("data-text");
    popupImage.src = hotspot.getAttribute("data-image");
  };
});

popupClose.onclick = function () {
  popup.style.display = "none";
};

popup.addEventListener("click", function (e) {
  if (e.target === popup) {
    popup.style.display = "none";
  }
});
