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

class Folder extends Controller
{
    private $folderId = 0;
    private $folderPath;

    public function route(Request $request, $userName, $path = "") {
//        return $this->_getFolderId($request);
//        $this->folderId = $this->_getFolderId($request);
        $this->_getFolderPath($request);

        if($this->folderId === NULL) {
            abort(404);
        }

        if($request->isMethod('post')) {
            // POST Method
            if($request->has('new_folder')) {

                return $this->createFolder(
                    $request,
                    $request->post('new_folder', 'New Folder')
                );

            }else if($request->has('new_file')) {

                return $this->createFile(
                    $request,
                    $request->post('new_file', 'New File'),
                    $request->post('file_content', '')
                );

            }else if ($request->has('rename_file')){

                return $this->renameFile(
                    $request,
                    $request->post('rename_file'),
                    $request->post('file_id')
                );

            }else {

                return $this->storeFile(
                    $request,
                    $userName,
                    $path
                );

            }
        }else if($request->isMethod('get')) {
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
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $userName, $path)
    {
        $files = Auth::user()
            ->files()
            ->where('parent_id', $this->folderId)
            ->get()
            ->toArray();

        return view('folder',
            [
                'files' => $files,
                'path' => explode('/', $request->path()),
                'path_url' => URL::to('/')
            ]);
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
    public function storeFile(Request $request, $user_name, $path = '')
    {
        $this->folderId = $this->_getFolderId($request);
        $this->folderPath = $this->_getFolderPath($request);

        $fileAttr['parent_id'] = $this->folderId;

        $fileAttr['path'] = $request->file('file')->store($this->folderPath);
        $fileAttr['name'] = $request->file('file')->getClientOriginalName();
        $fileAttr['mime_type'] = $request->file('file')->getMimeType();
        $fileAttr['size'] = $request->file('file')->getClientSize();
        $file = File::addFile($fileAttr, Auth::user());

        $file = $file->toArray();
        $file['size_normalized'] = UnitConverter::bytesToHuman($file['size']);

        return Response()->json($file, 201);
    }

    public function createFolder(Request $request, $folderName) {
        $folder = [
            'parent_id' => $this->folderId,
//            'path' => $this->folderPath . '/' . $folderName,
            'name' => $folderName,
            'mime_type' => 'folder',
            'size' => 0,
        ];

//        $folder = File::addFolder($folder, Auth::user())->toArray();
        $folder = File::addFolder($folder, Auth::user());
        $folderPath = $this->folderPath . DIRECTORY_SEPARATOR . $folder->id;

        if($folder->update(['path' => $folderPath])) {
            $folder = $folder->toArray();

            $folder['size_normalized'] = UnitConverter::bytesToHuman($folder['size']);

            return Response()->json($folder, 201);
        }else {
            return Response()->json(['msg' => "Can't create folder"], 404);
        }
    }

    public function createFile(Request $request, $fileName, $fileContent) {
        $filePath = $this->folderPath. DIRECTORY_SEPARATOR . $fileName;
        $path = Storage::put($filePath, $fileContent);

        $file = [
            'parent_id' => $this->folderId,
            'path' => $path,
            'name' => $fileName,
            'mime_type' => 'folder',
            'size' => 0,
        ];

        $file = File::addFile($file, Auth::user())->toArray();

        $file['size_normalized'] = UnitConverter::bytesToHuman($file['size']);

        return Response()->json($file, 201);
    }

    public function renameFile(Request $request, $fileName, $fileId) {
        $file = Auth::user()->files()->where('id', $fileId)->first();

        if($file == null) {
            return Response()->json([
                'error' => 'This file does not exist',
                'code' => 404
            ], 404);
        }

        $checkFile = Auth::user()->files()
            ->where('name', $fileName)
            ->where('parent_id', $file->parent_id)
            ->first();

        if($checkFile != null) {
            return Response()->json([
                'error' => 'File with this name already exist',
                'code' => 409
            ], 409);
        }

        $file->name = $fileName;

        $file = $file->save();

        if($file) {
            $file = Auth::user()->files()->where('id', $fileId)->first()->toArray();
            $file['size_normalized'] = UnitConverter::bytesToHuman($file['size']);

            return Response()->json($file, 201);
        }else {
            return Response('', 404);
        }
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
        if($request->path()) {
            $path = urldecode($request->path());

            if($path == Auth::user()->name) {
                return Auth::user()->id;
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
            abort(404);
        }
//
//
//        $path = Auth::user()->id . substr($request->path(), strpos($request->path().DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR));
//        return urldecode($path);
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

    public function shareFile(Request $request, $id) {
        $file = Auth::user()->files()->find($id);

        return FileSender::shareFiles($file);
    }
}
