<?php
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

if (!function_exists('validate_image')) {
    function validate_image($file, $providerName)
    {
        // Get image info
        $imageInfo = getimagesize($file);
        if (!$imageInfo) {
            return 'Invalid image file';
        }

        // Get file extension
        $extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);

        // Google provider allows only JPEG images
        if ($providerName === 'Google' && $extension !== 'jpg') {
            return 'Only JPG images are allowed for Google provider';
        }

        // Snapchat provider allows JPG and GIF images
        if ($providerName === 'Snapchat' && !in_array($extension, ['jpg', 'gif'])) {
            return 'Only JPG and GIF images are allowed for Snapchat provider';
        }

        

        return true;
    }
}


if (!function_exists('validate_video')) {
    function validate_video($file, $maxDuration, $maxSize)
    {
        $path = $file->store('temp');
        $videoDuration = FFMpeg::fromDisk('local')->open($path)->getDurationInSeconds();
        unlink(storage_path('app/' . $path)); // Clean up temporary file

        return $videoDuration <= $maxDuration && $file->getSize() <= $maxSize;
    }
}

if (!function_exists('validate_audio')) {
    function validate_audio($file, $maxDuration, $maxSize)
    {
        // Validate audio file duration and size
        $path = $file->store('temp');
        $audioDuration = FFMpeg::fromDisk('local')->open($path)->getDurationInSeconds();
        unlink(storage_path('app/' . $path)); // Clean up temporary file

        return $audioDuration <= $maxDuration && $file->getSize() <= $maxSize;
    }
}

if (!function_exists('validate_file')) {
    function validate_file($file, $extension, $size, $type, $providerName)
    {
        if ($providerName === 'Google') {
            if ($extension === 'jpg') {
                return validate_image($file, 4 / 3, 2 * 1024 * 1024);
            } elseif ($extension === 'mp4') {
                return validate_video($file, 60, 5 * 1024 * 1024);
            } elseif ($extension === 'mp3') {
                return validate_audio($file, 30, 5 * 1024 * 1024);
            }
        } elseif ($providerName === 'Snapchat') {
            if (in_array($extension, ['jpg', 'gif'])) {
                return validate_image($file, 16 / 9, 5 * 1024 * 1024);
            } elseif (in_array($extension, ['mp4', 'mov'])) {
                return validate_video($file, 300, 50 * 1024 * 1024);
            }
        }

        if ($type !== 'image' && $type !== 'video' && $type !== 'audio') {
            return false;
        }

        return true;
    }
}
