<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\HttpCodes;
use App\Helper\Logger;
use App\Models\Note;
use App\Services\NoteManager;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class DashboardController extends Controller
{
    use Logger;

    public function __construct(
        private readonly NoteManager $noteManager
    )
    {
        // Empty on purpose
    }

    public function showHome() {
        $user = auth()->user();

        return view('dashboard.home', [
            'user'  => $user,
            'notes' => $user->notes()
        ]);
    }

    public function createNote(Request $request): Response|RedirectResponse
    {
        if(!auth()->check()) {
            $this->log('A note attempt from a not logged client has been made.',
                $request,
                HttpCodes::HTTP_UNAUTHORIZED->value,
                self::getEmptyContext(),
                'warning'
            );

            return redirect()->route('login');
        }

        /** @var Authenticatable $currentUser */
        $currentUser = auth()->user();

        $this->noteManager->initializeBlankNote($currentUser);

        $this->log('User created a new note.', $request, HttpCodes::HTTP_CREATED->value);

        return response()->redirectToRoute('home');
    }

    public function updateNote(Request $request, Note $note): RedirectResponse {
        $this->validateNoteAccess($request, $note, 'User tried to update a note that is not owned by them.');

        if(!$request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ])) {
            $this->log(
                'User tried to update a note with invalid data.',
                $request,
                HttpCodes::HTTP_UNPROCESSABLE_ENTITY->value,
                $request->all(),
                'warning'
            );

            abort(HttpCodes::HTTP_UNPROCESSABLE_ENTITY->value);
        }

        $this->noteManager->updateNote($note, $request['title'], $request['content']);

        $this->log(
            'User updated a note.',
            $request,
            HttpCodes::HTTP_OK->value,
            ['note-id' => $note->getKey()],
            'alert'
        );

        return response()->redirectToRoute('home')
            ->with('selectedNote', $note->getKey());
    }

    public function deleteNote(Request $request, Note $note): RedirectResponse {

        $this->validateNoteAccess($request, $note, 'User tried to delete a note that is not owned by them.');

        $noteReference = $note->getKey();
        $this->noteManager->deleteNote($note);

        $this->log(
            'User deleted a note.',
            $request,
            HttpCodes::HTTP_DELETED->value,
            ['note-id' => $noteReference],
            'alert'
        );

        return response()->redirectToRoute('home');
    }

    private function validateNoteAccess(Request $request, Note $note, string $logMessage) {
        if(Gate::denies('operate-note', $note)) {
            $this->log($logMessage,
                $request,
                HttpCodes::HTTP_UNAUTHORIZED->value,
                ['note-slug' => $note->getKey()],
                'warning'
            );
            abort(HttpCodes::HTTP_UNAUTHORIZED->value);
        }
    }
}
