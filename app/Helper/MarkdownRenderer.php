<?php

declare(strict_types=1);

namespace App\Helper;

use Illuminate\Support\Str;

/**
 * Turns the markdown body of a note into HTML that is safe to drop into a page.
 *
 * Notes are stored as .md files (see App\Models\Note), so the raw `content`
 * attribute is markdown source: everything that renders it goes through here.
 */
class MarkdownRenderer
{
    /**
     * Note content is user input and is never trusted:
     *   - html_input: raw HTML in a note is escaped instead of injected,
     *   - allow_unsafe_links: javascript:/data:/vbscript: hrefs are dropped.
     */
    private const array OPTIONS = [
        'html_input' => 'escape',
        'allow_unsafe_links' => false,
    ];

    /**
     * GitHub flavoured markdown (tables, strikethrough, autolinks, task lists).
     */
    public static function toHtml(?string $markdown): string {
        $markdown = trim((string) $markdown);

        if ($markdown === '') {
            return '';
        }

        return Str::markdown($markdown, self::OPTIONS);
    }
}
