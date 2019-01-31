<?php
namespace App\Models;

use \PDO;
use \Slim\Container;
use App\Models\VideoModel;
use DateTime;

class OCRModel
{
    private $db;
    private $settings;
    private $path_to_proc;
    private $path_to_cleaner;
    private $videomodel;

    public function __construct(Container $c, PDO $db)
    {
        $this->db = $db;
        $this->settings = $c->get('settings');
        $this->path_to_proc = "{$this->settings['path_to_module']}ocr/proc";
        $this->path_to_cleaner = "{$this->settings['path_to_module']}ocr/cleaner";
        $this->videomodel = $c->get('VideoModel');
    }

    public function createTricade($string)
    {
        $tricades = [];
        for($i=0, $j=-2;$j<mb_strlen($string); $i++, $j++) {
            $tricades[$i] = "   ";
            for($t=0; $t<3; $t++) {
                $char = ' ';
                if(($j+$t>=0) && ($j+$t<mb_strlen($string))) $char = mb_substr($string, $j+$t, 1);
                $tricades[$i] = $tricades[$i].$char;
            }
        }
        return $tricades;
    }

    public function clearWord($string, $advanced = false)
    {
        $badChars = array(',',  '_', '-', '\\', '/', '*', '+', '`', '\'', '"', '%', '#', '!', '$', '^', '&', '?', '(', ')', '=', ':', ';', '[', ']', '{', '}', '~', '’', '…', '„', '‚', '“', '‚', '©', '—');
        //$advancedBadChars = array(' ');
        $advancedBadChars = array('_');
        $string = str_replace($badChars, '', $string);
        if($advanced)
            $string = str_replace($advancedBadChars, '', $string);
        return $string;
    }

    public function correctWord($string, $type)
    {
        $find = false;
        $result = null;
        $sql = "";
        if($type=='RF_NAME') {
            $sql = "SELECT name AS word, similarity(name, :string) AS sim 
FROM document_russian_names 
WHERE name % :string
ORDER BY sim DESC";
        } else if($type=='RF_SURNAME') {
            $sql = "SELECT surname AS word, similarity(surname, :string) AS sim 
FROM document_russian_surnames 
WHERE surname % :string
ORDER BY sim DESC";
        }

        $con = $this->db->prepare($sql);
        $con->bindParam("string", $string);
        $con->execute();
        $base = $con->fetchAll(PDO::FETCH_OBJ);
        $main_tricades = $this->createTricade($string);

        $tricadeResult = [];
        $max_similar = 0;
        $minLetters = 100;
        $minLettersIndex = 0;

        $similar = 0;
        for($i=0;$i<count($base);$i++)
        {
            $tricade = $this->createTricade($base[$i]->word);
            $similar = 0;
            for($k=0;$k<count($main_tricades) && $k<count($tricade);$k++)
            {
                if($main_tricades[$k]==$tricade[$k]) $similar++;
            }
            $tricadeResult[$i] = $similar;
            if($max_similar<$similar) $max_similar = $similar;
        }

        //Если половина трикад совпадает - возвращаем слово
        if(round(count($main_tricades)-$similar)>=round((count($main_tricades)/2))) {
            for ($i = 0; $i < count($base); $i++) {
                if ($tricadeResult[$i] >= $max_similar) {
                    $letters = abs(mb_strlen($string) - mb_strlen($base[$i]->word));
                    if ($letters < $minLetters) {
                        $minLetters = $letters;
                        $minLettersIndex = $i;
                        $find = true;
                    }
                }
            }
            if($find)
                $result = $base[$minLettersIndex]->word;
        }
        return $result;
    }

