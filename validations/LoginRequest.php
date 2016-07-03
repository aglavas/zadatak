<?php

namespace Validations;

use Illuminate\Http\Request;
use Hazzard\Validation\Validator;
use Illuminate\Http\Response;

class LoginRequest
{

    /**
     * Validation for POST login
     *
     * @param Request $request
     * @return Response
     */

    public static function validate(Request $request)
    {
        $validator = Validator::make($request->all() ,
            [
                'nickname' => 'required',
                'password' => 'required'
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