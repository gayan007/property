<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/property/API/FeedPropertyData.php');

$feed = new FeedPropertyData();
$feed->feedDataFromAPI();

$message = array('message' => "All the properties are updated from remote API");

$url = "../index.php" . '?' . http_build_query($message);
header("location: " . $url);
