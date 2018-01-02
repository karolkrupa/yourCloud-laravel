<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class User
 * @package App
 */
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

    /**
     * Returns list of files
     *
     * @return mixed
     */
    public function files() {
        return $this->belongsToMany('App\File', 'users_files', 'users_id', 'files_id')
            ->withPivot(['favorite', 'permissions', 'tag_id'])
            ->pivot();
    }
}
