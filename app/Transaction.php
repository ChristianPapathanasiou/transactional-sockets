<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{

    /**
     * The database table used by the model.
     * @var string
     */
    
    protected $table = 'transactions';

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'user_id', 
        'project_id', 
        'amount', 
        'transacted_at', 
        'expires_at'
    ];
    
    public function project()
    {
        return $this->belongsTo('App\Project');
    }
}
