<?php

namespace App\Http\Controllers;

use App\Enums\RecordStatusConstant;
use App\Models\Video;
use App\Http\Requests\StoreVideoRequest;
use App\Http\Requests\UpdateVideoRequest;
use App\Http\Resources\BaseResponse;
use App\Http\Resources\SearchResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page_size = $request->input('page_size', 10);
        $guard = Auth::guard(); 
        $userId = $guard->id(); 

        // method scopeFilter is in model file
        $videos = Video::with([
                    'user',
                    'history' => fn($query) => $query->orderBy('created_at', 'desc')->where('user_id', $userId),
                ])
            ->withCount(['comments', 'histories'])
            ->filter($request)
            ->paginate($page_size);

        $search_response = new SearchResponse($videos);
        $base_response = new BaseResponse(true, [], $search_response->toArray());

        return response()->json($base_response->toArray());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVideoRequest $request)
    {
        $validated = $request->validated();

        $imagePath = $request->file('image')->store('images', 'public');
        $imageUrl = Storage::url($imagePath);
        $validated['image'] = $imageUrl;

        $videoPath = $request->file('video')->store('videos', 'public');
        $videoUrl = Storage::url($videoPath);
        $validated['video'] = $videoUrl;

        $validated["duration"] = $request->input('duration');
        $request->user()->videos()->create($validated);

        $base_response = new BaseResponse(true, ['Video berhasil dibuat.'], null);

        return response()->json($base_response->toArray());
    }

    /**
     * Display the specified resource.
     */
    public function show(Video $video)
    {
        $guard = Auth::guard(); 
        $userId = $guard->id();  

        $existing_video = $video
            ->load([
                    'history' => fn($query) => $query->orderBy('created_at', 'desc')->where('user_id', $userId),
                    'user', 
                    'comments' => fn($query) => $query->orderBy('created_at', 'desc')
                                                ->where('record_status', RecordStatusConstant::active)
                                                ->limit(1), // only takes one comment
                ])
            ->loadCount(['comments', 'histories']);

        $first_comment = $video->comments->first();

        // load the user of the first comment
        if ($first_comment) {
            $first_comment->load('user');
        }

        $response = [
            'succeed' => true,
            'messages' => [],
            'data' => $existing_video
        ];

        return response()->json($response);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVideoRequest $request, Video $video)
    {
        $validated = $request->validated();

        if ($request->file('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
            $imageUrl = Storage::url($imagePath);
            $validated['image'] = $imageUrl;
        }

        if ($request->file('video')) {
            $videoPath = $request->file('video')->store('videos', 'public');
            $videoUrl = Storage::url($videoPath);
            $validated['video'] = $videoUrl;
        }

        $validated["duration"] = $request->input('duration');
        $video->update($validated);

        $response = [
            'succeed' => true,
            'messages' => ['Video berhasil diupdate'],
            'data' => null
        ];

        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Video $video)
    {
        // normally delete data
        // $video->delete();

        $video->update(['record_status' => RecordStatusConstant::deleted]);

        $response = [
            'succeed' => true,
            'messages' => ['Video berhasil dihapus'],
            'data' => null
        ];

        return $response;
    }
}
