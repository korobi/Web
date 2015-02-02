<?php

namespace Korobi\Http\Controller;

use Illuminate\Http\Response;
use Korobi\Util\GitInfoUtility;

class WelcomeController extends BaseController {

    /*
    |--------------------------------------------------------------------------
    | Welcome Controller
    |--------------------------------------------------------------------------
    |
    | This controller renders the "marketing page" for the application and
    | is configured to only allow guests. Like most of the other sample
    | controllers, you are free to modify or remove it as you desire.
    |
    */

    /**
     * Show the application welcome screen to the user.
     *
     * @param GitInfoUtility $git Git tool.
     * @return Response
     */
    public function index(GitInfoUtility $git) {
        return view('welcome', [
            'hash' => $git->getShortHash(),
            'branch' => $git->getBranch()
        ]);
    }
}
