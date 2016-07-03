<?php

namespace Framework\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Model\FollowingModel;
use Model\UserModel;
use Src\Auth;
use Objects\BigUserObject;
use Src\Filter;
use Src\Helpers;
use Objects\SmallUserObject;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Validations\GetUserByIdRequest;
use Validations\GetUserSearch;
use Validations\PostUserEditRequest;

class UserController
{

    /**
     * Gets user information
     *
     * @param Request $request
     * @param $route_info
     * @return Response
     */

    public function GetUserById(Request $request, $route_info)
    {
        //Validation of request

        $validation = GetUserByIdRequest::validate($request);

        if($validation instanceof Response)
        {
            return $validation;
        }

        //New model

        $model = new UserModel();

        //Try to find specified user

        try {
            $user = $model->findOrFail($route_info['id']);
        }catch (ModelNotFoundException $e)
        {
            return new Response(["status" => "error", "message" => "User not found."], 404);
        }

        //user found, check for additional

        if($request->input('big_data') || (!$request->has('big_data')))
        {
            $obj = new BigUserObject($user);
        }else
        {
            $obj = new SmallUserObject($user);
        }
        return new Response(["status" => "success", "message" => "Ok.", "user" => $obj], 200);
    }


    /**
     * Method for editing user information.
     *
     * @param Request $request
     * @param $route_info
     * @return Response
     */

    public function PostUserEdit(Request $request,$route_info)
    {

        // Check if user authenitcated.

        try{
            $user_id = Auth::Check($request);
        }catch (AccessDeniedHttpException $e)
        {
            return new Response(["status" => "error", "message" => "Forbidden."], 403);
        }

        // Check if user is editing his information.

        if($user_id != $route_info['id'])
        {
            return new Response(["status" => "error", "message" => "Editing a wrong user."], 403);
        }

        //Validates request.

        $validation = PostUserEditRequest::validate($request);

        if($validation instanceof Response)
        {
            return $validation;
        }

        //Filter request (allowed key for editing, because keys are optional)

        $filtered = Filter::UserEdit($request);

        //Finds user

        $model = new UserModel();
        try {
            $user = $model->findOrFail($route_info['id']);
        }catch (ModelNotFoundException $e)
        {
            return new Response(["status" => "error", "message" => "User not found."], 404);
        }


        //Checks if there are images uploaded

        if(array_key_exists("cover_image",$_FILES) || array_key_exists("profile_image",$_FILES) )
        {
            //Checks and creates folder structure for uploading
            Helpers::createFolderStructure($user_id);

            //If uploaded
            if(array_key_exists("cover_image",$_FILES))
            {

                //Get path
                $cover_path = Helpers::path()."Storage/".$user_id."/Cover/cover.jpg";

                // Move uploaded file
                move_uploaded_file($_FILES['cover_image']['tmp_name'], $cover_path);

                //Add uploaded path
                $filtered["cover_image"] = $cover_path;
            }

            if(array_key_exists("profile_image",$_FILES))
            {
                //Get path
                $profile_path = Helpers::path()."Storage/".$user_id."/Profile/profile.jpg";
                // Move uploaded file
                move_uploaded_file($_FILES['profile_image']['tmp_name'], $profile_path);

                //Add uploaded path
                $filtered["profile_image"] = $profile_path;
            }

        }

        //Save edits to database
        $user->edit($route_info['id'],$filtered);
        return new Response(["status" => "success", "message" => "Action completed!"], 200);

    }

    /**
     * Searches through users, either lists all users or searches using display_name
     *
     * @param Request $request
     * @return Response
     */

    public function GetUserSearch(Request $request)
    {

        //Validates request.

        $validation = GetUserSearch::validate($request);

        if($validation instanceof Response)
        {
            return $validation;
        }

        $user = new UserModel();

        $offset = $request->input('offset');
        $limit = $request->input('limit');

        //If "query" parameter not supplied there will be full search of all users

        if(!$request->has("query"))
        {
            //Get all data
            $data = $user->all();
            //Paginate collection
            $paginated = $data->slice($offset*$limit, $offset*$limit+$limit);
            $objects = array();

            foreach ($paginated as $key => $value)
            {
                $objects[] = new SmallUserObject($value);
            }

            return new Response(["status" => "success", "results" => $objects], 200);
        }

        //If there is query string

        $data = $user->where("display_name",$request->input('query'))->get();
        //Paginate collection
        $paginated = $data->slice($offset*$limit, $offset*$limit+$limit);
        $objects = array();
        foreach ($paginated as $key => $value)
        {
            $objects[] = new SmallUserObject($value);
        }
        return new Response(["status" => "success", "results" => $objects], 200);
    }


    /**
     * User follows other user
     *
     * @param Request $request
     * @param $route_info
     * @return Response
     */

    public function PostUserFollow(Request $request,$route_info)
    {

        // Check if user authenitcated.

        try{
            $user_id = Auth::Check($request);
        }catch (AccessDeniedHttpException $e)
        {
            return new Response(["status" => "error", "message" => "Forbidden."], 403);
        }

        // Check if user is trying to follow himself.

        if($user_id == $route_info['id'])
        {
            return new Response(["status" => "error", "message" => "You cannot follow yourself."], 400);
        }

        $model = new UserModel();

        //Find authenticated user information

        $following = $model->find($user_id);

        //Check if user is already following other user

        if(FollowingModel::query()->where("followed_id",$route_info['id'])->where("follower_id",$user_id)->exists())
        {
            return new Response(["status" => "error", "message" => "User is already followed."], 400);
        }

        //Try to find followed user

        try {
            $user = $model->findOrFail($route_info['id']);
        }catch (ModelNotFoundException $e)
        {
            return new Response(["status" => "error", "message" => "User not found."], 404);
        }

        //Add following (relation)

        $following->follow()->attach($user->id);

        return new Response(["status" => "success", "message" => "Follower added."], 201);
    }

    /**
     * User unfollows other user
     *
     * @param Request $request
     * @param $route_info
     * @return Response
     */


    public function PostUserUnFollow(Request $request,$route_info)
    {

        // Check if user authenitcated.

        try{
            $user_id = Auth::Check($request);
        }catch (AccessDeniedHttpException $e)
        {
            return new Response(["status" => "error", "message" => "Forbidden."], 403);
        }

        // Check if user is editing his information.

        if($user_id == $route_info['id'])
        {
            return new Response(["status" => "error", "message" => "You cannot unfollow yourself."], 400);
        }



        $model = new UserModel();

        //Find authenticated user information

        $following = $model->find($user_id);

        //Check if user is really followed

        if(!FollowingModel::query()->where("followed_id",$route_info['id'])->where("follower_id",$user_id)->exists())
        {
            return new Response(["status" => "error", "message" => "User is not followed."], 400);
        }

        //Try to find followed user information

        try {
            $user = $model->findOrFail($route_info['id']);
        }catch (ModelNotFoundException $e)
        {
            return new Response(["status" => "error", "message" => "User not found."], 404);
        }

        //Unfollow user

        $following->follow()->detach($user->id);

        return new Response(["status" => "success", "message" => "Unfollow successful."], 200);

    }


}