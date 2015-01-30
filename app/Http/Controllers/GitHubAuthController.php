<?php namespace Korobi\Http\Controllers;


use Illuminate\Session\Store;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;

class GitHubAuthController extends BaseController {

    public function __construct(SocialiteFactory $socialite) {
        $this->socialite = $socialite;
    }

    public function getUserDetails(Store $session) {
        $user = $this->socialite->driver('github')->user();

    }

    public function redirectToGitHub() {
        return $this->socialite->driver('github')->scopes([])->redirect();
    }

}
