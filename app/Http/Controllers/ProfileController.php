<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileImageRequest;
use App\Models\User;
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
}
