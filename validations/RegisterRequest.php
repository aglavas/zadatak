<?php

namespace Validations;

use Illuminate\Http\Request;
use Hazzard\Validation\Validator;
use Illuminate\Http\Response;

class RegisterRequest
{
    
    /**
     * Validation for POST register
     *
     * @param Request $request
     * @return Response
     */


    public static function validate(Request $request)
    {
        $validator = Validator::make($request->all() ,
            [
                'display_name' => 'required',
                'nickname' => 'required|unique:users',
                'password' => 'required|confirmed',
                'email' => 'required|email|unique:users',
                'terms' => 'required|boolean|in:1'
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