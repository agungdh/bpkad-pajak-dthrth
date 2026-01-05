<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skpd extends Model
{
    /** @use HasFactory<\Database\Factories\SkpdFactory> */
    use HasFactory;
    use \App\Traits\HasUuid;
}
