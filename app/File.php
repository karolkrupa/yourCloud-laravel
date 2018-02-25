<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Expression;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

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
        $query->addSelect(new Expression('`users_files`.`share_link` as `share_link`'));
        $query->addSelect(new Expression('`users_files`.`share_type` as `share_type`'));
        return $query->addSelect(new Expression('`files`.*'));
    }

    public function scopeIncludePivot($query) {
        $query->join('users_files', 'files.id', '=', 'users_files.files_id');
        return $this->scopePivot($query);
    }

    // public function scopeMinimalExtended($query) {
    //     $distinctSharedFiles = new Expression('(SELECT DISTINCT shared_files.files_id FROM shared_files) AS shf');
    //     $query->leftJoin($distinctSharedFiles, 'files.id', '=', 'shf.files_id');

    //     $sharedControl = new Expression('IFNULL(shf.files_id, 0) AS shared');
    //     $query->addSelect($sharedControl);

    //     $favoriteDistinct

    //     return $query->addSelect(new Expression('`files`.*'));
    // }

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
    static public function createFile($name, $content, File $parent = null, User $user = null) {
        if($user == null) {
            $user = Auth::user();
        }

        if($parent == null) {
            $path = $user->id;
            $parentId = 0;
        }else {
            $path = $parent->path;
            $parentId = $parent->id;
        }

        $extensionPos = strrpos($name, '.');
        $extension = '';

        if($extensionPos) {
            $extension = substr($name, $extensionPos);
        }

        $fileAttr = [
            'name' => $name,
            'users_id' => $user->id,
            'parent_id' => $parentId,
            'type' => 1,
        ];

        $fileAttr['name'] = self::_getNameIfIsset($fileAttr);

        $path .= DIRECTORY_SEPARATOR . md5($fileAttr['name'] . $content) . $extension;
        if(! Storage::put($path, $content)) {
            return false;
        }

        $fileAttr['path'] = $path;
        $fileAttr['size'] = Storage::size($path);
        $fileAttr['mime_type'] = Storage::mimeType($path);

        return $user->files()->save(new File($fileAttr));
    }


    /**
     * Adds folder for provided user or logged user.
     *
     * @param $fileAttr
     * @param User|null $user
     * @return mixed
     */
    static public function createFolder($name, File $parent = null, User $user = null) {
        if($user == null) {
            $user = Auth::user();
        }

        if($parent == null) {
            $path = $user->id;
            $parentId = 0;
        }else {
            $path = $parent->path;
            $parentId = $parent->id;
        }

        $path .= DIRECTORY_SEPARATOR . $name;

        $fileAttr = [
            'parent_id' => $parentId,
            'name' => $name,
            'users_id' => $user->id,
            'type' => 0,
            'path' => $path,
            'mime_type' => 'directory',
            'size' => 0
        ];

        $fileAttr['name'] = self::_getNameIfIsset($fileAttr);

        return $user->files()->save(new File($fileAttr));
    }

    static public function saveFile(UploadedFile $file, File $parent = null, User $user = null) {
        if($user == null) {
            $user = Auth::user();
        }

        if($parent == null) {
            $path = $user->id;
            $parentId = 0;
        }else {
            $path = $parent->path;
            $parentId = $parent->id;
        }

        $fileAttr = [
            'parent_id' => $parentId,
            'name' => $file->getClientOriginalName(),
            'users_id' => $user->id,
            'type' => 1,
            'path' => $file->store($path),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize()
        ];

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

        try {
            $user->favoriteFiles()->attach($this->id);
        }catch(\Illuminate\Database\QueryException $e) {
            return $e->getCode() == 23000;
        }

        return true;
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

        try {
            $user->favoriteFiles()->detach($this->id);
        }catch(\Illuminate\Database\QueryException $e) {
            return $e->getCode() == 23000;
        }
        
        return true;
    }

    /**
     * Adds the file to selected tag
     *
     * @param $tagId
     * @param null $user
     * @return mixed
     */
    public function setTagId($tagId, User $user = null) {
        if(! $user) {
            $user = Auth::user();
        }

        if(! $tagId) {
            try {
                $user->taggedFiles()->detach($this->id);
            }catch(\Illuminate\Database\QueryException $e) {
                return $e->getCode() == 23000;
            }
        }else {
            try {
                if(! $this->getTagId()) {
                    $user->taggedFiles()->attach($this->id, ['tag_id' => $tagId]);
                }else {
                    $user->taggedFiles()->updateExistingPivot($this->id, ['tag_id' => $tagId]);
                }
            }catch(\Illuminate\Database\QueryException $e) {
                return $e->getCode() == 23000;
            }
        }

        return true;
    }

    public function getTagId(User $user = null) {
        if($user == null) {
            $user = Auth::user();
        }

        $pivot = DB::table('file_tags')
            ->where('files_id', $this->id)
            ->where('users_id', $user->id)
            ->select('tag_id')
            ->first();

        return $pivot? $pivot->tag_id : 0;
    }

    /**
     * Returns extended iformations.
     *
     * @return array
     */
    public function getExtendedInfo(User $userIdOrUser = null) {
        if(! $userIdOrUser) {
            $userIdOrUser = Auth::user();
        }

        if(is_a($userIdOrUser, User::class)) {
            $userId = $userIdOrUser->id;
        }else {
            $userId = $userIdOrUser;
        }

        $info = $this->attributes;
        unset($info['pivot']);

        $shareUsers = DB::table('users')
            ->join('shared_files', 'users.id', '=', 'shared_files.shared_for')
            ->where('shared_files.files_id', $this->id)
            ->select(['users.name', 'users.id'])
            ->get();

        $favorite = DB::table('favorite_files')
            ->where('files_id', $this->id)
            ->where('users_id', $userId)
            ->select('*')
            ->get();

        $info['share_users'] = $shareUsers->toArray();
        foreach($info['share_users'] as &$user) {
            $user = (array)$user;
        }
        $info['tag_id'] = $this->getTagId();
        $info['favorite'] = empty($favorite->toArray())? false : true;
        $info['parents'] = $this->getParents(true);

        return $info;
    }

    public function getParents($minimized = false)
    {
        $parents = [];

        if($this->parent_id < 1) {
            return $parents;
        }

        $parent = $this->getParent();

        do{
            if($minimized) {
                $parents[] = [
                    'name' => $parent->name,
                    'id' => $parent->id
                ];
            }else {
                $parents[] = $parent;
            }
        }while($parent = $parent->getParent());

        return array_reverse($parents);
    }


    /**
     * Shares file for another user.
     *
     * @param $userId
     */
    public function shareFor($userOrId) {
        if(is_a($userOrId, User::class)) {
            $user = $userOrId;
        }else {
            $user = User::find($userOrId);
        }

        if(! $user) {
            return false;
        }

        if($this->users_id == $user->id) {
            return false;
        }

        try {
            $user->filesSharedForMe()->attach($this->id, [
                'permissions' => '7',
                'owner_id' => $this->users_id,
            ]);
        }catch(\Illuminate\Database\QueryException $e) {
            return $e->getCode() == 23000;
        }

        return true;
    }

    /**
     * Removes sharing for user.
     *
     * @param $userId
     */
    public function removeSharingFor($userOrId) {
        if(is_a($userOrId, User::class)) {
            $user = $userOrId;
        }else {
            $user = User::findOrFail($userOrId);
        };

        if(! $user) {
            return false;
        }

        try {
            $user->filesSharedForMe()->detach($this->id);
        }catch(\Illuminate\Database\QueryException $e) {
            return $e->getCode() == 23000;
        }


        return true;
    }

    public function shareByLink() {
        $shareId = md5(uniqid(rand(), true));

        $this->share_link = $shareId;

        $this->save();
    }

    public function removeShareByLink() {
        $this->share_link = null;

        $this->save();
    }

    public function isOwner($userIdOrUser) {
        if(is_a($userIdOrUser, User::class)) {
            $userId = $userIdOrUser->id;
        }else {
            $userId = $userIdOrUser;
        }

        return $this->users_id == $userId;
    }

    public function hasPermissions($userIdOrUser = null) {
        if(is_a($userIdOrUser, User::class)) {
            $user = $userIdOrUser;
        }else if($userIdOrUser == null) {
            $user = Auth::user();
        }else {
            $user = User::find($userIdOrUser);
        }

        if($this->isOwner($user)) {
            return true;
        }

        // Check in shared files
        $sharedFile = $user->sharedForMe()
            ->where('files.id', $this->id)
            ->first();

        if($sharedFile != null) {
            return $sharedFile->pivot->permissions;
        }

        // Check parent file
        $parentFile = $this->getParent();

        if($parentFile) {
            return $parentFile->hasPermissions();
        }else {
            return false;
        }
    }

    public function getParent() {
        $parent = File::find($this->parent_id);
        
        return $parent? $parent : false;
    }
}
