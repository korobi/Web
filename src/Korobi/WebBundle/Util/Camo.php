<?php

namespace Korobi\WebBundle\Util;

class Camo {
    /**
     * @var string $camoKey The shared key used to generate the HMAC digest
     */
    protected $key;
    /**
     * @var string $domain The domain where camo is hosted.
     */
    protected $domain;

    public function __construct($key, $domain) {
        $this->key = $key;
        $this->domain = $domain;
    }

    /**
     * @param $url
     * @return string
     */
    public function create($url) {
        $digest = $this->getDigest($url);
        $hex = bin2hex($url);

        return $this->domain . '/' . $digest . '/' . $hex . '/';
    }

    /**
     * @param $url
     * @return string
     */
    protected function getDigest($url) {
        return hash_hmac('sha1', $url, $this->key);
    }
}
