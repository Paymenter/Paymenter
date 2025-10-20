<?php

namespace App\Classes\Pdf;

class FilePdfWrapper
{
    private $filePath;

    private $fileName;

    public function __construct(string $filePath, ?string $fileName = null)
    {
        $this->filePath = $filePath;
        $this->fileName = $fileName ?? basename($filePath);
    }

    public function save($path)
    {
        return copy($this->filePath, $path);
    }

    public function download($name = null)
    {
        $name = $name ?: $this->fileName;

        return response()->download($this->filePath, $name);
    }

    public function stream($name = null)
    {
        $name = $name ?: $this->fileName;

        return response()->file($this->filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $name . '"',
        ]);
    }

    public function output()
    {
        return file_get_contents($this->filePath);
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }
}
