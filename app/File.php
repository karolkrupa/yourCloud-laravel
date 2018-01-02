<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Class File
 * @package App
 */
class File extends Model
{
    protected $fillable = [
        'type',
        'parent_id',
        'users_id',
        'name',
        'path',
        'mime_type',
        'size',
    ];

    protected $hidden = [
        'path',
    ];

//    protected static function boot()
//    {
//        parent::boot();
//
//        // Adding 'favorite' column
//        static::addGlobalScope('favorite_pointer', function (Builder $builder) {
////            $builder->getQuery()->leftJoin('favorite_files', 'favorite_files.files_id', '=', 'files.id');
////
////            $exp = new Expression('`files`.*, IF(`favorite_files`.`files_id` = `files`.`id`, TRUE, FALSE) as `favorite`');
////            $builder->getQuery()->select($exp);
//
//            $builder->addSelect(new Expression('`users_files`.`favorite` as `favorite`'));
//        });
//    }

    /**
     * Adds attributes to the returned model.
     *
     * @param $query
     * @return mixed
     */
    public function scopePivot($query) {
        $query->addSelect(new Expression('`users_files`.`favorite` as `favorite`'));
        $query->addSelect(new Expression('`users_files`.`permissions` as `permissions`'));
        $query->addSelect(new Expression('`users_files`.`tag_id` as `tag_id`'));
        return $query->addSelect(new Expression('`files`.*'));
    }

    /**
     * Returns file name with suitable postfix. (If file with this name already exist)
     *
     * @param $fileAttr
     * @return string
     */
    static private function _getNameIfIsset($fileAttr) {
        $user = User::find($fileAttr['users_id']);

        // Check wheter file with this name does not exist
        $check = $user->files()
            ->where('parent_id', $fileAttr['parent_id'])
            ->where('name', $fileAttr['name'])
            ->get()
            ->toArray();

        if(!empty($check)) {
            // Check wheter files with same name exist
            $check = $user->files()
                ->where('parent_id', $fileAttr['parent_id'])
                ->where('name', 'REGEXP', '^'. $fileAttr['name'] .' [[.(.]][[:digit:]]+[[.).]]$')
                ->orderBy('name', 'DESC')
                ->first();

            // Generating new name
            if($check != null) {
                preg_match('/^'. $fileAttr['name'] . ' \(([[:digit:]]+)\)$/', $check->name, $matches);

                $fileAttr['name'] .= ' ('. ($matches[1]+1) . ')';
            }else {
                $fileAttr['name'] .= ' (1)';
            }
        }

        return $fileAttr['name'];
    }

    /**
     * Returns User files.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'users_id');
    }

    /**
     * Adds file for provided user or logged user.
     *
     * @param $fileAttr
     * @param User|null $user
     * @return mixed
     */
    static public function addFile($fileAttr, User $user = null) {
        if($user == null) {
            $user = User::findOrFail($fileAttr['users_id']);
        }

        $fileAttr['users_id'] = $user->id;

        // File type
        $fileAttr['type'] = 1;

        $fileAttr['name'] = self::_getNameIfIsset($fileAttr);

        return $user->files()->save(new File($fileAttr));
    }

    /**
     * Adds folder for provided user or logged user.
     *
     * @param $fileAttr
     * @param User|null $user
     * @return mixed
     */
    static public function addFolder($fileAttr, User $user = null) {
        if($user == null) {
            $user = User::findOrFail($fileAttr['users_id']);
        }

        $fileAttr['users_id'] = $user->id;

        // Folder type
        $fileAttr['type'] = 0;

        $fileAttr['name'] = self::_getNameIfIsset($fileAttr);

        return $user->files()->save(new File($fileAttr));
    }

    /**
     * Sends file to user browser.
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function sendFile() {
        $response = response(Storage::get($this->path));

        $response->withHeaders([
            'Content-Type' => 'application/force-download',
            'Content-Disposition' => 'attachment; filename="'. $this->name .'";',
            'Content-Length' => Storage::size($this->path),
            'Expires' => 'Mon, 26 Jul 1997 05:00:00 GMT',
            'Last-Modified' => Storage::lastModified($this->path),
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache'
        ]);

        return $response;
    }

    /**
     * Checks whether selected file is folder.
     *
     * @return bool
     */
    public function isFolder() {
        return $this->type == 0;
    }

    /**
     * Checks whether selected file is file.
     *
     * @return bool
     */
    public function isFile() {
        return $this->type == 1;
    }

    /**
     * Returns collection of folders. (If selected file is a folder)
     * @return mixed
     */
    public function getFolders() {
        return File::where('parent_id', $this->id)
            ->where('type', 0)
            ->get();
    }

    /**
     * Returns Collection of files. (If selected file is a folder)
     *
     * @return Collection
     */
    public function getFiles() {
        return File::where('parent_id', $this->id)
            ->where('type', 1)
            ->get();
    }

    /**
     * Returns absolute path to file.
     *
     * @return string
     */
    public function getAbsolutePath() {
        return storage_path('app'). DIRECTORY_SEPARATOR . $this->path;
    }

    /**
     * Deletes the model and removes file from the database.
     *
     * @return bool|null
     *
     * @throws \Exception
     */
    public function delete() {
        if($this->isFolder()) {
            $files = File::where('parent_id', $this->id)
                ->where('users_id', $this->users_id)
                ->orderBy('type', 'DESC')
                ->get();

            foreach ($files as $file) {
                $file->delete();
            }
            Storage::deleteDirectory($this->path);
        }else {
            Storage::delete($this->path);
        }

        return parent::delete();
    }

    /**
     * Marks the file as a favorite.
     *
     * @param User|null $user
     * @return bool
     */
    public function setAsFavorite(User $user = null) {
        if(! $user) {
            $user = Auth::user();
        }

        if($this->pivot->favorite) {
            return true;
        }

        return $user->files()->updateExistingPivot($this->id, ['favorite' => true]);
    }

    /**
     * Marks the file as regular.
     *
     * @param User|null $user
     * @return bool
     */
    public function setAsRegular(User $user = null) {
        if(! $user) {
            $user = Auth::user();
        }

        if(! $this->pivot->favorite) {
            return true;
        }

        return $user->files()->updateExistingPivot($this->id, ['favorite' => false]);
    }

    /**
     * Adds the file to selected tag
     *
     * @param $tagId
     * @param null $user
     * @return mixed
     */
    public function setTagId($tagId = null, User $user = null) {
        if(! $user) {
            $user = Auth::user();
        }

        return $user->files()->updateExistingPivot($this->id, ['tag_id' => $tagId]);
    }
}
