<?php

namespace App\Http\Controllers;

use App\Enums\RecordStatusConstant;
use App\Models\Like;
use App\Http\Requests\StoreLikeRequest;
use App\Http\Requests\UpdateLikeRequest;
use App\Http\Resources\BaseResponse;
use App\Http\Resources\LikeResource;
use App\Http\Resources\SearchResponse;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page_size = $request->input('pageSize', 10);

        $likes = Like::where('user_id', $request->user()->id)
                        ->record($request)
                        ->paginate($page_size);

        $collection = LikeResource::collection($likes)->response()->getData(true);
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
    public function store(StoreLikeRequest $request)
    {
        $query = Like::where('user_id', $request->user()->id)
                ->where('video_id', $request->video_id)
                ->where('record_status', RecordStatusConstant::active);

        if ($query->exists()) {
            $query->update(['record_status' => RecordStatusConstant::deleted]);
        } else {   
            $validated = $request->validated();
            $validated['user_id'] = $request->user()->id;
            
            Like::create($validated);
        }

        $base_response = new BaseResponse(true, [], null);

        return response()->json($base_response->toArray());
    }

    /**
     * Display the specified resource.
     */
    public function show(Like $like)
    {
        $resource = new LikeResource($like->load(['user', 'video']));
        $base_response = new BaseResponse(true, [], $resource);

        return response()->json($base_response->toArray());
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Like $like)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLikeRequest $request, Like $like)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Like $like)
    {
        //
    }
}
