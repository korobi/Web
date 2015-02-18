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
     * @var string Current text
     */
    private $text;

    /**
     * @return string
     */
    public function getRawText() {
        return $this->text;
    }

    /**
     * @return $this
     */
    public function insertWhite() {
        $this->text .= self::COLOUR . '00';
        return $this;
    }

    /**
     * @return $this
     */
    public function insertBlack() {
        $this->text .= self::COLOUR . '01';
        return $this;
    }

    /**
     * @return $this
     */
    public function insertNavyBlue() {
        $this->text .= self::COLOUR . '02';
        return $this;
    }

    /**
     * @return $this
     */
    public function insertGreen() {
        $this->text .= self::COLOUR . '03';
        return $this;
    }

    /**
     * @return $this
     */
    public function insertRed() {
        $this->text .= self::COLOUR . '04';
        return $this;
    }

    /**
     * @return $this
     */
    public function insertBrown() {
        $this->text .= self::COLOUR . '05';
        return $this;
    }

    /**
     * @return $this
     */
    public function insertPurple() {
        $this->text .= self::COLOUR . '06';
        return $this;
    }

    /**
     * @return $this
     */
    public function insertOlive() {
        $this->text .= self::COLOUR . '07';
        return $this;
    }

    /**
     * @return $this
     */
    public function insertYellow() {
        $this->text .= self::COLOUR . '08';
        return $this;
    }

    /**
     * @return $this
     */
    public function insertLimeGreen() {
        $this->text .= self::COLOUR . '09';
        return $this;
    }

    /**
     * @return $this
     */
    public function insertTeal() {
        $this->text .= self::COLOUR . '10';
        return $this;
    }

    /**
     * @return $this
     */
    public function insertAquaLight() {
        $this->text .= self::COLOUR . '11';
        return $this;
    }

    /**
     * @return $this
     */
    public function insertRoyalBlue() {
        $this->text .= self::COLOUR . '12';
        return $this;
    }

    /**
     * @return $this
     */
    public function insertHotPink() {
        $this->text .= self::COLOUR . '13';
        return $this;
    }

    /**
     * @return $this
     */
    public function insertDarkGray() {
        $this->text .= self::COLOUR . '14';
        return $this;
    }

    /**
     * @return $this
     */
    public function insertLightGray() {
        $this->text .= self::COLOUR . '15';
        return $this;
    }

    /**
     * @return $this
     */
    public function insertBold() {
        $this->text .= self::BOLD;
        return $this;
    }

    /**
     * @param $text
     * @return $this
     */
    public function insertText($text) {
        $this->text .= $text;
        return $this;
    }

    /**
     * @return $this
     */
    public function insertReset() {
        $this->text .= self::RESET;
        return $this;
    }
}
