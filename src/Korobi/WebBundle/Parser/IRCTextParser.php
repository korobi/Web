<?php

namespace Korobi\WebBundle\Parser;

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
class IRCTextParser {

    const COLOR_CLASS_PREFIX = 'irc--';
    const DEFAULT_FOREGROUND = 'df';
    const DEFAULT_BACKGROUND = 'df';
    const CONTROL_CHAR_TYPES = [
        "\x02" => 'bold',
        "\x03" => 'colour',
        "\x0f" => 'clear',
        "\x16" => 'reverse',
        "\x1d" => 'italic',
        "\x1f" => 'underline',
    ];
    const INCREMENTAL_REGEX = "/(?P<char>\x02|\x03|\x0f|\x16|\x1d|\x1f)/S";
    //const COLOR_REGEX_255   = "/^(?P<fg>25[0-5]|2[0-4][0-9]|[01]?[0-9]?[0-9])?(?:,(?P<bg>25[0-5]|2[0-4][0-9]|[01]?[0-9]?[0-9]))?/S";
    //const COLOR_REGEX_99    = "/^(?P<fg>[0-9]{1,2})?(?:,(?P<bg>[0-9]{1,2}))?/S";
    const COLOR_REGEX_15    = "/^(?P<fg>1[0-5]|0?[0-9])?(?:,(?P<bg>1[0-5]|0?[0-9]))?/S";
    // http://data.iana.org/TLD/tlds-alpha-by-domain.txt
    // current version: 2015020500
    const EXTENSIONS = ['.abogado', '.ac', '.academy', '.accountants', '.active', '.actor', '.ad', '.adult', '.ae', '.aero', '.af', '.ag', '.agency', '.ai', '.airforce', '.al', '.allfinanz', '.alsace', '.am', '.amsterdam', '.an', '.android', '.ao', '.aq', '.aquarelle', '.ar', '.archi', '.army', '.arpa', '.as', '.asia', '.associates', '.at', '.attorney', '.au', '.auction', '.audio', '.autos', '.aw', '.ax', '.axa', '.az', '.ba', '.band', '.bank', '.bar', '.barclaycard', '.barclays', '.bargains', '.bayern', '.bb', '.bd', '.be', '.beer', '.berlin', '.best', '.bf', '.bg', '.bh', '.bi', '.bid', '.bike', '.bingo', '.bio', '.biz', '.bj', '.black', '.blackfriday', '.bloomberg', '.blue', '.bm', '.bmw', '.bn', '.bnpparibas', '.bo', '.boo', '.boutique', '.br', '.brussels', '.bs', '.bt', '.budapest', '.build', '.builders', '.business', '.buzz', '.bv', '.bw', '.by', '.bz', '.bzh', '.ca', '.cab', '.cal', '.camera', '.camp', '.cancerresearch', '.canon', '.capetown', '.capital', '.caravan', '.cards', '.care', '.career', '.careers', '.cartier', '.casa', '.cash', '.cat', '.catering', '.cc', '.cd', '.center', '.ceo', '.cern', '.cf', '.cg', '.ch', '.channel', '.chat', '.cheap', '.christmas', '.chrome', '.church', '.ci', '.citic', '.city', '.ck', '.cl', '.claims', '.cleaning', '.click', '.clinic', '.clothing', '.club', '.cm', '.cn', '.co', '.coach', '.codes', '.coffee', '.college', '.cologne', '.com', '.community', '.company', '.computer', '.condos', '.construction', '.consulting', '.contractors', '.cooking', '.cool', '.coop', '.country', '.cr', '.credit', '.creditcard', '.cricket', '.crs', '.cruises', '.cu', '.cuisinella', '.cv', '.cw', '.cx', '.cy', '.cymru', '.cz', '.dabur', '.dad', '.dance', '.dating', '.day', '.dclk', '.de', '.deals', '.degree', '.delivery', '.democrat', '.dental', '.dentist', '.desi', '.design', '.dev', '.diamonds', '.diet', '.digital', '.direct', '.directory', '.discount', '.dj', '.dk', '.dm', '.dnp', '.do', '.docs', '.domains', '.doosan', '.durban', '.dvag', '.dz', '.eat', '.ec', '.edu', '.education', '.ee', '.eg', '.email', '.emerck', '.energy', '.engineer', '.engineering', '.enterprises', '.equipment', '.er', '.es', '.esq', '.estate', '.et', '.eu', '.eurovision', '.eus', '.events', '.everbank', '.exchange', '.expert', '.exposed', '.fail', '.farm', '.fashion', '.feedback', '.fi', '.finance', '.financial', '.firmdale', '.fish', '.fishing', '.fit', '.fitness', '.fj', '.fk', '.flights', '.florist', '.flowers', '.flsmidth', '.fly', '.fm', '.fo', '.foo', '.forsale', '.foundation', '.fr', '.frl', '.frogans', '.fund', '.furniture', '.futbol', '.ga', '.gal', '.gallery', '.garden', '.gb', '.gbiz', '.gd', '.ge', '.gent', '.gf', '.gg', '.ggee', '.gh', '.gi', '.gift', '.gifts', '.gives', '.gl', '.glass', '.gle', '.global', '.globo', '.gm', '.gmail', '.gmo', '.gmx', '.gn', '.goog', '.google', '.gop', '.gov', '.gp', '.gq', '.gr', '.graphics', '.gratis', '.green', '.gripe', '.gs', '.gt', '.gu', '.guide', '.guitars', '.guru', '.gw', '.gy', '.hamburg', '.hangout', '.haus', '.healthcare', '.help', '.here', '.hermes', '.hiphop', '.hiv', '.hk', '.hm', '.hn', '.holdings', '.holiday', '.homes', '.horse', '.host', '.hosting', '.house', '.how', '.hr', '.ht', '.hu', '.ibm', '.id', '.ie', '.ifm', '.il', '.im', '.immo', '.immobilien', '.in', '.industries', '.info', '.ing', '.ink', '.institute', '.insure', '.int', '.international', '.investments', '.io', '.iq', '.ir', '.irish', '.is', '.it', '.iwc', '.jcb', '.je', '.jetzt', '.jm', '.jo', '.jobs', '.joburg', '.jp', '.juegos', '.kaufen', '.kddi', '.ke', '.kg', '.kh', '.ki', '.kim', '.kitchen', '.kiwi', '.km', '.kn', '.koeln', '.kp', '.kr', '.krd', '.kred', '.kw', '.ky', '.kyoto', '.kz', '.la', '.lacaixa', '.land', '.lat', '.latrobe', '.lawyer', '.lb', '.lc', '.lds', '.lease', '.legal', '.lgbt', '.li', '.lidl', '.life', '.lighting', '.limited', '.limo', '.link', '.lk', '.loans', '.london', '.lotte', '.lotto', '.lr', '.ls', '.lt', '.ltda', '.lu', '.luxe', '.luxury', '.lv', '.ly', '.ma', '.madrid', '.maison', '.management', '.mango', '.market', '.marketing', '.marriott', '.mc', '.md', '.me', '.media', '.meet', '.melbourne', '.meme', '.memorial', '.menu', '.mg', '.mh', '.miami', '.mil', '.mini', '.mk', '.ml', '.mm', '.mn', '.mo', '.mobi', '.moda', '.moe', '.monash', '.money', '.mormon', '.mortgage', '.moscow', '.motorcycles', '.mov', '.mp', '.mq', '.mr', '.ms', '.mt', '.mu', '.museum', '.mv', '.mw', '.mx', '.my', '.mz', '.na', '.nagoya', '.name', '.navy', '.nc', '.ne', '.net', '.network', '.neustar', '.new', '.nexus', '.nf', '.ng', '.ngo', '.nhk', '.ni', '.ninja', '.nl', '.no', '.np', '.nr', '.nra', '.nrw', '.ntt', '.nu', '.nyc', '.nz', '.okinawa', '.om', '.one', '.ong', '.onl', '.ooo', '.org', '.organic', '.osaka', '.otsuka', '.ovh', '.pa', '.paris', '.partners', '.parts', '.party', '.pe', '.pf', '.pg', '.ph', '.pharmacy', '.photo', '.photography', '.photos', '.physio', '.pics', '.pictures', '.pink', '.pizza', '.pk', '.pl', '.place', '.plumbing', '.pm', '.pn', '.pohl', '.poker', '.porn', '.post', '.pr', '.praxi', '.press', '.pro', '.prod', '.productions', '.prof', '.properties', '.property', '.ps', '.pt', '.pub', '.pw', '.py', '.qa', '.qpon', '.quebec', '.re', '.realtor', '.recipes', '.red', '.rehab', '.reise', '.reisen', '.reit', '.ren', '.rentals', '.repair', '.report', '.republican', '.rest', '.restaurant', '.reviews', '.rich', '.rio', '.rip', '.ro', '.rocks', '.rodeo', '.rs', '.rsvp', '.ru', '.ruhr', '.rw', '.ryukyu', '.sa', '.saarland', '.sale', '.samsung', '.sarl', '.sb', '.sc', '.sca', '.scb', '.schmidt', '.schule', '.schwarz', '.science', '.scot', '.sd', '.se', '.services', '.sew', '.sexy', '.sg', '.sh', '.shiksha', '.shoes', '.shriram', '.si', '.singles', '.sj', '.sk', '.sky', '.sl', '.sm', '.sn', '.so', '.social', '.software', '.sohu', '.solar', '.solutions', '.soy', '.space', '.spiegel', '.sr', '.st', '.style', '.su', '.supplies', '.supply', '.support', '.surf', '.surgery', '.suzuki', '.sv', '.sx', '.sy', '.sydney', '.systems', '.sz', '.taipei', '.tatar', '.tattoo', '.tax', '.tc', '.td', '.technology', '.tel', '.temasek', '.tennis', '.tf', '.tg', '.th', '.tienda', '.tips', '.tires', '.tirol', '.tj', '.tk', '.tl', '.tm', '.tn', '.to', '.today', '.tokyo', '.tools', '.top', '.toshiba', '.town', '.toys', '.tp', '.tr', '.trade', '.training', '.travel', '.trust', '.tt', '.tui', '.tv', '.tw', '.tz', '.ua', '.ug', '.uk', '.university', '.uno', '.uol', '.us', '.uy', '.uz', '.va', '.vacations', '.vc', '.ve', '.vegas', '.ventures', '.versicherung', '.vet', '.vg', '.vi', '.viajes', '.video', '.villas', '.vision', '.vlaanderen', '.vn', '.vodka', '.vote', '.voting', '.voto', '.voyage', '.vu', '.wales', '.wang', '.watch', '.webcam', '.website', '.wed', '.wedding', '.wf', '.whoswho', '.wien', '.wiki', '.williamhill', '.wme', '.work', '.works', '.world', '.ws', '.wtc', '.wtf', '.xn--1qqw23a', '.xn--3bst00m', '.xn--3ds443g', '.xn--3e0b707e', '.xn--45brj9c', '.xn--45q11c', '.xn--4gbrim', '.xn--55qw42g', '.xn--55qx5d', '.xn--6frz82g', '.xn--6qq986b3xl', '.xn--80adxhks', '.xn--80ao21a', '.xn--80asehdb', '.xn--80aswg', '.xn--90a3ac', '.xn--b4w605ferd', '.xn--c1avg', '.xn--cg4bki', '.xn--clchc0ea0b2g2a9gcd', '.xn--czr694b', '.xn--czrs0t', '.xn--czru2d', '.xn--d1acj3b', '.xn--d1alf', '.xn--fiq228c5hs', '.xn--fiq64b', '.xn--fiqs8s', '.xn--fiqz9s', '.xn--flw351e', '.xn--fpcrj9c3d', '.xn--fzc2c9e2c', '.xn--gecrj9c', '.xn--h2brj9c', '.xn--hxt814e', '.xn--i1b6b1a6a2e', '.xn--io0a7i', '.xn--j1amh', '.xn--j6w193g', '.xn--kprw13d', '.xn--kpry57d', '.xn--kput3i', '.xn--l1acc', '.xn--lgbbat1ad8j', '.xn--mgb9awbf', '.xn--mgba3a4f16a', '.xn--mgbaam7a8h', '.xn--mgbab2bd', '.xn--mgbayh7gpa', '.xn--mgbbh1a71e', '.xn--mgbc0a9azcg', '.xn--mgberp4a5d4ar', '.xn--mgbx4cd0ab', '.xn--ngbc5azd', '.xn--node', '.xn--nqv7f', '.xn--nqv7fs00ema', '.xn--o3cw4h', '.xn--ogbpf8fl', '.xn--p1acf', '.xn--p1ai', '.xn--pgbs0dh', '.xn--q9jyb4c', '.xn--qcka1pmc', '.xn--rhqv96g', '.xn--s9brj9c', '.xn--ses554g', '.xn--unup4y', '.xn--vermgensberater-ctb', '.xn--vermgensberatung-pwb', '.xn--vhquv', '.xn--wgbh1c', '.xn--wgbl6a', '.xn--xhq521b', '.xn--xkc2al3hye2a', '.xn--xkc2dl3a5ee0h', '.xn--yfro4i67o', '.xn--ygbi2ammx', '.xn--zfr164b', '.xxx', '.xyz', '.yachts', '.yandex', '.ye', '.yoga', '.yokohama', '.youtube', '.yt', '.za', '.zip', '.zm', '.zone', '.zuerich', '.zw'];
    const URL_PATTERN = "{\\b(https?://|ftp://)?(?:([^]\\x00-\x20\"(),:-<>[\x7f-\xff]{1,64})(:[^]\\x00-\x20\"(),:-<>[\x7f-\xff]{1,64})?@)?((?:[-a-zA-Z0-9\x7f-\xff]{1,63}\\.)+[a-zA-Z\x7f-\xff][-a-zA-Z0-9\x7f-\xff]{1,62}|(?:[1-9][0-9]{0,2}\\.|0\\.){3}(?:[1-9][0-9]{0,2}|0))((:[0-9]{1,5})?(/[!$-/0-9:;=@_\\':;!a-zA-Z\x7f-\xff]*?)?(\\?[!$-/0-9:;=@_\\':;!a-zA-Z\x7f-\xff]+?)?(#[!$-/0-9?:;=@_\\':;!a-zA-Z\x7f-\xff]+?)?)(?=[)'?.!,;:]*([^-_#$+.!*%'(),;/?:@=&a-zA-Z0-9\x7f-\xff]|$))}i";

