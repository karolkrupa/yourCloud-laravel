<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class shared_file extends Model
{
    public function __construct() {
        (new Storage)->put('asdas','asdas');
    }
}