    /*
     * OCR processing image
     * img - path to image
     * data - info array
     * delete - delete original image - $img
     */
    public function processing($img, $data, $delete = false)
    {
        $grayPixelsCount = 0;
        $totalPixels = 0;
        if (file_exists($img) && filesize($img) != 0) {
            $result = [];
            $doctype = $data['doctype'];
            $docpage = $data['docpage'];

            //$image = imagecreatefromjpeg($img);
            $image = open_image($img);

            //Create alter image
            $width = imagesx ($image);
            $height = imagesy ($image);
            $alterImage = imagecreate($width, $height);
            $background = imagecolorallocate($alterImage, 255, 255, 255);
            $white = imagecolorallocate($alterImage, 255, 255, 255);
            $black = imagecolorallocate($alterImage, 0, 0, 0);

            for ($i = 0; $i < $width; $i++) {
                for ($j = 0; $j < $height; $j++) {
                    $totalPixels++;
                    $rgb = ImageColorAt($image, $i, $j);
                    $r = ($rgb >> 16) & 0xFF;
                    $g = ($rgb >> 8) & 0xFF;
                    $b = $rgb & 0xFF;
                    $porog = 145;

                    if ($r <= $porog || $g == $porog || $b <= $porog) {
                        $grayPixelsCount++;
                        imagesetpixel($alterImage, $i, $j, $black);
                    }
                }
            }
            $someImagePath = upload_core_path().'someImage.jpg';
            imagejpeg($alterImage, $someImagePath);
            $image = open_image($someImagePath);
            //TODO: change to normal
            $imageWebPath = "http://uwpw.ru".GetImageUrl(basename($img), 'documents');
            //TODO: /ocr/manual -> use ocr folder
            // /scanning -> use documents folder
                        
            $img = $someImagePath;

            if ($image != false) {
                    if ($docpage == 1) {
                        //Кем выдан
                        $image_rotate_to_normal = imagerotate($image, 180, 0);
                        $image_place = $img . ".conv.place.main.jpg";
                        imagejpeg($image_rotate_to_normal, $image_place);

                        $image_rotate_to_serija = imagerotate($image, 270, 0);
                        $image_serija = $img . ".conv.place.serija.jpg";
                        imagejpeg($image_rotate_to_serija, $image_serija);

                        $command_place = "{$this->path_to_proc} -f {$image_place} -d {$doctype} -t PLACE";
                        $command_serija = "{$this->path_to_proc} -f {$image_serija} -d {$doctype} -t SERIJA";

                        $kem_result_sting = shell_exec($command_place);
                        $serija_result_string = shell_exec($command_serija);

                        //$data['info'] = $this->clearWord($kem_result_sting);

                        //Search date_birth
                        $pattern = "/\d{2}\.\d{2}\.\d{4}/";
                        if (preg_match($pattern, $kem_result_sting, $matches)) {
                            $data['date'] = $matches[0];
                        } else {
                            $data['date_birth'] = null;
                        }

                        if(isset($serija_result_string)) {
                            $serija_result_string = $this->clearWord($serija_result_string);
                            $pattern = "/\d{2}\ \d{2}\ \d{6}/";
                            if (preg_match($pattern, $serija_result_string, $matches)) {
                                $data['series_number'] = $matches[0];
                            } else {
                                $data['series_number'] = null;
                            }
                        }

                        $result = $data;
                        $result['status'] = 'success';

                        unlink($image_place);
                        unlink($image_serija);
                    } else if ($docpage == "2") {
                        //ФИО
                        $image_fio = $img . ".conv.jpg";
                        $image_serija = $img . ".conv.rotate.jpg";
                        $image_rotate = imagerotate($image, 90, 0);

                        imagejpeg($image, $image_fio);
                        imagejpeg($image_rotate, $image_serija);

                        $command_fio = "{$this->path_to_proc} -f {$image_fio} -d {$doctype} -t FIO";
                        $command_serija = "{$this->path_to_proc} -f {$image_serija} -d {$doctype} -t SERIJA";

                        $fio_result_string = shell_exec($command_fio);
                        $serija_result_string = shell_exec($command_serija);

                        if(isset($serija_result_string)) {
                            $serija_result_string = $this->clearWord($serija_result_string);
                            $pattern = "/\d{2}\ \d{2}\ \d{6}/";
                            if (preg_match($pattern, $serija_result_string, $matches)) {
                                $data['series_number'] = $matches[0];
                            } else {
                                $data['series_number'] = null;
                            }
                        }

                        $fio_result_string = $this->clearWord($fio_result_string);
                        while(mb_substr($fio_result_string, 0, 1)==" " || mb_substr($fio_result_string, 0, 1)==".") {
                            $fio_result_string = mb_substr($fio_result_string, 1);
                        }
                        //$data['info'] = $fio_result_string;

                        $words = explode(" ", $fio_result_string);

                        $surname = $first_name = $patronymic = false;
                        $surnameNum = -1;
                        for($i=0;$i<count($words);$i++) {

                            if(!$surname) {
                                //Search surname
                                $temp = $this->correctWord($words[$i], 'RF_SURNAME');
                                if ($temp != null) $surname = $temp;
                                continue;
                            }

                            if(!$first_name) {
                                //Search name
                                $temp = $this->correctWord($words[$i], 'RF_NAME');
                                if ($temp != null) $first_name = $temp;

                                //TODO: Search patronymic
                                //$patronymic = $words[$i+1];
                            }
                        }

                        $data['surname'] = $surname;
                        $data['first_name'] = $first_name;
                        //$data['patronymic'] = $patronymic;

                        //Search date_birth
                        $pattern = "/\d{2}\.\d{2}\.\d{4}/";
                        if (preg_match($pattern, $fio_result_string, $matches)) {
                            $data['date_birth'] = $matches[0];
                        } else {
                            $data['date_birth'] = null;
                        }

                        //Search birthplace
                        if(mb_stripos($fio_result_string, "ГОР.")>=0 || mb_stripos($fio_result_string, "ОР.")>=0 || mb_stripos($fio_result_string, "ГОР .")>=0 || mb_stripos($fio_result_string, "ОР .")>=0) {
                            $strpos = mb_stripos($fio_result_string, "ГОР.");
                            if($strpos==null)
                                $strpos = mb_stripos($fio_result_string, "ОР.");
                            if($strpos==null)
                                $strpos = mb_stripos($fio_result_string, "ГОР .");
                            if($strpos == null)
                                $strpos = mb_stripos($fio_result_string, "ОР .");
                            if($strpos == null && $data['date_birth']!=null) $strpos = mb_stripos($fio_result_string, $data['date_birth']) + mb_strlen($data['date_birth']);
                            if($strpos != null)
                                $data['birthplace'] = mb_substr($fio_result_string, $strpos);
                            else $data['birthplace'] = null;
                        }

                        //Search gender
                        if(mb_stripos($fio_result_string, "МУЖ.")>=0 || mb_stripos($fio_result_string, "УЖ.")>=0) {
                            $data['gender'] = 1;
                        } else if (mb_stripos($fio_result_string, "ЖЕН.")>=0 ||  mb_stripos($fio_result_string, "ЕН.")>=0) {
                            $data['gender'] = 2;
                        } else {
                            $data['gender'] = 3;
                        }

                        //TODO: Need to testing id_person
                        $data['id_person'] = $this->videomodel->identifyFace($imageWebPath, null);

                        $result = $data;
                        $result['status'] = 'success';

                        unlink($image_fio);
                        unlink($image_serija);
                    } else if ($docpage == 3) {
                        //Прописка
		        $result['status'] = 'success';
		        $result['message'] = 'Havent logics.';
                    }
                    if ($delete)
                        unlink($img);
                    //unlink($image_clear);
            } else {
                $result['status'] = 'fail';
                $result['message'] = 'Cant open original file.';
            }
        } else {
            $result['status'] = 'fail';
            $result['message'] = 'Original file is no exist.';
        }
        return $result;
    }
}
