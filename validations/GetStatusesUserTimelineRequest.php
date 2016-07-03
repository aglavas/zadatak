<?php

namespace Validations;

use Illuminate\Http\Request;
use Hazzard\Validation\Validator;
use Illuminate\Http\Response;

class GetStatusesUserTimelineRequest
{
    /**
     * Validation for GET user/search
     *
     * @param Request $request
     * @return Response
     */

    public static function validate(Request $request)
    {
        $validator = Validator::make($request->all() ,
            [
                'user_id' => 'required|integer',
                'limit' => 'required|integer|min:1',
                'offset' => 'required|integer|min:0'
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