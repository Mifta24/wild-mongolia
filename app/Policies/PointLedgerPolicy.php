<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PointLedger;

class PointLedgerPolicy
{
    /**
     * Determine if the user can view any point ledgers
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine if the user can view the point ledger
     */
    public function view(User $user, PointLedger $pointLedger): bool
    {
        return $user->id === $pointLedger->user_id || $user->hasRole('admin');
    }

    /**
     * Determine if the user can create point ledgers
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine if the user can update the point ledger
     */
    public function update(User $user, PointLedger $pointLedger): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine if the user can delete the point ledger
     */
    public function delete(User $user, PointLedger $pointLedger): bool
    {
        return $user->hasRole('admin');
    }
}
