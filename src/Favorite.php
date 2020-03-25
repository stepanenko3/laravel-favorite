<?php

namespace Stepanenko3\LaravelFavorite;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Stepanenko3\LaravelFavorite\Models\Favorite as FavoriteModel;

/**
 * This file is part of Laravel Favorite,
 *
 * @license MIT
 * @package Stepanenko3/laravel-favorite
 *
 * Copyright (c) 2016 Christian Kuri
 */
class Favorite
{
     protected $config;

     public function __construct()
     {
          $this->config = config('favorites');
     }

     /**
      * Convert session favorites to user favorites
      * @param $sessionId
      * @param $user_id
      */
     public function convertSessionToUserFavorites($sessionId, $user_id)
     {
          // sessions need to be active
          if ($this->config['sessions']) {
               FavoriteModel::where('session_id', $sessionId)
                    ->whereNull('user_id')
                    ->update(['user_id' => $user_id]);
          }
     }
}
