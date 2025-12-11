// ============================
// POPUP
// ============================
(function () {
  const popup = document.getElementById("popup");
  const titleEl = document.getElementById("popup-title");
  const textEl = document.getElementById("popup-text");
  const imageEl = document.getElementById("popup-image");
  const closeBtn = document.getElementById("popup-kruis");

  if (!popup || !titleEl || !textEl || !imageEl || !closeBtn) {
    return;
  }

  document.addEventListener("click", function (e) {
    if (!e.target.classList.contains("hotspot")) return;

    titleEl.textContent = e.target.dataset.title || "";
    textEl.textContent = e.target.dataset.text || "";
    imageEl.src = e.target.dataset.image || "";
    popup.style.display = "flex";
  });

  closeBtn.onclick = () => (popup.style.display = "none");

  // Klik op donkere achtergrond sluit ook popup
  popup.onclick = e => {
    if (e.target === popup) popup.style.display = "none";
  };
})();

// ============================
// MINIMAP + THUMBNAILS
// ============================

const track = document.getElementById("minimap-track");
const leftBtn = document.getElementById("arrow-left");
const rightBtn = document.getElementById("arrow-right");
const thumbs = document.querySelectorAll(".minimap-thumb");
const panorama = document.getElementById("panorama");
const pages = document.querySelectorAll(".panorama-page");

let activeIndex = 0;

if (track && leftBtn && rightBtn && thumbs.length > 0 && panorama && pages.length > 0) {
  // Pijlen voor horizontaal scrollen van de minimap
  rightBtn.onclick = () => {
    track.scrollBy({ left: 200, behavior: "smooth" });
  };

  leftBtn.onclick = () => {
    track.scrollBy({ left: -200, behavior: "smooth" });
  };

  thumbs.forEach((thumb, index) => {
    thumb.addEventListener("click", () => {
      activeIndex = index;
      updateThumbs();
      goToPage();
      centerThumb();
    });
  });

  function updateThumbs() {
    thumbs.forEach(t => t.classList.remove("active"));
    if (thumbs[activeIndex]) {
      thumbs[activeIndex].classList.add("active");
    }
  }

  // Spring naar de juiste panorama-pagina
  function goToPage() {
    const page = pages[activeIndex];
    if (!page) return;

    panorama.scrollTo({
      left: page.offsetLeft,
      behavior: "smooth"
    });
  }

  // Centreer de actieve thumbnail in de minimap (ongeveer)
  function centerThumb() {
    const thumb = thumbs[activeIndex];
    if (!thumb) return;

    track.scrollTo({
      left: thumb.offsetLeft - 100, // simpele centrering
      behavior: "smooth"
    });
  }

  // Start: eerste thumbnail actief
  updateThumbs();
  centerThumb();
}

// ============================
// ZOOM IN / UIT / RESET
// ============================

let zoom = 1;

const pano = document.getElementById("panorama");
const btnIn = document.getElementById("zoom-in");
const btnOut = document.getElementById("zoom-out");
const btnReset = document.getElementById("zoom-reset");

if (pano && btnIn && btnOut && btnReset) {
  // zoom in
  btnIn.onclick = function () {
    zoom = zoom + 0.2;
    applyZoom();
  };

  // zoom uit
  btnOut.onclick = function () {
    if (zoom > 0.4) { // minimale waarde
      zoom = zoom - 0.2;
    }
    applyZoom();
  };

  // reset
  btnReset.onclick = function () {
    zoom = 1;
    applyZoom();
  };

  function applyZoom() {
    pano.style.transform = "scale(" + zoom + ")";
    pano.style.transformOrigin = "center center";
  }
}
