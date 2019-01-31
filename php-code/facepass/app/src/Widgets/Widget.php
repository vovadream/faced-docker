<?php
namespace App\Widgets;

abstract class Widget {

    protected $container;

    public function __construct($settings = [], $container)
    {
        $this->container = $container;
        if(!empty($settings)) {
            foreach ($settings as $key => $setting) {
                $this->{$key} = $setting;
            }
        }
    }

    /**
     * Выполнение виджета
     *
     * @return string
     */
    abstract public function run();
}