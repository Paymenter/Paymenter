<?php

namespace Tests\Feature\Tickets;

use App\Livewire\Tickets\Create;
use App\Livewire\Tickets\Show;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\TestCase;

class TicketValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        config(['settings.ticket_departments' => ['Support']]);
    }

    public function test_subject_longer_than_the_column_is_refused(): void
    {
        $user = User::factory()->create();

        $this->createTicket($user, str_repeat('a', 256))
            ->assertHasErrors(['subject' => 'max']);

        $this->assertDatabaseCount('tickets', 0);
    }

    public function test_subject_of_255_characters_is_accepted(): void
    {
        $user = User::factory()->create();

        $this->createTicket($user, str_repeat('a', 255))
            ->assertHasNoErrors();

        $this->assertDatabaseHas('tickets', ['user_id' => $user->id, 'subject' => str_repeat('a', 255)]);
    }

    public function test_new_ticket_message_and_attachments_are_limited(): void
    {
        $user = User::factory()->create();

        $this->createTicket($user, 'Help', str_repeat('a', 16001), $this->files(11))
            ->assertHasErrors(['message' => 'max', 'attachments' => 'max']);

        $this->assertDatabaseCount('tickets', 0);
    }

    public function test_reply_is_validated(): void
    {
        [$user, $ticket] = $this->ticket();

        Livewire::actingAs($user)
            ->test(Show::class, ['ticket' => $ticket])
            ->set('message', '')
            ->call('save')
            ->assertHasErrors(['message' => 'required']);

        Livewire::actingAs($user)
            ->test(Show::class, ['ticket' => $ticket])
            ->set('message', str_repeat('a', 16001))
            ->set('attachments', $this->files(11))
            ->call('save')
            ->assertHasErrors(['message' => 'max', 'attachments' => 'max']);

        $this->assertSame(0, $ticket->messages()->count());
    }

    public function test_longest_reply_fits_the_message_column(): void
    {
        [$user, $ticket] = $this->ticket();

        // Four bytes per character, the worst case for the TEXT column
        $message = str_repeat("\u{1F600}", 16000);

        Livewire::actingAs($user)
            ->test(Show::class, ['ticket' => $ticket])
            ->set('message', $message)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame($message, $ticket->messages()->first()->message);
    }

    private function createTicket(User $user, string $subject, string $message = 'Hello', array $attachments = [])
    {
        return Livewire::actingAs($user)
            ->test(Create::class)
            ->set('department', 'Support')
            ->set('priority', 'low')
            ->set('subject', $subject)
            ->set('message', $message)
            ->set('attachments', $attachments)
            ->call('create');
    }

    private function ticket(): array
    {
        $user = User::factory()->create();

        return [$user, Ticket::factory()->create(['user_id' => $user->id, 'status' => 'open'])];
    }

    private function files(int $count): array
    {
        return array_map(fn ($i) => UploadedFile::fake()->create("file{$i}.txt", 1), range(1, $count));
    }
}
