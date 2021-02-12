<?php

namespace Model\Utilities;

use Core\Components\DBObject;
use Model\General\GamblersDenException;
use Model\General\Log;

class File extends DBObject
{
    public const table = TABLEPREFIX.'_file';
    public static $file_upload_error;

    public function __construct($data, $new = false)
    {

        if ($new) {
            $data = parent::create_new($data, self::table);
        }

        parent::__construct(self::table, $data);
    }

    public static function  uploadImage(String $filePath, String $folderLocation, $thumb = false, $silentIfNoFile = false)
    {
        if (!$filePath || !array_key_exists($filePath, $_FILES) || $_FILES[$filePath]['error'] == 4) {
            if(!$silentIfNoFile){
                self::$file_upload_error = "No file uploaded";
            }
            return false;
        }


        $fileObject = $_FILES[$filePath];

        if ($fileObject['error'] == 1) {
            self::$file_upload_error = "File Upload Error - File is too big.";
            return false;
        }

        if ($fileObject['size'] > MAXFILESIZE) {
            self::$file_upload_error = "Selected file size is too large. Max ".(MAXFILESIZE/1000000)."MB";

            return false;
        }

        $fileLocation = $fileObject['tmp_name'];
        $fileName = $fileObject['name'];
        $imagesize = getimagesize($fileLocation);
        $fileExt = end(explode(".", $fileName));

        if ($imagesize[0] < 100 || $imagesize[1] < 100) {
            self::$file_upload_error = "Selected image dimensions is too small";
            return false;
        }

        $tempLocation = "Assets/img/tmp/" . $fileName;
        $finalLocation = "Assets/img/" . $folderLocation;
        $fileName = md5($fileLocation);

        move_uploaded_file($fileLocation, $tempLocation);

        if ($thumb) {
            if(!$finalLocation = self::makeSquareImage($tempLocation, $finalLocation, $fileName)){
                new GamblersDenException('Thumbnail Create Failed.','When uploading a file, and trying to convert it to a thumbnail - something went wrong.');
                self::$file_upload_error = "An error occurred. Please get in touch with us.";
                return false;
            }
        } else {
            rename($tempLocation, $finalLocation . $fileName . '.' . $fileExt);
            $finalLocation = $finalLocation . $fileName .'.'. $fileExt;
        }

        unlink($tempLocation);

        Log::LogData('File Upload','File ('.$fileName.'.'.$fileExt.') was uploaded.');

        return new self([
            'file_path' => $finalLocation,
            'file_type' => 'image',
            'upload_date' => date('Y-m-d H:i:s'),
            'user_id' => \SiteSession::LOU()->user_id
        ], true);
    }

    public static function makeSquareImage($originalFile, $destinationFolder, $destinationFileName, $square_size = 100)
    {

        if (isset($destinationFolder) and $destinationFolder != null) {
            if(!file_exists($destinationFolder)){
                mkdir($destinationFolder, 0775);
            };
            if (!is_writable($destinationFolder)) {
                return false;
            }
        }

        // get width and height of original image
        $imagedata = getimagesize($originalFile);
        $original_width = $imagedata[0];
        $original_height = $imagedata[1];

        if ($original_width > $original_height) {
            $new_height = $square_size;
            $new_width = $new_height * ($original_width / $original_height);
        }
        if ($original_height > $original_width) {
            $new_width = $square_size;
            $new_height = $new_width * ($original_height / $original_width);
        }
        if ($original_height == $original_width) {
            $new_width = $square_size;
            $new_height = $square_size;
        }

        $new_width = round($new_width);
        $new_height = round($new_height);
        $oldmem = ini_get('memory_limit');
        ini_set ('memory_limit', '400M');

        // load the image
        if (substr_count(strtolower($originalFile), ".jpg") or substr_count(strtolower($originalFile), ".jpeg")) {
            $original_image = imagecreatefromjpeg($originalFile);
        }
        if (substr_count(strtolower($originalFile), ".gif")) {
            $original_image = imagecreatefromgif($originalFile);
        }
        if (substr_count(strtolower($originalFile), ".png")) {
            $original_image = imagecreatefrompng($originalFile);
        }

        ini_set('memory_limit', $oldmem);

        $smaller_image = imagecreatetruecolor($new_width, $new_height);
        $square_image = imagecreatetruecolor($square_size, $square_size);

        imagecopyresampled($smaller_image, $original_image, 0, 0, 0, 0, $new_width, $new_height, $original_width,
            $original_height);

        if ($new_width > $new_height) {
            $difference = $new_width - $new_height;
            $half_difference = round($difference / 2);
            imagecopyresampled($square_image, $smaller_image, 0 - $half_difference + 1, 0, 0, 0,
                $square_size + $difference, $square_size, $new_width, $new_height);
        }
        if ($new_height > $new_width) {
            $difference = $new_height - $new_width;
            $half_difference = round($difference / 2);
            imagecopyresampled($square_image, $smaller_image, 0, 0 - $half_difference + 1, 0, 0, $square_size,
                $square_size + $difference, $new_width, $new_height);
        }
        if ($new_height == $new_width) {
            imagecopyresampled($square_image, $smaller_image, 0, 0, 0, 0, $square_size, $square_size, $new_width,
                $new_height);
        }


        $destinationFile = $destinationFolder . "/" . $destinationFileName;

        // save the smaller image FILE if destination file given
        if (substr_count(strtolower($originalFile), ".jpg") || substr_count(strtolower($originalFile), ".jpeg")) {
            imagejpeg($square_image, $destinationFile . '.jpg', 100);
            $finalExt = '.jpg';
        }
        if (substr_count(strtolower($originalFile), ".gif")) {
            imagegif($square_image, $destinationFile . '.gif');
            $finalExt = '.gif';
        }
        if (substr_count(strtolower($originalFile), ".png")) {
            imagesavealpha($square_image, true);
            imagepng($square_image, $destinationFile . '.png', 9);
            $finalExt = '.png';
        }

        if (!($finalExt ?? false)) {
            return false;
        }

        imagedestroy($original_image);
        imagedestroy($smaller_image);
        imagedestroy($square_image);

        return $destinationFile . $finalExt;

    }

    public function remove(bool $sure = false){
        unlink($this->file_path);
        parent::remove($sure);
    }


}