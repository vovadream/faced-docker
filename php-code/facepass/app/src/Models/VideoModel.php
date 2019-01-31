<?php
namespace App\Models;

use \PDO;
use \Slim\Container;
use App\Controllers\HttpClientController;

/**
 * Class VideoModel
 * @package App\Models
 */
class VideoModel
{
    /**
     * @var PDO
     */
    private $db;

    /**
     * @var string url server
     */
    private $ff_server;

    /**
     * @var string auth server
     */
    private $ff_token;

    /**
     * @var HttpClientController
     */
    private $client;


    public function __construct(Container $c)
    {
        $this->db = $c->get('db');
        $this->ff_server = $c->get('settings')['ff_server'];
        $this->ff_token = $c->get('settings')['ff_token'];
        $this->ff_photo_url = $c->get('settings')['ff_photo_url'];
        $this->client = $c->get('HttpClientController');
    }

    public function getCameraList()
    {
        $url = $this->ff_server.'/v0/camera';
        $json = $this->sendRequest($url, "GET");
        return $json;
    }

    public function checkFace($camera, $data)
    {
        //get face box
        $bbox = $data["bbox"];
        $bbox = str_replace(['[', ']'], '', $bbox);
        $bbox = explode(",", $bbox);

        //	Fields to log:
        ob_start();
        var_dump($data);
        $vdump = ob_get_clean();
        file_put_contents("/var/www/html/logs/fields.log", "{$vdump}\r\n");

//        FILES to log:
        ob_start();
        var_dump($_FILES);
        $vdump = ob_get_clean();
        file_put_contents("/var/www/html/logs/files.log", "{$vdump}\r\n");

//      calculate face size
        $faceWidth = $bbox[2]-$bbox[0];
        $faceHeight = $bbox[3]-$bbox[1];

//        log face size
        ob_start();
        echo "cam_id = {$data['cam_id']}\r\nfaceWidth: {$faceWidth}; faceHeight: {$faceHeight}";
        $vdump = ob_get_clean();
        file_put_contents("/var/www/html/logs/faceSize.log", "{$vdump}\r\n");


//	    Save image to disk
        $saved = SaveImage($_FILES['photo']['tmp_name'], "user_photo");
	$saved = "http://localhost".GetImageURL($saved, 'user_photo');

        //If image upload
        if($saved != false) {
            $identifyFaceResult = $this->identifyFace($camera, $saved, $data);
            $personID = $identifyFaceResult['personID'];

            $activeUser = false;
            if($faceWidth==0 && $faceHeight==0) {
                //get image from snapshot
                $faceWidth = $identifyFaceResult['faceWidth'];
                $faceHeight = $identifyFaceResult['faceHeight'];;
            }
            if($faceWidth>=$camera->face_min_width || $faceHeight >=$camera->face_min_height)
                $activeUser = true;
            

            if($activeUser) {
                file_put_contents("/var/www/html/logs/active.log", "{$personID}\r\n");
                $this->activeUser($camera, $personID, $data);
            } else {
                file_put_contents("/var/www/html/logs/passive.log", "{$personID}\r\n");
                $this->passiveUser($personID, $data);
            }
        } else {
            //If image don't upload
            $json = [];
            $json['status'] = 'error';
            $json['message'] = 'image don\'t upload';
        }
        file_put_contents('/var/www/html/logs/checkedFaceResult.log', "personID: {$personID}\r\nfaceWidth: {$faceWidth}\r\nfaceiHeight: {$faceHeight}");
        return $json;
    }


