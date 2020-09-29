<?php

class DBHandler
{

    private $databaseConfigs;
    public $mysqli;

    /**
     * DBConnection constructor.
     */
    function __construct()
    {

        $configs = include($_SERVER['DOCUMENT_ROOT'] . '/property/config.php');
        $this->databaseConfigs = $configs['database'];

        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        try {
            $this->mysqli = new mysqli($this->databaseConfigs['host'],
                $this->databaseConfigs['username'],
                $this->databaseConfigs['password'],
                $this->databaseConfigs['db']);
            $this->mysqli->set_charset("utf8mb4");
        } catch (Exception $e) {
            error_log($e->getMessage());
            exit('Error connecting to database');
        }
    }

    /**
     * @param      $sql
     * @param null $types
     * @param null $params
     *
     * @return bool|false|mysqli_result
     */
    public function getResult($sql, $types = null, $params = null)
    {

        $stmt = $this->mysqli->prepare($sql);

        if (!empty($types) && !empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        if (!$stmt->execute()) {
            return false;
        }
        return $stmt->get_result();
    }

    /**
     * @param $propertyId
     *
     * @return false|mysqli
     */
    public function deletePropertyById($propertyId)
    {

        $this->getResult("UPDATE property SET deleted = 1  WHERE id= ?", 'i', [$propertyId]);

        return true;
    }
}