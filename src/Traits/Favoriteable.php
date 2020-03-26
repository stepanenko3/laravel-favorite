<?php

namespace Stepanenko3\LaravelFavorite\Traits;

use Stepanenko3\LaravelFavorite\Models\Favorite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

/**
 * This file is part of Laravel Favorite,
 *
 * @license MIT
 * @package Stepanenko3/laravel-favorite
 *
 * Copyright (c) 2020 Artem Stepanenko
 */
trait Favoriteable
{
    /**
     * Define a polymorphic one-to-many relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favoriteable');
    }

    /**
     * Add this Object to the user favorites
     * 
     * @param  int $user_id  [if  null its added to the auth user]
     */
    public function addFavorite($user_id = null)
    {
        $data = [
            'user_id' => $user_id ?: Auth::id(),
            'session_id' => Session::getId(),
        ];

        $favorite = $this->favorites()
            ->where('user_id', $data['user_id'])
            ->where('session_id', $data['session_id'])
            ->first();

        if ($favorite) return true;

        $favorite = new Favorite($data);
        $this->favorites()->save($favorite);

        return true;
    }

    /**
     * Remove this Object from the user favorites
     *
     * @param  int $user_id  [if  null its added to the auth user]
     * 
     */
    public function removeFavorite($user_id = null)
    {
        $this->favorites()
            ->where('user_id', $user_id ?: Auth::id())
            ->where('session_id', Session::getId())
            ->delete();

        return true;
    }

    /**
     * Toggle the favorite status from this Object
     * 
     * @param  int $user_id  [if  null its added to the auth user]
     */
    public function toggleFavorite($user_id = null)
    {
        $this->isFavorited($user_id) ? $this->removeFavorite($user_id) : $this->addFavorite($user_id);
    }

    /**
     * Check if the user has favorited this Object
     * 
     * @param  int $user_id  [if  null its added to the auth user]
     * @return boolean
     */
    public function isFavorited($user_id = null)
    {
        if (!$this->relationLoaded('favorites')) {
            $this->load('favorites');
        }
        
        return (bool) $this->favorites
            ->where('user_id', $user_id ?: Auth::id())
            ->where('session_id', Session::getId())
            ->count();
    }

    /**
     * Return a collection with the Users who marked as favorite this Object.
     * 
     * @return Collection
     */
    public function favoritedBy()
    {
        return $this->favorites()
            ->with('user')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item['user']->id => $item['user']];
            });
    }

    /**
     * Count the number of favorites
     * 
     * @return int
     */
    public function getFavoritesCountAttribute()
    {
        return $this->favorites()->count();
    }

    /**
     * @return favoritesCount attribute
     */
    public function favoritesCount()
    {
        return $this->favoritesCount;
    }

    /**
     * Add deleted observer to delete favorites registers
     * 
     * @return void
     */
    public static function bootFavoriteable()
    {
        static::deleted(
            function ($model) {
                $model->favorites()->delete();
            }
        );
    }

}