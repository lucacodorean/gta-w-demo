{{--
    Home / dashboard page: an Apple Notes style workspace.

    Layout: full bleed, edge to edge with the page (no floating window chrome).
      - a top bar spanning the full width, with the account and the sign-out action
      - a sidebar with search + the note list
      - a main pane with the note editor / empty state

    Notes are stored as markdown files, so the editor pane has two modes:
      - Edit: the raw .md source in a textarea
      - Preview: that source rendered to HTML (server side, see MarkdownRenderer)

    The controller currently only passes `user`, so every note related variable is
    resolved defensively below and the view degrades to an empty state.
--}}
@extends('layouts.app')

@use('App\Helper\MarkdownRenderer')

@section('title', 'Notes')
@section('body-class', 'app')

@php
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
                                    class="note-row {{ $selectedNote && $selectedNote->slug === $note->slug ? 'is-active' : '' }}"
                                    data-note-id="{{ $note->slug }}"
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
                          action="{{ url('/notes/' . $selectedNote->slug) }}"
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

                            {{-- Switches the pane between the markdown source and its rendering. --}}
                            <div class="mode-switch" role="group" aria-label="Editor mode">
                                <button type="button"
                                        class="mode-btn is-active"
                                        data-mode="edit"
                                        aria-pressed="true">Edit</button>
                                <button type="button"
                                        class="mode-btn"
                                        data-mode="preview"
                                        aria-pressed="false">Preview</button>
                            </div>

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

                        {{--
                            Rendered markdown. Built server side for the note that is open on
                            page load, so the very first preview needs no round trip; while
                            typing it is refreshed from /notes/preview with the same renderer.

                            The renderer escapes any raw HTML in a note and drops unsafe links,
                            so this output is safe to print unescaped.
                        --}}
                        <div class="editor-preview markdown-body"
                             id="note-preview"
                             data-preview-url="{{ url('/notes/preview') }}"
                             hidden>{!! MarkdownRenderer::toHtml(old('content', $selectedNote->content ?? '')) !!}</div>
                    </form>

                    <form method="POST"
                          action="{{ url('/notes/' . $selectedNote->slug) }}"
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

        /* ---------- edit / preview switch ---------- */

        .mode-switch {
            display: inline-flex;
            padding: 2px;
            background: var(--sidebar);
            border: 1px solid var(--border);
            border-radius: 8px;
        }

        .mode-btn {
            padding: 4px 10px;
            font: inherit;
            font-size: 12px;
            font-weight: 500;
            color: var(--text-muted);
            background: transparent;
            border: 0;
            border-radius: 6px;
            cursor: pointer;
        }

        .mode-btn:hover { color: var(--text); }

        .mode-btn.is-active {
            color: #1d1d1f;
            background: var(--accent);
        }

        /* ---------- rendered markdown ---------- */

        .editor-preview {
            flex: 1 1 auto;
            min-height: 0;
            padding: 12px 20px 20px;
            overflow-y: auto;
            line-height: 1.6;
        }

        .markdown-body:empty::before {
            content: 'Nothing to preview yet.';
            color: var(--text-muted);
        }

        .markdown-body > :first-child { margin-top: 0; }

        .markdown-body h1,
        .markdown-body h2,
        .markdown-body h3,
        .markdown-body h4 {
            margin: 1.4em 0 .5em;
            line-height: 1.3;
            letter-spacing: -.01em;
        }

        .markdown-body h1 { font-size: 22px; }
        .markdown-body h2 { font-size: 18px; }
        .markdown-body h3 { font-size: 16px; }
        .markdown-body h4 { font-size: 14px; }

        .markdown-body p,
        .markdown-body ul,
        .markdown-body ol,
        .markdown-body blockquote,
        .markdown-body pre,
        .markdown-body table { margin: 0 0 1em; }

        .markdown-body ul,
        .markdown-body ol { padding-left: 22px; }

        .markdown-body li { margin: .25em 0; }

        .markdown-body a { color: var(--text); text-underline-offset: 2px; }

        .markdown-body blockquote {
            padding: 2px 14px;
            color: var(--text-muted);
            border-left: 3px solid var(--border);
        }

        .markdown-body code {
            padding: 1px 5px;
            font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
            font-size: .9em;
            background: var(--sidebar);
            border: 1px solid var(--border);
            border-radius: 5px;
        }

        .markdown-body pre {
            padding: 12px 14px;
            overflow-x: auto;
            background: var(--sidebar);
            border: 1px solid var(--border);
            border-radius: 8px;
        }

        .markdown-body pre code {
            padding: 0;
            background: transparent;
            border: 0;
        }

        .markdown-body table {
            width: 100%;
            border-collapse: collapse;
        }

        .markdown-body th,
        .markdown-body td {
            padding: 6px 10px;
            text-align: left;
            border: 1px solid var(--border);
        }

        .markdown-body th { background: var(--sidebar); }

        .markdown-body hr {
            margin: 1.5em 0;
            border: 0;
            border-top: 1px solid var(--border);
        }

        .markdown-body img { max-width: 100%; }

        .markdown-body input[type="checkbox"] { margin-right: 6px; }

        .preview-error { color: var(--danger); }

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

                    // The pane now holds a different note: drop the rendered
                    // markdown of the previous one and go back to editing.
                    previewSource = null;
                    setMode('edit');
                });
            });

            // ---------- markdown preview ----------

            const preview = document.getElementById('note-preview');
            const modeButtons = Array.from(document.querySelectorAll('.mode-btn'));

            // Markdown the HTML currently in the preview pane was rendered from.
            // The pane starts server rendered from what the textarea holds, and
            // `null` means "whatever is in there is stale".
            let previewSource = content ? content.value : null;

            function setMode(mode) {
                const isPreview = mode === 'preview';

                if (content) {
                    content.hidden = isPreview;
                }

                if (preview) {
                    preview.hidden = !isPreview;
                }

                modeButtons.forEach(function (button) {
                    const active = button.dataset.mode === mode;
                    button.classList.toggle('is-active', active);
                    button.setAttribute('aria-pressed', active ? 'true' : 'false');
                });

                if (isPreview) {
                    renderPreview();
                }
            }

            function renderPreview() {
                if (!preview || !content || previewSource === content.value) {
                    return;
                }

                const source = content.value;
                const token = document.querySelector('meta[name="csrf-token"]');

                fetch(preview.dataset.previewUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token ? token.content : '',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ content: source }),
                }).then(function (response) {
                    if (!response.ok) {
                        throw new Error('Preview request failed: ' + response.status);
                    }

                    return response.json();
                }).then(function (data) {
                    // Safe as HTML: the server escapes raw HTML found in a note
                    // and strips unsafe links before sending it back.
                    preview.innerHTML = data.html || '';
                    previewSource = source;
                }).catch(function () {
                    preview.innerHTML = '<p class="preview-error">Preview is unavailable right now.</p>';
                    previewSource = null;
                });
            }

            modeButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    setMode(button.dataset.mode);
                });
            });
        })();
    </script>
@endpush
