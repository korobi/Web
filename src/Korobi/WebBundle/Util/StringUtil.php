<?php


namespace Korobi\WebBundle\Util;


class StringUtil {

    /**
     * @author Salman A via http://stackoverflow.com/a/10473026
     */
    public static function startsWith($haystack, $needle, $ignoreCase=false) {
        return $needle === "" || strrpos($ignoreCase ? strtolower($haystack) : $haystack, $ignoreCase ? strtolower($needle) : $needle, -strlen($haystack)) !== FALSE;
    }

    /**
     * @author Salman A via http://stackoverflow.com/a/10473026
     */
    public static function endsWith($haystack, $needle, $ignoreCase=false) {
        return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($ignoreCase ? strtolower($haystack) : $haystack, $ignoreCase ? strtolower($needle) : $needle, $temp) !== FALSE);
    }

}
