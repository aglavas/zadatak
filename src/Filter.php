<?php

namespace Src;

use Illuminate\Http\Request;

class Filter
{
    //class for filtering request, it is used for optional editing. Eloquent doesn't do it on it's own so it must be implemented.

    public static function UserEdit(Request $request)
    {
        return $request->only(['display_name','description', 'cover_image','profile_image']);
    }
}