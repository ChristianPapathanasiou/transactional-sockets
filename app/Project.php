<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{

    /**
     * The database table used by the model.
     * @var string
     */
    
    protected $table = 'projects';

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'name', 
        'amount_goal', 
        'amount_raised', 
        'amount_reserved'
    ];
}