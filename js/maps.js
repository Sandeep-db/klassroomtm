/**
 * 
 * IITH
 * 
 * lat: 17.5841589
 * lng: 78.1178767
 * 
 * JNTUK
 * 16.9783087,82.2378138
 * 
 */


var userl = {}

function renderMap() {

    let loc = userl
    let hloc = {
        lat: 16.9783087, lng: 82.2378138
    }
    console.log(loc)
    var map = L.map('map').setView([hloc.lat, hloc.lng], 19)
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        minZoom: 17,
        maxZoom: 20,
    }).addTo(map);

    let CarIcon = L.Icon.extend({
        options: {
            // iconUrl: './img/car-pointer.jpg',
            iconSize: [60, 60],
            shadowSize: [50, 64],
            iconAnchor: [30, 60],
            shadowAnchor: [4, 62],
            popupAnchor: [-3, -76]
        }
    })

    var marker = L.marker([hloc.lat, hloc.lng])
        .addTo(map).bindPopup("You are here");

}

$(document).ready(function () {
    navigator.geolocation
        .getCurrentPosition(function (data) {
            userl = {
                lat: data.coords.latitude,
                lng: data.coords.longitude
            }
            console.log('user-loc', userl)
            renderMap()
        });
})

