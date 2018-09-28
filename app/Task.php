<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'name', 'user_id', 'completed', 'completed_at'
    ];

    public function details()
    {
        return $this->hasMany('App\TaskDetail');
    }    
}
