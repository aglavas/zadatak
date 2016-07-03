<?php
namespace Framework\Controllers;

use Illuminate\Http\Request;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Model\UserModel;
use Illuminate\Http\Response;
use Objects\BigUserObject;
use Validations\LoginRequest;
use Validations\RegisterRequest;
use Lcobucci\JWT\Builder;

class AuthController
{

    /**
     * Logs user in.
     *
     * @param Request $request
     * @return Response
     */

    public function Login(Request $request)
    {

        // validates request

        $validation = LoginRequest::validate($request);

        if($validation instanceof Response)
        {
            return $validation;
        }

        $model = new UserModel();

        //check if username exists

        if($user = $model->where('nickname',$request->input('nickname'))->first())
        {
            //check if credentials correct

            if(password_verify($request->input('password'),$user->password))
            {
                $signer = new Sha256();
                $token = (new Builder())
                    ->setId(str_random(40))  //Random string, so tokens are unique
                ->setIssuedAt(time()) // Configures the time that the token was issue (iat claim)
                ->setNotBefore(time() + 60) // Configures the time that the token can be used (nbf claim)
                ->setExpiration(time() + 3600) // Configures the expiration time of the token (nbf claim)
                ->set('uid', $user->id) // Configures a new claim, called "uid"
                ->sign($signer,"SDASDA43fSDAgdsaDSA")  //Token secret
                ->getToken(); // Retrieves the generated token

                //Creates BigUserObject

                $obj = new BigUserObject($user);
                return new Response(["status" => "success", "message" => "Ok.", "user" => $obj, "token" =>  (string)$token], 200);
            }
            //Wrong credentials
            return new Response(["status" => "error", "message" => "Wrong credentials.", "user" => "null"], 403);
        }
        //User does not exists
        return new Response(["status" => "error", "message" => "Wrong credentials.", "user" => "null"], 403);
    }

    /**
     * Registers a new user.
     *
     * @param Request $request
     * @return Response
     */

    public function Register(Request $request)
    {
        //Validates request

        $validation = RegisterRequest::validate($request);

        if($validation instanceof Response)
        {
            return $validation;
        }

        $user = new UserModel();

        //Creates new user

        if($user->create($request->all()))
        {
            return new Response(["status" => "success","message"=>"Success"],201);
        }
        return new Response(["status" => "error","message"=>"Error"],500);
    }
}
