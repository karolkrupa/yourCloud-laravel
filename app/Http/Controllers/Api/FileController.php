<?php

namespace App\Http\Controllers\Api;

use App\File;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Support\Facades\Auth;
use App\Helpers\FileSender;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Response(['message' => 'Welcome in YourCloud REST API'], 201);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->get('parent_id') != 0) {
            $parent = File::find($request->get('parent_id'));

            if(! $parent->hasPermissions(Auth::user())) {
                return Response()->json(['message' => 'You do not have permissions', 'code' => 500], 500);
            }

            if(! $parent) {
                return Response()->json(['message' => 'Parent file not found', 'code' => 404], 404);
            }
        }else {
            $parent = null;
        }

        if($request->get('type') == 1) {
            return File::createFile($request->get('name'), '', $parent);
        }else {
            return File::createFolder($request->get('name'), $parent);
        }

        return Response()->json(['message' => 'You must set type parameter', 'code' => 500], 500);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if($id == 0) {
            return Response()->json([
                'id' => 0,
                'name' => '',
                'parents' => []
            ], 201);
        }

        $file = \App\File::find($id);

        if(! $file->hasPermissions(Auth::user())) {
            return Response()->json(['message' => 'You do not have permissions', 'code' => 500], 500);
        }

        if($file) {
            return $file->getExtendedInfo();
        }else {
            return Response([
                'message' => 'File not found',
                'code' => 404
            ], 404);
        }
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
        $file = File::find($id);

        if(! $file->hasPermissions(Auth::user())) {
            return Response()->json(['message' => 'You do not have permissions', 'code' => 500], 500);
        }

        if(! $file) {
            return Response()->json(['message' => 'File not found', 'code' => 404], 404);
        }

        $incomingData = $this->getNewFileData($file, $request);

        $response = $this->updateFileData($file, $incomingData['newData']);
        if($response != true) return $response;

        $response = $this->updateFileFavoriteStatus($file, $incomingData['newData']);
        if($response != true) return $response;

        $this->updateFileLinkSharing($file, $incomingData['newData']);

        $response = $this->updateFileTagId($file, $incomingData['newData']);
        if($response != true) return $response;

        $response = $this->updateFileShareUsers($file, $incomingData['fileData']['share_users'], $incomingData['newShareUsers']);
        if($response != true) return $response;

        return Response(Auth::user()->files()->find($id)->getExtendedInfo());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $file = \App\File::find($id);

        if(! $file->hasPermissions(Auth::user())) {
            return Response()->json(['message' => 'You do not have permissions', 'code' => 500], 500);
        }

        if(! $file) {
            return Response()->json(['message' => 'File not found', 'code' => 404], 404);
        }

        if(! $file->delete()) {
            return Response()->json(['message' => 'Can not delete file', 'code' => 500], 500);
        }

        return Response()->json(['message' => 'success', 'code' => 201], 201);
    }

    public function authSend($id)
    {
        if(! Auth::user()) {
            Abort(500);
        }

        $file = \App\File::find($id);

        if(! $file) {
            Abort(404);
        }

        if(! $file->hasPermissions()) {
            Abort(500);
        }

        return FileSender::shareFiles($file);
    }

    public function sharedSend($id)
    {
        $file = \App\File::where('share_link', $id)->first();

        if(! $file) {
            return 'Plik nie istnieje';
        }

        return FileSender::shareFiles($file);
    }

    public function storeFile(Request $request, $folderId = 0) {
        if($folderId > 0) {
            $parentFolder = File::find($folderId);

            if(! $parentFolder && ($folderId > 0)) {
                return Response()->json([
                    'message' => 'Target directory does not exist',
                    'code' => 404
                ], 404);
            }

            if(! $parentFolder->hasPermissions() && ($folderId > 0)) {
                return Response()->json([
                    'message' => 'You do not have permissions to parent directory',
                    'code' => 500
                ], 500);
            }
        }else {
            $parentFolder = null;
        }

        if(! $request->has('file')) {
            return Response()->json([
                'message' => 'You must send another file',
                'code' => 404
            ], 404);
        }

        if(! $request->file('file')->isValid()) {
            return Response()->json([
                'message' => 'File is invalid',
                'code' => 500
            ], 500);
        }

        return File::saveFile($request->file('file'), $parentFolder)->getExtendedInfo();
    }

    private function getNewFileData(File $file, Request $request) {
        $fileData = $file->getExtendedInfo();

        // Filtering data
        $incomingData = array_intersect_key($request->all(), $fileData);
        $newShareUsers = isset($incomingData['share_users'])? $incomingData['share_users'] : [];
        unset($incomingData['share_users']);
        unset($incomingData['parents']);

        return [
            'newData' => array_diff_assoc($incomingData, $fileData),
            'newShareUsers' => $newShareUsers,
            'fileData' => $fileData
        ];
    }

    private function updateFileData(File $file, $newData) {
        try {
            $file->update($newData);
        }catch(QueryException $e) {
            if($e->errorInfo[1] == 1062) {
                return Response()->json(['message' => 'Duplicate entry', 'code' => 500], 500);
            }else {
                return Response()->json(['message' => 'Can not update', 'code' => 500], 500);
            }
        }

        return true;
    }

    private function updateFileFavoriteStatus(File $file, $newData) {
        // Favorite file
        if(isset($newData['favorite'])) {
            if($newData['favorite']) {
                if(! $file->setAsFavorite()) {
                    return Response()->json(['message' => 'Can not set file as favorite', 'code' => 500], 500);
                }
            }else {
                if(! $file->setAsRegular()) {
                    return Response()->json(['message' => 'Can not set file as regular', 'code' => 500], 500);
                }
            }
        }

        return true;
    }

    private function updateFileLinkSharing(File $file, $newData) {
        // Sharing by link
        if(isset($newData['share_link'])) {
            if($newData['share_link'] == true) {
                $file->shareByLink();
            }else {
                $file->removeShareByLink();
            }
        }

        return true;
    }

    private function updateFileTagId(File $file, $newData) {
        // Set tag id
        if(isset($newData['tag_id'])) {
            $tagId = intval($newData['tag_id']);

            if(($tagId > 5) && ($tagId < 0)) {
                return Response()->json(['message' => 'Invalid tag', 'code' => 500], 500);
            }

            if(! $file->setTagId($tagId)) {
                return Response()->json(['message' => 'Can not set tag', 'code' => 500], 500);
            }
        }

        return true;
    }

    private function updateFileShareUsers(File $file, $shareUsers, $newShareUsers) {
        // Shared users
        function cmp_func($a, $b) {
            $a = (array)$a;
            $b = (array)$b;
            if(empty(array_diff_assoc($a, $b))) {
                return 0;
            }

            return 1;
        }
//        $shareUsers = $fileData['share_users'];
        $usersToDelete = array_udiff($shareUsers, $newShareUsers, cmp_func::class);
        $usersToAdd = array_udiff($newShareUsers, $shareUsers, cmp_func::class);


        foreach($usersToAdd as $user) {
            $user = (array)$user;
            if(! $file->shareFor($user['id'])) {
                return Response()->json(['message' => 'Can not set sharing for '. $user['name'], 'code' => 500], 500);
            }
        }

        foreach($usersToDelete as $user) {
            $user = (array)$user;
            if(! $file->removeSharingFor($user['id'])) {
                return Response()->json(['message' => 'Can not remove sharing for '. $user['name'], 'code' => 500], 500);
            }
        }

        return true;
    }
}
