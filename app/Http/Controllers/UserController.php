<?php

namespace App\Http\Controllers;

use App\Http\Resources\BaseResponse;
use App\Http\Resources\SearchResponse;
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

        $response = [
            "succeed" => true,
            "messages" => [],
            "data" => [
                "items" => $users->items(),
                'total_item' => $users->total(),
                'current_page' => $users->currentPage(),
                'page_size' => $users->perPage(),
                'total_pages' => $users->lastPage(),
                'has_previous_page' => $users->currentPage() > 1,
                'has_next_page' => $users->currentPage() < $users->lastPage()
            ]
        ];

        return response()->json($response);
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
        return $user->load('videos')->loadCount('videos');
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

        $search_response = new SearchResponse($videos);
        $base_response = new BaseResponse(true, [], $search_response->toArray());

        return response()->json($base_response->toArray());
    }

    public function getUserComments(Request $request, User $user)
    {
        $page_size = $request->input('page_size', 10);

        $comments = Comment::with(['user', 'video'])->where('user_id', $user->id)->filter($request)->paginate($page_size);

        $search_response = new SearchResponse($comments);
        $base_response = new BaseResponse(true, [], $search_response->toArray());

        return response()->json($base_response->toArray());
    }
}
