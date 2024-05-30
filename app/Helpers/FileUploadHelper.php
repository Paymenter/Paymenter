<?php

namespace App\Helpers;

use App\Models\FileUpload;
use Illuminate\Support\Str;

class FileUploadHelper
{
    public static function upload($file, $fileable, $fileable_type)
    {
        $filename = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $size = $file->getSize();
        $mime_type = $file->getMimeType();
        $uuid = Str::uuid();

        $file->storeAs('uploads', $uuid.'.'.$extension);

        $fileUpload = FileUpload::create([
            'uuid' => $uuid,
            'fileable_id' => $fileable->id,
            'fileable_type' => $fileable_type,
            'filename' => $filename,
            'mime_type' => $mime_type,
            'size' => $size,
        ]);

        return $fileUpload;
    }
}
