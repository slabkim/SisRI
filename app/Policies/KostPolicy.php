<?php

namespace App\Policies;

use App\Models\Kost;
use App\Models\User;

class KostPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isOwner();
    }

    public function create(User $user): bool
    {
        return $user->isOwner();
    }

    public function update(User $user, Kost $kost): bool
    {
        return $user->isOwner() && $kost->owner_id === $user->id;
    }

    public function delete(User $user, Kost $kost): bool
    {
        return $this->update($user, $kost);
    }

    public function restore(User $user, Kost $kost): bool
    {
        return $this->update($user, $kost);
    }

    public function forceDelete(User $user, Kost $kost): bool
    {
        return $this->update($user, $kost);
    }
}
