<?php

namespace App\Widgets;

use App\Models\TopologyModel;

class TopologyMenuWidget extends Widget
{

    public $view = 'menu';

    public $parent = 0;

    public function renderSubMenu($id, $type)
    {
        $model = $this->container->TopologyModel;



        $html = '';
        if ($type == TopologyModel::TYPE_FLOOR) {
            $html =  tpl('/topology/views/widget/submenu/_groupRoomsSubmenu',
                ['id' => $id]
            );
        }

        if ($type == TopologyModel::TYPE_DEPARTAMENT) {
            $parentId = $model->getParentId($id, 'departament');

            $html =  tpl('/topology/views/widget/submenu/_departamentSubmenu',
                ['id' => $id, 'parentId' => $parentId]
            );
        }

        if ($type == TopologyModel::TYPE_ROOM) {

            $parentId = $model->getParentId($id, 'room');

            $html =  tpl('/topology/views/widget/submenu/_roomSubmenu',
                ['id' => $id, 'parentId' => $parentId]
            );
        }

        if ($type == TopologyModel::TYPE_WORKER) {
            $parentId = $model->getParentId($id, 'worker');

            $html =  tpl('/topology/views/widget/submenu/_workerSubmenu',
                ['id' => $id, 'parentId' => $parentId]
            );
        }

        if ($type == TopologyModel::TYPE_HEARING) {
            $parentId = $model->getParentId($id, 'hearing');

            $html =  tpl('/topology/views/widget/submenu/_hearingSubmenu',
                ['id' => $id, 'parentId' => $parentId]
            );
        }

        return $html;
    }



    public function run()
    {
        $model = $this->container->TopologyModel;

        $menu = $model->tree($this->parent);

        foreach ($menu as $k => $item) {
            $menu[$k]['htmlCode'] = $this->renderSubMenu($item['id'], $item['type']);
        }

        $submenuView = $this->view === 'menu' ? 'menu' : 'menu-schedule';

        # Генерируем шаблон для отображения
        return tpl('/topology/views/widget/'.$submenuView, [
            'menu' => $menu,
            'view' => $this->view
            ] );
    }
}