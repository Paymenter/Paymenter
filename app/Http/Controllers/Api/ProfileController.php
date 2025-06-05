<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Dedoc\Scramble\Attributes\ExcludeAllRoutesFromDocs;
use Illuminate\Http\Request;

#[ExcludeAllRoutesFromDocs]
class ProfileController extends Controller
{
    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
