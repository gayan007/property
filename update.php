<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/property/DB/DBHandler.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/property/Actions/UpdateAction.php');

$dbHandler = new DBHandler();

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
} else {
    $message = array('message' => "Property not found, Please try again.");

    $url = "./index.php" . '?' . http_build_query($message);
    header("location: " . $url);
}

$stmt = "SELECT  uuid, 
                property_type_id, 
                county, 
                country, 
                town, 
                description, 
                address, 
                image_full, 
                image_thumbnail, 
                latitude, 
                longitude, 
                num_bedrooms, 
                num_bathrooms, 
                price, 
                type, 
                created_at, 
                updated_at FROM property WHERE id = ?";

$result = $dbHandler->getResult($stmt, 'i', [$id]);

if ($result->num_rows > 0) {

    $property = $result->fetch_assoc();
} else {
    $message = array('message' => "Property not found, Please try again.");

    $url = "./index.php" . '?' . http_build_query($message);
    header("location: " . $url);
}
$result->close();

// Processing form data when form is submitted
if (isset($_POST["id"]) && !empty($_POST["id"])) {

    // Get hidden input value
    $id = $_POST["id"];
    $validationPassed = true;

    // Validate country
    $input_country = trim($_POST["country"]);
    if (empty($input_country)) {
        $validationPassed = false;
        $country_err = "Please enter a country.";
    } elseif (!filter_var($input_country, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "/^[a-zA-Z\s]+$/")))) {
        $validationPassed = false;
        $country_err = "Please enter a valid country.";
    }

    // Validate address
    $input_address = trim($_POST["address"]);
    if (empty($input_address)) {
        $validationPassed = false;
        $address_err = "Please enter an address.";
    }

    if ($validationPassed) {

        $updateAction = new UpdateAction();

        if ($updateAction->updateProperty()) {
            $message = array('message' => "Property updated successfully.");

            $url = "./index.php" . '?' . http_build_query($message);
            header("location: " . $url);
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Record</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        .wrapper {
            width: 500px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="page-header">
                    <h2>Update Record</h2>
                </div>
                <p>Please edit the input values and submit to update the record.</p>
                <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post" enctype="multipart/form-data">
                    <div class="form-group <?= (!empty($country_err)) ? 'has-error' : '' ?>">
                        <label>Country</label>
                        <input type="text" name="country" class="form-control" value="<?= isset($_POST["country"]) ? $_POST["country"] : $property['country']; ?>">
                        <span class="help-block"><?= (!empty($country_err)) ? $country_err : '' ?></span>
                    </div>
                    <div class="form-group <?= (!empty($address_err)) ? 'has-error' : '' ?>">
                        <label>Address</label>
                        <textarea name="address" class="form-control"><?= isset($_POST["address"]) ? $_POST["address"] : $property['address']; ?></textarea>
                        <span class="help-block"><?= (!empty($address_err)) ? $address_err : '' ?></span>
                    </div>
                    <div class="form-group">
                        <label>Select image to upload:</label>
                        <input type="file" name="photo" id="fileSelect">
                    </div>
                    <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                    <input type="submit" class="btn btn-primary" value="Submit">
                    <a href="index.php" class="btn btn-default">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>


