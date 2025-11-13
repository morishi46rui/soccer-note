<?php

declare(strict_types=1);

namespace App\UseCase\Note;

use App\Models\Note;

class DeleteNoteAction
{
    public function execute(Note $note): bool
    {
        return $note->delete();
    }
}
