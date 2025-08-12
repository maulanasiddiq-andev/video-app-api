<?php

namespace App\Http\Controllers;

use App\Enums\RecordStatusConstant;
use App\Http\Resources\BaseResponse;
use App\Mail\ConfirmationMail;
use App\Models\Otp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->whereNotNull('email_verified_at')->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'messages' => ['Email atau password invalid']
            ]);
        }

        $response = [
            "succeed" => true,
            "messages" => [],
            "data" => [
                "token" => $user->createToken('api-token')->plainTextToken,
                "user" => $user
            ]
        ];

        return response()->json($response);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:users,email',
            'name' => 'required',
            'username' => 'required',
            'password' => 'required'
        ]);

        $validated['password'] = Hash::make($request->password);
        $validated['record_status'] = 'active';

        $user = User::create($validated);

        $otpCode = rand(1000, 9999);

        Otp::create([
            'user_id' => $user->id,
            'code' => $otpCode,
            'expires_at' => Carbon::now()->addMinute(15)
        ]);

        Mail::to($user)->send(new ConfirmationMail($user, $otpCode));

        $base_response = new BaseResponse(true, ['Akun berhasil terdaftar'], $user);

        return response()->json($base_response->toArray());
    }

    public function resendOtp(Request $request)
    {
        $user_id = $request->input('user_id');

        $user = User::where('id', $user_id)->first();

        $otpCode = rand(1000, 9999);

        $otp = Otp::create([
            'user_id' => $user_id,
            'code' => $otpCode,
            'expires_at' => Carbon::now()->addMinute(15)
        ]);

        Mail::to($user)->send(new ConfirmationMail($user, $otpCode));

        $base_response = new BaseResponse(true, ['Kode OTP sudah dikirim kembali'], null);

        return response()->json($base_response->toArray());
    }

    public function verifyAccount(Request $request)
    {
        $otp = $request->input('otp_code');
        $user_id = $request->input('user_id');

        $otp = Otp::where('code', $otp)->where('user_id', $user_id)->where('expires_at', '>', now())->first();

        if ($otp) {
            $user = User::where('id', $user_id)->first();
            $user->email_verified_at = now();

            $user->save();

            $base_response = new BaseResponse(true, ['Email anda telah diverifikasi, silakan login'], $user);

            return response()->json($base_response->toArray());
        } else {
            $base_response = new BaseResponse(false, ['Kode OTP salah'], null);

            return response()->json($base_response->toArray());
        }
    }
}
