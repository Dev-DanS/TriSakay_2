<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TriSakay | Commuter</title>
    <?php
    include '../dependencies/dependencies.php';
    ?>
    <link rel="stylesheet" href="../css/booking.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>

<body>

    <?php
    include('../php/navbar_commuter.php');
    ?>
    <div class="search">
        <input id="search-input" type="text" placeholder="Where are you heading to?">
        <button id="search-button"><i class="fa-solid fa-magnifying-glass-location fa-lg" style="color: #ffffff;"></i>
            Search</button>
    </div>

    <div id="map" style="width: 100%; height: 50vh;"></div>
    <div class="locations">
        <h5>Add a Pickup Point</h5>
        <h5>Add a Drop-off Point</h5>
    </div>

    

    <script>
        var map = L.map('map', {
            zoomControl: false
        }).setView([0, 0], 14);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        var marker = L.marker([0, 0]).addTo(map).bindPopup('You are here').openPopup();

        function updateLocation(position) {
            var latitude = position.coords.latitude;
            var longitude = position.coords.longitude;

            marker.setLatLng([latitude, longitude]).update();
            map.setView([latitude, longitude], 14);
        }

        function handleError(error) {
            // console.error('Error getting user location: ' + error.message);
        }

        // Get the user's position once and center the map to it
        navigator.geolocation.getCurrentPosition(updateLocation, handleError);

        const searchInput = document.getElementById("search-input");
        const searchButton = document.getElementById("search-button");

        searchInput.addEventListener("keyup", function (event) {
            if (event.keyCode === 13) {
                searchButton.click();
            }
        });

        searchButton.addEventListener("click", function () {
            var searchValue = searchInput.value;
            axios.get("https://nominatim.openstreetmap.org/search?q=" + searchValue + "&format=json&limit=1")
                .then(function (response) {
                    var result = response.data[0];
                    map.setView([result.lat, result.lon], 16);
                })
                .catch(function (error) {
                    console.log(error);
                });
        });
    </script>
</body>

</html>
