<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Note;
use Illuminate\Container\Attributes\Singleton;

#[Singleton]
final readonly class NoteManager
{
    public function createNote(Note $note) {
        // TODO: Create a Note. Ensure that it is stored properly.
    }

    public function updateNote(Note $note) {
        // TODO: When updates are made in the .md file, also update the timestamps.
    }

    public function deleteNote(Note $note) {
        // TODO: Delete the note from the database, ensure that the .md file at content_path is deleted as well.
    }
}
