<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class S3Controller extends Controller
{
    /**
     * Generate a signed URL for uploading a file to S3.
     */
    public function uploadUrl(Request $request): JsonResponse
    {
        $request->validate([
            'filename' => ['required', 'string', 'max:255'],
            'content_type' => ['nullable', 'string', 'max:100'],
        ]);

        $filename = $request->input('filename');
        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        // Generate unique path: temp/{epoch}-{uuid}.{extension}
        $path = sprintf(
            'temp/%d-%s.%s',
            now()->timestamp,
            Str::uuid(),
            $extension
        );

        $url = Storage::disk('s3')->temporaryUploadUrl(
            $path,
            now()->addMinutes(15),
            [
                'ContentType' => $request->input('content_type', 'application/octet-stream'),
            ]
        );

        return response()->json([
            'upload_url' => $url['url'],
            'headers' => $url['headers'] ?? [],
            'path' => $path,
            'expires_at' => now()->addMinutes(15)->toIso8601String(),
        ]);
    }

    /**
     * Generate a signed URL for viewing/downloading a file from S3.
     */
    public function viewUrl(Request $request): JsonResponse
    {
        $request->validate([
            'path' => ['required', 'string', 'max:500'],
            'expires_in' => ['nullable', 'integer', 'min:1', 'max:60'],
        ]);

        $path = $request->input('path');
        $expiresIn = $request->input('expires_in', 5); // default 5 minutes

        // Check if file exists
        if (! Storage::disk('s3')->exists($path)) {
            return response()->json([
                'error' => 'File not found',
            ], 404);
        }

        $url = Storage::disk('s3')->temporaryUrl(
            $path,
            now()->addMinutes($expiresIn)
        );

        return response()->json([
            'view_url' => $url,
            'path' => $path,
            'expires_at' => now()->addMinutes($expiresIn)->toIso8601String(),
        ]);
    }
}
