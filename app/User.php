<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class User
 * @package App
 */
class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

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
        return $this->hasMany(File::class, 'users_id');
    }

    public function filesSharedForMe() {
        return $this->belongsToMany('App\File', 'shared_files', 'shared_for', 'files_id')
            ->where('shared_files.owner_id', '<>', $this->id);
    }

    public function filesSharedByMe() {
        return $this->belongsToMany('App\File', 'shared_files', 'owner_id', 'files_id')
            ->where('shared_files.owner_id', $this->id);
    }

    public function taggedFiles() {
        return $this->belongsToMany('App\File', 'file_tags', 'users_id', 'files_id')
            ->withPivot(['tag_id']);
    }

    public function favoriteFiles() {
        return $this->belongsToMany('App\File', 'favorite_files', 'users_id', 'files_id');
    }
}
