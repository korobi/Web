<?php

namespace Korobi\WebBundle\Extension;

class ExtensionTwigFunctions extends \Twig_Extension {

    public function getName() {
        return 'korobi_extension_twig_functions';
    }

    public function getFunctions() {
        return [
            new \Twig_SimpleFunction('cahFullCard', [$this, 'cahFullCard']),
            new \Twig_SimpleFunction('cahPlays', [$this, 'cahPlays'], [
                'is_safe' => ['html']
            ]),
            new \Twig_SimpleFunction('gmdate', [$this, 'gmdate']),
            new \Twig_SimpleFunction('relativeTime', [$this, 'relativeTime']),
        ];
    }

    public function cahFullCard($blackCard, $data) {
        foreach($data as $item) {
            $blackCard = preg_replace('/<BLANK>/', '<strong>' . $item . '</strong>', $blackCard, 1);
        }

        return $blackCard;
    }

    public function cahPlays($blackCard, $plays) {
        $result = '';

        foreach($plays as $play) {
            $result .= key($plays) . ': ';
            $result .= preg_replace('/<BLANK>/', '<strong>' . $play[0] . '</strong>', $blackCard, 1);;
            $result .= '<br><br>';
            next($plays);
        }


        return $result;
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

        $prevSeconds = 60;
        $prevName = 'seconds';
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
