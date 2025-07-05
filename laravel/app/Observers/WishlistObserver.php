<?php

namespace App\Observers;

use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;

class WishlistObserver
{
    /**
     * Handle the Wishlist "created" event.
     */
    public function created(Wishlist $wishlist): void
    {
        if (Auth::check()) {
            $wishlist->user_id = Auth::user()->uuid;
        }
    }

    /**
     * Handle the Wishlist "updated" event.
     */
    public function updated(Wishlist $wishlist): void
    {
        //
    }

    /**
     * Handle the Wishlist "deleted" event.
     */
    public function deleted(Wishlist $wishlist): void
    {
        //
    }

    /**
     * Handle the Wishlist "restored" event.
     */
    public function restored(Wishlist $wishlist): void
    {
        //
    }

    /**
     * Handle the Wishlist "force deleted" event.
     */
    public function forceDeleted(Wishlist $wishlist): void
    {
        //
    }
}
