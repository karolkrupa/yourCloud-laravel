<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\File;
use App\Helpers\FileSender;
use App\Helpers\UnitConverter;
use App\Http\Controllers\Auth\ResetPasswordController;
use Chumper\Zipper\Zipper;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class ResourceController extends Controller
{
    private $folderId = 0;
    private $folderPath;



    function route(Request $request, $userName, $path = "") {
        $this->_getFolderPath($request);

        if($this->folderPath === NULL) {
            abort(404);
        }

        if(strtolower($userName) != strtolower(Auth::user()->name)) {
            return 'Inny user';
        }

        if($request->isMethod('GET')) {
            // GET Method
            if($request->has('download_file')) {

                return $this->shareFile($request, $request->get('download_file'));
            }else {
                // FolderController
                $folderController = new FolderController($this->folderId, $this->folderPath);

                return $folderController->route($request, $userName, $path);
            }
        }else {
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

            }else if($request->has('delete_file')) {

                return $this->deleteFile(
                    $request,
                    $request->post('delete_file')
                );

            }else if($request->has('add_favorite_file')) {

                return $this->addFavoriteFile(
                    $request,
                    $request->post('add_favorite_file')
                );

            }else if($request->has('remove_favorite_file')) {

                return $this->removeFavoriteFile(
                    $request,
                    $request->post('remove_favorite_file')
                );

            }else {

                return $this->storeFile(
                    $request
                );

            }
        }
    }


    public function storeFile(Request $request)
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


        $folder = File::addFolder($folder, Auth::user());
        $folderPath = $this->folderPath . DIRECTORY_SEPARATOR . $folder->id;

        if($folder->update(['path' => $folderPath])) {
            $folder = $folder->toArray();

            $folder['size_normalized'] = UnitConverter::bytesToHuman($folder['size']);

            return Response()->json($folder, 201);
        }else {
            return Response()->json([
                'error' => "Can't create folder",
                'code' => '404'
            ], 404);
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
        $file = Auth::user()->files()->find($fileId);

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
            $file = Auth::user()->files()->find($fileId)->toArray();
            $file['size_normalized'] = UnitConverter::bytesToHuman($file['size']);

            return Response()->json($file, 201);
        }else {
            return Response('', 404);
        }
    }

    public function deleteFile(Request $request, $fileId) {
        $file = Auth::user()->files()->find($fileId);


        if(! $file) {
            return Response()->json([
                'error' => 'This file does not exist',
                'code' => '404'
            ], 404);
        }

        if($file->delete()) {
            return Response()->json([
                'success'=> true
            ]);
        }else {
            return Response()->json([
                'error' => "Can not delete this file",
                'code' => '404'
            ], 404);
        }
    }

    public function addFavoriteFile(Request $request, $id) {
        $file = Auth::user()->files()->find($id);

        if($file && $file->addToFavorites()) {

            return Response()->json(
                [
                    'success' => true
                ],
                201);
        }else {
            return Response()->json(
                [
                    'error' => 'File not found',
                    'code' => 404
                ],
                404);
        }
    }

    public function removeFavoriteFile(Request $request, $id) {
        $file = Auth::user()->files()->find($id);

        if($file && $file->removeFromFavorites()) {
            return Response()->json(
                [
                    'success' => true
                ],
                201);
        }else {
            return Response()->json(
                [
                    'error' => 'File not found',
                    'code' => 404
                ],
                404);
        }
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

    public function shareFile(Request $request, $id) {
        $file = Auth::user()->files()->find($id);

        return FileSender::shareFiles($file);
    }
}
