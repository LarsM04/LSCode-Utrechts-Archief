// Selecteer alle hotspots
const hotspots = document.querySelectorAll('.hotspot');

// Voeg klik-event toe aan elke hotspot
hotspots.forEach(hotspot => {
    hotspot.addEventListener('click', () => {
        const uitleg = hotspot.querySelector('.hotspot-uitleg');
        if (uitleg) {
            uitleg.classList.toggle('show'); // toggle class 'show'
        }
    });
});
