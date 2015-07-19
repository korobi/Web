<?php

namespace Korobi\WebBundle\Util;

/**
 * @package Korobi\WebBundle\Util
 */
class AkioMessageBuilder {

    const COLOUR = '{C}';
    const BOLD = '{B}';
    const RESET = '{R}';

    /**
     * @var Akio
     */
    private $akio;

    /**
     * @var string Current text
     */
    private $text;

    public function __construct(Akio $akio) {
        $this->akio = $akio;
    }

    /**
     * @return string
     */
    public function getText() {
        return $this->text;
    }

    /**
     * @return $this
     */
    public function white() {
        $this->text .= self::COLOUR . '00';
        return $this;
    }

    /**
     * @return $this
     */
    public function black() {
        $this->text .= self::COLOUR . '01';
        return $this;
    }

    /**
     * @return $this
     */
    public function navyBlue() {
        $this->text .= self::COLOUR . '02';
        return $this;
    }

    /**
     * @return $this
     */
    public function green() {
        $this->text .= self::COLOUR . '03';
        return $this;
    }

    /**
     * @return $this
     */
    public function red() {
        $this->text .= self::COLOUR . '04';
        return $this;
    }

    /**
     * @return $this
     */
    public function brown() {
        $this->text .= self::COLOUR . '05';
        return $this;
    }

    /**
     * @return $this
     */
    public function purple() {
        $this->text .= self::COLOUR . '06';
        return $this;
    }

    /**
     * @return $this
     */
    public function olive() {
        $this->text .= self::COLOUR . '07';
        return $this;
    }

    /**
     * @return $this
     */
    public function yellow() {
        $this->text .= self::COLOUR . '08';
        return $this;
    }

    /**
     * @return $this
     */
    public function limeGreen() {
        $this->text .= self::COLOUR . '09';
        return $this;
    }

    /**
     * @return $this
     */
    public function teal() {
        $this->text .= self::COLOUR . '10';
        return $this;
    }

    /**
     * @return $this
     */
    public function aquaLight() {
        $this->text .= self::COLOUR . '11';
        return $this;
    }

    /**
     * @return $this
     */
    public function royalBlue() {
        $this->text .= self::COLOUR . '12';
        return $this;
    }

    /**
     * @return $this
     */
    public function hotPink() {
        $this->text .= self::COLOUR . '13';
        return $this;
    }

    /**
     * @return $this
     */
    public function darkGray() {
        $this->text .= self::COLOUR . '14';
        return $this;
    }

    /**
     * @return $this
     */
    public function lightGray() {
        $this->text .= self::COLOUR . '15';
        return $this;
    }

    /**
     * @return $this
     */
    public function bold() {
        $this->text .= self::BOLD;
        return $this;
    }

    /**
     * @param $text
     * @return $this
     */
    public function text($text) {
        $this->text .= $text;
        return $this;
    }

    /**
     * @return $this
     */
    public function reset() {
        $this->text .= self::RESET;
        return $this;
    }

    /**
     * @param $type
     * @param string $context
     */
    public function send($type, $context = 'public') {
        $this->akio->sendMessage($this, $context, $type);
    }
}
