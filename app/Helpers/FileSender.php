<?php
/**
 * Created by PhpStorm.
 * User: karol
 * Date: 27.12.17
 * Time: 00:00
 */

namespace App\Helpers;


use App\File;
use Chumper\Zipper\Zipper;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FileSender
{
    static private function zipperAddFolder($zipper, $folder, $prevPath = '') {
//        $files = Auth::user()->files()
//            ->where('parent_id', $folder->id)
//            ->where('type', 1)
//            ->get();

        $files = $folder->getFiles();

//        $folders = Auth::user()->files()
//            ->where('parent_id', $folder->id)
//            ->where('type', 0)
//            ->get();

        $folders = $folder->getFolders();

        $prevPath .= '/'. $folder->name;

        $zipper->addEmptyDir($prevPath);

        $zipper->folder($prevPath);

        if($files) {
            foreach ($files as $file) {
                $path = storage_path('app'). DIRECTORY_SEPARATOR . $file->path;
                $zipper->add($path, $file->name);
            }
        }

        if($folders) {
            foreach ($folders as $folder) {
                self::zipperAddFolder($zipper, $folder, $prevPath);
            }
        }
    }

    static public function shareFiles($filesOrFile, $fileName = false, $deleteIfMultiple = true) {
        if(is_a($filesOrFile, Collection::class) || $filesOrFile->isFolder()) { // Multiple files or one folder
            $zipper = new Zipper();

            if(is_a($filesOrFile, Collection::class)) {
                $fileName = isset(Auth::user()->name)? Auth::user()->name : 'undefinedUser';
                $fileName .= time();
            }else {
                $fileName = $filesOrFile->name? $filesOrFile->name : 'undefinedFolder';

                // Convert one folder to array for foreach
                $filesOrFile = [ $filesOrFile ];
            }

            $zipPath = storage_path('app/tmp'). DIRECTORY_SEPARATOR . $fileName;

            $zipper->make($zipPath);

            foreach ($filesOrFile as $file) {
                $path = storage_path('app'). DIRECTORY_SEPARATOR . $file->path;
                if($file->isFolder()) {
                    self::zipperAddFolder($zipper, $file);
                    $zipper->home();
                }else {
                    $zipper->add($path, $file->name);
                }
            }

            $zipper->close();

            return self::send($zipPath, $fileName, $deleteIfMultiple);
        }else if(is_a($filesOrFile, File::class)) { // Regular file
            return self::send($filesOrFile->getAbsolutePath(), $filesOrFile->name);
        }

        return false;
    }


    static public function send($filePath, $fileName = false, $delete = false) {
        $fs = new Filesystem();

//       return ($fileName != false)? $fileName : $fs->name($filePath);

        $fileName = $fileName? $fileName : $fs->name($filePath);

        $response = response($fs->get($filePath));

        $response->withHeaders([
            'Content-Type' => 'application/force-download',
            'Content-Disposition' => 'attachment; filename="'. $fileName .'";',
            'Content-Length' => $fs->size($filePath),
            'Expires' => 'Mon, 26 Jul 1997 05:00:00 GMT',
            'Last-Modified' => $fs->lastModified($filePath),
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache'
        ]);

        if($delete) {
            $fs->delete($filePath);
        }

        return $response;
    }
}