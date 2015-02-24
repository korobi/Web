<?php

namespace Korobi\WebBundle\Parser;

class IRCTextParser {

    // http://data.iana.org/TLD/tlds-alpha-by-domain.txt
    // current version: 2015020500
    private static $extensions = ['.abogado', '.ac', '.academy', '.accountants', '.active', '.actor', '.ad', '.adult', '.ae', '.aero', '.af', '.ag', '.agency', '.ai', '.airforce', '.al', '.allfinanz', '.alsace', '.am', '.amsterdam', '.an', '.android', '.ao', '.aq', '.aquarelle', '.ar', '.archi', '.army', '.arpa', '.as', '.asia', '.associates', '.at', '.attorney', '.au', '.auction', '.audio', '.autos', '.aw', '.ax', '.axa', '.az', '.ba', '.band', '.bank', '.bar', '.barclaycard', '.barclays', '.bargains', '.bayern', '.bb', '.bd', '.be', '.beer', '.berlin', '.best', '.bf', '.bg', '.bh', '.bi', '.bid', '.bike', '.bingo', '.bio', '.biz', '.bj', '.black', '.blackfriday', '.bloomberg', '.blue', '.bm', '.bmw', '.bn', '.bnpparibas', '.bo', '.boo', '.boutique', '.br', '.brussels', '.bs', '.bt', '.budapest', '.build', '.builders', '.business', '.buzz', '.bv', '.bw', '.by', '.bz', '.bzh', '.ca', '.cab', '.cal', '.camera', '.camp', '.cancerresearch', '.canon', '.capetown', '.capital', '.caravan', '.cards', '.care', '.career', '.careers', '.cartier', '.casa', '.cash', '.cat', '.catering', '.cc', '.cd', '.center', '.ceo', '.cern', '.cf', '.cg', '.ch', '.channel', '.chat', '.cheap', '.christmas', '.chrome', '.church', '.ci', '.citic', '.city', '.ck', '.cl', '.claims', '.cleaning', '.click', '.clinic', '.clothing', '.club', '.cm', '.cn', '.co', '.coach', '.codes', '.coffee', '.college', '.cologne', '.com', '.community', '.company', '.computer', '.condos', '.construction', '.consulting', '.contractors', '.cooking', '.cool', '.coop', '.country', '.cr', '.credit', '.creditcard', '.cricket', '.crs', '.cruises', '.cu', '.cuisinella', '.cv', '.cw', '.cx', '.cy', '.cymru', '.cz', '.dabur', '.dad', '.dance', '.dating', '.day', '.dclk', '.de', '.deals', '.degree', '.delivery', '.democrat', '.dental', '.dentist', '.desi', '.design', '.dev', '.diamonds', '.diet', '.digital', '.direct', '.directory', '.discount', '.dj', '.dk', '.dm', '.dnp', '.do', '.docs', '.domains', '.doosan', '.durban', '.dvag', '.dz', '.eat', '.ec', '.edu', '.education', '.ee', '.eg', '.email', '.emerck', '.energy', '.engineer', '.engineering', '.enterprises', '.equipment', '.er', '.es', '.esq', '.estate', '.et', '.eu', '.eurovision', '.eus', '.events', '.everbank', '.exchange', '.expert', '.exposed', '.fail', '.farm', '.fashion', '.feedback', '.fi', '.finance', '.financial', '.firmdale', '.fish', '.fishing', '.fit', '.fitness', '.fj', '.fk', '.flights', '.florist', '.flowers', '.flsmidth', '.fly', '.fm', '.fo', '.foo', '.forsale', '.foundation', '.fr', '.frl', '.frogans', '.fund', '.furniture', '.futbol', '.ga', '.gal', '.gallery', '.garden', '.gb', '.gbiz', '.gd', '.ge', '.gent', '.gf', '.gg', '.ggee', '.gh', '.gi', '.gift', '.gifts', '.gives', '.gl', '.glass', '.gle', '.global', '.globo', '.gm', '.gmail', '.gmo', '.gmx', '.gn', '.goog', '.google', '.gop', '.gov', '.gp', '.gq', '.gr', '.graphics', '.gratis', '.green', '.gripe', '.gs', '.gt', '.gu', '.guide', '.guitars', '.guru', '.gw', '.gy', '.hamburg', '.hangout', '.haus', '.healthcare', '.help', '.here', '.hermes', '.hiphop', '.hiv', '.hk', '.hm', '.hn', '.holdings', '.holiday', '.homes', '.horse', '.host', '.hosting', '.house', '.how', '.hr', '.ht', '.hu', '.ibm', '.id', '.ie', '.ifm', '.il', '.im', '.immo', '.immobilien', '.in', '.industries', '.info', '.ing', '.ink', '.institute', '.insure', '.int', '.international', '.investments', '.io', '.iq', '.ir', '.irish', '.is', '.it', '.iwc', '.jcb', '.je', '.jetzt', '.jm', '.jo', '.jobs', '.joburg', '.jp', '.juegos', '.kaufen', '.kddi', '.ke', '.kg', '.kh', '.ki', '.kim', '.kitchen', '.kiwi', '.km', '.kn', '.koeln', '.kp', '.kr', '.krd', '.kred', '.kw', '.ky', '.kyoto', '.kz', '.la', '.lacaixa', '.land', '.lat', '.latrobe', '.lawyer', '.lb', '.lc', '.lds', '.lease', '.legal', '.lgbt', '.li', '.lidl', '.life', '.lighting', '.limited', '.limo', '.link', '.lk', '.loans', '.london', '.lotte', '.lotto', '.lr', '.ls', '.lt', '.ltda', '.lu', '.luxe', '.luxury', '.lv', '.ly', '.ma', '.madrid', '.maison', '.management', '.mango', '.market', '.marketing', '.marriott', '.mc', '.md', '.me', '.media', '.meet', '.melbourne', '.meme', '.memorial', '.menu', '.mg', '.mh', '.miami', '.mil', '.mini', '.mk', '.ml', '.mm', '.mn', '.mo', '.mobi', '.moda', '.moe', '.monash', '.money', '.mormon', '.mortgage', '.moscow', '.motorcycles', '.mov', '.mp', '.mq', '.mr', '.ms', '.mt', '.mu', '.museum', '.mv', '.mw', '.mx', '.my', '.mz', '.na', '.nagoya', '.name', '.navy', '.nc', '.ne', '.net', '.network', '.neustar', '.new', '.nexus', '.nf', '.ng', '.ngo', '.nhk', '.ni', '.ninja', '.nl', '.no', '.np', '.nr', '.nra', '.nrw', '.ntt', '.nu', '.nyc', '.nz', '.okinawa', '.om', '.one', '.ong', '.onl', '.ooo', '.org', '.organic', '.osaka', '.otsuka', '.ovh', '.pa', '.paris', '.partners', '.parts', '.party', '.pe', '.pf', '.pg', '.ph', '.pharmacy', '.photo', '.photography', '.photos', '.physio', '.pics', '.pictures', '.pink', '.pizza', '.pk', '.pl', '.place', '.plumbing', '.pm', '.pn', '.pohl', '.poker', '.porn', '.post', '.pr', '.praxi', '.press', '.pro', '.prod', '.productions', '.prof', '.properties', '.property', '.ps', '.pt', '.pub', '.pw', '.py', '.qa', '.qpon', '.quebec', '.re', '.realtor', '.recipes', '.red', '.rehab', '.reise', '.reisen', '.reit', '.ren', '.rentals', '.repair', '.report', '.republican', '.rest', '.restaurant', '.reviews', '.rich', '.rio', '.rip', '.ro', '.rocks', '.rodeo', '.rs', '.rsvp', '.ru', '.ruhr', '.rw', '.ryukyu', '.sa', '.saarland', '.sale', '.samsung', '.sarl', '.sb', '.sc', '.sca', '.scb', '.schmidt', '.schule', '.schwarz', '.science', '.scot', '.sd', '.se', '.services', '.sew', '.sexy', '.sg', '.sh', '.shiksha', '.shoes', '.shriram', '.si', '.singles', '.sj', '.sk', '.sky', '.sl', '.sm', '.sn', '.so', '.social', '.software', '.sohu', '.solar', '.solutions', '.soy', '.space', '.spiegel', '.sr', '.st', '.style', '.su', '.supplies', '.supply', '.support', '.surf', '.surgery', '.suzuki', '.sv', '.sx', '.sy', '.sydney', '.systems', '.sz', '.taipei', '.tatar', '.tattoo', '.tax', '.tc', '.td', '.technology', '.tel', '.temasek', '.tennis', '.tf', '.tg', '.th', '.tienda', '.tips', '.tires', '.tirol', '.tj', '.tk', '.tl', '.tm', '.tn', '.to', '.today', '.tokyo', '.tools', '.top', '.toshiba', '.town', '.toys', '.tp', '.tr', '.trade', '.training', '.travel', '.trust', '.tt', '.tui', '.tv', '.tw', '.tz', '.ua', '.ug', '.uk', '.university', '.uno', '.uol', '.us', '.uy', '.uz', '.va', '.vacations', '.vc', '.ve', '.vegas', '.ventures', '.versicherung', '.vet', '.vg', '.vi', '.viajes', '.video', '.villas', '.vision', '.vlaanderen', '.vn', '.vodka', '.vote', '.voting', '.voto', '.voyage', '.vu', '.wales', '.wang', '.watch', '.webcam', '.website', '.wed', '.wedding', '.wf', '.whoswho', '.wien', '.wiki', '.williamhill', '.wme', '.work', '.works', '.world', '.ws', '.wtc', '.wtf', '.xn--1qqw23a', '.xn--3bst00m', '.xn--3ds443g', '.xn--3e0b707e', '.xn--45brj9c', '.xn--45q11c', '.xn--4gbrim', '.xn--55qw42g', '.xn--55qx5d', '.xn--6frz82g', '.xn--6qq986b3xl', '.xn--80adxhks', '.xn--80ao21a', '.xn--80asehdb', '.xn--80aswg', '.xn--90a3ac', '.xn--b4w605ferd', '.xn--c1avg', '.xn--cg4bki', '.xn--clchc0ea0b2g2a9gcd', '.xn--czr694b', '.xn--czrs0t', '.xn--czru2d', '.xn--d1acj3b', '.xn--d1alf', '.xn--fiq228c5hs', '.xn--fiq64b', '.xn--fiqs8s', '.xn--fiqz9s', '.xn--flw351e', '.xn--fpcrj9c3d', '.xn--fzc2c9e2c', '.xn--gecrj9c', '.xn--h2brj9c', '.xn--hxt814e', '.xn--i1b6b1a6a2e', '.xn--io0a7i', '.xn--j1amh', '.xn--j6w193g', '.xn--kprw13d', '.xn--kpry57d', '.xn--kput3i', '.xn--l1acc', '.xn--lgbbat1ad8j', '.xn--mgb9awbf', '.xn--mgba3a4f16a', '.xn--mgbaam7a8h', '.xn--mgbab2bd', '.xn--mgbayh7gpa', '.xn--mgbbh1a71e', '.xn--mgbc0a9azcg', '.xn--mgberp4a5d4ar', '.xn--mgbx4cd0ab', '.xn--ngbc5azd', '.xn--node', '.xn--nqv7f', '.xn--nqv7fs00ema', '.xn--o3cw4h', '.xn--ogbpf8fl', '.xn--p1acf', '.xn--p1ai', '.xn--pgbs0dh', '.xn--q9jyb4c', '.xn--qcka1pmc', '.xn--rhqv96g', '.xn--s9brj9c', '.xn--ses554g', '.xn--unup4y', '.xn--vermgensberater-ctb', '.xn--vermgensberatung-pwb', '.xn--vhquv', '.xn--wgbh1c', '.xn--wgbl6a', '.xn--xhq521b', '.xn--xkc2al3hye2a', '.xn--xkc2dl3a5ee0h', '.xn--yfro4i67o', '.xn--ygbi2ammx', '.xn--zfr164b', '.xxx', '.xyz', '.yachts', '.yandex', '.ye', '.yoga', '.yokohama', '.youtube', '.yt', '.za', '.zip', '.zm', '.zone', '.zuerich', '.zw'];
    private static $pattern = "{\\b(https?://|ftp://)?(?:([^]\\x00-\x20\"(),:-<>[\x7f-\xff]{1,64})(:[^]\\x00-\x20\"(),:-<>[\x7f-\xff]{1,64})?@)?((?:[-a-zA-Z0-9\x7f-\xff]{1,63}\\.)+[a-zA-Z\x7f-\xff][-a-zA-Z0-9\x7f-\xff]{1,62}|(?:[1-9][0-9]{0,2}\\.|0\\.){3}(?:[1-9][0-9]{0,2}|0))((:[0-9]{1,5})?(/[!$-/0-9:;=@_\\':;!a-zA-Z\x7f-\xff]*?)?(\\?[!$-/0-9:;=@_\\':;!a-zA-Z\x7f-\xff]+?)?(#[!$-/0-9?:;=@_\\':;!a-zA-Z\x7f-\xff]+?)?)(?=[)'?.!,;:]*([^-_#$+.!*%'(),;/?:@=&a-zA-Z0-9\x7f-\xff]|$))}i";

