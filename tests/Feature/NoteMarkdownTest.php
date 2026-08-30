<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoteMarkdownTest extends TestCase
{
    use RefreshDatabase;

    /** Notes are files on disk, so every note made here is removed again. */
    private array $notes = [];

    protected function tearDown(): void
    {
        foreach ($this->notes as $note) {
            $note->delete();
        }

        $this->notes = [];

        parent::tearDown();
    }

    private function makeUser(): User
    {
        return User::create([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => 'password123',
        ]);
    }

    private function makeNote(User $user, string $content): Note
    {
        $note = Note::create([
            'slug' => 'test-' . bin2hex(random_bytes(8)),
            'user_id' => $user->id,
            'title' => 'Markdown note',
            'content' => $content,
            'created_at' => now()->toDateTimeString(),
        ]);

        $this->notes[] = $note;

        return $note;
    }

    public function test_dashboard_renders_the_selected_note_as_markdown(): void
    {
        $user = $this->makeUser();
        $this->makeNote($user, "# Heading\n\nSome **bold** text.");

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertOk();
        $response->assertSee('<h1>Heading</h1>', false);
        $response->assertSee('<strong>bold</strong>', false);
    }

    public function test_preview_endpoint_renders_unsaved_markdown(): void
    {
        $user = $this->makeUser();

        $response = $this->actingAs($user)->postJson(route('preview-note'), [
            'content' => "## Draft\n\n- one\n- two",
        ]);

        $response->assertOk();
        $this->assertStringContainsString('<h2>Draft</h2>', $response->json('html'));
        $this->assertStringContainsString('<li>one</li>', $response->json('html'));
    }

    public function test_preview_neither_injects_raw_html_nor_unsafe_links(): void
    {
        $user = $this->makeUser();

        $response = $this->actingAs($user)->postJson(route('preview-note'), [
            'content' => "<script>alert('xss')</script>\n\n[x](javascript:alert(1))",
        ]);

        $html = $response->assertOk()->json('html');

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringNotContainsString('javascript:', $html);
    }

    public function test_preview_is_closed_to_guests(): void
    {
        $this->post(route('preview-note'), ['content' => '# hi'])
            ->assertRedirect(route('login-form'));
    }
}
