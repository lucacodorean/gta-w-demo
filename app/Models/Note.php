<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use JacobJoergensen\LaravelPaper\Attributes\ContentPath;
use JacobJoergensen\LaravelPaper\Attributes\Driver;
use JacobJoergensen\LaravelPaper\Attributes\Timestamps;
use JacobJoergensen\LaravelPaper\Paper;


#[Fillable(['user_id', 'slug', 'title', 'created_at', 'content'])]
#[Driver('markdown')]
#[ContentPath('content/notes')]
#[Timestamps]
class Note extends Model
{

    use Paper;

    /**
     * The owner is a real database row, so a plain belongsTo works here.
     * The other direction does not: see User::notes().
     */
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
