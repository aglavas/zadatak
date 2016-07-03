<?php

namespace Objects;

use Illuminate\Http\Request;

class PaginateLinksObject
{
    public $self, $next, $prev;

    public function __construct($offset, $limit, $last = false, $first = false)
    {

        //Construct pagination links

        $this->self = $_SERVER['HTTP_HOST'] .  Request::capture()->getRequestUri();
        //Check if first page
        if($first == false)
        {
            $new = $offset-1;
            $this->prev = $_SERVER['HTTP_HOST'] . str_replace("offset=".$offset,"offset=". $new,Request::capture()->getRequestUri());
        }
        //Check if last page
        if($last == false)
        {
            $new = $offset+1;
            $this->next = $_SERVER['HTTP_HOST'] . str_replace("offset=".$offset,"offset=" . $new,Request::capture()->getRequestUri());;
        }

        return $this;

    }
}