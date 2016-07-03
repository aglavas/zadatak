<?php

namespace Src;

use Illuminate\Http\Request;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class Auth
{
    /**
     * Checks if request comes from authenticated user
     *
     * @param Request $request
     * @return mixed
     */

    public static function Check(Request $request)
    {
        //request must have authorization header with token inside

        if(!($request->header('Authorization') == null))
        {

            //parse token

            $token = (new Parser())->parse((string) $request->header('Authorization')); // Parses from a string

            //verify if token is ok and take user_id from token

            //Algorithm, secret key

            if($token->verify(new Sha256(),"SDASDA43fSDAgdsaDSA"))
            {
                //return user id of authenticated user
                return $token->getClaim('uid');
            }

            throw new AccessDeniedHttpException;
        }
        throw new AccessDeniedHttpException;

    }
}