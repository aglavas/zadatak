<?php

namespace Model;

use Illuminate\Database\Eloquent\Model as Eloquent;


class FollowingModel extends Eloquent
{

    protected $table = 'following';

    protected $fillable = ['follower_id','followed_id'];


}