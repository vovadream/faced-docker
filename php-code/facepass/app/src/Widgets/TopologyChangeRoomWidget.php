<?php

namespace App\Widgets;

use App\Models\TopologyModel;

class TopologyChangeRoomWidget extends Widget
{

    public $view = 'menu';

    public $parent = 0;

    public $workerChangeId = 0;


    public function run()
    {

        $model = $this->container->TopologyModel;

        $menu = $model->treeChangeRoom($this->parent);

        # Генерируем шаблон для отображения
        return tpl('/topology/views/widget/change-item', ['menu' => $menu, 'workerChangeId' => $this->workerChangeId] );
    }
}