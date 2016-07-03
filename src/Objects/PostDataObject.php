<?php

namespace Objects;

use Illuminate\Database\Eloquent\Model;
use Model\UserModel;

class PostDataObject
{
    public $id, $text, $user, $created_at, $updated_at, $links;

    public function __construct(Model $model)
    {
        //Populate PostDataObject

        foreach($this as $key => $value)
        {
            if(isset($model->{$key}))
            {
                $this->{$key} = $model->{$key};
            }
        }

        $this->created_at =  $this->created_at->timestamp;
        $this->updated_at =  $this->updated_at->timestamp;
        $this->links =  $_SERVER['HTTP_HOST'] . "/statuses/" . $model->id;
        $this->user = new SmallUserObject(UserModel::find($this->user));
    }
}