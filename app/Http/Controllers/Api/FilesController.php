<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Support\Facades\Auth;

class FilesController extends Controller
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
    public function store(Request $request)
    {
        //
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id = 0)
    {
        $id = intval($id);

        $files = Auth::user()->files()->where('parent_id', $id)->get();

        if($files) {
            $filesExtended = [];
            foreach ($files as $file) {
                $filesExtended[] = $file->getExtendedInfo();
            }

            return Response()->json($filesExtended, 201);
        }else {
            return Response([
                'message' => 'Files not found',
                'code' => 404
            ], 404);
        }
    }

    public function showShareByMe()
    {
        $files = Auth::user()->filesSharedByMe()
            ->get();

        $filesExtended = [];
        foreach ($files as $file) {
            $filesExtended[] = $file->getExtendedInfo();
        }

        return Response()->json($filesExtended, 201);
    }

    public function showShareForMe()
    {
        $files = Auth::user()->filesSharedForMe;

        $filesExtended = [];
        foreach ($files as $file) {
            $filesExtended[] = $file->getExtendedInfo();
        }

        return Response()->json($filesExtended, 201);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showFavorites($id = 0)
    {
        $id = intval($id);

        if($id == 0) {
            $files = Auth::user()
            ->favoriteFiles()
            ->get();

            $extededFiles = [];
            foreach($files as $file) {
                $extededFiles[] = $file->getExtendedInfo();
            }

            if($files) {
                return $extededFiles;
            }else {
                return Response([
                    'message' => 'Files not found',
                    'code' => 404
                ], 404);
            }
        }else {
            return $this->show($id);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showTag($tagId = 0, $fileId = null)
    {
        $tagId = intval($tagId);

        if(($fileId == 0) || ($fileId == null)) {
            $files = Auth::user()
                ->taggedFiles()
                ->where('tag_id', $tagId)
                ->get();

            $extededFiles = [];
            foreach($files as $file) {
                $extededFiles = $file->getExtendedInfo();
            }

            if($files) {
                return $extededFiles;
            }else {
                return Response([
                    'message' => 'Files not found',
                    'code' => 404
                ], 404);
            }
        }else {
            return $this->show($fileId);
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
}
