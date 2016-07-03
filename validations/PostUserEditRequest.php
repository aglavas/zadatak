<?php

namespace Validations;

use Illuminate\Http\Request;
use Hazzard\Validation\Validator;
use Illuminate\Http\Response;

class PostUserEditRequest
{
    /**
     * Validation for POST user/{id}/edit
     *
     * @param Request $request
     * @return Response
     */

    public static function validate(Request $request)
    {
        $validator = Validator::make($request->all() ,
            [
                'cover_image' => 'image',
                'profile_image' => 'image'
            ]
        );

        if($validator->fails())
        {
            $error = $validator->messages();
            $message = $error->get(key($validator->failed()));
            return new Response(["status" => "error","message"=>$message[0]],422);
        }

    }

}