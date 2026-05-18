<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
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

        if (!preg_match('/^[a-zA-Z0-9\/_-]+$/', $folder)) {
            abort(422, 'Pasta de upload inválida.');
        }

        return str_starts_with($folder, self::BASE_FOLDER)
            ? $folder
            : self::BASE_FOLDER . '/' . $folder;
    }

    private function assertManagedPath(string $path): void
    {
        if (!preg_match('/^[a-zA-Z0-9\/_.-]+$/', $path) || !str_starts_with($path, self::BASE_FOLDER . '/')) {
            abort(422, 'Caminho do arquivo inválido.');
        }
    }

    /**
     * Upload an image file.
     */
    public function uploadImage(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
            'folder' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $file = $request->file('image');
        $folder = $this->normalizeFolder($request->input('folder', self::BASE_FOLDER));

        // Generate unique filename
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

        // Store file
        $storedPath = $file->storeAs($folder, $filename, 'public');

        // Get public URL
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
    public function uploadImages(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'images' => 'required|array|min:1|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'folder' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $files = $request->file('images');
        $folder = $this->normalizeFolder($request->input('folder', self::BASE_FOLDER));
        $uploaded = [];

        foreach ($files as $file) {
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
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
    public function deleteImage(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'path' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $path = $request->input('path');
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
