var layerGroup = new L.MarkerClusterGroup();

$(function() {
    var map = L.map('map');

    L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
        attribution: 'Map data © <a href="http://openstreetmap.org">OpenStreetMap</a>'
    }).addTo(map);

    map.locate({setView: true, maxZoom: 12});


    $('#list a').each(function() {

        var latitude = $(this).data('latitude');
        var longitude = $(this).data('longitude');
        layer = L.marker([latitude, longitude]);

        var popupText =
            "<div>" +
            "<h6 align='center' style='margin-bottom: 0'>" +
            "<img src='/uploads/violation_images/" + $(this).data('image') + "' height='120px' width='240px'/>"+
            "</h6>";

        console.log($(this).val());

        layer.bindPopup(popupText);
        layerGroup.addLayer(layer).addTo(map);
    });

    map.addLayer(layerGroup);

    $('#list a').click(function() {
        var latitude = $(this).data('latitude');
        var longitude = $(this).data('longitude');

        map.panTo(new L.LatLng(latitude, longitude));
        var markers = layerGroup.getLayers();
        markers.forEach(function(item) {
            var LatLng = item.getLatLng();
            if (LatLng['lat'] == latitude && LatLng['lng'] == longitude) {
                item.openPopup();
            }
        });
    });
});