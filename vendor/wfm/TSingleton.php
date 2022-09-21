<?php

namespace wfm;

trait TSingleton
{

    private static ?self $instance = null;   // ?self - такая запись говорит что здесь может быть либо экземпляр класса или же null (то что мы дальше указали)

    private function __construct() {}

    public static function getInstance(): static
    {
        return static::$instance ?? static::$instance = new static(); // данная функция возврашает обьект если он есть в $instance а если он отсутсвует то она записывает в него обьект и при последуйшим обрашений к нему там будет обьект
    }

}