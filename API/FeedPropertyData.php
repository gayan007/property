<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/property/DB/DBHandler.php');

class FeedPropertyData
{
    private $apiConfigs;
    private $dbHandler;

    /**
     * FeedPropertyData constructor.
     */
    function __construct()
    {

        $configs = include($_SERVER['DOCUMENT_ROOT'] . '/property/config.php');
        $this->apiConfigs = $configs['propertyAPI'];
        $this->dbHandler = new DBHandler();
    }

    /**
     *
     */
    public function feedDataFromAPI()
    {

        $properties = $this->getDataFromAPI($this->getApiPageURL());

        do {
            $this->feedPropertiesToDB($properties);
            $properties = $this->getDataFromAPI($properties['next_page_url']);
        } while ($properties['next_page_url'] != null);
    }

    /**
     * @param int $pageNumber
     *
     * @return string
     */
    protected function getApiPageURL($pageNumber = 1)
    {

        $params = array(
            'api_key' => $this->apiConfigs['key'],
            'page[number]' => $pageNumber,
            'page[size]' => $this->apiConfigs['defaultPageSize'],
        );

        return $this->apiConfigs['url'] . '?' . http_build_query($params);
    }

    /**
     * @param $apiURL
     *
     * @return mixed
     */
    protected function getDataFromAPI($apiURL)
    {

        $curl = curl_init($apiURL);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, $apiURL);
        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response, true);
    }

    /**
     * @param $properties
     */
    protected function feedPropertiesToDB($properties)
    {

        foreach ($properties['data'] as $property) {

            $sql = "SELECT id, deleted, updated FROM property WHERE uuid = ?";
            $result = $this->dbHandler->getResult($sql, 's', [$property['uuid']]);

            if ($result) {
                //if no record found by the uuid then insert it
                if ($result->num_rows === 0) {
                    $this->insertProperty($property);
                } else {
                    //if the found record is deleted or updated by site do not update
                    $result = $result->fetch_assoc();
                    if ($result['deleted'] != 1 and $result['updated'] != 1) {
                        $this->updateProperty($property);
                    }
                }
            }
        }
    }

    /**
     * @param $property
     */
    protected function insertProperty($property)
    {

        $this->feedPropertyTypeToDB($property['property_type']);

        $sql = 'INSERT INTO `property` (`uuid`, 
                                        `property_type_id`, 
                                        `county`, 
                                        `country`, 
                                        `town`, 
                                        `description`, 
                                        `address`, 
                                        `image_full`, 
                                        `image_thumbnail`, 
                                        `latitude`, 
                                        `longitude`, 
                                        `num_bedrooms`, 
                                        `num_bathrooms`, 
                                        `price`, 
                                        `type`, 
                                        `created_at`, 
                                        `updated_at`) 
                            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
        $parameters = array(
            $property["uuid"],
            $property["property_type_id"],
            $property["county"],
            $property["country"],
            $property["town"],
            $property["description"],
            $property["address"],
            $property["image_full"],
            $property["image_thumbnail"],
            $property["latitude"],
            $property["longitude"],
            $property["num_bedrooms"],
            $property["num_bathrooms"],
            $property["price"],
            $property["type"],
            $property["created_at"],
            $property["updated_at"]
        );

        $this->dbHandler->getResult($sql, 'sssssssssssssssss', $parameters);
        echo "New property created successfully<br>";
    }

    /**
     * @param $property
     */
    protected function updateProperty($property)
    {

        $this->feedPropertyTypeToDB($property['property_type']);

        $sql = "UPDATE `property` SET   uuid = ?,
                                        property_type_id = ?,
                                        county = ?,
                                        country = ?,
                                        town = ?,
                                        description = ?,
                                        address = ?,
                                        image_full = ?,
                                        image_thumbnail = ?,
                                        latitude = ?,
                                        longitude = ?,
                                        num_bedrooms = ?,
                                        num_bathrooms = ?,
                                        price = ?,
                                        type = ?,
                                        created_at = ?,
                                        updated_at = ?
                         WHERE uuid = ?";
        $parameters = array(
            $property["uuid"],
            $property["property_type_id"],
            $property["county"],
            $property["country"],
            $property["town"],
            $property["description"],
            $property["address"],
            $property["image_full"],
            $property["image_thumbnail"],
            $property["latitude"],
            $property["longitude"],
            $property["num_bedrooms"],
            $property["num_bathrooms"],
            $property["price"],
            $property["type"],
            $property["created_at"],
            $property["updated_at"],
            $property['uuid']
        );
        $this->dbHandler->getResult($sql, 'ssssssssssssssssss', $parameters);
        echo "New property updated successfully";

        $this->feedPropertyTypeToDB($property['property_type']);
    }

    /**
     * @param $propertyType
     */
    protected function feedPropertyTypeToDB($propertyType)
    {

        $sql = "SELECT COUNT(id) FROM property_type WHERE id = ?";
        $result = $this->dbHandler->getResult($sql, 'i', [$propertyType['id']]);

        if (!($result && $result->fetch_array()[0] > 0)) {
            $sql = 'INSERT INTO `property_type`(`id`, `title`, `description`, `created_at`, `updated_at`) 
                    VALUES (?, ?, ?, ?, ?);';

            $parameters = array(
                $propertyType['id'],
                $propertyType['title'],
                $propertyType['description'],
                $propertyType['created_at'],
                $propertyType['updated_at']
            );

            $this->dbHandler->getResult($sql, 'issss', $parameters);
            echo "New property type created successfully <br>";
        } else {
            $result->close();
        }
    }
}