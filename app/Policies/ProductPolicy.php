<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function create(User $user)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }

    public function update(User $user)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }

    public function delete(User $user)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }
}
