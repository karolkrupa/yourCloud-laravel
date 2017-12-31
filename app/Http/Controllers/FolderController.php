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

    function __construct($folderId, $folderPath)
    {
        $this->folderId = $folderId;
        $this->folderPath = $folderPath;
    }

    public function route(Request $request, $userName, $path = "") {

        return $this->index($request, $userName, $path);
    }

    /**
     * Display a folder view.
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

        $files = Auth::user()->files()
            ->where('parent_id', $this->folderId);

        // Select only favorites files
        if(session('overlap', 'main') == 'favorites') {
            $files = $files->where('favorite', true);
        }

        if(session('overlap', 'main') == 'tag') {
            $files = $files->where('tag_id', $request->get('tag_id', '1'));

            session([
                'overlap' => $request->get('overlap', 'main') . '-' . $request->get('tag_id', '1')
            ]);
        }

        $files = $files->get()->toArray();

        return view('folder',
            [
                'overlap' => session('overlap', 'main'),
                'files' => $files,
                'path' => explode('/', $request->path()),
                'path_url' => URL::to('/')
            ]);
    }

}
