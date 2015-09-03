$(function() {
    var map = L.map('map-violation').setView([49.337118, 31.055737], 6);

    L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    map.locate({ setView : true, maxZoom : 12 });

    var marker = null;

    var onMapClick = function(e) {
        $("#violation_video_form_latitude").val(e.latlng.lat.toString());
        $("#violation_video_form_longitude").val(e.latlng.lng.toString());

        if (marker) {
            map.removeLayer(marker);
        }

        $("#found_item_areaType").val('marker');
        marker = L.marker([e.latlng.lat, e.latlng.lng]).addTo(map);
    };

    map.on('click', onMapClick);
});