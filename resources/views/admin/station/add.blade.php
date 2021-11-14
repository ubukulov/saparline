@extends('admin.layouts.app')

@section('content')
    <form action="{{route('admin.station.create')}}" enctype="multipart/form-data" method="post">
        @csrf
        <div class="form-group">
            <label>называние</label>
            <input type="text" required  class="form-control" name="name"  >
        </div>

        <div id="map" ></div>
        <input type="hidden" name="lat" id="lat">
        <input type="hidden" name="lng" id="lng">

        <input type="hidden" name="city_id" value="{{$city_id}}">




        <div class="form-group">
            <button class="btn btn-primary" type="submit">Сохранить</button>
        </div>
    </form>

    <style>
        #map{
            width: 100%;
            height: 350px;
        }
    </style>

@endsection

@push('js')
    <script>

        // In the following example, markers appear when the user clicks on the map.
        // The markers are stored in an array.
        // The user can then click an option to hide, show or delete the markers.
        var map;
        var markers = [];

        function initMap() {
            var haightAshbury = {lat: 43.238949, lng: 76.889709};

            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 12,
                center: haightAshbury,
                mapTypeId: 'terrain'
            });

            // This event listener will call addMarker() when the map is clicked.
            map.addListener('click', function(event) {
                clearMarkers();
                addMarker(event.latLng);
                document.getElementById('lat').value = event.latLng.lat();
                document.getElementById('lng').value = event.latLng.lng();
            });

            // Adds a marker at the center of the map.
            addMarker(haightAshbury);
        }

        // Adds a marker to the map and push to the array.
        function addMarker(location) {
            var marker = new google.maps.Marker({
                position: location,
                map: map
            });
            markers.push(marker);
        }

        // Sets the map on all markers in the array.
        function setMapOnAll(map) {
            for (var i = 0; i < markers.length; i++) {
                markers[i].setMap(map);
            }
        }

        // Removes the markers from the map, but keeps them in the array.
        function clearMarkers() {
            setMapOnAll(null);
        }

        // Shows any markers currently in the array.
        function showMarkers() {
            setMapOnAll(map);
        }

        // Deletes all markers in the array by removing references to them.
        function deleteMarkers() {
            clearMarkers();
            markers = [];
        }

    </script>
    <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBDYUi9nox54qtCdlkGpU1C_VzULNkjmfg&callback=initMap">
    </script>

@endpush

