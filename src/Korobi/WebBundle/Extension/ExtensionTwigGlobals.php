<?php

namespace Korobi\WebBundle\Extension;

use Korobi\WebBundle\Util\GitInfo;

class ExtensionTwigGlobals extends \Twig_Extension {

    /**
     * @var GitInfo
     */
    private $gitInfo;

    /**
     * @param GitInfo $gitInfo
     */
    public function __construct(GitInfo $gitInfo) {
        $this->gitInfo = $gitInfo;
    }

    public function getName() {
        return 'korobi_extension_twig_globals';
    }

    public function getGlobals() {
        return [
            'app_name' => 'Korobi',
            'git_hash' => $this->gitInfo->getShortHash()
        ];
    }
}
