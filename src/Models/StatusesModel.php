<?php

namespace Model;

use Illuminate\Database\Eloquent\Model as Eloquent;


class StatusesModel extends Eloquent
{

    protected $table = 'statuses';

    protected $fillable = ['text','user'];


}