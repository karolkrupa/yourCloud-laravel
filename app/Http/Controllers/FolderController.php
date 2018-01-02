<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

/**
 * Class FolderController
 * @package App\Http\Controllers
 */
class FolderController extends Controller
{
    private $folderId = 0;
    private $folderPath;

    /**
     * FolderController constructor.
     * @param $folderId
     * @param $folderPath
     */
    function __construct($folderId, $folderPath)
    {
        $this->folderId = $folderId;
        $this->folderPath = $folderPath;
    }

    /**
     * @param Request $request
     * @param $userName
     * @param string $path
     * @return Response
     */
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
