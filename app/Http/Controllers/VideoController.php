<?php

namespace App\Http\Controllers;

use App\Enums\RecordStatusConstant;
use App\Http\Requests\StoreLikeRequest;
use App\Models\Video;
use App\Http\Requests\StoreVideoRequest;
use App\Http\Requests\UpdateVideoRequest;
use App\Http\Resources\BaseResponse;
use App\Http\Resources\CommentResource;
use App\Http\Resources\SearchResponse;
use App\Http\Resources\VideoResource;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page_size = $request->input('pageSize', 10);
        $guard = Auth::guard(); 
        $userId = $guard->id(); 

        // method scopeFilter is in model file
        $videos = Video::with([
                    'user',
                    'history' => fn($query) => $query->orderBy('created_at', 'desc')->where('user_id', $userId),
                ])
            ->withCount(['comments', 'histories'])
            ->record($request)
            ->filter($request)
            ->paginate($page_size);

        $collection = VideoResource::collection($videos)->response()->getData(true);
        $search_response = new SearchResponse($collection);
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
        if ($video->record_status == RecordStatusConstant::deleted) {
            throw new NotFoundHttpException();
        }

        $guard = Auth::guard(); 
        $userId = $guard->id();  

        $existing_video = $video
            ->load([
                    'user', 
                    'history' => fn($query) => $query->orderBy('created_at', 'desc')
                                                ->where('user_id', $userId)
                                                ->where('record_status', RecordStatusConstant::active),
                ])
            ->loadCount(['comments', 'histories']);

        $resource = $existing_video->toResource();
        $base_response = new BaseResponse(true, [], $resource);

        return response()->json($base_response->toArray());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVideoRequest $request, Video $video)
    {
        if ($video->record_status == RecordStatusConstant::deleted) {
            throw new NotFoundHttpException();
        }

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

        $base_response = new BaseResponse(true, ['Video berhasil diupdate'], null);

        return response()->json($base_response->toArray());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Video $video)
    {
        // normally delete data
        // $video->delete();
        if ($video->record_status == RecordStatusConstant::deleted) {
            throw new NotFoundHttpException();
        }

        $video->update(['record_status' => RecordStatusConstant::deleted]);
        $base_response = new BaseResponse(true, ['Video berhasil dihapus'], null);

        return response()->json($base_response->toArray());
    }

    public function getComments(Video $video, Request $request)
    {
        if ($video->record_status == RecordStatusConstant::deleted) {
            throw new NotFoundHttpException();
        }

        $page_size = $request->input('pageSize', 10);

        $comments = Comment::record($request)
                            ->filter($request)
                            ->where('video_id', $video->id)
                            ->with('user')
                            ->paginate($page_size);
        $collection = CommentResource::collection($comments)->response()->getData(true);
        $search_response = new SearchResponse($collection);
        $base_response = new BaseResponse(true, [], $search_response->toArray());

        return response()->json($base_response->toArray());
    }

    public function getLastComment(Video $video, Request $request)
    {
        if ($video->record_status == RecordStatusConstant::deleted) {
            throw new NotFoundHttpException();
        }

        $comment = Comment::record($request)
                            ->where('video_id', $video->id)
                            ->with('user')
                            ->latest()->first();
        $resource = new CommentResource($comment);
        $base_response = new BaseResponse(true, [], $resource);
        return response()->json($base_response->toArray());
    }

    public function likeVideo(Video $video)
    {
        $user = Auth::guard();

        if ($video->likes()->where('user_id', $user->id())->where('record_status', RecordStatusConstant::active)->exists()) {
            $video->likes()->update(['record_status' => RecordStatusConstant::deleted]);   
        } else {
            $like = [
                'record_status' => RecordStatusConstant::active,
                'user_id' => $user->id()
            ];
            
            $video->likes()->create($like);
        }

        return $video->load('likes.user');
    }
}
