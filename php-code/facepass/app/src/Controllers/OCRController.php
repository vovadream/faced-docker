<?php

namespace App\Controllers;

use \Slim\Container;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Slim\Http\Response as Response;
use \App\Models\OCRModel;

/**
 * Для отображения страниц
 * Class PageController
 * @package App\Controllers
 */
class OCRController
{
    /**
     * @var settings
     * @var OCRModel
     */
    private $settings;
    private $model;

    public function __construct(Container $c)
    {
        $this->settings = $c->get('settings');
        $this->model = $c->get('OCRModel');
    }

    public function ocrManualPageController(Request $request, Response $response)
    {
        $data['web_path'] = $this->settings['web_path'];
        $HTML = tpl('ocr-manual', $data);
        $response->write($HTML);
        return $response;
    }

    public function processingController(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        if(basename($_FILES['userfile']['name'])!="") {
            $saved = SaveImage($_FILES['userfile']['tmp_name'], "documents");
            if ($saved != false) {
                //TODO: more doctypes
                $data['doctype'] = 'PASSPORT_RF';
                $saved = $this->settings['path_to_core_uploads'].'documents/'.$saved;
                $data = $this->model->processing($saved, $data, true);
            }
            else {
                $data['status'] = 'error';
                $data['message'] = "Ошибка загрузки файла.";
            }
        } else {
            $data['status'] = 'error';
            $data['message'] = "Вы не выбрали файл.";
        }
        $response = $response->withJson($data);
        return $response;
    }
}