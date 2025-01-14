<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StorageController extends Controller
{
    public function profile(Request $request, $filename)
    {
        $path = "public/profile/" . $filename;

        if (!Storage::exists($path)) {
            abort(404);
        }

        return response(Storage::get($path), 200)->header("Content-Type", Storage::mimeType($path));
    }
}
