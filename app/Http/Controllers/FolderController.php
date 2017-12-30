<?php

namespace App\Http\Controllers;

use App\File;
use App\Helpers\FileSender;
use App\Helpers\UnitConverter;
use App\Http\Controllers\Auth\ResetPasswordController;
use Chumper\Zipper\Zipper;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class FolderController extends Controller
{
    private $folderId = 0;
    private $folderPath;

    public function route(Request $request, $userName, $path = "") {
        $this->_getFolderPath($request);

        if($this->folderPath === NULL) {
            abort(404);
        }

        if($request->isMethod('GET')) {
            // GET Method
            if(strtolower($userName) == strtolower(Auth::user()->name)) {
                if($request->has('download_file')) {
                    return $this->shareFile($request, $request->get('download_file'));
                }else {
                    return $this->index($request, $userName, $path);
                }
            }else {
                return 'Inny user';
            }
        }else {
            $resourceController = new ResourceController($this->folderId, $this->folderPath);

            return $resourceController->route($request);
        }
    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $userName, $path)
    {
        if($this->folderId == 0) { // Main folder
            session([
                'overlap' => $request->get('overlap', 'main')
            ]);
        }

        if(session('overlap', 'main') == 'favorites') {
            $files = Auth::user()
                ->files()
                ->where('favorite', true)
                ->where('parent_id', $this->folderId)
                ->get()
                ->toArray();
        }else {
            $files = Auth::user()
                ->files()
                ->where('parent_id', $this->folderId)
                ->get()
                ->toArray();
        }


        return view('folder',
            [
                'overlap' => session('overlap', 'main'),
                'files' => $files,
                'path' => explode('/', $request->path()),
                'path_url' => URL::to('/')
            ]);
    }


    private function _getFolderPath(Request $request) {
        if($request->path()) {
            $path = urldecode($request->path());

            if($path == Auth::user()->name) {
                $this->folderPath = Auth::user()->id;
                $this->folderId = 0;

                return $this->folderPath;
            }

            $path = explode('/', $path);
            $path = array_reverse($path);

            foreach ($path as $folderName) {
                $folder = Auth::user()->files()->where('name', $folderName)->get();

                if(count($folder) > 1) {
                    $reverseArrray[] = $folderName;
                }else if(count($folder) < 1) {
                    if($folderName == Auth::user()->name) {
                        $lastFolderId = 0;
                        $path = Auth::user()->id;
                        break;
                    }else {
                        abort(404);
                    }
                }else {
                    $path = $folder->first()->path;
                    $lastFolderId = $folder->first()->id;
                    break;
                }
            }

            if(isset($reverseArrray)) {
                $reverseArrray = array_reverse($reverseArrray);

                foreach ($reverseArrray as $folderName) {
                    $folder = Auth::user()->files()
                        ->where('name', $folderName)
                        ->where('parent_id', $lastFolderId)
                        ->first();

                    if($folder == null) {
                        abort(404);
                    }

                    $lastFolderId = $folder->id;
                    $path .= DIRECTORY_SEPARATOR . $folder->id;
                }
            }

            $this->folderPath = $path;
            $this->folderId = $lastFolderId;

            return $path;
        }else {
            return abort(404);
        }
    }
}
