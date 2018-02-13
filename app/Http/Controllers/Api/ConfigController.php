<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller as Controller;
use Illuminate\Support\Facades\App;

class ConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getConfig()
    {
        $localizationArray = __('folderView');
        $config = [
            'lang' => App::getLocale(),
            'localizationArray' => $localizationArray,
        ];

        return Response($config, 201);
    }
}
