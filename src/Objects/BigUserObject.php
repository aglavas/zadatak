<?php

namespace Objects;

use Illuminate\Database\Eloquent\Model;

class BigUserObject
{
    public $id, $display_name, $nickname, $email, $description, $followers_count, $followed_count, $images ,$links;

    public function __construct(Model $model)
    {

        //Populate object

        foreach($this as $key => $value)
        {
            if(isset($model->{$key}))
            {
                $this->{$key} = $model->{$key};
            }

            $this->followed_count = $model->follow()->count();
            $this->followers_count = $model->followers()->count();

            $this->images = new ImagesObject($this->id);

            $this->links  = new LinksObject($this->id);

        }
    }
}