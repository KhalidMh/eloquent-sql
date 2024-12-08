<?php

namespace KhalidMh\EloquentSQL\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use KhalidMh\EloquentSQL\Database\Factories\UserFactory;

class User extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return UserFactory::new();
    }
}
