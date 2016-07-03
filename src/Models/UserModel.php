<?php

namespace Model;

use Illuminate\Database\Eloquent\Model as Eloquent;


class UserModel extends Eloquent
{

    protected $table = 'users';

    protected $fillable = ['display_name','nickname','password','email','terms','description','cover_image','profile_image'];

    //password field mutator

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = crypt($value);
    }

    /**
     * Edits database fields
     *
     * @param $id
     * @param $data
     * @return mixed
     */

    public function edit($id,$data)
    {
        //filters empty fileds

        $finalData = array_filter($data, function ($k)
        {
            if (!empty($k)||strlen($k)>0) {
                return true;
            }

        });

        //updates filtered fields

        return $this->where("id","=",$id)->update($finalData);
    }

    //Relation to itself, following

    public function follow()
    {
        return $this->belongsToMany('Model\UserModel','following', 'follower_id','followed_id');
    }

    //Relation to itself, followed

    public function followers()
    {
        return $this->belongsToMany('Model\UserModel', 'following', 'followed_id', 'follower_id');
    }

    //Relation to statuses model

    public function statuses()
    {
        return $this->hasMany('Model\StatusesModel','user','id');
    }
}