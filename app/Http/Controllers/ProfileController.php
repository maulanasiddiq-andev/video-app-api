<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileImageRequest;
use App\Http\Resources\BaseResponse;
use App\Http\Resources\SearchResponse;
use App\Models\Comment;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function editProfileImage(UpdateProfileImageRequest $request)
    {
        $request->validated();

        $old_image = $request->input('old_image');
        if ($old_image) {
            $relative_path = str_replace('/storage/', '', $old_image);
            Storage::disk('public')->delete($relative_path);
        }

        $imagePath = $request->file('image')->store('profile_images', 'public');
        $imageUrl = Storage::url($imagePath);

        $user = $request->user();
        $user->profile_image = $imageUrl;

        $user->update();

        $response = [
            "succeed" => true,
            "messages" => ['Gambar profil berhasil diubah'],
            "data" => $request->user()
        ];

        return response()->json($response);
    }

    public function getSelf(Request $request)
    {
        $response = [
            "succeed" => true,
            "messages" => [],
            "data" => $request->user()
        ];

        return $response;
    }

    public function getMyVideos(Request $request)
    {
        $page_size = $request->input('page_size', 10);

        $videos = Video::with('user')
                        ->where('user_id', $request->user()->id)
                        ->withCount('histories')
                        ->filter($request)
                        ->paginate($page_size);

        $search_response = new SearchResponse($videos);
        $base_response = new BaseResponse(true, [], $search_response->toArray());

        return response()->json($base_response->toArray());
    }

    public function getMyComments(Request $request)
    {
        $page_size = $request->input('page_size', 10);

        $comments = Comment::with(['user', 'video.user'])
                            ->where('user_id', $request->user()->id)
                            ->withCount('histories')
                            ->filter($request)
                            ->paginate($page_size);

        $search_response = new SearchResponse($comments);
        $base_response = new BaseResponse(true, [], $search_response->toArray());

        return response()->json($base_response->toArray());
    }
}
