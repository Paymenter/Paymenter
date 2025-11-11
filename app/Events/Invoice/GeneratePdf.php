<?php

namespace App\Events\Invoice;

use App\Models\Invoice;
use Illuminate\Foundation\Events\Dispatchable;

class GeneratePdf
{
    use Dispatchable;

    public function __construct(
        public Invoice $invoice,
        public $pdf = null,
        public $pdfPath = null,  // For remote/file-based PDFs
        public $pdfContent = null, // For base64/binary content
        public $fileName = null,
        public $contentType = 'application/pdf'
    ) {}

    /**
     * Set a PDF from file path
     */
    public function setPdfFromPath(string $path, ?string $fileName = null): void
    {
        $this->pdfPath = $path;
        $this->fileName = $fileName ?? basename($path);
    }

    /**
     * Set a PDF from content (base64, binary, etc.)
     */
    public function setPdfFromContent(string $content, ?string $fileName = null): void
    {
        $this->pdfContent = $content;
        $this->fileName = $fileName ?? 'invoice.pdf';
    }

    /**
     * Set a DomPDF instance
     */
    public function setPdf($pdf): void
    {
        $this->pdf = $pdf;
    }

    /**
     * Check if any PDF was provided
     */
    public function hasPdf(): bool
    {
        return $this->pdf !== null || $this->pdfPath !== null || $this->pdfContent !== null;
    }
}
