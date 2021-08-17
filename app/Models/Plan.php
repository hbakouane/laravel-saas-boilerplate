<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    public $fillable = ['title', 'description', 'price', 'interval', 'stripe_plan_id', 'stripe_product_id'];
}
