<?php

namespace App\Http\Controllers;

use App\File;
use App\Helpers\UnitConverter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Folder extends Controller
{
    private $folderId = 0;
    private $folderPath;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($userName, $path = null)
    {
        if(isset($path)) {
            // Get folder ID and path
        }else {
            $this->folderPath = Auth::user()->id;
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
    public function store(Request $request)
    {
        $fileAttr['parent_id'] = $this->folderId;

        if($request->has('create_folder')) {
            $fileAttr['path'] = $this->folderPath . '/' . $request->create_folder;
            $fileAttr['name'] = $request->create_folder;
            $fileAttr['mime_type'] = 'folder';
            $fileAttr['size'] = 0;

            $file = File::addFolder($fileAttr, Auth::user());
        }else if($request->has('file')) {
            $fileAttr['path'] = $request->file('file')->store($this->folderPath);
            $extension = $request->file('file')->getClientOriginalExtension();
            $fileAttr['name'] = str_replace('.'.$extension, '', $request->file('file')->getClientOriginalName());
            $fileAttr['mime_type'] = $request->file('file')->getMimeType();
            $fileAttr['size'] = $request->file('file')->getClientSize();

            $file = File::addFile($fileAttr, Auth::user());
        }

        $file = $file->toArray();
        $file['size_normalized'] = UnitConverter::bytesToHuman($file['size']);

        return Response()->json($file);
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
}