    /**
     * @param string $line
     * @return string
     */
    public static function parse($line) {
        return self::parseLine($line, false);
    }

    /**
     * @param string $line
     * @param bool $pretty_only Whether to also parse links and stuff as well
     * @return string
     */
    public static function parseLine($line, $pretty_only) {
        $result = '';
        $next = $line;

        self::closeTags($styles);

        while (preg_match(self::INCREMENTAL_REGEX, $next, $matches, PREG_OFFSET_CAPTURE)) {
            $index = $matches[1][1];
            $skip = 1; // Skip the format char
            $tag = ''; // Tag opened in this iteration
            $closeTag = '';

            $style = self::CONTROL_CHAR_TYPES[$matches[1][0]];
            switch ($style) {
                case 'bold':
                case 'italic':
                case 'underline':
                    if ($styles[$style]) {
                        $closeTag = self::closeTag();
                    }
                    $tag = self::createFormatTag($style);
                    $styles[$style] ^= 1;
                    break;
                case 'reverse':
                    // ignore if no color is set
                    if ($styles['fg'] != self::DEFAULT_FOREGROUND || $styles['bg'] != self::DEFAULT_BACKGROUND) {
                        $closeTag = self::closeTag();
                        $tmp = $styles['fg'];
                        $styles['fg'] = $styles['bg'];
                        $styles['bg'] = $tmp;
                        $tag = self::createColorTag($styles['fg'], $styles['bg']);
                    }
                    break;
                case 'colour':
                    if ($styles['fg'] != self::DEFAULT_FOREGROUND || $styles['bg'] != self::DEFAULT_BACKGROUND) {
                        // reset colors first
                        $closeTag = self::closeTag();
                        $styles['fg'] = self::DEFAULT_FOREGROUND;
                        $styles['bg'] = self::DEFAULT_BACKGROUND;
                    }
                    $colour_info = self::parseColour(substr($next, $index + 1, 7));
                    if($colour_info['skip']) { // A color code without simply resets the color
                        $tag = self::createColorTag($colour_info['fg'], $colour_info['bg']);
                        $skip += $colour_info['skip'];
                    }
                    $styles['fg'] = $colour_info['fg'];
                    $styles['bg'] = $colour_info['bg'];
                    break;
                case 'clear':
                    $closeTag = self::closeTags($styles);
                    break;
            }

            $result .= substr($next, 0, $index) . $closeTag . $tag;
            $next = substr($next, $index + $skip);
        }

        $result .= $next . self::closeTags($styles);

        if ($pretty_only) {
            return $result;
        } else {
            return self::transform($result);
        }
    }

