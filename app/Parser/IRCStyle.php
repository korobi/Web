<?php

namespace Korobi\Parser;

class IRCStyle {
    private static $extensions = ['.ac', '.academy', '.accountants', '.actor', '.ad', '.ae', '.aero', '.af', '.ag', '.agency', '.ai', '.airforce', '.al', '.am', '.an', '.ao', '.aq', '.ar', '.archi', '.army', '.arpa', '.as', '.asia', '.associates', '.at', '.attorney', '.au', '.audio', '.autos', '.aw', '.ax', '.axa', '.az', '.ba', '.bar', '.bargains', '.bayern', '.bb', '.bd', '.be', '.beer', '.berlin', '.best', '.bf', '.bg', '.bh', '.bi', '.bid', '.bike', '.bio', '.biz', '.bj', '.black', '.blackfriday', '.blue', '.bm', '.bn', '.bo', '.boutique', '.br', '.bs', '.bt', '.build', '.builders', '.buzz', '.bv', '.bw', '.by', '.bz', '.ca', '.cab', '.camera', '.camp', '.capital', '.cards', '.care', '.career', '.careers', '.cash', '.cat', '.catering', '.cc', '.cd', '.center', '.ceo', '.cf', '.cg', '.ch', '.cheap', '.christmas', '.church', '.ci', '.citic', '.ck', '.cl', '.claims', '.cleaning', '.clinic', '.clothing', '.club', '.cm', '.cn', '.co', '.codes', '.coffee', '.college', '.cologne', '.com', '.community', '.company', '.computer', '.condos', '.construction', '.consulting', '.contractors', '.cooking', '.cool', '.coop', '.country', '.cr', '.credit', '.creditcard', '.cruises', '.cu', '.cv', '.cw', '.cx', '.cy', '.cz', '.dance', '.dating', '.de', '.degree', '.democrat', '.dental', '.dentist', '.desi', '.diamonds', '.digital', '.directory', '.discount', '.dj', '.dk', '.dm', '.dnp', '.do', '.domains', '.dz', '.ec', '.edu', '.education', '.ee', '.eg', '.email', '.engineer', '.engineering', '.enterprises', '.equipment', '.er', '.es', '.estate', '.et', '.eu', '.eus', '.events', '.exchange', '.expert', '.exposed', '.fail', '.farm', '.feedback', '.fi', '.finance', '.financial', '.fish', '.fishing', '.fitness', '.fj', '.fk', '.flights', '.florist', '.fm', '.fo', '.foo', '.foundation', '.fr', '.frogans', '.fund', '.furniture', '.futbol', '.ga', '.gal', '.gallery', '.gb', '.gd', '.ge', '.gf', '.gg', '.gh', '.gi', '.gift', '.gives', '.gl', '.glass', '.globo', '.gm', '.gmo', '.gn', '.gop', '.gov', '.gp', '.gq', '.gr', '.graphics', '.gratis', '.gripe', '.gs', '.gt', '.gu', '.guide', '.guitars', '.guru', '.gw', '.gy', '.hamburg', '.haus', '.hiphop', '.hiv', '.hk', '.hm', '.hn', '.holdings', '.holiday', '.homes', '.horse', '.host', '.house', '.hr', '.ht', '.hu', '.id', '.ie', '.il', '.im', '.immobilien', '.in', '.industries', '.info', '.ink', '.institute', '.insure', '.int', '.international', '.investments', '.io', '.iq', '.ir', '.is', '.it', '.je', '.jetzt', '.jm', '.jo', '.jobs', '.jp', '.juegos', '.kaufen', '.ke', '.kg', '.kh', '.ki', '.kim', '.kitchen', '.kiwi', '.km', '.kn', '.koeln', '.kp', '.kr', '.kred', '.kw', '.ky', '.kz', '.la', '.land', '.lawyer', '.lb', '.lc', '.lease', '.li', '.life', '.lighting', '.limited', '.limo', '.link', '.lk', '.loans', '.london', '.lr', '.ls', '.lt', '.lu', '.luxe', '.luxury', '.lv', '.ly', '.ma', '.maison', '.management', '.mango', '.market', '.marketing', '.mc', '.md', '.me', '.media', '.meet', '.menu', '.mg', '.mh', '.miami', '.mil', '.mk', '.ml', '.mm', '.mn', '.mo', '.mobi', '.moda', '.moe', '.monash', '.mortgage', '.moscow', '.motorcycles', '.mp', '.mq', '.mr', '.ms', '.mt', '.mu', '.museum', '.mv', '.mw', '.mx', '.my', '.mz', '.na', '.nagoya', '.name', '.navy', '.nc', '.ne', '.net', '.neustar', '.nf', '.ng', '.nhk', '.ni', '.ninja', '.nl', '.no', '.np', '.nr', '.nu', '.nyc', '.nz', '.okinawa', '.om', '.onl', '.org', '.pa', '.paris', '.partners', '.parts', '.pe', '.pf', '.pg', '.ph', '.photo', '.photography', '.photos', '.pics', '.pictures', '.pink', '.pk', '.pl', '.plumbing', '.pm', '.pn', '.post', '.pr', '.press', '.pro', '.productions', '.properties', '.ps', '.pt', '.pub', '.pw', '.py', '.qa', '.qpon', '.quebec', '.re', '.recipes', '.red', '.rehab', '.reise', '.reisen', '.ren', '.rentals', '.repair', '.report', '.republican', '.rest', '.reviews', '.rich', '.rio', '.ro', '.rocks', '.rodeo', '.rs', '.ru', '.ruhr', '.rw', '.ryukyu', '.sa', '.saarland', '.sb', '.sc', '.schule', '.sd', '.se', '.services', '.sexy', '.sg', '.sh', '.shiksha', '.shoes', '.si', '.singles', '.sj', '.sk', '.sl', '.sm', '.sn', '.so', '.social', '.software', '.sohu', '.solar', '.solutions', '.soy', '.space', '.sr', '.st', '.su', '.supplies', '.supply', '.support', '.surgery', '.sv', '.sx', '.sy', '.systems', '.sz', '.tattoo', '.tax', '.tc', '.td', '.technology', '.tel', '.tf', '.tg', '.th', '.tienda', '.tips', '.tirol', '.tj', '.tk', '.tl', '.tm', '.tn', '.to', '.today', '.tokyo', '.tools', '.town', '.toys', '.tp', '.tr', '.trade', '.training', '.travel', '.tt', '.tv', '.tw', '.tz', '.ua', '.ug', '.uk', '.university', '.uno', '.us', '.uy', '.uz', '.va', '.vacations', '.vc', '.ve', '.vegas', '.ventures', '.versicherung', '.vet', '.vg', '.vi', '.viajes', '.villas', '.vision', '.vn', '.vodka', '.vote', '.voting', '.voto', '.voyage', '.vu', '.wang', '.watch', '.webcam', '.website', '.wed', '.wf', '.wien', '.wiki', '.works', '.ws', '.wtc', '.wtf', '.xn--3bst00m', '.xn--3ds443g', '.xn--3e0b707e', '.xn--45brj9c', '.xn--4gbrim', '.xn--55qw42g', '.xn--55qx5d', '.xn--6frz82g', '.xn--6qq986b3xl', '.xn--80adxhks', '.xn--80ao21a', '.xn--80asehdb', '.xn--80aswg', '.xn--90a3ac', '.xn--c1avg', '.xn--cg4bki', '.xn--clchc0ea0b2g2a9gcd', '.xn--czr694b', '.xn--czru2d', '.xn--d1acj3b', '.xn--fiq228c5hs', '.xn--fiq64b', '.xn--fiqs8s', '.xn--fiqz9s', '.xn--fpcrj9c3d', '.xn--fzc2c9e2c', '.xn--gecrj9c', '.xn--h2brj9c', '.xn--i1b6b1a6a2e', '.xn--io0a7i', '.xn--j1amh', '.xn--j6w193g', '.xn--kprw13d', '.xn--kpry57d', '.xn--l1acc', '.xn--lgbbat1ad8j', '.xn--mgb9awbf', '.xn--mgba3a4f16a', '.xn--mgbaam7a8h', '.xn--mgbab2bd', '.xn--mgbayh7gpa', '.xn--mgbbh1a71e', '.xn--mgbc0a9azcg', '.xn--mgberp4a5d4ar', '.xn--mgbx4cd0ab', '.xn--ngbc5azd', '.xn--nqv7f', '.xn--nqv7fs00ema', '.xn--o3cw4h', '.xn--ogbpf8fl', '.xn--p1ai', '.xn--pgbs0dh', '.xn--q9jyb4c', '.xn--rhqv96g', '.xn--s9brj9c', '.xn--ses554g', '.xn--unup4y', '.xn--wgbh1c', '.xn--wgbl6a', '.xn--xkc2dl3a5ee0h', '.xn--xkc2al3hye2a', '.xn--yfro4i67o', '.xn--ygbi2ammx', '.xn--zfr164b', '.xxx', '.xyz', '.yachts', '.ye', '.yokohama', '.yt', '.za', '.zm', '.zw', '.zone'];
    private static $pattern = "{\\b(https?://|ftp://)?(?:([^]\\x00-\x20\"(),:-<>[\x7f-\xff]{1,64})(:[^]\\x00-\x20\"(),:-<>[\x7f-\xff]{1,64})?@)?((?:[-a-zA-Z0-9\x7f-\xff]{1,63}\\.)+[a-zA-Z\x7f-\xff][-a-zA-Z0-9\x7f-\xff]{1,62}|(?:[1-9][0-9]{0,2}\\.|0\\.){3}(?:[1-9][0-9]{0,2}|0))((:[0-9]{1,5})?(/[!$-/0-9:;=@_\\':;!a-zA-Z\x7f-\xff]*?)?(\\?[!$-/0-9:;=@_\\':;!a-zA-Z\x7f-\xff]+?)?(#[!$-/0-9?:;=@_\\':;!a-zA-Z\x7f-\xff]+?)?)(?=[)'?.!,;:]*([^-_#$+.!*%'(),;/?:@=&a-zA-Z0-9\x7f-\xff]|$))}i";

