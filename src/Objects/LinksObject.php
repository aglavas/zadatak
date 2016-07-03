<?php

namespace Objects;

class LinksObject
{
    public $profile, $user_timeline;

    public function __construct($id)
    {
        //Create links
        $this->profile = $_SERVER['HTTP_HOST'] . "/user/" . $id;
        $this->user_timeline = $_SERVER['HTTP_HOST'] . "/statuses/user-timeline/?limit=2&offset=0&user_id=" . $id;
    }
}