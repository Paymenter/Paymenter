<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\Register;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        config(['settings.optional_fields' => [
            'first_name',
            'last_name',
            'phone',
            'company_name',
            'country',
            'address',
            'address2',
            'city',
            'state',
            'zip',
        ]]);
    }

    /**
     * @test
     * Test if the register page renders successfully
     */
    public function renders_successfully()
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);
    }

    /**
     * @test
     * Test if the user can register with valid credentials
     */
    public function can_register_with_valid_credentials()
    {
        $response = Livewire::test(Register::class)
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
     * @test
     * Test if the user can't register with invalid credentials
     */
    public function cant_register_with_invalid_credentials()
    {
        $response = Livewire::test(Register::class)
            ->set('email', 'corwin@paymenter.org')
            ->set('password', 'password')
            ->set('password_confirmation', 'pswrd')
            ->call('submit');

        $response->assertHasErrors('password');
    }

    /**
     * @test
     * Test if non-optional fields are required
     */
    public function optional_fields_are_required()
    {
        config(['settings.optional_fields' => []]);
        $response = Livewire::test(Register::class)
            ->set('email', 'corwin@paymenter.org')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->call('submit');

        $response->assertHasErrors('first_name')
            ->assertHasErrors('last_name')
            ->assertHasErrors('phone')
            ->assertHasErrors('company_name')
            ->assertHasErrors('address')
            ->assertHasErrors('address2')
            ->assertHasErrors('city')
            ->assertHasErrors('state')
            ->assertHasErrors('zip');

    }

}
