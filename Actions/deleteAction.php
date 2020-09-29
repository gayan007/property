<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/property/DB/DBHandler.php');

if (isset($_GET['id'])) {
    $propertyId = (int)$_GET['id'];
} else {
    $message = array('message' => "The property not found.");

    $url = "../index.php" . '?' . http_build_query($message);
    header("location: " . $url);
}

$dbHandler = new DBHandler();
$deleteResult = $dbHandler->deletePropertyById($propertyId);

if ($deleteResult) {
    $message = array('message' => "The property is deleted successfully.");

    $url = "../index.php" . '?' . http_build_query($message);
    header("location: " . $url);
};