<?php

namespace Korobi\WebBundle\Extension;

class ExtensionTwigGlobals extends \Twig_Extension {

    public function getName() {
        return 'korobi_extension_twig_globals';
    }

    public function getGlobals() {
        return [
            'app_name' => 'Korobi',
        ];
    }
}