    /**
     * @param $line
     * @return string
     */
    public static function parseLine($line) {
        $character_map = self::getCharacterMap();
        $enabled_map = array();

        foreach ($character_map as $key => $value) {
            $enabled_map[$key] = 0;
        }

        $line_escape = htmlentities($line, ENT_QUOTES);
        $line_parsed = '';
        $length = strlen($line_escape);

        for ($i = 0; $i < $length; $i++) {
            $character = $line_escape[$i];

            if (in_array($character, $character_map)) {
                if (self::isBold($character)) {
                    $line_parsed .= self::wrapInElement($character_map['bold'], $enabled_map['bold'] == 1);
                    $enabled_map['bold'] = $enabled_map['bold'] == 0 ? 1 : 0;
                    continue;
                }

                if (self::isColor($character)) {
                    if ($length > $i + 1 && is_numeric($line_escape[$i + 1])) {
                        if ($length > $i + 2 && is_numeric($line_escape[$i + 2])) {
                            $line_parsed .= self::wrapInElement($line_escape[$i + 1] . $line_escape[$i + 2]);
                            $enabled_map['color']++;
                            $i += 2;
                            continue;
                        }

                        $line_parsed .= self::wrapInElement($line_escape[$i + 1]);
                        $enabled_map['color']++;
                        $i++;
                        continue;
                    }

                    if ($enabled_map['color'] > 0) {
                        for ($j = 0; $j < $enabled_map['color']; $j++) {
                            $line_parsed .= self::wrapInelement($character_map['color'], true);
                        }

                        $enabled_map['color'] = 0;
                        continue;
                    }

                    continue;
                }

                if (self::isClear($character)) {
                    foreach ($enabled_map as $key => $value) {
                        while ($enabled_map[$key] > 0) {
                            $line_parsed .= self::wrapInElement($character_map[$key], $enabled_map[$key]-- > 0);
                        }
                    }

                    continue;
                }

                if (self::isReverseTv($character)) {
                    $line_parsed .= self::wrapInElement($character_map['reversetv'], $enabled_map['reversetv'] == 1);
                    $enabled_map['reversetv'] = $enabled_map['reversetv'] == 0 ? 1 : 0;
                    continue;
                }

                if (self::isItalic($character)) {
                    $line_parsed .= self::wrapInElement($character_map['italic'], $enabled_map['italic'] == 1);
                    $enabled_map['italic'] = $enabled_map['italic'] == 0 ? 1 : 0;
                    continue;
                }

                if (self::isUnderline($character)) {
                    $line_parsed .= self::wrapInElement($character_map['underline'], $enabled_map['underline'] == 1);
                    $enabled_map['underline'] = $enabled_map['underline'] == 0 ? 1 : 0;
                    continue;
                }
            }

            $line_parsed .= $character;
        }

        foreach ($enabled_map as $key => $value) {
            while ($enabled_map[$key] > 0) {
                $line_parsed .= self::wrapInElement($character_map[$key], $enabled_map[$key]-- > 0);
            }
        }

        return self::transform($line_parsed);
    }

