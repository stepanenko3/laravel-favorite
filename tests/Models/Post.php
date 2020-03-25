<?php

namespace Stepanenko3\LaravelFavorite\Test\Models;

use Stepanenko3\LaravelFavorite\Traits\Favoriteable;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
   use Favoriteable;

   protected $table = 'posts';
   protected $guarded = [];
}
