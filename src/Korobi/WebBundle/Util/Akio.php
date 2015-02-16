<?php

namespace Korobi\WebBundle\Util;

use GuzzleHttp\Client as GuzzleClient;

/**
 * Class to talk to Akio.
 * @package Korobi\WebBundle\Util
 */
class Akio {

    protected $guzzle;
    private $host;
    private $port;
    private $key;

    /**
     * Initialize the class.
     * @param string $host The instance host
     * @param int $port The instance port
     * @param string $key The auth key
     */
    public function __construct($host, $port, $key) {
        $this->guzzle = new GuzzleClient();
        $this->host = $host;
        $this->port = $port;
        $this->key = $key;
    }

    public function startMessage() {
        return new AkioMessageBuilder();
    }

    public function sendMessage(AkioMessageBuilder $message) {
        $text = $message->getRawText();
        $this->guzzle->get("http://" . $this->host . ":" . $this->port, [
            'query' => ['message' => $text],
            'headers' => ['X-Akio-Korobi' => $this->key]
        ]);
    }

}
