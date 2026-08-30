<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\HttpCodes;
use App\Enums\LogEvents;
use App\Helper\Logger;
use App\Helper\MarkdownRenderer;
use App\Models\Note;
use App\Services\NoteManager;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
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

    /**
     * Renders markdown for the editor preview pane.
     *
     * The editor holds unsaved markdown, so the preview cannot be built from
     * the stored file: the browser posts what is currently typed and gets the
     * rendered HTML back, using the very same renderer as the saved view.
     */
    public function previewNote(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'content' => 'nullable|string',
        ]);

        return response()->json([
            'html' => MarkdownRenderer::toHtml($validated['content'] ?? ''),
        ]);
    }

    public function createNote(Request $request): Response|RedirectResponse
    {
        if(!auth()->check()) {
            $this->log(LogEvents::NOTE_CREATE_UNAUTHENTICATED,
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

        $this->log(LogEvents::NOTE_CREATED, $request, HttpCodes::HTTP_CREATED->value);

        return response()->redirectToRoute('home');
    }

    public function updateNote(Request $request, Note $note): RedirectResponse {
        $this->validateNoteAccess($request, $note, LogEvents::NOTE_UPDATE_FORBIDDEN);

        if(!$request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ])) {
            $this->log(
                LogEvents::NOTE_UPDATE_INVALID,
                $request,
                HttpCodes::HTTP_UNPROCESSABLE_ENTITY->value,
                $request->all(),
                'warning'
            );

            abort(HttpCodes::HTTP_UNPROCESSABLE_ENTITY->value);
        }

        $this->noteManager->updateNote($note, $request['title'], $request['content']);

        $this->log(
            LogEvents::NOTE_UPDATED,
            $request,
            HttpCodes::HTTP_OK->value,
            ['note-id' => $note->getKey()],
            'alert'
        );

        return response()->redirectToRoute('home')
            ->with('selectedNote', $note->getKey());
    }

    public function deleteNote(Request $request, Note $note): RedirectResponse {

        $this->validateNoteAccess($request, $note, LogEvents::NOTE_DELETE_FORBIDDEN);

        $noteReference = $note->getKey();
        $this->noteManager->deleteNote($note);

        $this->log(
            LogEvents::NOTE_DELETED,
            $request,
            HttpCodes::HTTP_DELETED->value,
            ['note-id' => $noteReference],
            'alert'
        );

        return response()->redirectToRoute('home');
    }

    private function validateNoteAccess(Request $request, Note $note, LogEvents $event) {
        if(Gate::denies('operate-note', $note)) {
            $this->log($event,
                $request,
                HttpCodes::HTTP_UNAUTHORIZED->value,
                ['note-slug' => $note->getKey()],
                'warning'
            );
            abort(HttpCodes::HTTP_UNAUTHORIZED->value);
        }
    }
}
