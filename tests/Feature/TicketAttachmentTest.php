<?php

namespace Tests\Feature;

use App\Livewire\Tickets\Create;
use App\Livewire\Tickets\Show;
use App\Models\Ticket;
use App\Models\TicketMessageAttachment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;
use Tests\TestCase;

class TicketAttachmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // The ticket views load the theme's assets
        $this->withoutVite();
    }

    private function replyWith(UploadedFile $file)
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create(['user_id' => $user->id, 'status' => 'open']);

        return Livewire::actingAs($user)
            ->test(Show::class, ['ticket' => $ticket])
            ->set('message', 'See the attached file')
            ->set('attachments', [$file])
            ->call('save');
    }

    protected function tearDown(): void
    {
        foreach (TicketMessageAttachment::all() as $attachment) {
            File::delete($attachment->localPath);
        }

        parent::tearDown();
    }

    public function test_disallowed_file_type_is_rejected()
    {
        $this->replyWith(UploadedFile::fake()->createWithContent('shell.php', '<?php echo 1;'))
            ->assertHasErrors('attachments.0');

        $this->assertDatabaseCount('ticket_message_attachments', 0);
    }

    public function test_file_content_must_match_its_extension()
    {
        // Livewire detects the type from the contents of real uploads; in tests it uses the fake file's type
        $this->replyWith(UploadedFile::fake()->createWithContent('photo.png', '<?php echo 1;')->mimeType('text/x-php'))
            ->assertHasErrors('attachments.0');

        $this->assertDatabaseCount('ticket_message_attachments', 0);
    }

    public function test_allowed_file_type_is_saved()
    {
        $this->replyWith(UploadedFile::fake()->createWithContent('notes.txt', "Some notes\n"))
            ->assertHasNoErrors();

        $this->assertDatabaseHas('ticket_message_attachments', ['filename' => 'notes.txt']);
    }

    public function test_any_file_type_is_allowed_when_the_setting_is_empty()
    {
        config(['settings.ticket_attachment_extensions' => []]);

        $this->replyWith(UploadedFile::fake()->createWithContent('tool.exe', 'MZ binary'))
            ->assertHasNoErrors();

        $this->assertDatabaseHas('ticket_message_attachments', ['filename' => 'tool.exe']);
    }

    public function test_new_ticket_rejects_disallowed_file_type()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('subject', 'Help')
            ->set('message', 'See the attached file')
            ->set('priority', 'low')
            ->set('attachments', [UploadedFile::fake()->createWithContent('page.html', '<script>alert(1)</script>')])
            ->call('create')
            ->assertHasErrors('attachments.0');

        $this->assertDatabaseCount('ticket_message_attachments', 0);
    }

    public function test_email_attachments_follow_the_same_rules()
    {
        config(['settings.ticket_attachment_extensions' => ['pdf', '.ZIP', 'not valid']]);

        $this->assertSame(['pdf', 'zip'], TicketMessageAttachment::allowedExtensions());
        $this->assertTrue(TicketMessageAttachment::isAllowed('report.PDF', 1024));
        $this->assertFalse(TicketMessageAttachment::isAllowed('virus.exe', 1024));
        $this->assertFalse(TicketMessageAttachment::isAllowed('archive.zip', (TicketMessageAttachment::MAX_SIZE + 1) * 1024));
        $this->assertFalse(TicketMessageAttachment::isAllowed(null, 1024));
    }
}
