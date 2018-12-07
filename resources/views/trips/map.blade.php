@extends('layouts.app')

@section('content')

    <div id="map" style="height: 100%;"></div>

    <script src="https://www.gstatic.com/firebasejs/5.7.0/firebase.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ $apikey }}&callback=initMap" defer async></script>

    <script>
        var map;
        var markers = [];
        var infowindows = [];
        var coords = [];
        var config = {
            apiKey: '{{ $firebase['apiKey'] }}',
            authDomain: '{{ $firebase['authDomain'] }}',
            databaseURL: '{{ $firebase['databaseURL'] }}',
            projectId: '{{ $firebase['projectId'] }}',
            storageBucket: '{{ $firebase['storageBucket'] }}',
            messagingSenderId: '{{ $firebase['messagingSenderId'] }}'
        };
        firebase.initializeApp(config);


        function initMap() {



            var defaultDatabase = firebase.database();

            var tripId = '{{ isset($tripId) ? $tripId : '' }}';

            var ref = '/trips';
            if(tripId == '')
            {
                alert('no trip id');
                return;
            }
            ref += '/' + tripId;


            defaultDatabase.ref(ref).once('value').then(function(snapshot) {



                map = new google.maps.Map(document.getElementById('map'), {
                    center: {lat: -34.397, lng: 150.644},
                    zoom: 8
                });


                var trip = snapshot.val();

                if(typeof trip.positions !== 'undefined'){
                    for(var i = 0; i < trip.positions.length; i++){
                        var c = {
                            lat: trip.positions[i].latitude,
                            lng: trip.positions[i].longitude
                        };
                        coords.push(c);
                        var date = new Date(trip.positions[i].timestamp);
                        markers[i] = new google.maps.Marker({
                            position: c,
                            map: map,
                            title: date.toISOString()
                        });
                    }
                }

                var path = new google.maps.Polyline({
                    path: coords,
                    geodesic: true,
                    strokeColor: '#FF0000',
                    strokeOpacity: 1.0,
                    strokeWeight: 2
                });

                path.setMap(map);
                zoomToObject(path);
            });



        }

        function zoomToObject(obj){
            var bounds = new google.maps.LatLngBounds();
            var points = obj.getPath().getArray();
            for (var n = 0; n < points.length ; n++){
                bounds.extend(points[n]);
            }
            map.fitBounds(bounds);
        }

    </script>

@endsection