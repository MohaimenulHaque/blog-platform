<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreImageUploadRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ImageUploadController extends Controller
{
    /**
     * Store an image uploaded from the rich text editor.
     */
    public function store(StoreImageUploadRequest $request): JsonResponse
    {
        $path = $request->file('image')->store(config('blog.images.editor_dir'), 'public');

        return response()->json([
            'location' => asset('storage/'.$path),
            'path' => $path,
        ]);
    }

    /**
     * Delete an image uploaded from the rich text editor.
     */
    public function destroy(): JsonResponse
    {
        return response()->json(['success' => true]);
    }
}
