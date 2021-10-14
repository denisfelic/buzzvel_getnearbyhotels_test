<?php

use DenisFelic\BuzzVelHotelsTest\Services\Search;

require __DIR__ .  '/../vendor/autoload.php';

echo json_encode(Search::getNearbyHotels("-23.55563315", "-46.6511581"));
