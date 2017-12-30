<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function files() {
//        $favorties = File::where('users_id', $this->id)->has('favoriteFiles');

//        return $this->hasMany('\App\File', 'users_id', 'id');
        return $this->belongsToMany('App\File', 'users_files', 'users_id', 'files_id')
            ->withPivot(['favorite', 'permissions'])
            ->pivot();
    }
}