    /**
     * Create an element to wrap text in.
     *
     * @param $code
     * @param bool $close
     * @return string
     */
    private static function wrapInElement($code, $close = false) {
        if (self::isBold($code)) {
            return !$close ? '<strong>' : '</strong>';
        }

        if (self::isReverseTv($code)) {
            return !$close ? '<span style="color: white; background-color: ' . self::getColorCode(1) . ';">' : '</span>';
        }

        if (self::isItalic($code)) {
            return !$close ? '<em>' : '</em>';
        }

        if (self::isUnderline($code)) {
            return !$close ? '<u>' : '</u>';
        }

        return !$close ? '<span style="color: ' . self::getColorCode($code) . ';">' : '</span>';
    }

    /**
     * @param $line
     * @return bool
     */
    private static function shouldIgnore($line) {
        if (preg_match("/(\\[\\d\\d:\\d\\d:\\d\\d\\] .*\\* .* (has (joined|quit|left)|is now known|sets mode)).*/", $line)) {
            return true;
        }

        return false;
    }

    /**
     * @param $raw
     * @return string
     */
    private static function transform($raw) {
        if (self::shouldIgnore($raw)) {
            return $raw;
        }

        $inside_anchor = false;
        $position = 0;
        $result = '';

        while (true) {
            $match = array();
            preg_match('{</?([a-z]+)([^"\'>]|"[^"]*"|\'[^\']*\')*>|&#?[a-zA-Z0-9]+;|$}', $raw, $match, PREG_OFFSET_CAPTURE, $position);

            list($markup, $markup_position) = $match[0];

            $text = substr($raw, $position, $markup_position - $position);

            if (!$inside_anchor) {
                $text = self::transformUnsafe($text);
            }

            $result .= $text;

            if ($markup === '') {
                break;
            }

            if ($markup[0] !== '&' && $match[1][0] === 'a') {
                $inside_anchor = ($markup[1] !== '/');
            }

            $result .= $markup;

            $position = $markup_position + strlen($markup);
        }

        return $result;
    }

