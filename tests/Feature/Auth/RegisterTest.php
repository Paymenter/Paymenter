<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\Register;
use App\Models\CustomProperty;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        CustomProperty::each(function ($property) {
            $property->required = false;
            $property->save();
        });
    }

    /**
     * Test if the register page renders successfully
     */
    public function test_renders_successfully()
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);
    }

    /**
     * Test if the user can register with valid credentials
     */
    public function test_can_register_with_valid_credentials()
    {
        $response = Livewire::test(Register::class)
            ->set('first_name', 'Corwin')
            ->set('last_name', 'Corwin')
            ->set('email', 'corwin@paymenter.org')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->call('submit');

        $response->assertHasNoErrors();

        $this->assertDatabaseHas('users', [
            'email' => 'corwin@paymenter.org',
        ]);
    }

    /**
     * Test if the user can't register with invalid credentials
     */
    public function test_cant_register_with_invalid_credentials()
    {
        $response = Livewire::test(Register::class)
            ->set('first_name', 'Corwin')
            ->set('last_name', 'Corwin')
            ->set('email', 'corwin@paymenter.org')
            ->set('password', 'password')
            ->set('password_confirmation', 'pswrd')
            ->call('submit');

        $response->assertHasErrors('password');
    }

    /**
     * Test if non-optional fields are required
     */
    public function test_optional_fields_are_required()
    {
        CustomProperty::each(function ($property) {
            $property->required = true;
            $property->save();
        });

        $response = Livewire::test(Register::class)
            ->set('email', 'corwin@paymenter.org')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->call('submit');

        $response->assertHasErrors('first_name')
            ->assertHasErrors('last_name')
            ->assertHasErrors(CustomProperty::all()->pluck('key')->map(function ($property) {
                return "properties.$property";
            })->toArray());
    }
}