    /*
     * Return: personID
     */
    public function identifyFace($camera, $photoURL = null, $data = null)
    {
        $url = $this->ff_server.'/v0/identify';
        //Prepare request to send in FFace
        if(!isset($data['cam_id'])) $data['cam_id'] = null;
        if(!isset($data['detectorParams'])) $data['detectorParams'] = null;
        if(!isset($data['bbox'])) $data['bbox'] = null;

        $fields = array(
            'photo' => urlencode($photoURL),
            'cam_id' => urlencode($data['cam_id']),
            'detectorParams' => urlencode($data['detectorParams']),
            'bbox' => $data['bbox'],
            'threshold' => '0.75',
        );
        $json = $this->sendRequest($url, "POST", $fields);

ob_start();
var_dump($json);
$jsonDump = ob_get_clean();
file_put_contents('/var/www/html/logs/identifyRequestInfo.log', "url: {$url}\r\nphoto: {$photoURL}\r\njson: {$jsonDump}");

        //get first face key
        if(isset($json['results'])) {
            $first_key = key($json['results']);
//            First Key to log:
            file_put_contents("/var/www/html/logs/firstKey.log", "{$first_key}\r\n");
            $addImage = false;
            $currentID = null;
            $personID = null;


            //get face box
            $bbox = $first_key;
            $bbox = str_replace(['[', ']'], '', $bbox);
            $bbox = explode(",", $bbox);

            //calculate face size
            $faceWidth = $bbox[2]-$bbox[0];
            $faceHeight = $bbox[3]-$bbox[1];

            if (isset($json['results'][$first_key][0]['face']['id'])) {
                //face find in base
                $personID = $json['results'][$first_key][0]['face']['person_id'];
                $currentID = $json['results'][$first_key][0]['face']['id'];
                if ($json['results'][$first_key][0]['face']['detector_info'] != null) {
                    //finded face add by videostream
                    if ($data["detectorParams"] != null) {
                        //current face find by videostream
                        $currentScoreDir = abs($json['results'][$first_key][0]['face']['detector_info']['direction_score']);
                        $currentScore = abs($json['results'][$first_key][0]['face']['detector_info']['score']);

                        $score = json_decode($data["detectorParams"], true);
                        $newScoreDir = abs($score['direction_score']) + 0.2;
                        $newScore = abs($score['score']) + 0.2;

                        if (($newScore < $currentScore || $newScoreDir < $currentScoreDir) && $faceWidth>=$camera->face_min_width && $faceHeight>=$camera->face_min_height) {
                            //if current face score is better
                            $addImage = true;
                        }
                    } else if($faceWidth>=$camera->face_min_width && $faceHeight>=$camera->face_min_height) {
                        //current face find by snapshot & have good size
                        //$addImage = true;
                    }
                }
            } else if ($json['code'] == null && $faceWidth>=$camera->face_min_width && $faceHeight>=$camera->face_min_height) {
                //face not found && face size is good
                file_put_contents("/var/www/html/logs/user.log", "UnknownUser\r\n");
                $addImage = true;
            } else {
//                if FF return error code
                file_put_contents("/var/www/html/logs/user.log", "ServerError: {$json['code']} - {$json['reason']}");
            }

            if ($addImage) {
                //TODO: Александр - send image to terminal
                $json = $this->addFace($photoURL, $data);
                $jsonPersonID = $json['results'][0]['person_id'];
                //after add need to check to add && check to delete old face
                if(isset($jsonPersonID) && isset($currentID)) {
                    $this->deleteFace($currentID);
                    $personID = $jsonPersonID;
                }
            }

            ob_start();
            var_dump($personID);
            $vdump = ob_get_clean();
            file_put_contents("/var/www/html/logs/personID.log", "{$vdump}\r\n");
        } else {
	    ob_start();
	    var_dump($json);
	    $vdump = ob_get_clean();
            $personID = "false";
        }

        file_put_contents('/var/www/html/logs/identifyFaceResult.log', "bbox: {$first_key}\r\npersonID: {$personID}\r\nfaceWidth: {$faceWidth}\r\nfaceiHeight: {$faceHeight}");
        return ['personID' => $personID, 'faceWidth' => $faceWidth, 'faceHeight' => $faceHeight];
    }



    public function addFace($photoURL = null, $data = null) {
        $url = $this->ff_server.'/v0/face';
        $fields = array(
            'photo' => urlencode($photoURL),
            'cam_id' => urlencode($data['cam_id']),
            'detectorParams' => urlencode($data['detectorParams']),
            'bbox' => urlencode($data['bbox']),
            'meta' => urlencode('Unknown User')
        );
        $json = $this->sendRequest($url, "POST", $fields);
        ob_start();
        var_dump($json);
        $vdump = ob_get_clean();
        file_put_contents('/var/www/html/logs/addFaceResult.log', $vdump);
        return $json;
    }



    public function deleteFace($id = null) {
        $json = null;
        if(isset($id)) {
            $url = $this->ff_server.'/v0/face/id/'.$id.'/';
            $json = $this->sendRequest($url, "DELETE");
        }
        return $json;
    }

