<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Provider;
use Illuminate\Http\Request;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class MediaController extends Controller
{



    public function uploadFile(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'provider_id' => 'required|exists:providers,id',
            'file' => 'required|file|max:5120', // Max file size: 5MB
            'type' => 'required|in:image,video,audio', // Type must be either image or video
        ]);

        $file = $request->file('file');
        $extension = $file->extension();
        $size = $file->getSize();

        if (!$file->isValid()) {
            return response()->json(['error' => 'Invalid file'], 400);
        }

        $provider = Provider::findOrFail($request->provider_id);
        $providerName = $provider->name;

        $validationResult = validate_file($file, $extension, $size, $request->type, $providerName);
        if ($validationResult !== true) {
            return $validationResult;
        }

        // Save file to storage
        $path = $file->store('files');

        // Save file information to database
        $fileModel = new File();
        $fileModel->name = $request->name;
        $fileModel->provider_id = $request->provider_id;
        $fileModel->type = $request->type;
        $fileModel->file = $path;
        $fileModel->save();

        return response()->json($fileModel, 201);
    }


    
    public function uploadFileBAckup(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'provider_id' => 'required|exists:providers,id',
            'file' => 'required|file|max:5120', // Max file size: 5MB
            'type' => 'required|in:image,video,audio', // Type must be either image or video
        ]);


        // Get file details
        $file = $request->file('file');
        $extension = $file->extension();
        $size = $file->getSize();

        if (!$file->isValid()) {
            return response()->json(['error' => 'Invalid file'], 400);
        }

        // Check provider restrictions
        $provider = Provider::findOrFail($request->provider_id);
        $providerName = $provider->name;

        if ($providerName === 'Google') {
            if ($extension === 'jpg') {
                // return response()->json(getimagesize($file));
                // Image-specific validation for Google
                if (getimagesize($file)[0] / getimagesize($file)[1] !== 4 / 3) {
                    return response()->json(['error' => 'Image aspect ratio must be 4:3'], 400);
                }
                if ($size > 2 * 1024 * 1024) { // 2 MB
                    return response()->json(['error' => 'Image size must be less than 2 MB'], 400);
                }

                if ($request->type !== "image") {
                    return response()->json(['error' => 'the type is not correct'], 400);
                }
            } elseif ($extension === 'mp4') {
                $path = $file->store('files');
                // Video-specific validation for Google
                $videoDuration = FFMpeg::fromDisk('local')->open($path)->getDurationInSeconds();

                if ($videoDuration > 60) { // 1 minute
                    return response()->json(['error' => 'Video duration must be less than 1 minute'], 400);
                }
                if ($size > 5 * 1024 * 1024) { // 5 MB
                    return response()->json(['error' => 'Video size must be less than 5 MB'], 400);
                }
                if ($request->type !== "video") {
                    return response()->json(['error' => 'the type is not correct'], 400);
                }
            } elseif ($extension === 'mp3') {
                $path = $file->store('files');
                // Audio-specific validation for Google
                $audioDuration = FFMpeg::fromDisk('local')->open($path)->getDurationInSeconds();
                if ($audioDuration > 30) { // 30 seconds
                    return response()->json(['error' => 'Audio duration must be less than 30 seconds'], 400);
                }
                if ($size > 5 * 1024 * 1024) { // 5 MB
                    return response()->json(['error' => 'Audio size must be less than 5 MB'], 400);
                }
                if ($request->type !== "audio") {
                    return response()->json(['error' => 'the type is not correct'], 400);
                }
            }
        } elseif ($providerName === 'Snapchat') {
            if (in_array($extension, ['jpg', 'gif'])) {
                if (getimagesize($file)[0] / getimagesize($file)[1] !== 16 / 9) {
                    return response()->json(['error' => 'Image aspect ratio must be 16:9'], 400);
                }
                if ($size > 5 * 1024 * 1024) { // 5 MB
                    return response()->json(['error' => 'Image size must be less than 5 MB'], 400);
                }
                if ($request->type !== "image") {
                    return response()->json(['error' => 'the type is not correct'], 400);
                }
            } elseif (in_array($extension, ['mp4', 'mov'])) {
                $path = $file->store('files');
                // Video-specific validation for Snapchat
                if ($size > 50 * 1024 * 1024) { // 50 MB
                    return response()->json(['error' => 'Video size must be less than 50 MB'], 400);
                }
                $videoDuration = FFMpeg::fromDisk('local')->open($path)->getDurationInSeconds();
                if ($videoDuration > 300) { // 5 minutes
                    return response()->json(['error' => 'Video duration must be less than 5 minutes'], 400);
                }

                if ($request->type !== "video") {
                    return response()->json(['error' => 'the type is not correct'], 400);
                }
                // Extract preview image from the middle of the video
                // You'll need to implement this part
            }
        }

        // Save file to storage
        // $path = $file->store('files');

        // Save file information to database
        $fileModel = new File();
        $fileModel->name = $request->name;
        $fileModel->provider_id = $request->provider_id;
        $fileModel->type = $request->type;
        $fileModel->file = $path;
        $fileModel->save();

        return response()->json($fileModel, 201);
    }
}
