<?php

namespace Objects;

use Illuminate\Database\Eloquent\Model;

class SmallUserObject
{
    public $id, $display_name, $nickname, $email, $images, $links;

    public function __construct(Model $model)
    {
        //Populate SmallUserObject
        foreach($this as $key => $value)
        {
            if(isset($model->{$key}))
            {
                $this->{$key} = $model->{$key};
            }
        }
        $this->images = new ImagesObject($this->id);
        $links = new LinksObject($this->id);
        //Small user object doesn't use user_timeline
        unset($links->user_timeline);
        $this->links  = $links;
    }
}