<?php

namespace Paymenter\Extensions\Others\CookieBanner;

use App\Classes\Extension\Extension;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\HtmlString;

#[ExtensionMeta(
    name: 'CookieBanner',
    description: 'Customizable cookie banner with markdown, opacity, and styled buttons.',
    version: '1.1',
    author: 'Paymenter x QKing',
    url: 'https://paymenter.org',
)]
class CookieBanner extends Extension
{

    public function getConfig($values = []): array
    {
        return [
            [
                'name' => 'message_text',
                'type' => 'markdown',
                'label' => 'Banner Message Text',
                'description' => 'Supports full markdown',
                'required' => true,
            ],
            [
                'name' => 'accept_text',
                'type' => 'text',
                'label' => 'Accept Button Text',
                'required' => true,
            ],
            [
                'name' => 'decline_enabled',
                'type' => 'checkbox',
                'label' => 'Enable Decline Button',
                'description' => 'Shows a Decline button',
                'required' => false,
            ],
            [
                'name' => 'decline_text',
                'type' => 'text',
                'label' => 'Decline Button Text',
                'description' => 'Text for decline button if enabled.',
                'required' => false,
            ],
            [
                'name' => 'banner_bg_color',
                'type' => 'color',
                'label' => 'Banner Background Color',
                'required' => false,
            ],
            [
                'name' => 'banner_text_color',
                'type' => 'color',
                'label' => 'Banner Text Color',
                'required' => false,
            ],
            [
                'name' => 'banner_opacity',
                'type' => 'number',
                'label' => 'Banner Opacity (1-100)',
                'description' => 'Set opacity of banner',
                'required' => false,
                'value' => $values['banner_opacity'] ?? 100,
            ],
        ];
    }

    public function boot(): void
    {
        if (!request()->isMethod('GET')) {
            return;
        }

        $bodyScript = $this->getBodyScript();
        Event::listen('body', function () use ($bodyScript) {
            return ['view' => new HtmlString($bodyScript)];
        });
    }

    private function getBodyScript(): string
    {
        $messageText = $this->getSetting('message_text');
        $acceptText = $this->getSetting('accept_text');
        $declineText = $this->getSetting('decline_text');
        $declineEnabled = $this->getSetting('decline_enabled') ? 'true' : 'false';
        $bgColor = $this->getSetting('banner_bg_color');
        $textColor = $this->getSetting('banner_text_color');
        $opacity = floatval($this->getSetting('banner_opacity', 100)) / 100;

        return <<<HTML
<style>
    #cookie-banner {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: {$bgColor};
        color: {$textColor};
        opacity: {$opacity};
        padding: 12px 20px;
        font-size: 14px;
        box-shadow: 0 -2px 5px rgba(0,0,0,.2);
        z-index: 99999;
        display: none;
        flex-wrap: wrap;
    }

    #cookie-banner span a {
        text-decoration: underline;
        color: {$textColor};
    }

    #cookie-banner span a:hover {
        color: darken({$textColor}, 15%);
    }

    #cookie-banner button {
        margin-left: 10px;
        padding: 8px 16px;
        cursor: pointer;
        border: 1px solid #888;
        border-radius: 4px;
        background: transparent;
        font-size: 14px;
        transition: all 0.2s;
    }

    #cookie-banner button:hover {
        background-color: rgba(0,0,0,0.05);
        border-color: #555;
    }
</style>

<div id="cookie-banner">
    <span id="cookie-banner-text">{$messageText}</span>
    <span>
        <button id="cookie-accept">{$acceptText}</button>
        <button id="cookie-decline" style="display: none;">{$declineText}</button>
    </span>
</div>

<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const banner = document.getElementById('cookie-banner');
    const declineEnabled = {$declineEnabled};
    const bannerText = document.getElementById('cookie-banner-text');

    bannerText.innerHTML = marked.parse(bannerText.innerHTML);

    if (!document.cookie.includes("cookieConsent=")) {
        banner.style.display = 'flex';

        if (declineEnabled) {
            document.getElementById('cookie-decline').style.display = 'inline-block';
        }

        document.getElementById('cookie-accept').onclick = function() {
            document.cookie = "cookieConsent=accepted; path=/; SameSite=Lax";
            banner.remove();
        };

        const declineBtn = document.getElementById('cookie-decline');
        if (declineBtn) {
            declineBtn.onclick = function() {
                document.cookie = "cookieConsent=declined; path=/; SameSite=Lax";
                window.cookiedeclined = 1;
                banner.remove();
            };
        }
    }
});
</script>
HTML;
    }

    public function enabled(): void {}
    public function disabled(): void {}
    public function updated(): void {}
    public function install(): void {}

    private function getSetting(string $key, $default = null)
    {
        return $this->config($key, $default);
    }
}
