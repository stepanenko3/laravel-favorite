<?php

namespace Stepanenko3\LaravelFavorite\Test\Models;

use Stepanenko3\LaravelFavorite\Traits\Favoriteable;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
   use Favoriteable;
   
   protected $table = 'articles';
   protected $guarded = [];
}
