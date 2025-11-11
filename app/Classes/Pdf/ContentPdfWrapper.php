<?php

namespace App\Classes\Pdf;

class ContentPdfWrapper
{
    private $content;

    private $fileName;

    private $tempPath;

    public function __construct(string $content, ?string $fileName = null)
    {
        // Decode base64 if it looks like base64
        if ($this->isBase64($content)) {
            $content = base64_decode($content);
        }

        $this->content = $content;
        $this->fileName = $fileName ?? 'invoice.pdf';

        // Create temporary file for operations that need a file path
        $this->tempPath = tempnam(sys_get_temp_dir(), 'pdf_wrapper_');
        file_put_contents($this->tempPath, $content);
    }

    public function save($path)
    {
        return file_put_contents($path, $this->content) !== false;
    }

    public function download($name = null)
    {
        $name = $name ?: $this->fileName;

        return response($this->content)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $name . '"');
    }

    public function stream($name = null)
    {
        $name = $name ?: $this->fileName;

        return response($this->content)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . $name . '"');
    }

    public function output()
    {
        return $this->content;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getTempPath(): string
    {
        return $this->tempPath;
    }

    private function isBase64(string $data): bool
    {
        return base64_encode(base64_decode($data, true)) === $data;
    }

    public function __destruct()
    {
        if (file_exists($this->tempPath)) {
            unlink($this->tempPath);
        }
    }
}
