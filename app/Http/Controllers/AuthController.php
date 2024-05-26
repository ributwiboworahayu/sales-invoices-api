<?php

namespace App\Http\Controllers;


use App\Http\Requests\Auth\LoginRequest;
use App\Models\OauthClient;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $email = $request->input('email');
        $password = $request->input('password');

        $user = User::where('email', $email)->first();
        if (!$user) return $this->failResponse(message: 'User not found');

        if (!Hash::check($password, $user->password)) return $this->failResponse(message: 'Wrong password');

        // set 2 for grant client password
        $clientId = 2;
        $clientSecret = OauthClient::find($clientId)->secret;

        $response = Http::asForm()->timeout(10)->post(env('APP_URL') . 'oauth/token', [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'grant_type' => 'password',
            'username' => $email,
            'password' => $password,
            'scope' => ''
        ]);

        if ($response->failed()) {
            return $this->failResponse(message: $response->json()['message']);
        }

        return $this->successResponse(data: $response->json(), code: $response->status());
    }
}
