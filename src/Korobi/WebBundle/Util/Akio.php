<?php

namespace Korobi\WebBundle\Util;

use GuzzleHttp\Client as GuzzleClient;

/**
 * Used to communicate with the notification component of Akio.
 *
 * @package Korobi\WebBundle\Util
 */
class Akio {

    private $guzzle;
    private $enabled;
    private $url;
    private $key;

    /**
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
    public function message() {
        return new AkioMessageBuilder($this);
    }

    /**
     * @param AkioMessageBuilder $message
     * @param $context
     * @param $type
     */
    public function sendMessage(AkioMessageBuilder $message, $context, $type) {
        if ($this->enabled) {
            $text = $message->getText();
            $this->guzzle->get($this->url, [
                'query' => [
                    'context' => $context,
                    'type' => $type,
                    'message' => $text,
                ],
                'headers' => [
                    'X-Korobi-Key' => $this->key,
                ],
            ]);
        }
    }
}