    /**
     * @param $line
     * @return string
     */
    public static function parse($line) {
        return self::parseLine($line, false);
    }

    /**
     * @param $line
     * @param $pretty_only
     * @return string
     */
    public static function parseLine($line, $pretty_only) {
        $characterMap = self::getCharacterMap();
        $activeMap = [];

        foreach ($characterMap as $key => $value) {
            $activeMap[$key] = 0;
        }

        if (!$pretty_only) {
            $line = htmlentities($line, ENT_QUOTES);
        }

        $result = '';
        $length = strlen($line);

        for ($i = 0; $i < $length; $i++) {
            $character = $line[$i];

            if (in_array($character, $characterMap)) {
                if (self::isBold($character)) {
                    $result .= self::wrapInElement($characterMap['bold'], $activeMap['bold'] == 1);
                    $activeMap['bold'] = $activeMap['bold'] == 0 ? 1 : 0;
                    continue;
                }

                if (self::isColor($character)) {
                    $sixSubsequentCharacters = substr($line, $i, 6);
                    $colours = IRCColourParser::parseColour($sixSubsequentCharacters);
                    if ($colours !== null) {
                        $result .= '<span class="irc--' . $colours['foreground'] . '-' . $colours['background'] . '">';
                        $activeMap['color'] = $activeMap['color'] + 1;
                        $i += $colours['skip'];
                        continue;
                    }

                    if ($activeMap['color'] > 0) {
                        for ($j = 0; $j < $activeMap['color']; $j++) {
                            $result .= self::wrapInelement($characterMap['color'], true);
                        }

                        $activeMap['color'] = 0;
                        continue;
                    }

                    continue;
                }

                if (self::isClear($character)) {
                    foreach ($activeMap as $key => $value) {
                        while ($activeMap[$key] > 0) {
                            $result .= self::wrapInElement($characterMap[$key], $activeMap[$key]-- > 0);
                        }
                    }

                    continue;
                }

                if (self::isReverseTv($character)) {
                    $result .= self::wrapInElement($characterMap['reversetv'], $activeMap['reversetv'] == 1);
                    $activeMap['reversetv'] = $activeMap['reversetv'] == 0 ? 1 : 0;
                    continue;
                }

                if (self::isItalic($character)) {
                    $result .= self::wrapInElement($characterMap['italic'], $activeMap['italic'] == 1);
                    $activeMap['italic'] = $activeMap['italic'] == 0 ? 1 : 0;
                    continue;
                }

                if (self::isUnderline($character)) {
                    $result .= self::wrapInElement($characterMap['underline'], $activeMap['underline'] == 1);
                    $activeMap['underline'] = $activeMap['underline'] == 0 ? 1 : 0;
                    continue;
                }
            }

            $result .= $character;
        }

        foreach ($activeMap as $key => $value) {
            while ($activeMap[$key] > 0) {
                $result .= self::wrapInElement($characterMap[$key], $activeMap[$key]-- > 0);
            }
        }

        if ($pretty_only) {
            return $result;
        } else {
            return self::transform($result);
        }
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
            return !$close ? '<span class="' . self::getColorClass(1, '-inverse') . '">' : '</span>';
        }

