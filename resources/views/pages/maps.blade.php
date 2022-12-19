@extends('layouts.main')
@section('head')
    @include('includes.head')
@endsection
@section('header')
    @include('includes.header')
@endsection
@section('content')
    <!-- BEGIN: Content-->
    <div class="app-content content ">
        <!-- BEGIN: Header-->

        <div class="content-wrapper ">
            <div class="content-body d-flex justify-content-center">
                <div id="map" style="height: 500px; width:1000px"></div>
            </div>
        </div>

    </div>
    <!-- End: Content-->

    <script type="module">
        var map = L.map('map').setView([-6.2777832, 106.8026676], 10);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map)



        let kemangGeojson = await fetch("{{ asset('geojson/Adm_Kemang_FeaturesToJSON.json') }}");

        const geojsonBody = await kemangGeojson.json();
        // let geoLayer = L.geoJson(geojsonBody, {
        //     style: function(params) {
        //         return {
        //             color: params.properties.Warna,
        //             opacity: 1.0,
        //             weight: 2
        //         }
        //     }
        // }).bindPopup(function(layer) {
        //     return layer.feature.properties.NAMOBJ;
        // }).addTo(map);
        // map.fitBounds(geoLayer.getBounds());

        let geolayer = L.geoJson(geojsonBody, {
            style: style,
            onEachFeature: onEachFeature
        }).addTo(map);
        map.fitBounds(geolayer.getBounds())


		function popUp(feature, layer) {
			let html = "";
			if(feature.properties){
				html += '<p>'+ feature.properties['NAMOBJ'] +'</p>';
				html += '<p>' + feature.properties['WADMKC'] + '</p>';
				layer.bindPopup(html);
			}
		}

        // Set Warna Berdasarkan Value Kepadatan Penduduk Field INDEKS
        function getColor(d) {
            return d < 1.9 ? '#b7fbb8' :
                d < 2.37 ? '#ffeabd ' :
                d > 2.36 ? '#feff73' :
                '#FF0000';
        }

        function style(feature) {
            return {
                weight: 3,
                opacity: 1,
                color: 'black',
                dashArray: '3',
                fillOpacity: 1,
                fillColor: feature.properties.Warna
            };
        }

        function highlightFeature(e) {
            var layer = e.target;

            layer.setStyle({
                weight: 5,
                color: '#fff',
                dashArray: '',
                fillOpacity: 0.7
            });

            if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
                layer.bringToFront();
            }

            info.update(layer.feature.properties);
        }

        function resetHighlight(e) {
            geolayer.resetStyle(e.target);
            info.update();
        }

        function zoomToFeature(e) {
            map.fitBounds(e.target.getBounds());
        }

        function onEachFeature(feature, layer) {
            layer.on({
                mouseover: highlightFeature,
                mouseout: resetHighlight,
                click: zoomToFeature
            });


			popUp(feature,layer);

            var label = L.marker(layer.getBounds().getCenter(), {
                icon: L.divIcon({
                    className: 'label',
                    html: feature.properties.NAMOBJ,
                    iconSize: [20, 10]
                })
            }).addTo(map);
        }
    </script>
@endsection
