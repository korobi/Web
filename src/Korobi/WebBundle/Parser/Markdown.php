<?php

namespace Korobi\WebBundle\Parser;

use Korobi\WebBundle\Controller\DocsController;
use Korobi\WebBundle\Util\StringUtil;

class Markdown extends \Michelf\Markdown {
    const PATTERN = '/\b/';

    protected function _doAnchors_inline_callback($matches) {
        $whole_match	=  $matches[1];
        $link_text		=  $this->runSpanGamut($matches[2]);
        $url			=  $matches[3] == '' ? $matches[4] : $matches[3];
        $title			=& $matches[7];

        // if the URL was of the form <s p a c e s> it got caught by the HTML
        // tag parser and hashed. Need to reverse the process before using the URL.
        $unhashed = $this->unhash($url);
        if ($unhashed != $url)
            $url = preg_replace('/^<(.*)>$/', '\1', $unhashed);

        $url = $this->encodeAttribute($url);
        $url = $this->transformSingleWordHref($url);

        $result = "<a href=\"$url\"";
        if (isset($title)) {
            $title = $this->encodeAttribute($title);
            $result .=  " title=\"$title\"";
        }

        $link_text = $this->runSpanGamut($link_text);
        $result .= ">$link_text</a>";

        return $this->hashPart($result);
    }

    protected function transformSingleWordHref($href) {
        if(StringUtil::startsWith($href, '/')) {
            return DocsController::BASE_HREF . substr($href, 1);
        }

        return $href;
    }
}
