<?php
// controller/MainPageController.php
ini_set('memory_limit', '256M');

require_once '../models/Rating.php';
require_once '../database.php';

$ratingModel = new Rating($db);
$ratings = $ratingModel->getAllRatings();
if ($ratings === false) {
    $ratings = [];
}

require '../views/mainpage.php';