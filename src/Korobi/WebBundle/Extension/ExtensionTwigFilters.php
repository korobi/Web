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
            new \Twig_SimpleFilter('commitauthorname', [$this, 'replaceAuthorName'])
        ];
    }

    public function ircFormat($string) {
        return IRCTextParser::parse($string);
    }

    public function replaceAuthorName($name) {
        if ($name === 'Joshua Popoff') {
            return 'kashike';
        }

        return $name;
    }
}
