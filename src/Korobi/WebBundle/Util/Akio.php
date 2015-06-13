<?php

namespace Korobi\WebBundle\Util;

use GuzzleHttp\Client as GuzzleClient;

/**
 * Used to communicate with the notification component of Akio.
 *
 * @package Korobi\WebBundle\Util
 */
class Akio {

    protected $guzzle;
    private $enabled;
    private $url;
    private $key;

    /**
     * Initialize the class.
     * @param boolean $enabled If Akio is enabled
     * @param string $url The instance url
     * @param string $key The auth key
     */
    public function __construct($enabled, $url, $key) {
        $this->guzzle = new GuzzleClient();
        $this->enabled = $enabled;
        $this->url = $url;
        $this->key = $key;
    }

    /**
     * @return AkioMessageBuilder
     */
    public function startMessage() {
        return new AkioMessageBuilder();
    }

    /**
     * @param AkioMessageBuilder $message
     */
    public function sendMessage(AkioMessageBuilder $message) {
        if ($this->enabled) {
            $text = $message->getRawText();
            $this->guzzle->get($this->url, [
                'query' => ['message' => $text],
                'headers' => ['X-Korobi-Key' => $this->key]
            ]);
        }
    }
}