    /**
     * @param $text
     * @return string
     */
    private static function transformUnsafe($text) {
        $result = '';
        $index = 0;
        $match = [];

        while (preg_match(self::$pattern, $text, $match, PREG_OFFSET_CAPTURE, $index)) {
            list($url, $url_index) = $match[0];

            $result .= htmlspecialchars(substr($text, $index, $url_index - $index));

            $scheme = $match[1][0];
            $username = $match[2][0];
            $password = $match[3][0];
            $domain = $match[4][0];
            $after = $match[5][0];
            $port = $match[6][0];
            $path = $match[7][0];

            $tld = strtolower(strrchr($domain, '.'));

            if (preg_match('{^\.[0-9]{1,3}$}', $tld) || in_array($tld, self::$extensions)) {
                if (!$scheme && $password) {
                    $result .= htmlspecialchars($username);
                    $index = $url_index + strlen($username);

                    continue;
                }

                if (!$scheme && $username && !$password && !$after) {
                    $complete_url = "mailto:$url";
                    $linkText = $url;
                } else {
                    $complete_url = $scheme ? $url : "http://$url";
                    $linkText = $complete_url;
                }

                $result .= self::createLinkTag($complete_url, $linkText);
            } else {
                $result .= htmlspecialchars($url);
            }

            $index = $url_index + strlen($url);
        }

        $result .= htmlspecialchars(substr($text, $index));

        return $result;
    }

