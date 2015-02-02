<?php

namespace Korobi\Http\Controller;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class BaseController extends Controller {
    use DispatchesCommands, ValidatesRequests;
}
