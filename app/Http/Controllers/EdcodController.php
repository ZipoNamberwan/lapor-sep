<?php

namespace App\Http\Controllers;

use App\Models\Subdistrict;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EdcodController extends Controller
{
    function input()
    {
        if (!Auth::check()) {
            return abort(401);
        }

        $user = User::find(Auth::user()->id);

        $subdistricts = Subdistrict::where(['regency_id' => $user->regency->id])->get();
        return view('edcod/input', ['subdistricts' => $subdistricts]);
    }
}
