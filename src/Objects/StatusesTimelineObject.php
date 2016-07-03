<?php

namespace Objects;

use Illuminate\Database\Eloquent\Model;
use Model\UserModel;

class StatusesTimelineObject
{
    public $id, $text, $user, $links, $created_at, $updated_at;

    public function __construct(Model $model, UserModel $userModel)
    {
        //Populate StatusesTimelineObject
        foreach($this as $key => $value)
        {
            if(isset($model->{$key}))
            {
                //use unix timestamp for time attributes
                if(($key == "created_at")||(($key == "updated_at")))
                {
                    $this->{$key} = $model->{$key}->timestamp;
                }
                else{
                    $this->{$key} = $model->{$key};
                }
            }
            $this->user =  new SmallUserObject($userModel);
            $this->links  = new TimelineLinksObject($this->id);

        }
    }
}