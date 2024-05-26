<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserBalanceController extends Controller
{
    public function getBalance(Request $request)
    {
        $user = $request->get('user');

        return response()->json([
            'balance' => $user->balance,
        ]);
    }
}

