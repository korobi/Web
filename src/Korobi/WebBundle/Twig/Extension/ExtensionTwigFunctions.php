<?php

namespace Korobi\WebBundle\Twig\Extension;

class ExtensionTwigFunctions extends \Twig_Extension {

    public function getName() {
        return 'korobi_extension_twig_functions';
    }

    public function getFunctions() {
        return [
            new \Twig_SimpleFunction('gmdate', [$this, 'gmdate']),
            new \Twig_SimpleFunction('relativeTime', [$this, 'relativeTime']),
        ];
    }

    public function gmdate($format, $timestamp = null) {
        return gmdate($format, $timestamp);
    }

    public function relativeTime($current, $previous) {
        $secondsIn = [
            'minute' => 60,
            'hour'   => 60 * 60,
            'day'    => 60 * 60 * 24,
            'month'  => 60 * 60 * 24 * 30,
            'year'   => 60 * 60 * 24 * 365,
        ];

        $elapsed = $current - $previous;

        if($elapsed <= 0) {
            return 'just now';
        }

        $prevSeconds = 1;
        $prevName = 'second';
        foreach($secondsIn as $name => $seconds) {
            if($elapsed < $seconds) {
                return self::plural(round($elapsed / $prevSeconds), $prevName) . ' ago';
            }
            $prevSeconds = $seconds;
            $prevName = $name;
        }
        return 'Invalid time';
    }

    private static function plural($count, $word) {
        $result = $count . ' ' . $word;
        if($count > 1) {
            return $result . 's';
        }
        return $result;
    }
}
