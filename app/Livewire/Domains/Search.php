<?php

namespace App\Livewire\Domains;

use App\Livewire\Component;
use App\Models\Product;
use App\Models\Tld;
use App\Services\Domain\DomainCheckoutService;
use Livewire\Attributes\Validate;

class Search extends Component
{
    #[Validate('required|string|max:63|regex:/^[a-z0-9-]+$/i')]
    public string $sld = '';

    public string $tld = '';

    public int $years = 1;

    public array $availableYears = [];

    public ?array $result = null;

    public ?string $error = null;

    public ?int $productId = null;

    public function mount(): void
    {
        $this->tld = Tld::where('enabled', true)->orderBy('tld')->value('tld') ?? '';
        $this->syncYears();
    }

    public function updatedTld(): void
    {
        $this->syncYears();
        $this->result = null;
        $this->error = null;
    }

    public function updatedYears(): void
    {
        $this->result = null;
        $this->error = null;
    }

    public function check(): void
    {
        $this->validate();

        $this->result = null;
        $this->error = null;
        $this->productId = null;

        if (!in_array($this->years, $this->availableYears, true)) {
            $this->error = 'This registration period is not available.';

            return;
        }

        $tld = Tld::where('tld', $this->tld)->where('enabled', true)->first();
        $product = $tld?->product ?: ($tld ? Product::where('server_id', $tld->server_id)->where('hidden', false)->first() : null);

        if (!$tld || !$product) {
            $this->error = 'No domain product is configured for this TLD.';

            return;
        }

        $result = DomainCheckoutService::check($product, [
            'sld' => $this->sld,
            'tld' => $this->tld,
            'years' => $this->years,
        ], true);

        $this->result = $result;
        $this->productId = $product->id;

        if (!($result['available'] ?? false)) {
            $this->error = $result['error'] ?? 'This domain is not available.';
        }
    }

    public function register()
    {
        if (!$this->productId || !($this->result['available'] ?? false)) {
            return;
        }

        $product = Product::with('category')->find($this->productId);
        if (!$product || !$product->category) {
            $this->error = 'No domain product is configured for this TLD.';

            return;
        }

        return redirect()->route('products.checkout', [
            'category' => $product->category->slug,
            'product' => $product->slug,
            'config' => [
                'sld' => $this->sld,
                'tld' => $this->tld,
                'years' => $this->years,
            ],
        ]);
    }

    private function syncYears(): void
    {
        $currency = session('currency', config('settings.default_currency'));

        $this->availableYears = Tld::where('tld', $this->tld)
            ->where('enabled', true)
            ->first()
            ?->prices()
            ->where('currency_code', $currency)
            ->whereNotNull('register')
            ->orderBy('years')
            ->pluck('years')
            ->map(fn ($years) => (int) $years)
            ->unique()
            ->values()
            ->all() ?? [];

        if ($this->availableYears === []) {
            $this->availableYears = [1];
        }

        if (!in_array($this->years, $this->availableYears, true)) {
            $this->years = $this->availableYears[0];
        }
    }

    public function render()
    {
        return view('domains.search', [
            'tlds' => Tld::where('enabled', true)->with('prices')->orderBy('tld')->get(),
        ])->layoutData([
            'title' => 'Domain Search',
        ]);
    }
}
