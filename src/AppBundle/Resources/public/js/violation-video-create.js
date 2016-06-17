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

    // We can attach the `fileselect` event to all file inputs on the page
    $(document).on('change', ':file', function () {
        var input = $(this);
        var numFiles = input.get(0).files ? input.get(0).files.length : 1;
        var label = input.val().replace(/\\/g, '/').replace(/.*\//, '');

        input.trigger('fileselect', [numFiles, label]);
    });

    // We can watch for our custom `fileselect` event like this
    $(document).ready(function () {
        $(':file').on('fileselect', function (event, numFiles, label) {
            var input = $(this).parents('.form-group').find('.filename');
            var log = numFiles > 1 ? numFiles + ' files selected' : label;

            if (input.length) {
                input.html(log);
            }
        });
    });
});
