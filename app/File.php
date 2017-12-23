<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
}
