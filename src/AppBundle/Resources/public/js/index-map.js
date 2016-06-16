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
            "<a class='fancybox fancybox-href' href='#' data-video='"+$(this).data('video')+
            "' data-car-number='"+$(this).text()+"'>Переглянути відео порушення</a>" +
            "</h6>";

        layer.bindPopup(popupText);
        layerGroup.addLayer(layer).addTo(map);
    });

    map.addLayer(layerGroup);

    $('#list a').click(function(e) {
        $('.modal-title').text('Номер порушника:' + $(this).text());
        var latitude = $(this).data('latitude');
        var longitude = $(this).data('longitude');
        map.setView([latitude, longitude], 30);
        var markers = layerGroup.getLayers();

        markers.forEach(function(item) {
            var LatLng = item.getLatLng();
            if (LatLng['lat'] == latitude && LatLng['lng'] == longitude) {
                item.openPopup();
            }
        });

    });

    var player;

    map.on('popupopen', function() {
        $('.fancybox-href').on('click', function () {
            var video = $(this).data('video'),
                car_number = $(this).data('car-number');
            $('.fancybox').fancybox({
                tpl: {
                    // wrap template with custom inner DIV: the empty player container
                    wrap: '<div class="fancybox-wrap" tabIndex="-1">' +
                    '<div class="fancybox-skin">' +
                    '<div class="fancybox-outer">' +
                    '<div id="player">' + // player container replaces fancybox-inner
                    '</div></div></div></div>'
                },

                beforeShow: function () {
                    var base = video,
                        cdn = "/uploads/violation_videos/";

                    this.title = 'Номер порушника: ' + car_number;
                    // install player into empty container
                    player = flowplayer("#player", {
                        autoplay: true,
                        ratio: 9 / 16,
                        defaultQuality: "360p",
                        clip: {
                            autoplay: true,
                            sources: [
                                {type: "video/mp4", src: cdn + base}
                            ]
                        }
                    });

                },
                beforeClose: function () {
                    // important! shut down the player as fancybox removes container
                    player.shutdown();
                }
            });
        });
    });
});
