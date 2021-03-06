<?php

namespace AppBundle\Service;

/**
 * Class GeoCoordinatesConverter
 */
class GeoCoordinatesConverter
{
    /**
     * @param array $info
     *
     * @return bool
     */
    public function convert($info)
    {
        if (isset($info['GPSLatitude']) && isset($info['GPSLongitude']) &&
            isset($info['GPSLatitudeRef']) && isset($info['GPSLongitudeRef']) &&
            in_array($info['GPSLatitudeRef'], array('E', 'W', 'N', 'S')) &&
            in_array($info['GPSLongitudeRef'], array('E', 'W', 'N', 'S'))
        ) {
            $GPSLatitudeRef = strtolower(trim($info['GPSLatitudeRef']));
            $GPSLongitudeRef = strtolower(trim($info['GPSLongitudeRef']));

            $latDegreesA = explode('/', $info['GPSLatitude'][0]);
            $latMinutesA = explode('/', $info['GPSLatitude'][1]);
            $latSecondsA = explode('/', $info['GPSLatitude'][2]);
            $lngDegreesA = explode('/', $info['GPSLongitude'][0]);
            $lngMinutesA = explode('/', $info['GPSLongitude'][1]);
            $lngSecondsA = explode('/', $info['GPSLongitude'][2]);

            $latDegrees = $latDegreesA[0] / $latDegreesA[1];
            $latMinutes = $latMinutesA[0] / $latMinutesA[1];
            $latSeconds = $latSecondsA[0] / $latSecondsA[1];
            $lngDegrees = $lngDegreesA[0] / $lngDegreesA[1];
            $lngMinutes = $lngMinutesA[0] / $lngMinutesA[1];
            $lngSeconds = $lngSecondsA[0] / $lngSecondsA[1];

            $lat = (float) $latDegrees + ((($latMinutes * 60) + ($latSeconds)) / 3600);
            $lng = (float) $lngDegrees + ((($lngMinutes * 60) + ($lngSeconds)) / 3600);

            //If the latitude is South, make it negative.
            //If the longitude is west, make it negative
            $GPSLatitudeRef == 's' ? $lat *= -1 : '';
            $GPSLongitudeRef == 'w' ? $lng *= -1 : '';
        } else {
            return false;
        }
        $data = [
            'latitude'  => $lat,
            'longitude' => $lng,
        ];

        return $data;
    }
}
