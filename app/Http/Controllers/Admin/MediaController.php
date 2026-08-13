<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMediaRequest;
use App\Http\Requests\UpdateMediaRequest;
use App\Models\Media;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MediaController extends Controller
{
    /**
     * Display a listing of the media library.
     */
    public function index(Request $request): View
    {
        $this->authorize('manage-media');

        $query = Media::query()->search($request->query('q'));

        if ($request->filled('type')) {
            $query->when($request->query('type') === 'images', fn ($q) => $q->where('mime_type', 'like', 'image/%'));
        }

        $media = $query->latest()->paginate(config('blog.pagination.admin_media', 30))->withQueryString();

        return view('admin.media.index', [
            'media' => $media,
            'search' => trim((string) $request->query('q')),
            'picker' => $request->boolean('picker'),
        ]);
    }

    /**
     * Store newly uploaded media items.
     */
    public function store(StoreMediaRequest $request): RedirectResponse
    {
        $this->authorize('manage-media');

        $uploaded = 0;

        foreach ($request->validated('files') as $file) {
            $path = $file->store(config('blog.images.media_dir'), 'public');

            [$width, $height] = @getimagesize($file->getRealPath()) ?: [null, null];

            Media::create([
                'user_id' => $request->user()->id,
                'name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'extension' => $file->getClientOriginalExtension(),
                'size' => $file->getSize(),
                'path' => $path,
                'width' => $width,
                'height' => $height,
            ]);

            $uploaded++;
        }

        $flash = $uploaded === 1 ? '1 image uploaded.' : $uploaded.' images uploaded.';

        return Redirect::route('admin.media.index')
            ->with('status', $flash);
    }

    /**
     * Update the specified media item.
     */
    public function update(UpdateMediaRequest $request, Media $media): RedirectResponse
    {
        $this->authorize('manage-media');

        $media->update([
            'name' => $request->validated('name'),
            'alt_text' => $request->validated('alt_text'),
        ]);

        return back()->with('status', 'Media updated.');
    }

    /**
     * Remove the specified media item.
     */
    public function destroy(Media $media): RedirectResponse
    {
        $this->authorize('manage-media');

        Storage::disk('public')->delete($media->path);

        $media->delete();

        return Redirect::route('admin.media.index')
            ->with('status', 'Media deleted.');
    }
}
