<?php

namespace App\Models;

use Dflydev\DotAccessData\Data;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory;
    protected $guarded = [];

    public static function productsCount(){
        $user = auth()->user();
        if ($user->hasRole('Super Admin')){
           return DB::table('products')->count();
        }elseif ($user->hasRole('normal user')){
            return $user->products()->count();
        }
        else
            return 0;
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_products')->withPivot('price','status');
    }

}
