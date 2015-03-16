<?php

namespace Korobi\WebBundle\Extension;

use Korobi\WebBundle\Parser\IRCTextParser;

class ExtensionTwigFilters extends \Twig_Extension {

    public function getName() {
        return 'korobi_extension_twig_filters';
    }

    public function getFilters() {
        return [
            new \Twig_SimpleFilter('ircformat', [$this, 'ircFormat']),
            new \Twig_SimpleFilter('arsort', [$this, 'arsort'])
        ];
    }

    /**
     * @param $string
     * @return string
     */
    public function ircFormat($string) {
        return IRCTextParser::parse($string);
    }

    /**
     * @param $data
     * @return mixed
     */
    public function arsort($data) {
        arsort($data);
        return $data;
    }
}
