<?php

namespace Objects;

class TimelineLinksObject
{
    public $show;

    public function __construct($id)
    {
        //Link for status
        $this->show = $_SERVER['HTTP_HOST'] . "/statuses/" . $id;
    }
}