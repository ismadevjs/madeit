<?php
// FileController.php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Provider;
use Illuminate\Http\Request;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class FileController extends Controller
{


    public function files(Request $request)
    {
        // Set default pagination values
        $perPage = $request->input('perPage', 10);
        $page = $request->input('page', 1);

        // Query parameters for filtering
        $mediaType = $request->input('mediaType');
        $uploadDate = $request->input('uploadDate');

        // Validate mediaType if provided
        if ($mediaType && !in_array($mediaType, ['image', 'video', 'audio'])) {
            return response()->json(['error' => 'Invalid mediaType'], 404);
        }

        // Validate uploadDate if provided
        if ($uploadDate && !File::whereDate('created_at', $uploadDate)->exists()) {
            return response()->json(['error' => 'No files uploaded on the specified date'], 404);
        }

        // Start building the query
        $query = File::query();

        // Apply filters
        if ($mediaType) {
            $query->where('type', $mediaType);
        }
        if ($uploadDate) {
            $query->whereDate('created_at', $uploadDate);
        }

        // Include provider name
        $query->with('provider');

        // Fetch uploaded files from the database, ordered by most recent first
        $files = $query->orderBy('created_at', 'desc')->paginate($perPage, ['*'], 'page', $page);

        // Extract provider name from the provider object
        $files->transform(function ($file) {
            $file['provider_name'] = $file->provider ? $file->provider->name : null;
            unset($file['provider']); // Remove the provider object if not needed
            return $file;
        });

        return response()->json($files);
    }




    private function uploadFile(Request $request, $type)
    {
        $file = $request->file('file');
        $extension = $file->extension();
        $size = $file->getSize();

        if (!$file->isValid()) {
            return response()->json(['error' => 'Invalid file'], 400);
        }

        $provider = Provider::findOrFail($request->provider_id);
        $providerName = $provider->name;

        if ($type === 'image') {
            if (!$this->validateImage($file, 4 / 3, 2 * 1024 * 1024)) {
                return response()->json(['error' => 'Image validation failed'], 400);
            }
        } else {
            if (!$this->validateVideo($file, 60, 5 * 1024 * 1024)) {
                return response()->json(['error' => 'Video validation failed'], 400);
            }
        }
        // Save file to storage
        $path = $file->store('files');

        // Save file information to database
        $fileModel = new File();
        $fileModel->name = $request->name;
        $fileModel->provider_id = $request->provider_id;
        $fileModel->type = $type;
        $fileModel->file = $path;
        if($request->provider_id === "2") $fileModel->thumbnail = $this->captureVideoThumbnail($file);
        $fileModel->save();

        return response()->json($fileModel, 201);
    }




    public function uploadImage(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'provider_id' => 'required|exists:providers,id',
            'file' => 'required|file|image',
            'type' => 'required|in:image', // Type must be "image"
        ]);

        $file = $request->file('file');
        $provider = Provider::findOrFail($request->provider_id);
        $providerName = $provider->name;

        // Validate file type based on provider
        if ($providerName === 'Google') {
            // Google provider allows only JPG images
            $extension = $file->getClientOriginalExtension();
            if ($extension !== 'jpg') {
                return response()->json(['error' => 'Only JPG images are allowed for Google provider'], 400);
            }

            // Check aspect ratio for Google images
            if (!$this->validateAspectRatio($file, 4 / 3)) {
                return response()->json(['error' => 'Image aspect ratio must be 4:3 for Google provider'], 400);
            }

            // Check image size for Google images
            if ($file->getSize() > 2 * 1024 * 1024) {
                return response()->json(['error' => 'Image size must be less than 2 MB for Google provider'], 400);
            }
        } elseif ($providerName === 'Snapchat') {
            // Snapchat provider allows JPG and GIF images
            $extension = $file->getClientOriginalExtension();
            if (!in_array($extension, ['jpg', 'gif'])) {
                return response()->json(['error' => 'Only JPG and GIF images are allowed for Snapchat provider'], 400);
            }

            // Check aspect ratio for Snapchat images
            if (!$this->validateAspectRatio($file, 16 / 9)) {
                return response()->json(['error' => 'Image aspect ratio must be 16:9 for Snapchat provider'], 400);
            }

            // Check image size for Snapchat images
            if ($file->getSize() > 5 * 1024 * 1024) {
                return response()->json(['error' => 'Image size must be less than 5 MB for Snapchat provider'], 400);
            }
        }

        // If validation passes, proceed with file upload
        return $this->uploadFile($request, 'image');
    }


    private function validateImage($file, $aspectRatio, $maxSize)
    {
        // Get image dimensions
        $imageInfo = getimagesize($file->getPathname());
        if (!$imageInfo) {
            return 'Invalid image file';
        }

        // Calculate aspect ratio
        $ratio = $imageInfo[0] / $imageInfo[1];

        // Check aspect ratio
        if ($ratio !== $aspectRatio) {
            return 'Image aspect ratio must be ' . $aspectRatio;
        }

        // Check file size
        if ($file->getSize() > $maxSize) {
            return 'Image size must be less than ' . ($maxSize / 1024) . ' KB';
        }

        // Validation passed
        return true;
    }


    public function uploadVideo(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'provider_id' => 'required|exists:providers,id',
            'file' => 'required|file', // Max file size: 50MB
            'type' => 'required|in:video,audio', // Type must be either video or audio
        ]);

        $file = $request->file('file');
        $provider = Provider::findOrFail($request->provider_id);
        $providerName = $provider->name;

        $extension = $file->getClientOriginalExtension();

        if ($providerName === 'Google') {
            // Google provider allows only MP4 videos and MP3 audio
            if ($extension === 'mp4' && $request->type === "video") {
                if (!$this->validateVideo($file, 60, 5 * 1024 * 1024)) {
                    return response()->json(['error' => 'Video validation failed'], 400);
                }
            } elseif ($extension === 'mp3' && $request->type === "audio") {
                $audioValidationResult = $this->validateAudio($file, 30, 5 * 1024 * 1024);
                if ($audioValidationResult !== true) {
                    return $audioValidationResult; // Return the error response
                }
            } else {
                return response()->json(['error' => 'Invalid file type for Google provider'], 400);
            }
        } elseif ($providerName === 'Snapchat') {
            // Snapchat provider allows MP4 and MOV videos
            if (in_array($extension, ['mp4', 'mov']) && $request->type === "video") {
                if (!$this->validateVideo($file, 300, 50 * 1024 * 1024)) {
                    return response()->json(['error' => 'Video validation failed'], 400);
                }
                // generate the thumbnail file
                $this->captureVideoThumbnail($file);
            } else {
                return response()->json(['error' => 'Invalid file type for Snapchat provider'], 400);
            }
        } else {
            return response()->json(['error' => 'Invalid provider'], 400);
        }

        // If validation passes, proceed with file upload
        return $this->uploadFile($request, $request->type);
    }



    private function validateVideo($file, $maxDuration, $maxSize)
    {
        $path = $file->store('temp');
        $videoDuration = FFMpeg::fromDisk('local')->open($path)->getDurationInSeconds();
        unlink(storage_path('app/' . $path)); // Clean up temporary file

        return $videoDuration <= $maxDuration && $file->getSize() <= $maxSize;
    }


    private function validateAudio($file, $maxDuration, $maxSize)
    {
        $path = $file->store('temp');
        $audioDuration = FFMpeg::fromDisk('local')->open($path)->getDurationInSeconds();
        unlink(storage_path('app/' . $path)); // Clean up temporary file

        $extension = $file->getClientOriginalExtension();

        if ($audioDuration > $maxDuration) {
            return response()->json(['error' => 'Audio duration for MP3 files must be less than ' . $maxDuration . ' seconds.'], 400);
        }

        if ($file->getSize() > $maxSize) {
            return response()->json(['error' => 'Audio file size exceeds the maximum allowed size of ' . ($maxSize / 1024 / 1024) . ' MB.'], 400);
        }

        return true; // Validation passed
    }



    private function validateAspectRatio($file, $aspectRatio)
    {
        // Get image dimensions
        $imageInfo = getimagesize($file->getPathname());
        if (!$imageInfo) {
            return false;
        }

        // Calculate aspect ratio
        $width = $imageInfo[0];
        $height = $imageInfo[1];
        $ratio = $width / $height;


        // Check aspect ratio with a small threshold for comparison
        $threshold = 0.01; // Adjust as needed
        if (abs($ratio - $aspectRatio) > $threshold) {
            return false;
        }

        // Validation passed
        return true;
    }

    private function captureVideoThumbnail($videoPath)
    {
        // Get the duration of the video
        $videoDuration = FFMpeg::fromDisk('local')->open($videoPath)->getDurationInSeconds();

        // Calculate the time to capture the thumbnail from the middle
        $middleTime = $videoDuration / 2;

        // Generate a random time within a range around the middle time
        $randomTime = mt_rand(max(0, $middleTime - 5), min($videoDuration, $middleTime + 5));

        // Capture the thumbnail at the random time
        $thumbnailPath = 'thumbnails/' . uniqid() . '.jpg'; // Define the storage path for the thumbnail
        FFMpeg::fromDisk('local')->open($videoPath)->getFrameFromSeconds($randomTime)->export()
            ->toDisk('local')->save($thumbnailPath);

        // Return the path of the captured thumbnail
        return $thumbnailPath;
    }
}
