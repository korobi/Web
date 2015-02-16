<?php

namespace Korobi\WebBundle\Util;

use GuzzleHttp\Client as GuzzleClient;

/**
 * Class to talk to Akio.
 * @package Korobi\WebBundle\Util
 */
class Akio {

    protected $guzzle;
    private $url;
    private $key;

    /**
     * Initialize the class.
     * @param string $url The instance url
     * @param string $key The auth key
     */
    public function __construct($url, $key) {
        $this->guzzle = new GuzzleClient();
        $this->url = $url;
        $this->key = $key;
    }

    public function startMessage() {
        return new AkioMessageBuilder();
    }

    public function sendMessage(AkioMessageBuilder $message) {
        $text = $message->getRawText();
        $this->guzzle->get($this->url, [
            'query' => ['message' => $text],
            'headers' => ['X-Akio-Korobi' => $this->key]
        ]);
    }

}
