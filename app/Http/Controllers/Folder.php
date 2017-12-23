<?php

namespace App\Http\Controllers;

use App\File;
use App\Helpers\UnitConverter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Folder extends Controller
{
    private $folderId = 0;
    private $folderPath;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $userName, $path = null)
    {
        if($request->has('download_file')) {
            return $this->shareFile($request->get('download_file'));
        }

        if(isset($path)) {
            $this->folderId = $this->_getFolderId($request);
        }else {
            $this->folderPath = $this->_getFolderPath($request);
        }

        $files = Auth::user()
            ->files()
            ->where('parent_id', $this->folderId)
            ->get()
            ->toArray();

        return view('folder', ['files' => $files]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $user_name, $path = '')
    {
        $this->folderId = $this->_getFolderId($request);
        $this->folderPath = $this->_getFolderPath($request);

        $fileAttr['parent_id'] = $this->folderId;

        if($request->has('create_folder')) {
            $fileAttr['path'] = $this->folderPath . '/' . $request->create_folder;
            $fileAttr['name'] = $request->create_folder;
            $fileAttr['mime_type'] = 'folder';
            $fileAttr['size'] = 0;

            $file = File::addFolder($fileAttr, Auth::user());
        }else if($request->has('file')) {
            $fileAttr['path'] = $request->file('file')->store($this->folderPath);
//            $extension = $request->file('file')->getClientOriginalExtension();
//            $fileAttr['name'] = str_replace('.'.$extension, '', $request->file('file')->getClientOriginalName());
            $fileAttr['name'] = $request->file('file')->getClientOriginalName();
            $fileAttr['mime_type'] = $request->file('file')->getMimeType();
            $fileAttr['size'] = $request->file('file')->getClientSize();

            $file = File::addFile($fileAttr, Auth::user());
        }

        $file = $file->toArray();
        $file['size_normalized'] = UnitConverter::bytesToHuman($file['size']);

        return Response()->json($file);
    }


    public function get($file_id) {

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    private function _getFolderPath(Request $request) {
        return Auth::user()->id . substr($request->path(), strpos($request->path().DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR));
    }

    private function _getFolderId(Request $request) {
        $folderPath = $this->_getFolderPath($request);

        if($folderPath == (string)Auth::user()->id) {
            return 0;
        }else {
            $folder = Auth::user()->files()
                ->select('id')
                ->where('path', $folderPath)
                ->where('type', 0)
                ->first();

            return ($folder != NULL)? $folder->id : NULL;
        }
    }

    public function shareFile($id) {
        $file = File::find($id);

        $response = response(Storage::get($file->path));
        $response->withHeaders([
            'Content-Type' => 'application/force-download',
            'Content-Disposition' => 'attachment; filename="'. $file->name .'";',
            'Content-Length' => Storage::size($file->path),
            'Expires' => 'Mon, 26 Jul 1997 05:00:00 GMT',
            'Last-Modified' => Storage::lastModified($file->path),
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache'
        ]);

        return $response;
    }
}