        if (self::isItalic($code)) {
            return !$close ? '<em>' : '</em>';
        }

        if (self::isUnderline($code)) {
            return !$close ? '<u>' : '</u>';
        }

        return !$close ? '<span class="' . self::getColorClass($code) . '">' : '</span>';
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

        $insideAnchor = false;
        $position = 0;
        $result = '';

        while (true) {
            $match = [];
            preg_match('{</?([a-z]+)([^"\'>]|"[^"]*"|\'[^\']*\')*>|&#?[a-zA-Z0-9]+;|$}', $raw, $match, PREG_OFFSET_CAPTURE, $position);

            list($markup, $markupIndex) = $match[0];

            $text = substr($raw, $position, $markupIndex - $position);

            if (!$insideAnchor) {
                $text = self::transformUnsafe($text);
            }

            $result .= $text;

            if ($markup === '') {
                break;
            }

            if ($markup[0] !== '&' && $match[1][0] === 'a') {
                $insideAnchor = ($markup[1] !== '/');
            }

            $result .= $markup;

            $position = $markupIndex + strlen($markup);
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
            list($url, $urlIndex) = $match[0];

            $result .= htmlspecialchars(substr($text, $index, $urlIndex - $index));

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
                    $index = $urlIndex + strlen($username);

                    continue;
                }

