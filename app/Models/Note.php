<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use JacobJoergensen\LaravelPaper\Attributes\ContentPath;
use JacobJoergensen\LaravelPaper\Attributes\Driver;
use JacobJoergensen\LaravelPaper\Paper;


#[Fillable(['user_id'])]
#[Driver('markdown')]
#[ContentPath('content/notes')]
class Note extends Model
{

    use Paper;

    public $timestamps = true;


    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
