<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/property/DB/DBHandler.php');

$dbHandler = new DBHandler();
$filters = 'deleted IS NULL';
$types = '';
$parameters = array();

if (isset($_GET['pageNumber'])) {
    $pageNumber = (int)$_GET['pageNumber'];
} else {
    $pageNumber = 1;
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    if (isset($_GET["name"]) && $_GET["name"] != '') {
        $parameters[] = "%" . $_GET['name'] . "%";
        $types .= 's';
        $filters .= " AND title LIKE ?";
    }

    if (isset($_GET["numberOfBedRooms"]) && $_GET["numberOfBedRooms"] != '') {
        $parameters[] = $_GET["numberOfBedRooms"];
        $types .= 's';
        $filters .= " AND num_bedrooms = ?";
    }

    if (isset($_GET["price"]) && $_GET["price"] != '') {
        $price = (int)preg_replace('/[^0-9]/', '', $_GET["price"]);
        $parameters[] = $price;
        $types .= 'i';
        $filters .= " AND price = ?";
    }
}

$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$currentURL = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$key = "pageNumber";
$filteredURL = preg_replace('~(\?|&)' . $key . '=[^&]*~', '$1', $currentURL);
$url = $filteredURL . (parse_url($filteredURL, PHP_URL_QUERY) ? '&' : '?');

$noOfRecordsPerPage = 20;
$totalPagesSql = "SELECT COUNT(property.id) FROM property INNER JOIN property_type ON property.property_type_id = property_type.id WHERE $filters";
$pageCountResult = $dbHandler->getResult($totalPagesSql, $types, $parameters);
$totalRows = $pageCountResult->fetch_array()[0];
$totalPages = ceil($totalRows / $noOfRecordsPerPage);
$offset = (int)(($pageNumber - 1) * $noOfRecordsPerPage);

$sql = "SELECT  property.id AS id,
                uuid,
                property_type.title AS title,
                county,
                country,
                town,
                property.description AS description,
                address,
                image_full,
                image_thumbnail,
                latitude,
                longitude,
                num_bedrooms,
                num_bathrooms,
                price,
                type
        FROM property INNER JOIN property_type ON property.property_type_id = property_type.id
        WHERE $filters
        LIMIT $offset, $noOfRecordsPerPage";

$result = $dbHandler->getResult($sql, $types, $parameters);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.js"></script>
    <style type="text/css">
        .wrapper {
            /*width: 650px;*/
            margin: 0 auto;
        }

        .page-header h2 {
            margin-top: 0;
        }

        table tr td:last-child a {
            margin-right: 15px;
        }
    </style>
    <script type="text/javascript">
		$(document).ready(function () {
			$('[data-toggle="tooltip"]').tooltip();
		});
    </script>
    <?php
    if (isset($_GET['message'])) {
        echo '<div class="alert alert-info"><strong>Info!</strong>' . $_GET['message'] . '</div>';
    }
    ?>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
            <div class="page-header">
                <h2>Search</h2>
            </div>

            <form action="<?= htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="get">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" value="<?= isset($_GET["name"]) ? $_GET["name"] : ''; ?>">
                </div>
                <div class="form-group">
                    <label>Number of Bedrooms</label>
                    <input type="text" name="numberOfBedRooms" class="form-control" value="<?= isset($_GET["numberOfBedRooms"]) ? $_GET["numberOfBedRooms"] : ''; ?>">
                </div>
                <div class="form-group">
                    <label>Price</label>
                    <input type="text" name="price" class="form-control" value="<?= isset($_GET["price"]) ? $_GET["price"] : ''; ?>">
                </div>
                <!--                       TODO:add filters
                                        <div class="form-group"> -->
                <!--                            <label>Property Type</label>-->
                <!--                            <input type="text" name="propertyType" class="form-control" value="">-->
                <!--                        </div>-->
                <!--                        <div class="form-group">-->
                <!--                            <label>For Sale / For Rent</label>-->
                <!--                            <input type="text" name="saleOrRent" class="form-control" value="">-->
                <!--                        </div>-->

                <input type="submit" class="btn btn-primary" value="Search">
                <a href="index.php" class="btn btn-default">Clear</a>
            </form>
        </div>
        <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10">
            <div class="page-header clearfix">
                <h2 class="pull-left">Property Details</h2>
                <a href="create.php" class="btn btn-success pull-right" style="margin-left: 10px">Add New Property</a>
                <a href="Actions/feedDataAction.php" class="btn btn-success pull-right">Update from API</a>
            </div>
            <?php
            if ($result) {
                if ($result->num_rows > 0) {
                    echo "<table class='table table-bordered table-striped'>";
                    echo "<thead>";
                    echo "<tr>";
                    echo "<th>Photo</th>";
                    echo "<th>Type</th>";
                    echo "<th>Address</th>";
                    echo "<th>Description</th>";
                    echo "<th># of Bedrooms</th>";
                    echo "<th>Price (&#36;)</th>";
                    echo "<th>Action</th>";
                    echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";
                    while ($row = $result->fetch_array()) {
                        echo "<tr>";
                        echo "<td><img src='" . $row['image_thumbnail'] . "'/></td>";
                        echo "<td>" . $row['title'] . "</td>";
                        echo "<td>" . $row['address'] . "</td>";
                        echo "<td>" . $row['description'] . "</td>";
                        echo "<td>" . $row['num_bedrooms'] . "</td>";
                        echo "<td>" . number_format(($row['price'] / 100), 2, '.', ',') . "</td>";
                        echo "<td>";
                        echo "<a href='read.php?id=" . $row['id'] . "' title='View Record' data-toggle='tooltip'><span class='glyphicon glyphicon-eye-open'></span></a>";
                        echo "<a href='update.php?id=" . $row['id'] . "' title='Update Record' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a>";
                        echo "<a href='Actions/deleteAction.php?id=" . $row['id'] . "' title='Delete Record' data-toggle='tooltip'><span class='glyphicon glyphicon-trash'></span></a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                    echo "</tbody>";
                    echo "</table>";
                    // Free result set
                    $result->free();
                } else {
                    echo "<p class='lead'><em>No records were found.</em></p>";
                }
            }
            ?>
            <ul class="pagination">
                <li><a href="<?= $url ?>pageNumber=1">First</a></li>
                <li class="<?php if ($pageNumber <= 1) {
                    echo 'disabled';
                } ?>">
                    <a href="<?php if ($pageNumber <= 1) {
                        echo '#';
                    } else {
                        echo $url . "pageNumber=" . ($pageNumber - 1);
                    } ?>">Prev</a>
                </li>
                <li class="<?php if ($pageNumber >= $totalPages) {
                    echo 'disabled';
                } ?>">
                    <a href="<?php if ($pageNumber >= $totalPages) {
                        echo '#';
                    } else {
                        echo $url . "pageNumber=" . ($pageNumber + 1);
                    } ?>">Next</a>
                </li>
                <li><a href="<?= $url ?>pageNumber=<?= $totalPages; ?>">Last</a></li>
            </ul>
        </div>
    </div>
</div>
</body>
</html>