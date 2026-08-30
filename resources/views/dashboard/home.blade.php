{{--
    Home / dashboard page: an Apple Notes style workspace.

    Layout: full bleed, edge to edge with the page (no floating window chrome).
      - a top bar spanning the full width, with the account and the sign-out action
      - a sidebar with search + the note list
      - a main pane with the note editor / empty state

    The controller currently only passes `user`, so every note related variable is
    resolved defensively below and the view degrades to an empty state.
--}}
@extends('layouts.app')

@section('title', 'Notes')
@section('body-class', 'app')

@php
    // TODO: DashboardController should pass the user's notes once NoteManager is implemented.
    $notes = collect($notes ?? []);
    $selectedNote = $selectedNote ?? $notes->first();
@endphp

@section('content')
    <div class="app-shell">
        <header class="topbar">
            <span class="topbar-brand">Notes</span>
            <div class="topbar-spacer"></div>
            <span class="topbar-meta">{{ $user?->name }}</span>

            <form method="POST" action="{{ route('logout') }}">
                {{-- CSRF protection: expands to a hidden _token field holding csrf_token(). --}}
                @csrf
                <button type="submit" class="btn btn-ghost">Sign out</button>
            </form>
        </header>

        <div class="workspace">
            <aside class="sidebar">
                <div class="sidebar-header">
                    <input type="search"
                           id="note-search"
                           class="search"
                           placeholder="Search"
                           autocomplete="off"
                           aria-label="Search notes">

                    <form method="POST" action="{{ url('/notes') }}">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-block">New note</button>
                    </form>
                </div>

                <ul class="note-list" id="note-list" data-base-url="{{ url('/notes') }}">
                    @forelse ($notes as $note)
                        <li>
                            <button type="button"
                                    class="note-row {{ $selectedNote && $selectedNote->id === $note->id ? 'is-active' : '' }}"
                                    data-note-id="{{ $note->id }}"
                                    data-note-title="{{ $note->title }}"
                                    data-note-content="{{ $note->content ?? '' }}">
                                <span class="note-row-title">{{ $note->title ?: 'New Note' }}</span>
                                <span class="note-row-meta">
                                    {{ optional($note->updated_at)->format('M j, Y') }}
                                </span>
                            </button>
                        </li>
                    @empty
                        <li class="note-list-empty" data-empty-state>No notes yet.</li>
                    @endforelse

                    <li class="note-list-empty" data-no-results hidden>No matches.</li>
                </ul>
            </aside>

            <main class="editor">
                @if ($selectedNote)
                    <form method="POST"
                          action="{{ url('/notes/' . $selectedNote->id) }}"
                          class="editor-form"
                          id="note-form">
                        {{-- CSRF protection: expands to a hidden _token field holding csrf_token(). --}}
                        @csrf
                        @method('PUT')

                        <div class="editor-toolbar">
                            <span class="editor-timestamp">
                                {{ optional($selectedNote->updated_at)->format('F j, Y \a\t g:i A') }}
                            </span>
                            <div class="topbar-spacer"></div>
                            <button type="submit" class="btn">Save</button>
                        </div>

                        <input type="text"
                               name="title"
                               id="note-title"
                               class="editor-title"
                               value="{{ old('title', $selectedNote->title) }}"
                               placeholder="Title">

                        <textarea name="content"
                                  id="note-content"
                                  class="editor-content"
                                  placeholder="Start writing…">{{ old('content', $selectedNote->content ?? '') }}</textarea>
                    </form>

                    <form method="POST"
                          action="{{ url('/notes/' . $selectedNote->id) }}"
                          class="editor-danger"
                          id="note-delete-form"
                          onsubmit="return confirm('Delete this note?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-ghost">Delete note</button>
                    </form>
                @else
                    <div class="empty-state">
                        <h2>No note selected</h2>
                        <p>Create a note to get started.</p>
                    </div>
                @endif
            </main>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* .app-shell / .topbar live in the shared layout; auth pages use them too. */
        body.app {
            display: flex;
            min-height: 100vh;
        }

        .workspace {
            display: flex;
            flex: 1 1 auto;
            min-height: 0;
        }

        /* ---------- sidebar ---------- */

        .sidebar {
            display: flex;
            flex-direction: column;
            flex: 0 0 260px;
            min-height: 0;
            background: var(--sidebar);
            border-right: 1px solid var(--border);
        }

        .sidebar-header {
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 12px;
            border-bottom: 1px solid var(--border);
        }

        .search {
            width: 100%;
            padding: 7px 10px;
            font: inherit;
            color: var(--text);
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 8px;
        }

        .search:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-soft);
        }

        .note-list {
            flex: 1 1 auto;
            margin: 0;
            padding: 8px;
            overflow-y: auto;
            list-style: none;
        }

        .note-row {
            display: block;
            width: 100%;
            padding: 9px 10px;
            font: inherit;
            text-align: left;
            color: var(--text);
            background: transparent;
            border: 0;
            border-radius: 8px;
            cursor: pointer;
        }

        .note-row:hover { background: rgba(127, 127, 127, .12); }

        .note-row.is-active { background: var(--accent); color: #1d1d1f; }

        .note-row-title {
            display: block;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .note-row-meta {
            display: block;
            margin-top: 2px;
            font-size: 12px;
            color: var(--text-muted);
        }

        .note-row.is-active .note-row-meta { color: rgba(29, 29, 31, .7); }

        .note-list-empty {
            padding: 14px 10px;
            font-size: 13px;
            color: var(--text-muted);
        }

        /* ---------- editor ---------- */

        .editor {
            display: flex;
            flex-direction: column;
            flex: 1 1 auto;
            min-width: 0;
            background: var(--bg);
        }

        .editor-form {
            display: flex;
            flex-direction: column;
            flex: 1 1 auto;
            min-height: 0;
        }

        .editor-toolbar {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 18px;
            border-bottom: 1px solid var(--border);
        }

        .editor-timestamp {
            font-size: 12px;
            color: var(--text-muted);
        }

        .editor-title,
        .editor-content {
            font: inherit;
            color: var(--text);
            background: transparent;
            border: 0;
            resize: none;
        }

        .editor-title,
        .editor-content { padding: 0 20px; }

        .editor-title {
            padding-top: 20px;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: -.02em;
        }

        .editor-content {
            flex: 1 1 auto;
            min-height: 0;
            padding-top: 12px;
            padding-bottom: 20px;
            line-height: 1.6;
        }

        .editor-title:focus,
        .editor-content:focus { outline: none; }

        .editor-danger {
            padding: 8px 14px;
            border-top: 1px solid var(--border);
        }

        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex: 1 1 auto;
            gap: 4px;
            color: var(--text-muted);
        }

        .empty-glyph { font-size: 40px; }

        .empty-state h2 {
            margin: 6px 0 0;
            font-size: 16px;
            color: var(--text);
        }

        .empty-state p { margin: 0; }

        @media (max-width: 720px) {
            .sidebar { flex-basis: 190px; }
        }
    </style>
@endpush

@push('scripts')
    <script>
        (function () {
            const search = document.getElementById('note-search');
            const list = document.getElementById('note-list');
            const rows = Array.from(document.querySelectorAll('.note-row'));
            const noResults = document.querySelector('[data-no-results]');

            if (search) {
                search.addEventListener('input', function () {
                    const term = search.value.trim().toLowerCase();
                    let visible = 0;

                    rows.forEach(function (row) {
                        const haystack = (row.dataset.noteTitle + ' ' + row.dataset.noteContent).toLowerCase();
                        const match = term === '' || haystack.includes(term);
                        row.parentElement.hidden = !match;
                        visible += match ? 1 : 0;
                    });

                    if (noResults) {
                        noResults.hidden = !(rows.length > 0 && visible === 0);
                    }
                });
            }

            // Client side note switching so the list feels instant; the form still
            // posts to the note's own URL on save.
            const form = document.getElementById('note-form');
            const deleteForm = document.getElementById('note-delete-form');
            const title = document.getElementById('note-title');
            const content = document.getElementById('note-content');
            const baseUrl = list ? list.dataset.baseUrl : '';

            rows.forEach(function (row) {
                row.addEventListener('click', function () {
                    if (!form || !title || !content) {
                        return;
                    }

                    rows.forEach(function (other) { other.classList.remove('is-active'); });
                    row.classList.add('is-active');

                    const action = baseUrl + '/' + row.dataset.noteId;
                    form.setAttribute('action', action);

                    if (deleteForm) {
                        deleteForm.setAttribute('action', action);
                    }

                    title.value = row.dataset.noteTitle || '';
                    content.value = row.dataset.noteContent || '';
                });
            });
        })();
    </script>
@endpush
