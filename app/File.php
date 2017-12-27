<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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
//                ->where('name', 'REGEXP', '^'. $name .'([[.(.]][[:digit:]]+[[.).]])*[[...]].*$')
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

    public function user()
    {
        return $this->belongsTo('App\User', 'users_id');
    }

    static public function addFile($fileAttr, User $user = null) {
        if($user == null) {
            $user = User::findOrFail($fileAttr['users_id']);
        }

        $fileAttr['users_id'] = $user->id;

        // File type
        $fileAttr['type'] = 1;

        $fileAttr['name'] = self::_getNameIfIsset($fileAttr);

        return File::create($fileAttr);
    }

    static public function addFolder($fileAttr, User $user = null) {
        if($user == null) {
            $user = User::findOrFail($fileAttr['users_id']);
        }

        $fileAttr['users_id'] = $user->id;

        // Folder type
        $fileAttr['type'] = 0;

        $fileAttr['name'] = self::_getNameIfIsset($fileAttr);

        return File::create($fileAttr);
    }

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

    public function isFolder() {
        return $this->type == 0;
    }

    public function isFile() {
        return $this->type == 1;
    }

    public function getFolders() {
        return File::where('parent_id', $this->id)
            ->where('type', 0)
            ->get();
    }

    public function getFiles() {
        return File::where('parent_id', $this->id)
            ->where('type', 1)
            ->get();
    }

    public function getAbsolutePath() {
        return storage_path('app'). DIRECTORY_SEPARATOR . $this->path;
    }
}