    /**
     * @param string $messageFragment The fragment of message to extract the colours from.
     * @return array An array of data containing fg, bg and char count to skip.
     */
    public static function parseColour($messageFragment, $fg = self::DEFAULT_FOREGROUND, $bg = self::DEFAULT_BACKGROUND) {
        $result = [
            'fg' => $fg,
            'bg' => $bg,
            'skip' => 0
        ];

        $retVal = preg_match(
            self::COLOR_REGEX_15,
            $messageFragment,
            $matches,
            PREG_OFFSET_CAPTURE
        );

        if ($retVal) {
            if (isset($matches['fg'])) {
                $result['fg'] = intval($matches['fg'][0]);
                $result['skip'] += strlen($matches['fg'][0]);
            }

            if (isset($matches['bg'])) {
                $result['bg'] = intval($matches['bg'][0]);
                $result['skip'] += strlen($matches['bg'][0]) + 1; // + 1 for the comma
            }
        }

        return $result;
    }

    private static function closeTags(&$styles) {
        $result = '';

        if (!empty($styles)) {
            $fg = array_shift($styles);
            $bg = array_shift($styles);
            if ($fg != self::DEFAULT_FOREGROUND || $bg != self::DEFAULT_BACKGROUND) {
                $result .= self::closeTag();
            }
            foreach ($styles as $k => $v) {
                if ($v) {
                    $result .= self::closeTag();
                }
            }
        }

        // Reset the array
        $styles = [
            'fg' => self::DEFAULT_FOREGROUND,
            'bg' => self::DEFAULT_BACKGROUND,
            'bold' => false,
            'underline' => false,
            'italic' => false,
        ];

        return $result;
    }

