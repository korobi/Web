<?php

namespace Korobi\WebBundle\Test\Unit;

use Korobi\WebBundle\Util\StringUtil;
use Symfony\Component\Translation\TranslatorInterface;

class DummyNoXssTranslator implements TranslatorInterface {

    public function trans($id, array $parameters = [], $domain = null, $locale = null) {
        return $this->checkForTags($id, $parameters);
    }

    private function checkForTags($id, $params) {
        foreach ($params as $param) {
            if (StringUtil::stringContains($param, "<marquee>")) {
                throw new \Exception("Possible XSS!");
            }
        }
        return $id;
    }

    public function transChoice($id, $number, array $parameters = [], $domain = null, $locale = null) {
        return $this->checkForTags($id, $parameters);
    }


    public function setLocale($locale) {
        // TODO: Implement setLocale() method.
    }

    public function getLocale() {
        // TODO: Implement getLocale() method.
    }
}
