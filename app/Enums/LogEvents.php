<?php

declare(strict_types=1);

namespace App\Enums;

enum LogEvents: string
{
    case NOTE_CREATE_UNAUTHENTICATED = 'note.create.unauthenticated';
    case NOTE_CREATED = 'note.created';
    case NOTE_UPDATE_INVALID = 'note.update.invalid';
    case NOTE_UPDATE_FORBIDDEN = 'note.update.forbidden';
    case NOTE_UPDATED = 'note.updated';
    case NOTE_DELETE_FORBIDDEN = 'note.delete.forbidden';
    case NOTE_DELETED = 'note.deleted';

    case LOGIN_FAILED = 'auth.login.failed';
    case LOGIN_SUCCEEDED = 'auth.login.succeeded';
    case LOGOUT = 'auth.logout';

    /**
     * The human readable line written next to the key.
     */
    public function message(): string {
        return match ($this) {
            self::NOTE_CREATE_UNAUTHENTICATED => 'A note attempt from a not logged client has been made.',
            self::NOTE_CREATED => 'User created a new note.',
            self::NOTE_UPDATE_INVALID => 'User tried to update a note with invalid data.',
            self::NOTE_UPDATE_FORBIDDEN => 'User tried to update a note that is not owned by them.',
            self::NOTE_UPDATED => 'User updated a note.',
            self::NOTE_DELETE_FORBIDDEN => 'User tried to delete a note that is not owned by them.',
            self::NOTE_DELETED => 'User deleted a note.',

            self::LOGIN_FAILED => 'Login attempt rejected.',
            self::LOGIN_SUCCEEDED => 'Login successfully made.',
            self::LOGOUT => 'Session ended by the user.',
        };
    }
}
