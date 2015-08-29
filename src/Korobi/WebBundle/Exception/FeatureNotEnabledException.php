<?php

namespace Korobi\WebBundle\Exception;

class FeatureNotEnabledException extends \Exception implements CustomPageExceptionInterface {

    private $network;
    private $channel;
    private $feature;

    /**
     * FeatureNotEnabledException constructor.
     * @param string $network
     * @param string $channel
     * @param string $feature
     */
    public function __construct($network, $channel, $feature) {
        parent::__construct("The requested feature $feature is not enabled for $channel on network $network.", 404);
        $this->network = $network;
        $this->channel = $channel;
        $this->feature = $feature;
    }

    /**
     * @return string
     */
    public function getNetwork() {
        return $this->network;
    }

    /**
     * @return string
     */
    public function getChannel() {
        return $this->channel;
    }

    /**
     * @return string
     */
    public function getFeature() {
        return $this->feature;
    }

    /**
     * @return string Error page name used to display error details to user.
     */
    public function getViewName() {
        return "feature-not-enabled";
    }
}
