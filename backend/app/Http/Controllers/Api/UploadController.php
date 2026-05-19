<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DeleteUploadImageRequest;
use App\Http\Requests\Admin\UploadImageRequest;
use App\Http\Requests\Admin\UploadImagesRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    private const BASE_FOLDER = 'images';

    private function normalizeFolder(?string $folder): string
    {
        $folder = trim((string) $folder, '/');

        if ($folder === '') {
            return self::BASE_FOLDER;
        }

        if (! preg_match('/^[a-zA-Z0-9\/_-]+$/', $folder)) {
            abort(422, 'Pasta de upload inválida.');
        }

        return str_starts_with($folder, self::BASE_FOLDER)
            ? $folder
            : self::BASE_FOLDER.'/'.$folder;
    }

    private function assertManagedPath(string $path): void
    {
        if (! preg_match('/^[a-zA-Z0-9\/_.-]+$/', $path) || ! str_starts_with($path, self::BASE_FOLDER.'/')) {
            abort(422, 'Caminho do arquivo inválido.');
        }
    }

    /**
     * Upload an image file.
     */
    public function uploadImage(UploadImageRequest $request): JsonResponse
    {
        $file = $request->file('image');
        $folder = $this->normalizeFolder($request->input('folder', self::BASE_FOLDER));

        $filename = Str::uuid().'.'.$file->getClientOriginalExtension();

        $storedPath = $file->storeAs($folder, $filename, 'public');

        $url = Storage::disk('public')->url($storedPath);

        return response()->json([
            'message' => 'Image uploaded successfully',
            'url' => $url,
            'path' => $storedPath,
        ], 201);
    }

    /**
     * Upload multiple images.
     */
    public function uploadImages(UploadImagesRequest $request): JsonResponse
    {
        $files = $request->file('images');
        $folder = $this->normalizeFolder($request->input('folder', self::BASE_FOLDER));
        $uploaded = [];

        foreach ($files as $file) {
            $filename = Str::uuid().'.'.$file->getClientOriginalExtension();
            $storedPath = $file->storeAs($folder, $filename, 'public');
            $url = Storage::disk('public')->url($storedPath);

            $uploaded[] = [
                'url' => $url,
                'path' => $storedPath,
            ];
        }

        return response()->json([
            'message' => 'Images uploaded successfully',
            'images' => $uploaded,
        ], 201);
    }

    /**
     * Delete an image.
     */
    public function deleteImage(DeleteUploadImageRequest $request): JsonResponse
    {
        $path = $request->validated('path');
        $this->assertManagedPath($path);

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);

            return response()->json([
                'message' => 'Image deleted successfully',
            ]);
        }

        return response()->json([
            'message' => 'Image not found',
        ], 404);
    }
}
