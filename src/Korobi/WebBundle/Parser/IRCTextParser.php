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
    const COLOR_REGEX_15    = "/^(?P<fg>1[0-5]|0?[0-9])?(?:,(?P<bg>1[0-5]|0?[0-9]))?/S";
    const DEFAULT_STYLES    = [
        'fg' => self::DEFAULT_FOREGROUND,
        'bg' => self::DEFAULT_BACKGROUND,
        'reverse' => false,
        'bold' => false,
        'italic' => false,
        'underline' => false,
    ];
    const URL_REGEX = "/(\\b(?:https?:\\/\\/|ftp:\\/\\/)(?:([^]\\\\\\\\\\x00-\\x20\\\"(),:-<>[\\x7f-\\xff]{1,64})(:[^]\\\\\\\\\\x00-\\x20\\\"(),:-<>[\\x7f-\\xff]{1,64})?@)?(?:(?:[-a-zA-Z0-9\\x7f-\\xff]{1,63}\\.)+[a-zA-Z\\x7f-\\xff][-a-zA-Z0-9\\x7f-\\xff]{1,62}|(?:[1-9][0-9]{0,2}\\.|0\\.){3}(?:[1-9][0-9]{0,2}|0))(?:(:[0-9]{1,5})?(?:\\/[!$-\\/0-9:;=@_\\':;!a-zA-Z\\x7f-\\xff]*?)?(?:\\?[!$-\\/0-9:;=@_\\':;!a-zA-Z\\x7f-\\xff]+?)?(?:#[!$-\\/0-9?:;=@_\\':;!a-zA-Z\\x7f-\\xff]+?)?)(?=[)'?.!,;:]*(?:[^-_#$+.!*%'(),;\\/?:@=&a-zA-Z0-9\\x7f-\\xff]|$)))/i";

    /**
     * Parses an irc line containing irc format control chars, parsing links additionally.
     *
     * @param string $line
     * @return string
     */
    public static function parse($line) {
        return self::parseLine($line, false);
    }

    /**
     * Parses an irc line containing irc format control chars.
     *
     * @param string $line
     * @param bool $pretty_only Whether to also parse links and stuff as well
     * @return string An html formatted string
     */
    public static function parseLine($line, $pretty_only) {
        $result = '';
        $next = $line;

        $styles = self::DEFAULT_STYLES;

        while (preg_match(self::INCREMENTAL_REGEX, $next, $matches, PREG_OFFSET_CAPTURE)) {
            $prev_styles = $styles;
            $index = $matches[1][1];
            $skip = 1; // Skip the format char

            $style = self::CONTROL_CHAR_TYPES[$matches[1][0]];
            switch ($style) {
                case 'bold':
                case 'italic':
                case 'underline':
                case 'reverse':
                    $styles[$style] ^= 1;
                    break;
                case 'colour':
                    $colour_info = self::parseColour(
                        substr($next, $index + 1, 7),
                        $styles['fg'],
                        $styles['bg']
                    );
                    $skip += $colour_info['skip'];
                    $styles['fg'] = $colour_info['fg'];
                    $styles['bg'] = $colour_info['bg'];
                    break;
                case 'clear':
                    $styles = self::DEFAULT_STYLES;
                    break;
            }

            // Add text before the style change
            $result .= self::transform(substr($next, 0, $index));

            if($prev_styles != $styles) {
                // Close previous style and apply the new one
                $result .= self::closeTags($prev_styles);
                $result .= self::openTags($styles);
            }

            // Keep the rest of the string for further processing
            $next = substr($next, $index + $skip);
        }

        // Add the rest of the stuff and close tags
        $result .= self::transform($next) . self::closeTags($styles);

        return $result;
    }

    /**
     * Close all tags opened per the provided style array.
     *
     * @param array $styles
     * @return string
     */
    private static function closeTags(array $styles) {
        $result = '';

        if (!empty($styles)) {
            $fg = array_shift($styles);
            $bg = array_shift($styles);
            array_shift($styles); // Ignore reverse
            if ($fg != self::DEFAULT_FOREGROUND || $bg != self::DEFAULT_BACKGROUND) {
                $result .= self::closeTag();
            }
            foreach ($styles as $format => $is_applied) {
                if ($is_applied) {
                    $result .= self::closeTag();
                }
            }
        }

        return $result;
    }

    /**
     * Returns the closing tag;
     *
     * @return string
     */
    public static function closeTag() {
        return '</span>';
    }

    /**
     * Opens tags corresponding to the provided array.
     *
     * @param array $styles
     * @return string
     */
    private static function openTags(array $styles) {
        $result = '';

        $fg = array_shift($styles);
        $bg = array_shift($styles);
        $reverse = array_shift($styles);
        if($fg != self::DEFAULT_FOREGROUND || $bg != self::DEFAULT_BACKGROUND) {
            $result .= self::createColorTag($fg, $bg, $reverse);
        }

        foreach ($styles as $format => $is_applied) {
            if($is_applied) {
                $result .= self::createFormatTag($format);
            }
        }

        return $result;
    }

    /**
     * Creates a formatting element to wrap text in.
     *
     * @param string $style
     * @return string
     */
    private static function createFormatTag($style) {
        return '<span class="' . $style . '">';
    }

    /**
     * Creates a span element with a class corresponding to the provided colour information.
     *
     * @param int $fg
     * @param int $bg
     * @param bool $reverse
     * @return string
     */
    public static function createColorTag($fg, $bg, $reverse = false) {
        return '<span class="'. ($reverse
            ? self::getColorClass($bg, $fg)
            : self::getColorClass($fg, $bg)) . '">';
    }

    /**
     * Gets the css class corresponding to the provided colours.
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
     * Parses a string starting with a colour code to extract the foreground and background
     * colour codes as well as the count of chars to skip (the number of chars taken by the
     * actual code).
     *
     * @param string $messageFragment The fragment of message to extract the colours from.
     * @param string $fg The previous foreground to use if it's not changed
     * @param string $bg The previous background to use if it's not changed
     * @return array An array of data containing fg, bg and char count to skip.
     */
    public static function parseColour($messageFragment, $fg = self::DEFAULT_FOREGROUND, $bg = self::DEFAULT_BACKGROUND) {
        $result = [
            'fg' => $fg,
            'bg' => $bg,
            'skip' => 0
        ];

        preg_match(
            self::COLOR_REGEX_15,
            $messageFragment,
            $matches,
            PREG_OFFSET_CAPTURE
        );

        if (isset($matches['fg']) || isset($matches['bg'])) {
            if (isset($matches['fg'])) {
                $result['fg'] = intval($matches['fg'][0]);
                $result['skip'] += strlen($matches['fg'][0]);
            }

            if (isset($matches['bg'])) {
                $result['bg'] = intval($matches['bg'][0]);
                $result['skip'] += strlen($matches['bg'][0]) + 1; // + 1 for the comma
            }
        } else {
            // a color char alone resets the foreground
            $result['fg'] = self::DEFAULT_FOREGROUND;
        }

        return $result;
    }

    /**
     * Transforms a string adding html links while preserving current html tags.
     *
     * @param string $raw
     * @return string
     */
    private static function transform($raw) {
        $result = '';
        $next = $raw;

        while (preg_match(self::URL_REGEX, $next, $matches, PREG_OFFSET_CAPTURE)) {
            $url = $matches[1][0];
            $index = $matches[1][1];
            $url_len = strlen($url);

            $result .= htmlentities(substr($next, 0, $index), ENT_HTML5)
                . self::createLinkTag($url);
            $next = substr($next, $index + $url_len);
        }

        return $result . htmlentities($next, ENT_HTML5 | ENT_COMPAT);
    }

    /**
     * Creates an html <a> tag from the provided url.
     *
     * @param string $url
     * @return string
     */
    private static function createLinkTag($url) {
        return sprintf(
            '<a href="%s" target="_blank">%s</a>',
            htmlspecialchars($url),
            htmlentities($url, ENT_HTML5)
        );
    }
}
