<?php

namespace App\Http\Controllers;

use App\Enums\RecordStatusConstant;
use App\Models\Comment;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Resources\BaseResponse;
use App\Http\Resources\CommentResource;
use App\Http\Resources\SearchResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page_size = $request->input('pageSize', 10);

        $comments = Comment::record($request)->filter($request)->with('user')->paginate($page_size);

        $collection = CommentResource::collection($comments)->response()->getData(true);
        $search_response = new SearchResponse($collection);
        $base_response = new BaseResponse(true, [], $search_response->toArray());

        return response()->json($base_response->toArray());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommentRequest $request)
    {
        $user = Auth::guard();
        $validated = $request->validated();
        $validated["user_id"] = $user->id();

        $comment = Comment::create($validated);

        $base_response = new BaseResponse(true, ['Komentar berhasil ditambahkan'], new CommentResource($comment->load('user')));

        return response()->json($base_response->toArray());
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment)
    {
        if ($comment->record_status == RecordStatusConstant::deleted) {
            throw NotFoundHttpException::class;
        }  

        $existing_comment = $comment->load('user');

        $resource = $existing_comment->toResource();
        $base_response = new BaseResponse(true, [], $resource);

        return response()->json($base_response->toArray());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommentRequest $request, Comment $comment)
    {
        if ($comment->record_status == RecordStatusConstant::deleted) {
            throw NotFoundHttpException::class;
        }  

        $validated = $request->validated();
        $comment->update($validated);

        $base_response = new BaseResponse(true, ['Komentar berhasil diupdate'], $comment->load('user'));

        return response()->json($base_response->toArray());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        if ($comment->record_status == RecordStatusConstant::deleted) {
            throw NotFoundHttpException::class;
        }  

        $comment->update(['record_status' => RecordStatusConstant::deleted]);
        $base_response = new BaseResponse(true, ['Komentar berhasil dihapus'], null);

        return response()->json($base_response->toArray());
    }
}
