<?php

namespace App\Controllers\Ui;

use App\Controllers\Controller;
use App\Models\TopologyModel;
use App\Models\WorkScheduleModel;
use Slim\Container;
use Slim\Http\Request as Request;
use Slim\Http\Response as Response;

use PDO;

class WorkScheduleController extends Controller
{


    public function actionIndex(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');
        $type = $request->getAttribute('type');
        $category=$request->getAttribute('category');

        $currentYear = date('Y');
        $date = \DateTime::createFromFormat('d-m-Y', "01-01-{$currentYear}");
        $currentDate = \DateTime::createFromFormat('d-m-Y',  date('d-m-Y'));

        $model = $this->WorkScheduleModel;

        $topologyModel = $this->TopologyModel;
        $topology = $topologyModel->tree();

        $data = layout('work-schedule/index', [
            'id' => $id,
            'type' => $type,
            'category' => $category,
            'date' => $date,
            'currentYear' => $currentYear,
            'currentDate' => $currentDate
        ]);

        return $response->getBody()->write($data);
    }

    /*
    * Вывод кнопки создания графика
    * Return: JSON
    */

    public function showCreateWorkScheduleControl(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');

        $html = tpl('work-schedule/button', [
            'id' => $id
        ]);
        $data['status'] = 'success';
        $data['div'] = 'workschedulebutton';
        $data['html'] = $html;
        $response = $response->withJson($data);
        return $response;
    }


    /*
    * Вывод вкладки графика работ для конкретного элемента топологии
    * Return: HTML
    */

    public function showWorkScheduleControl(Request $request)
    {
        $html = tpl('header');
        $id = $request->getAttribute('id');
        $type = $request->getAttribute('type');
        $category=$request->getAttribute('category');
        $html .= $this->workScheduleView($id,$type,$category);
        $html .= tpl('footer');
        return $html;
    }

    /*
    * Вывод формы выбора дат
    * Return: JSON
    */

    public function showWorkScheduleStartEndDateControl(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');
        $html = $this->showWorkScheduleStartEndDateView($id);
        $data['status'] = 'success';
        $data['div'] = 'workschedulestartenddate';
        $data['html'] = $html;
        $response = $response->withJson($data);
        return $response;
    }

    /*
     * Вывод календаря в заданном диапазоне дат
     * Return: JSON
     */

    public function showWorkScheduleCalendarControl(Request $request, Response $response)
    {
        $start = $request->getAttribute('start');
        $end = $request->getAttribute('end');
        $html = $this->showWorkScheduleCalendarView($start,$end);
        $data['status'] = 'success';
        $data['div'] = 'workschedulecalendar';
        $data['html'] = $html;
        $response = $response->withJson($data);
        return $response;
    }

    /*
    * Создание рабочего графика
    * Return: JSON
    */

    public function createWorkScheduleControl(Request $request, Response $response)
    {
        $model = $this->WorkScheduleModel;
        $data = $request->getParsedBody();
        $id = $request->getAttribute('id');

        $data = $model->createWorkScheduleModel($data,$id);
        $response = $response->withJson($data);
        return $response;
    }

    /*
    * Изменение шаблона для услуги
    * Return: JSON
     * TODO: Функция дублируется в топологии(В контроллере как и её гет аналог, возможно удалить)
    */

    public function updateHearingWeekTemplateControl(Request $request, Response $response)
    {
        $model = $this->WorkScheduleModel;
        $data = $request->getParsedBody();

        $hearing_id = $request->getAttribute('hearing_id');
        $data = $model->updateHearingWeekTemplateModel($data,$hearing_id);
        $response = $response->withJson($data);
        return $response;
    }

    /*
    * Отображение формы изменения шаблона для услуги
    * Return: JSON
    */

    public function showUpdateHearingWeekTemplateControl(Request $request, Response $response)
    {
        $hearing_id = $request->getAttribute('hearing_id');

        $model = $this->WorkScheduleModel;
        $hearing = $model->sendQuery("SELECT * FROM filial_rooms_hearing WHERE id='{$hearing_id}'");
        $day_types =array('1'=>"Рабочий",'2'=>"Сокращенный",'3'=>"Выходной");

        $html = tpl('work-schedule/edit-tmp', [
            'hearing' => $hearing,
            'day_types' => $day_types
        ]);

        $data['status'] = 'success';
        $data['div'] = 'popup';
        $data['html'] = $html;
        $response = $response->withJson($data);
        return $response;
    }

    public function showWorkScheduleCalendarView($start=null,$end=null)
    {
        $HTML = "";
        $startArray = explode("-", $start);
        $endArray=explode("-", $end);
        $startMonth = $startArray[1];
        $endMonth = $endArray[1];
        if ($startMonth <=$endMonth)
        {
            $year=date('Y');
            for ($i=(int)$startMonth;$i<=$endMonth;$i++)
            {
                $HTML .="<input type='month' value='{$year}-";
                if ($i<10) $HTML .="0";
                $HTML .="{$i}'>";
            }
        }
        return $HTML;
    }

    public function showDayTypesOnWeekFormView($hearing_id=null)
    {


       return $HTML;
    }

    public function showWorkScheduleStartEndDateView($id=null)
    {



        //if ($id==1)
        //{

        //}
        return $HTML;
    }


}