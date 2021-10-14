<?php

namespace App\Services;

use App\Models\Hotel;

class Search
{

    public const BUZZVEL_URL = "https://buzzvel-interviews.s3.eu-west-1.amazonaws.com/hotels.json";


    /**
     * Calculate the distance between two coordinates
     *
     * @param float $lat1
     * @param float $lon1
     * @param float $lat2
     * @param float $lon2
     * @return float
     */
    private static function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2)
    {

        $pi80 = M_PI / 180;
        $lat1 *= $pi80;
        $lon1 *= $pi80;
        $lat2 *= $pi80;
        $lon2 *= $pi80;

        $r = 6372.797; // mean radius of Earth in km
        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;
        $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlon / 2) * sin($dlon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $km = $r * $c;

        //echo '<br/>'.$km;
        return $km;
    }

    /**
     * Get a list of hotels
     *
     * @param float $latitude
     * @param float $longitude
     * @param string $orderby
     * @return array
     */
    public static function getNearbyHotels($latitude, $longitude, $orderby = "proximity"): array
    {
        // Get data from get request
        $response = file_get_contents(self::BUZZVEL_URL);
        $hotelList = [];
        $response = json_decode($response);

        // Fill an array of Hotel Objects with the data retrieved from buzzvel hotels api
        foreach ($response->message as $index => $item) {
            // Check if all field of an item is not null
            if (!($item[0] && $item[1] && $item[2] && $item[3])) {
                continue;
            }
            $hotel = new Hotel($item[0], floatval($item[1]), floatval($item[2]), $item[3]);
            $hotel->setDistance(
                self::calculateDistance($latitude, $longitude, $hotel->getLatitude(), $hotel->getLongitude())
            );
            array_push($hotelList, $hotel);
        }


        // Check if orderby is "pricepernight" and return the data according "orderBy" choice.
        if ($orderby == "pricepernight") {
            self::orderByPrice($hotelList);
        } else {
            self::orderByDistance($hotelList);
        }

        $hotelListFormated = self::getFormatedHotelList($hotelList);
        return $hotelListFormated;
    }


    private static function getFormatedHotelList(array $hotels)
    {
        $hotelListFormated = [];
        foreach ($hotels as $hotel) {
            array_push($hotelListFormated, "Hotel {$hotel->getName()}, {$hotel->getFormatedDistance()} KM, {$hotel->getPrice()} EUR");
        }
        return $hotelListFormated;
    }

    /**
     * Order Hotel list by most nearby hotels
     *
     * @param array $hotelList
     * @return void
     */
    private static function orderByDistance(array &$hotelList)
    {
        usort($hotelList, function ($hotel1, $hotel2) {
            if ($hotel1->getDistance() === $hotel2->getDistance()) {
                return 0;
            }
            return $hotel1->getDistance() > $hotel2->getDistance() ? 1 : -1;
        });
    }

    /**
     * Order Hotel list by price
     *
     * @param array $hotelList
     * @return void
     */
    private static function orderByPrice(array &$hotelList)
    {
        usort($hotelList, function ($hotel1, $hotel2) {
            if ($hotel1->getPrice() === $hotel2->getPrice()) {
                return 0;
            }
            return $hotel1->getPrice() > $hotel2->getPrice() ? 1 : -1;
        });
    }
}
