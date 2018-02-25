<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller as Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class ConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getConfig()
    {
        App::setlocale(Auth::user()->lang);
        $localizationArray = [
            'folderView' => __('folderView'),
            'settingsView' => __('settingsView'),
        ];
        $config = [
            'debug' => config('app.debug'),
            'lang' => Auth::user()->lang,
            'availableLangs' => config('app.languages'),
            'localizationArray' => $localizationArray,
        ];

        return Response($config, 201);
    }
}