    public function sendRequest($url, $type, $fields="")
    {
        $fields_string = "";
	if($fields!="")
        	foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
        rtrim($fields_string, '&');

        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, count($fields));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            $this->ff_token
        ));

        ob_start();
        $result = curl_exec($ch);
        $json = ob_get_clean();
        curl_close($ch);
        $json = json_decode($json, true);
        return $json;
    }

    public function activeUser($camera, $person_id, $data)
    {
        //Update camera ff_person_id
        /*$sql = "UPDATE filial_camera SET ff_person_id=:ff_person_id WHERE id=:id";
        $con = $this->db->prepare($sql);
        $con->bindParam('ff_person_id', $person_id);
        $con->bindParam('id', $camera->id);
        $con->execute();*/

        //Выборка пользователя
        $user = [];
        $user = array("userId" => false, "personId" => $person_id);
        if(isset($person_id)) {
            $sql = "SELECT * FROM users WHERE ff_person_id=:ff_person_id";
            $con = $this->db->prepare($sql);
            $con->bindParam('ff_person_id', $person_id);
            $con->execute();
            if ($con->rowCount() >= 1) {
                $user = $con->fetchObject();
                $user = array("userId" => $user->id, "personId" => $person_id);
            }
        }


        //TODO: uncomment for work stantion
        //Если опознаный user != тому, который в БД
        if ($camera->ff_person_id != $person_id) {
            //Выбор терминалов с данной камерой
            $sql = "SELECT filial_equipment.*, filial_terminal.camera_id
FROM filial_equipment
LEFT JOIN filial_terminal ON filial_terminal.equipment_id=filial_equipment.id
WHERE filial_equipment.active=true AND filial_terminal.camera_id='{$camera->equipment_id}' ";

            $con = $this->db->prepare($sql);
            $con->execute();
            $terminals = $con->fetchAll(PDO::FETCH_OBJ);
            for($i=0;$i<count($terminals);$i++)
            {
                $this->client->SendTerminal($terminals[$i], 'visitor', $user);
            }
        }


        //Выбор проходных с данной камерой
        $sql = "SELECT filial_equipment.*, filial_turnstiles.camera_in_id
FROM filial_equipment 
LEFT JOIN filial_turnstiles ON filial_equipment.id = filial_turnstiles.equipment_id 
WHERE filial_equipment.active=true AND filial_turnstiles.camera_in_id=:camera_id";
        $con = $this->db->prepare($sql);
        $con->bindParam('camera_id', $camera->equipment_id);
        $con->execute();
        if ($con->rowCount() >= 1) {
            //Камера стоит в турникете на вход
            $turnstiles = $con->fetchAll(PDO::FETCH_OBJ);
            if($user['userId']!=0) {
                //Известный пользователь
                //Проверяем есть ли у него сегодня пропуск
                $sql = "SELECT user_access.*, hearing.hdate
FROM user_access 
LEFT JOIN hearing ON hearing.id=user_access.hearing_id
WHERE user_id=:user_id AND hdate=(SELECT CURRENT_DATE)";
                $con = $this->db->prepare($sql);
                $con->bindParam('user_id', $user['userId']);
                $con->execute();
                if ($con->rowCount() >= 1) {
                    //TODO: Игорь + Александр - У посетителя есть пропуск (нужно действие?) - нет!
                } else {
                    //TODO: Александр - У посетителя нет пропуска (тест нужен)
                    for($i=0;$i<count($turnstiles);$i++)
                        $this->client->SendTurnstile($turnstiles[$i], 0, 2);
                }
            } else {
                //Пользователя нет в БД, посылаем голосовую команду, чтобы он зарегался
                for($i=0;$i<count($turnstiles);$i++)
                    $this->client->SendTurnstile($turnstiles[$i], 0, 1);
            }
        }
    }

    public function passiveUser($person_id, $data)
    {
        if(isset($person_id)) {
            $sql = "SELECT * FROM users_search WHERE filial_id=:filial_id AND status=false AND person_id=:person_id";
            $con = $this->db->prepare($sql);
            $con->bindParam('filial_id', $_SESSION['id']);
            $con->bindParam('person_id', $person_id);
            $con->execute();
            if($con->rowCount()>=1) {
                //TODO: notification find person
            }
        }
    }
}
