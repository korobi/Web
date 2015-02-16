<?php

namespace Korobi\WebBundle\Util;

use GuzzleHttp\Client as GuzzleClient;

/**
 * Class to talk to Akio.
 * @package Korobi\WebBundle\Util
 */
class Akio {

    protected $guzzle;

    /**
     * Initialize the class.
     * @param GuzzleClient $guzzle
     */
    public function __construct(GuzzleClient $guzzle, $akio) {
        $this->guzzle = $guzzle;
    }

    public function startMessage() {
        return new AkioMessageBuilder();
    }

    public function sendMessage(AkioMessageBuilder $message, $channel) {
        $text = $message->getRawText();
    }

}
