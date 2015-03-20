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
            ])
        ];
    }

    public function cahFullCard($blackCard, $data) {
        foreach($data as $item) {
            $blackCard = preg_replace('/<BLANK>/', '<strong>' . $item . '</strong>', $blackCard, 1);
        }

        return $blackCard;
    }

    public function cahPlays($blackCard, $plays) {
        dump($plays);

        $result = '';

        foreach($plays as $play) {
            $result .= key($plays) . ': ';
            $result .= preg_replace('/<BLANK>/', '<strong>' . $play[0] . '</strong>', $blackCard, 1);;
            $result .= '<br><br>';
            next($plays);
        }


        return $result;
    }
}
