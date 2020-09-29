<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/property/DB/DBHandler.php');

class UpdateAction
{
    private $dbHandler;

    /**
     * UpdateAction constructor.
     */
    function __construct()
    {

        $this->dbHandler = new DBHandler();
    }

    /**
     * @return false|mysqli
     */
    public function updateProperty()
    {

        $imageLocations = $this->handlePropertyImage(uniqid());

        $sql = "UPDATE property SET country = ? ,address = ? ,";
        $types = 'ss';
        $parameters = array($_POST['country'], $_POST['address']);

        if (is_array($imageLocations)) {
            $types .= 'ss';
            $parameters[] = $imageLocations[0];
            $parameters[] = $imageLocations[1];
            $sql .= "image_full = ?, image_thumbnail = ?,";
        }

        $sql .= "updated_at = NOW(), updated = 1 WHERE id = ?";
        $types .= 'i';
        $parameters[] = (int)$_POST['id'];

        $this->dbHandler->getResult($sql, $types, $parameters);

        return true;
    }

    /**
     * @param $newFileName
     *
     * @return array
     */
    protected function handlePropertyImage($newFileName)
    {

        // Check if the form was submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Check if file was uploaded without errors
            if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] == 0) {

                $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");
                $filename = $_FILES["photo"]["name"];
                $filetype = $_FILES["photo"]["type"];
                $filesize = $_FILES["photo"]["size"];

                // Verify file extension
                $ext = pathinfo($filename, PATHINFO_EXTENSION);

                if (!array_key_exists($ext, $allowed)) {
                    die("Error: Please select a valid file format.");
                }

                // Verify file size - 5MB maximum
                $maxsize = 5 * 1024 * 1024;

                if ($filesize > $maxsize) {
                    die("Error: File size is larger than the allowed limit.");
                }

                // Verify MYME type of the file
                if (in_array($filetype, $allowed)) {

                    $imageDestination = "uploads/" . $newFileName . "." . $ext;
                    move_uploaded_file($_FILES["photo"]["tmp_name"], $imageDestination);
                    $thumbnailDestination = $this->makeThumbnail("uploads/", $newFileName . "." . $ext, "check");

                    return array($imageDestination, $thumbnailDestination);

                } else {
                    echo "Error: There was a problem uploading your file. Please try again.";
                }
            }
        }
    }

    /**
     * @param $updir
     * @param $img
     * @param $id
     *
     * @return string
     */
    protected function makeThumbnail($updir, $img, $id)
    {

        $thumbnail_width = 134;
        $thumbnail_height = 189;
        $thumb_beforeword = "thumb";
        $arr_image_details = getimagesize("$updir" . "$img"); // pass id to thumb name
        $original_width = $arr_image_details[0];
        $original_height = $arr_image_details[1];

        if ($original_width > $original_height) {
            $new_width = $thumbnail_width;
            $new_height = intval($original_height * $new_width / $original_width);
        } else {
            $new_height = $thumbnail_height;
            $new_width = intval($original_width * $new_height / $original_height);
        }

        $dest_x = intval(($thumbnail_width - $new_width) / 2);
        $dest_y = intval(($thumbnail_height - $new_height) / 2);

        if ($arr_image_details[2] == IMAGETYPE_GIF) {
            $imgt = "ImageGIF";
            $imgcreatefrom = "ImageCreateFromGIF";
        }
        if ($arr_image_details[2] == IMAGETYPE_JPEG) {
            $imgt = "ImageJPEG";
            $imgcreatefrom = "ImageCreateFromJPEG";
        }
        if ($arr_image_details[2] == IMAGETYPE_PNG) {
            $imgt = "ImagePNG";
            $imgcreatefrom = "ImageCreateFromPNG";
        }

        if ($imgt) {
            $old_image = $imgcreatefrom("$updir" . "$img");
            $new_image = imagecreatetruecolor($thumbnail_width, $thumbnail_height);
            imagecopyresized($new_image, $old_image, $dest_x, $dest_y, 0, 0, $new_width, $new_height, $original_width, $original_height);
            $imgt($new_image, "$updir" . $id . '_' . "$thumb_beforeword" . "$img");
            return "$updir" . $id . '_' . "$thumb_beforeword" . "$img";
        }
    }
}