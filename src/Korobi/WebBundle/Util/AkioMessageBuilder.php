<?php


namespace Korobi\WebBundle\Util;


class AkioMessageBuilder {

    const COLOUR = "{C}";
    const BOLD = "{B}";
    const RESET = "{R}";

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
        $this->text .= self::COLOUR . "0";
        return $this;
    }

    /**
     * @return $this
     */
    public function insertBlack() {
        $this->text .= self::COLOUR . "1";
        return $this;
    }

    /**
     * @return $this
     */
    public function insertNavyBlue() {
        $this->text .= self::COLOUR . "2";
        return $this;
    }

    /**
     * @return $this
     */
    public function insertGreen() {
        $this->text .= self::COLOUR . "3";
        return $this;
    }

    /**
     * @return $this
     */
    public function insertRed() {
        $this->text .= self::COLOUR . "4";
        return $this;
    }

    /**
     * @return $this
     */
    public function insertBrown() {
        $this->text .= self::COLOUR . "5";
        return $this;
    }

    /**
     * @return $this
     */
    public function insertPurple() {
        $this->text .= self::COLOUR . "6";
        return $this;
    }

    /**
     * @return $this
     */
    public function insertOlive() {
        $this->text .= self::COLOUR . "7";
        return $this;
    }

    /**
     * @return $this
     */
    public function insertYellow() {
        $this->text .= self::COLOUR . "8";
        return $this;
    }

    /**
     * @return $this
     */
    public function insertLimeGreen() {
        $this->text .= self::COLOUR . "9";
        return $this;
    }

    /**
     * @return $this
     */
    public function insertTeal() {
        $this->text .= self::COLOUR . "10";
        return $this;
    }

    /**
     * @return $this
     */
    public function insertAquaLight() {
        $this->text .= self::COLOUR . "11";
        return $this;
    }

    /**
     * @return $this
     */
    public function insertRoyalBlue() {
        $this->text .= self::COLOUR . "12";
        return $this;
    }

    /**
     * @return $this
     */
    public function insertHotPink() {
        $this->text .= self::COLOUR . "13";
        return $this;
    }

    /**
     * @return $this
     */
    public function insertDarkGray() {
        $this->text .= self::COLOUR . "14";
        return $this;
    }

    /**
     * @return $this
     */
    public function insertLightGray() {
        $this->text .= self::COLOUR . "15";
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
