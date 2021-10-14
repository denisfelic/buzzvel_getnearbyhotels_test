<?php

use App\Services\Search;


require __DIR__ .  '/../vendor/autoload.php';

echo json_encode(Search::getNearbyHotels("-23.524727", "-46.4296952"));
