<?php

namespace Tests\Feature;

use App\Models\Extension;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;
use Paymenter\Extensions\Others\Affiliates\Affiliates;
use Paymenter\Extensions\Others\Affiliates\Livewire\Affiliates\Affiliate;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use Tests\TestCase;

class AffiliateSignupTest extends TestCase
{
    // The extension's tables live outside the core migrations, and creating them inside
    // RefreshDatabase's transaction would commit it.
    use DatabaseMigrations;

    protected function afterRefreshingDatabase()
    {
        $this->artisan('migrate', ['--path' => 'extensions/Others/Affiliates/database/migrations']);

        Extension::create(['name' => 'Affiliates', 'extension' => 'Affiliates', 'type' => 'other', 'enabled' => true]);

        (new Affiliates)->boot();

        // Otherwise the next application to boot would load the extension without its tables
        $this->beforeApplicationDestroyed(fn () => Extension::query()->forceDelete());
    }

    private function setCodeType(string $type): void
    {
        Extension::where('extension', 'Affiliates')->first()->settings()->create([
            'key' => 'type',
            'value' => $type,
        ]);
    }

    // Called directly: the page's breadcrumb declares a global function, so it can only render once per process
    public function test_page_loads_before_settings_are_saved(): void
    {
        $this->actingAs(User::factory()->create());

        $component = new Affiliate;
        $component->mount();
        $this->assertSame('random', $component->signup_type);

        $component->signup();
        $this->assertSame(10, strlen(auth()->user()->affiliate()->first()->code));
    }

    public function test_custom_code_is_used_when_enabled(): void
    {
        $this->setCodeType('custom');
        $this->actingAs(User::factory()->create());

        $component = new Affiliate;
        $component->mount();
        $component->referral_code = 'MyCode123';
        $component->signup();

        $this->assertSame('MyCode123', auth()->user()->affiliate()->first()->code);
    }

    // In its own process, since a successful update would render the breadcrumb a second time
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_customer_cannot_switch_to_custom_codes(): void
    {
        $this->setCodeType('random');
        $this->actingAs(User::factory()->create());

        $component = Livewire::test(Affiliate::class)->assertSet('signup_type', 'random');

        $this->expectException(CannotUpdateLockedPropertyException::class);
        $component->set('signup_type', 'custom');
    }
}
