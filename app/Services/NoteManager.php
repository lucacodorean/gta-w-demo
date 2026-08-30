<?php

declare(strict_types=1);

namespace App\Services;

use App\Helper\SlugGenerator;
use App\Models\Note;
use Illuminate\Container\Attributes\Singleton;
use Illuminate\Contracts\Auth\Authenticatable;

#[Singleton]
final readonly class NoteManager
{
    public function initializeBlankNote(Authenticatable $user): Note {
        return Note::create([
            'slug' => SlugGenerator::generate(),
            'user_id' => $user->id,
            'created_at' => now()->toDateTimeString(),
        ]);
    }

    public function updateNote(Note $note, string $title, string $content): void {
        $note->update([
            'title' => $title,
            'content' => $content,
        ]);

        $note->save();
    }

    public function deleteNote(Note $note): void {
        $note->delete();
    }
}
