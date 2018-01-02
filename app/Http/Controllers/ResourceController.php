<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\File;
use App\Helpers\FileSender;
use App\Helpers\UnitConverter;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Class ResourceController
 * @package App\Http\Controllers
 */
class ResourceController extends Controller
{
    private $folderId = 0;
    private $folderPath;


    /**
     * Routes the request to proper method.
     *
     * @param Request $request
     * @param $userName
     * @param string $path
     * @return bool|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|Response|string|\Symfony\Component\HttpFoundation\Response
     */
    function route(Request $request, $userName, $path = "") {
        $this->_getFolderPath($request);

        if($this->folderPath === NULL) {
            abort(404);
        }

        // Checks whether user is in own directory
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

                return $this->setFileAsFavorite(
                    $request,
                    $request->post('add_favorite_file')
                );

            }else if($request->has('remove_favorite_file')) {

                return $this->setFileAsRegular(
                    $request,
                    $request->post('remove_favorite_file')
                );

            }else if($request->has('tag_file')) {

                return $this->setFileTagId(
                    $request,
                    $request->post('tag_file'),
                    $request->post('tag_id')
                );

            }else {

                return $this->storeFile(
                    $request
                );

            }
        }
    }


    /**
     * Stores file in db and local storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeFile(Request $request)
    {
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

    /**
     * Creates a folder.
     *
     * @param Request $request
     * @param $folderName
     * @return \Illuminate\Http\JsonResponse
     */
    public function createFolder(Request $request, $folderName) {
        $folder = [
            'parent_id' => $this->folderId,
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

    /**
     * Creates file.
     *
     * @param Request $request
     * @param $fileName
     * @param $fileContent
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Renames file.
     *
     * @param Request $request
     * @param $fileName
     * @param $fileId
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
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

    /**
     * Removes file.
     *
     * @param Request $request
     * @param $fileId
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Sets the file as favorite.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function setFileAsFavorite(Request $request, $id) {
        $file = Auth::user()->files()->find($id);

        if($file && $file->setAsFavorite()) {

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

    /**
     * Sets the file as regular.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function setFileAsRegular(Request $request, $id) {
        $file = Auth::user()->files()->find($id);

        if($file && $file->setAsRegular()) {
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

    /**
     * Gets local folder path and id
     *
     * @param Request $request
     * @return array|string|void
     */
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

    /**
     * Sends file to user browser.
     *
     * @param Request $request
     * @param $id
     * @return bool|\Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function shareFile(Request $request, $id) {
        $file = Auth::user()->files()->find($id);

        return FileSender::shareFiles($file);
    }

    /**
     * Sets file tag.
     *
     * @param Request $request
     * @param $fileId
     * @param $tagId
     * @return \Illuminate\Http\JsonResponse
     */
    public function setFileTagId(Request $request, $fileId, $tagId) {
        $file = Auth::user()->files()->find($fileId);

        // Remove tag if file is in selected tag
        if($file->pivot['tag_id'] == $tagId) {
            $tagId = null;
        }

        if($file->setTagId($tagId)) {
            return Response()->json([
                'succes' => true
            ], 201);
        }else {
            return Response()->json([
                'error' => 'FIle not found',
                'code' => 404
            ], 404);
        }
    }
}
