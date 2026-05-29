<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Classroom;
use App\Models\User;

trait ChecksClassroomAccess
{
    /**
     * A user may access a classroom if they are an admin or a parent linked to it.
     */
    protected function canAccessClassroom(User $user, Classroom $classroom): bool
    {
        return $user->is_admin
            || $user->classrooms()->whereKey($classroom->getKey())->exists();
    }

    protected function authorizeClassroomAccess(User $user, Classroom $classroom): void
    {
        abort_unless($this->canAccessClassroom($user, $classroom), 403);
    }
}
