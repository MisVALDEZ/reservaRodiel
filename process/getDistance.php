<?php
/**
 * @function getDistanceAndTime()
 * Calculates the distance and estimated travel time between two addresses
 * 
 * @params
 * $addressFrom - Starting point
 * $addressTo - End point
 * $unit - Unit type (K for kilometers, M for meters, default is miles)
 * 
 * @return string - Distance and estimated travel time
 */
function getDistanceAndTime($addressFrom, $addressTo, $unit = ''){
    // Google API key
    $apiKey = 'AIzaSyBLdzByXZrnF2iCr6Bq8fcF9fFFi4eEBCI';
    
    // Change address format
    $formattedAddrFrom = str_replace(' ', '+', $addressFrom);
    $formattedAddrTo = str_replace(' ', '+', $addressTo);
    
    // Distance Matrix API request
    $distanceMatrixUrl = 'https://maps.googleapis.com/maps/api/distancematrix/json?origins='.$formattedAddrFrom.'&destinations='.$formattedAddrTo.'&key='.$apiKey;
    $response = file_get_contents($distanceMatrixUrl);
    $output = json_decode($response, true);
    
    if(!empty($output['error_message'])){
        return $output['error_message'];
    }
    
    if(isset($output['rows'][0]['elements'][0]['status']) && $output['rows'][0]['elements'][0]['status'] == 'OK'){
        $distance = $output['rows'][0]['elements'][0]['distance']['value']; // in meters
        $duration = $output['rows'][0]['elements'][0]['duration']['value']; // in seconds

        // Convert distance to desired unit
        switch (strtoupper($unit)) {
            case 'K':
                $distance = round($distance / 1000, 2) . ' km';
                break;
            case 'M':
                $distance = round($distance, 2) . ' meters';
                break;
            default:
                $distance = round($distance / 1609.344, 2) . ' miles';
                break;
        }
        
        // Convert duration to hours and minutes
        $hours = floor($duration / 3600);
        $minutes = floor(($duration % 3600) / 60);
        $time = $hours . ' hours ' . $minutes . ' minutes';

        return 'Distance: ' . $distance . ', Estimated Travel Time: ' . $time;
    } else {
        return 'Unable to calculate the distance and time.';
    }
}
