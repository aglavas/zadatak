<?php
namespace Framework\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Model\StatusesModel;
use Model\UserModel;
use Illuminate\Http\Response;
use Objects\PostDataObject;
use Objects\StatusesTimelineObject;
use Src\Auth;
use Src\Paginator;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Validations\GetStatusesHomeRequest;
use Validations\GetStatusesUserTimelineRequest;
use Validations\PostStatusesCreateRequest;

class StatusesController
{

    /**
     * Creates new status.
     *
     * @param Request $request
     * @return Response
     */


    public function PostStatusesCreate(Request $request)
    {
        // validates request

        $validation = PostStatusesCreateRequest::validate($request);

        if($validation instanceof Response)
        {
            return $validation;
        }

        //authenticates user

        try{
            $user_id = Auth::Check($request);
        }catch (AccessDeniedHttpException $e)
        {
            return new Response(["status" => "error", "message" => "Forbidden."], 403);
        }

        //find user that is authenticates

        $model = new UserModel();

        $user = $model->find($user_id);

        //creates status

        if($user->statuses()->create(["text" => $request->input('text')]))
        {
            return new Response(["status" => "success", "message" => "Status created."], 201);
        }

        return new Response(["status" => "error", "message" => "There was error."], 500);
    }


    /**
     * Finds status by id
     *
     * @param Request $request
     * @param $route_info
     * @return Response
     */

    public function getStatusesById(Request $request,$route_info)
    {

        $model = new StatusesModel();

        //Tries to find specified status

        try {
            $status = $model->findOrFail($route_info['id']);
        }catch (ModelNotFoundException $e)
        {
            return new Response(["status" => "error", "message" => "Status not found."], 404);
        }

        //Status found

        return new Response(["status" => "success", "message" => "Status found.", "result" => new PostDataObject($status) ], 200);
    }



    /**
     * Gets all statuses, pagination using offset and limit
     *
     * @param Request $request
     * @return Response
     */

    public function getStatusesHome(Request $request)
    {
        // validates request

        $validation = GetStatusesHomeRequest::validate($request);

        if($validation instanceof Response)
        {
            return $validation;
        }

        $model = new StatusesModel();

        //Count how many statuses there is

        $total = $model->all()->count();

        //Get statuses

        $all = $model->all();


        $offset = $request->input('offset');
        $limit = $request->input('limit');

        //Get only specified data

        $data = $all->slice($offset*$limit, $offset*$limit+$limit);

        //If page out of scope

        if($data->isEmpty())
        {
            return new Response(["status" => "error", "message" => "Out of scope."], 400);
        }


        //List all objects for output

        $objects = array();
        foreach ($data as $key => $value)
        {
            $objects[] = new PostDataObject($value);
        }

        //Output is paginated by Paginator class

        return new Response(["status" => "success", "message" => "OK", "results" => $objects, "links" => Paginator::paginate($request->input('offset'),$request->input('limit'),$total), "total" => $total], 200);
    }




    /**
     * Gets all statuses by specific user
     *
     * @param Request $request
     * @return Response
     */

    public function getStatusesUserTimeline(Request $request)
    {

        //Validates request

        $validation = GetStatusesUserTimelineRequest::validate($request);

        if($validation instanceof Response)
        {
            return $validation;
        }

        //Try to find specified user

        $model = new UserModel();
        try {
            $user = $model->findOrFail($request->input('user_id'));
        }catch (ModelNotFoundException $e)
        {
            return new Response(["status" => "error", "message" => "User not found."], 404);
        }

        //Gets user's statuses

        $user_statuses = $user->statuses()->get();

        //Counts user's statuses

        $total = $user->statuses()->count();

        if($user_statuses->isEmpty())
        {
            return new Response(["status" => "error", "message" => "User has no statuses."], 400);
        }

        $offset = $request->input('offset');
        $limit = $request->input('limit');

        //Get only specified results (creates pages)

        $paginated = $user_statuses->slice($offset*$limit, $offset*$limit+$limit);

        if($paginated->isEmpty())
        {
            return new Response(["status" => "error", "message" => "Out of scope."], 400);
        }

        //Format output

        $results = array();
        foreach ($paginated as $key => $value)
        {
            $results[] = new StatusesTimelineObject($value, $user);
        }

        //Paginator class paginates results

        return new Response(json_encode(["status" => "success", "message" => "Found.", "results" => $results, "links"=> Paginator::paginate($request->input('offset'),$request->input('limit'),$total),"total" => $total],JSON_PRETTY_PRINT), 200);


    }


}
