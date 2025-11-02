<?php

namespace Paymenter\Extensions\Others\Knowledgebase\Http\Controllers;

use App\Helpers\ExtensionHelper;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Paymenter\Extensions\Others\Knowledgebase\Models\KnowledgeArticle;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ArticleDownloadController extends Controller
{
    public function pdf(Request $request, KnowledgeArticle $article): StreamedResponse
    {
        $extension = ExtensionHelper::getExtension('other', 'Knowledgebase');
        if ($extension && !$this->downloadsEnabled($extension->config('allow_downloads'))) {
            abort(404);
        }

        if (!$article->isPublished()) {
            abort(404);
        }

        $article->load('category');

        $filename = sprintf('%s.pdf', $article->slug);

        $branding = [
            'name' => config('settings.company_name'),
            'url' => rtrim(config('settings.app_url') ?: config('app.url'), '/'),
            'email' => config('settings.system_email_address'),
        ];

        return response()->streamDownload(function () use ($article, $branding) {
            echo Pdf::loadView('knowledgebase::pdf.article', [
                'article' => $article,
                'branding' => $branding,
            ])->setPaper('a4')->stream();
        }, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    protected function downloadsEnabled(mixed $raw): bool
    {
        if ($raw === null) {
            return true;
        }

        $parsed = filter_var($raw, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        return $parsed ?? (bool) $raw;
    }
}