                if (!$scheme && $username && !$password && !$after) {
                    $linkRef = "mailto:$url";
                    $linkText = $url;
                } else {
                    $linkRef = $scheme ? $url : "http://$url";
                    if (!$scheme) {
                        $linkText = $url;
                    } else {
                        $linkText = $linkRef;
                    }
                }

                $result .= self::createLinkTag($linkRef, $linkText);
            } else {
                $result .= htmlspecialchars($url);
            }

            $index = $urlIndex + strlen($url);
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
     * @param null $item
     * @return array|int|null|string
     */
    private static function getCharacterMap($item = null) {
        $map = [
            'bold' => chr(2),
            'color' => chr(3),
            'clear' => chr(15),
            'reversetv' => chr(22),
            'italic' => chr(29),
            'underline' => chr(31),
        ];

        if ($item !== null) {
            foreach ($map as $key => $value) {
                if ($key == $item) {
                    return $value;
                } else if ($value == $item) {
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
     * @param null $suffix
     * @return string
     */
    private static function getColorClass($input, $suffix = null) {
        if (is_int($input)) {
            if ($input >= 10) {
                $input = strval($input);
            } else {
                $input = '0' . strval($input);
            }
        }

        $suffix = $suffix ?: '-99';

        $prefix = 'irc--';
        switch ($input) {
            case '00':
            case '01':
            case '02':
            case '03':
            case '04':
            case '05':
            case '06':
            case '07':
            case '08':
            case '09':
                return $prefix . substr($input, 1, 1) . $suffix;
            case '10':
            case '11':
            case '12':
            case '13':
            case '14':
            case '15':
                return $prefix . $input . $suffix;
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
