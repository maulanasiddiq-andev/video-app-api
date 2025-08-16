<?php

namespace App\Http\Controllers;

use App\Http\Resources\BaseResponse;
use App\Http\Resources\CommentResource;
use App\Http\Resources\SearchResponse;
use App\Http\Resources\UserResource;
use App\Http\Resources\VideoResource;
use App\Models\Comment;
use App\Models\User;
use App\Models\Video;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page_size = $request->input('page_size', 10);

        $users = User::withCount(['videos'])->paginate($page_size);

        $collection = UserResource::collection($users)->response()->getData(true);
        $search_response = new SearchResponse($collection);
        $base_response = new BaseResponse(true, [], $search_response->toArray());

        return response()->json($base_response->toArray());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $resource = new UserResource($user->loadCount('videos'));
        $base_response = new BaseResponse(true, [], $resource);
        return response()->json($base_response->toArray());
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getUserVideos(Request $request, User $user)
    {
        $page_size = $request->input('page_size', 10);

        $videos = Video::with('user')->where('user_id', $user->id)->filter($request)->paginate($page_size);

        $collection = VideoResource::collection($videos)->response()->getData(true);
        $search_response = new SearchResponse($collection);
        $base_response = new BaseResponse(true, [], $search_response->toArray());

        return response()->json($base_response->toArray());
    }

    public function getUserComments(Request $request, User $user)
    {
        $page_size = $request->input('page_size', 10);

        $comments = Comment::with(['user', 'video'])->where('user_id', $user->id)->filter($request)->paginate($page_size);

        $collection = CommentResource::collection($comments)->response()->getData(true);
        $search_response = new SearchResponse($collection);
        $base_response = new BaseResponse(true, [], $search_response->toArray());

        return response()->json($base_response->toArray());
    }
}
