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
    <style>
        .custom-btn {
            display: none;
        }
    </style>
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
        <p id="pickup-address">Add a Pickup Point</p>
        <p>Add a Drop-off Point</p>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-md-6 mb-2">
                <button type="submit" class="btn btn-default custom-btn" id="confirm-btn">
                    Confirm
                </button>
            </div>
            <div class="col-md-6 mb-2">
                <button type="submit" class="btn btn-default custom-btn" id="undo-btn">
                    Undo
                </button>
            </div>
        </div>
    </div>

    <script>
        var map = L.map('map', {
            zoomControl: false,
            doubleClickZoom: false // Disable double-click zoom
        }).setView([0, 0], 14);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        var blueMarkerIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34]
        });

        var greenMarkerIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34]
        });

        var blueMarker = L.marker([0, 0], {
            icon: blueMarkerIcon
        }).addTo(map).bindPopup('You are here').openPopup();

        var pickupMarker = null; // Initialize pickup marker variable

        function updateLocation(position) {
            var latitude = position.coords.latitude;
            var longitude = position.coords.longitude;

            blueMarker.setLatLng([latitude, longitude]).update();
            map.setView([latitude, longitude], 14);
        }

        function handleError(error) {
            // console.error('Error getting user location: ' + error.message);
        }

        // Get the user's position once and center the map to it
        navigator.geolocation.getCurrentPosition(updateLocation, handleError);

        const searchInput = document.getElementById("search-input");
        const searchButton = document.getElementById("search-button");
        const pickupAddress = document.getElementById("pickup-address"); // Add the ID to your <h4> element

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

        // Function to handle double-click events
        function handleDoubleClick(event) {
            var latlng = event.latlng; // Get the clicked coordinates

            // Remove existing pickup marker if it exists
            if (pickupMarker) {
                map.removeLayer(pickupMarker);
            }

            pickupMarker = L.marker(latlng, {
                icon: greenMarkerIcon
            }).addTo(map).bindPopup('Pickup point').openPopup();

            // Get the address using reverse geocoding
            axios.get("https://nominatim.openstreetmap.org/reverse?lat=" + latlng.lat + "&lon=" + latlng.lng + "&format=json&limit=1")
                .then(function (response) {
                    var address = response.data.display_name;
                    pickupAddress.textContent = "Pickup Point: " + address; // Update the <h4> content
                })
                .catch(function (error) {
                    console.log(error);
                });
        }

        // Add double-click event listener to the map
        map.on('dblclick', handleDoubleClick);
    </script>




</body>

</html>