    /**
     * @param $url
     * @param $content
     * @return string
     */
    private static function createLinkTag($url, $content) {
        return sprintf('<a href="%s" target="_blank">%s</a>', htmlspecialchars($url), htmlspecialchars($content));
    }

    /**
     * @param null $array_item
     * @return array|int|null|string
     */
    private static function getCharacterMap($array_item = null) {
        $map = [
            'bold' => chr(2),
            'color' => chr(3),
            'clear' => chr(15),
            'reversetv' => chr(22),
            'italic' => chr(29),
            'underline' => chr(31),
        ];

        if ($array_item !== null) {
            foreach ($map as $key => $value) {
                if ($key == $array_item) {
                    return $value;
                } else if ($value == $array_item) {
                    return $key;
                }
            }

            return null;
        }

        return $map;
    }

    /* ---- Colour Map ----
     * --------------------
     *    00 - white
     *    01 - black
     *    02 - dark blue
     *    03 - dark green
     *    04 - red
     *    05 - brown
     *    06 - purple
     *    07 - olive
     *    08 - yellow
     *    09 - green
     *    10 - teal
     *    11 - cyan
     *    12 - blue
     *    13 - magenta
     *    14 - dark grey
     *    15 - light grey
     */
    /**
     * Get a hex colour value for an integer-based colour.
     *
     * @param $input
     * @return string
     */
    private static function getColorCode($input) {
        if (is_int($input)) {
            if ($input >= 10) {
                $input = strval($input);
            } else {
                $input = '0' . strval($input);
            }
        }

        switch ($input) {
            case '00':
            case '01':
                return '#000000';
            case '02':
                return '#000080';
            case '03':
                return '#8000FF';
            case '04':
                return '#FF0000';
            case '05':
                return '#A52A2A';
            case '06':
                return '#8000FF';
            case '07':
                return '#808000';
            case '08':
                return '#FFFF00';
            case '09':
                return '#00FF00';
            case '10':
                return '#008080';
            case '11':
                return '#00FFFF';
            case '12':
                return '#0000FF';
            case '13':
                return '#FF00FF';
            case '14':
                return '#808080';
            case '15':
                return '#C0C0C0';
            case '16':
                return '#FFFFFF';
            default:
                return $input;
        }
    }

    /*
     * helpers
     */
    /**
     * Determine if the provided colour code is bold.
     *
     * @param $code
     * @return bool
     */
    private static function isBold($code) {
        return $code == self::getCharacterMap('bold');
    }

    /**
     * Determine if the provided colour code is color.
     *
     * @param $code
     * @return bool
     */
    private static function isColor($code) {
        return $code == self::getCharacterMap('color');
    }

    /**
     * Determine if the provided colour code is clear.
     *
     * @param $code
     * @return bool
     */
    private static function isClear($code) {
        return $code == self::getCharacterMap('clear');
    }

    /**
     * Determine if the provided colour code is reversetv.
     *
     * @param $code
     * @return bool
     */
    private static function isReverseTv($code) {
        return $code == self::getCharacterMap('reversetv');
    }

    /**
     * Determine if the provided colour code is italic.
     *
     * @param $code
     * @return bool
     */
    private static function isItalic($code) {
        return $code == self::getCharacterMap('italic');
    }

    /**
     * Determine if the provided colour code is underline.
     *
     * @param $code
     * @return bool
     */
    private static function isUnderline($code) {
        return $code == self::getCharacterMap('underline');
    }
}
