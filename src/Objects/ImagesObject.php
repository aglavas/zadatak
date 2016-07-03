<?php

namespace Objects;

use Src\Helpers;

class ImagesObject
{
    public $profile, $cover;

    public function __construct($id)
    {
        //Create images links

        $this->profile = Helpers::path() ."Storage\\" . $id . "\\Profile\\profile.jpg";
        $this->cover = Helpers::path() ."Storage\\" . $id . "\\Cover\\cover.jpg";
    }
}