<?php

namespace Paymenter\Extensions\Others\SimpleAnalytics;

use App\Classes\Extension\Extension;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\HtmlString;

class SimpleAnalytics extends Extension
{
    public function boot(): void
    {
        Event::listen('body', function () {
            return ['view' => new HtmlString($this->getScriptTemplate())];
        });
    }

    private function getScriptTemplate(): string
    {
        $alwaysEnable = $this->getConfigValue('always_enable', false);

        if ($alwaysEnable) {
            return <<<HTML
<script async src="https://scripts.simpleanalyticscdn.com/latest.js"></script>
HTML;
        }

        return <<<HTML
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (document.cookie.indexOf('cookieConsent=accepted') !== -1) {
        var script = document.createElement('script');
        script.async = true;
        script.src = 'https://scripts.simpleanalyticscdn.com/latest.js';
        document.head.appendChild(script);
    }
});
</script>
HTML;
    }

    public function getConfig($values = [])
    {
        return [
            [
                'name' => 'always_enable',
                'label' => 'Always Enable',
                'type' => 'checkbox',
                'description' => 'If enabled, Simple Analytics will always be injected.',
                'default' => false,
            ],
        ];
    }

    public function enabled(): void {}
    public function disabled(): void {}
    public function updated(): void {}
    public function install(): void {}
}
