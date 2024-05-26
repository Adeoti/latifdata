<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthenticateWithApiToken
{
    public function handle(Request $request, Closure $next)
    {
        $email = $request->header('email');
        $password = $request->header('password');
        $apiKey = $request->header('api_key');

        if (!$email || !$password || !$apiKey) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password) || $user->api_key !== $apiKey) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->merge(['user' => $user]);
        
        return $next($request);
    }
}