    private static function closeTag() {
        return '</span>';
    }

    /**
     * Create an element to wrap text in.
     *
     * @param string $style
     * @return string
     * @internal param bool $close
     */
    private static function createFormatTag($style) {
        return '<span class="' . $style . '">';
    }

    /**
     * Creates a span element with a class corresponding to the provided colour information.
     *
     * @param int $fg
     * @param int $bg
     * @return string
     * @internal param string $style
     */
    private static function createColorTag($fg, $bg) {

        return '<span class="' . self::getColorClass($fg, $bg) . '">';
    }

    /**
     * Gets the class corresponding to the provided colours.
     *
     * @param int|string $fg
     * @param int|string $bg
     * @return string
     */
    private static function getColorClass($fg = self::DEFAULT_FOREGROUND, $bg = self::DEFAULT_BACKGROUND) {
        return self::COLOR_CLASS_PREFIX
        . str_pad($fg, 2, '0', STR_PAD_LEFT) . '-'
        . str_pad($bg, 2, '0', STR_PAD_LEFT);
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

        while (preg_match(self::URL_PATTERN, $text, $match, PREG_OFFSET_CAPTURE, $index)) {
            list($url, $urlIndex) = $match[0];

            $result .= self::makeSafe(substr($text, $index, $urlIndex - $index));

            $scheme = $match[1][0];
            $username = $match[2][0];
            $password = $match[3][0];
            $domain = $match[4][0];
            $after = $match[5][0];
            $port = $match[6][0];
            $path = $match[7][0];

            $tld = strtolower(strrchr($domain, '.'));

            if (preg_match('{^\.[0-9]{1,3}$}', $tld) || in_array($tld, self::EXTENSIONS)) {
                if (!$scheme && $password) {
                    $result .= self::makeSafe($username);
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
                $result .= self::makeSafe($url);
            }

            $index = $urlIndex + strlen($url);
        }

        $result .= self::makeSafe(substr($text, $index));

        return $result;
    }

    /**
     * @param $string
     * @param bool $entities
     * @return mixed|string
     */
    private static function makeSafe($string, $entities = false) {
        if ($entities) {
            $string = htmlentities($string, ENT_QUOTES);
        } else {
            $string = htmlspecialchars($string);
        }

        // Replace &amp; with & to fix link parsing
        $string = str_replace('&amp;', '&', $string);

        return $string;
    }

    /**
     * @param $url
     * @param $content
     * @return string
     */
    private static function createLinkTag($url, $content) {
        return sprintf('<a href="%s" target="_blank">%s</a>', self::makeSafe($url), self::makeSafe($content));
    }
}
