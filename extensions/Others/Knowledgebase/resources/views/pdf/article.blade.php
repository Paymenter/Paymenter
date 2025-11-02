<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <title>{{ $article->title }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #1f2937;
            margin: 0;
            padding: 24px;
        }

        header {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            border-bottom: 1px solid #d1d5db;
            margin-bottom: 24px;
            padding-bottom: 12px;
            align-items: flex-start;
            justify-content: space-between;
        }

        header .meta-group {
            flex: 1 1 60%;
            min-width: 240px;
        }

        header .branding {
            flex: 1 1 30%;
            min-width: 200px;
            font-size: 11px;
            color: #4b5563;
            text-align: right;
            line-height: 1.5;
        }

        header .branding strong {
            display: block;
            color: #111827;
            font-size: 12px;
        }

        header .branding a {
            color: #2563eb;
            text-decoration: none;
        }

        h1 {
            font-size: 24px;
            margin: 0 0 8px 0;
            color: #111827;
        }

        .meta {
            font-size: 12px;
            color: #6b7280;
        }

        .meta span {
            display: inline-block;
            margin-right: 12px;
        }

        .summary {
            font-size: 13px;
            margin-top: 12px;
            color: #374151;
        }

        article {
            margin-top: 24px;
            line-height: 1.6;
        }

        article h2,
        article h3,
        article h4 {
            color: #111827;
        }

        article p {
            margin-bottom: 12px;
        }

        article a {
            color: #2563eb;
            text-decoration: none;
        }

        article ul,
        article ol {
            margin: 12px 0 12px 24px;
        }

        article pre {
            background: #f3f4f6;
            border-radius: 4px;
            padding: 12px;
            overflow: auto;
            font-family: "Courier New", Courier, monospace;
        }
    </style>
</head>

<body>
    <header>
        <div class="meta-group">
            <div class="meta">
                <span>{{ $article->category?->name }}</span>
                @if ($article->published_at)
                    <span>{{ $article->published_at->timezone(config('app.timezone'))->translatedFormat('M d, Y') }}</span>
                @endif
            </div>
            <h1>{{ $article->title }}</h1>
            @if ($article->summary)
                <p class="summary">{{ $article->summary }}</p>
            @endif
        </div>

        @php
            $companyName = $branding['name'] ?? null;
            $companyUrl = $branding['url'] ?? null;
            $companyEmail = $branding['email'] ?? null;
        @endphp

        @if ($companyName || $companyUrl || $companyEmail)
            <div class="branding">
                @if ($companyName)
                    <strong>{{ $companyName }}</strong>
                @endif

                @if ($companyUrl)
                    <div><a href="{{ $companyUrl }}">{{ $companyUrl }}</a></div>
                @endif

                @if ($companyEmail)
                    <div><a href="mailto:{{ $companyEmail }}">{{ $companyEmail }}</a></div>
                @endif
            </div>
        @endif
    </header>

    <article>
        {!! $article->content !!}
    </article>
</body>

</html>
