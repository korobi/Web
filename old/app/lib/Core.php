<?php

define('SBNC_ROOT_DIR', '/data1/apps/ircd/sbnc/');
define('SBNC_STATS_DIR', '/data2/sync/kitten/');
define('ASSET_VERSION', 'cuddle');
define('CUSTCMD_PREFIX', '.');
define('CUSTCMD_ENABLED', true);
//exec('git describe --always', $version_mini_hash);
$version_mini_hash = substr(file_get_contents(dirname(dirname(__DIR__)) . '/.git/refs/heads/master'), 0, 8);
if ($version_mini_hash == '') {
	$version_mini_hash = 'dev';
}

define('GIT_HASH', $version_mini_hash);

function getNetworkDisplayNameFromSlug($sluggy) {
	if ($sluggy == "esper") {
		return "EsperNet";
	} else if ($sluggy == "solas") {
		return "Solas";
	} else if ($sluggy == "rizon") {
		return "Rizon";
	}
}

function getKeyForChannel($channel) {
	if (stringMatchWithWildcard($channel, "esper/drtshock*")) {
		return "kittygetspets";
	}

	if (stringMatchWithWildcard($channel, "esper/zarthus*")) {
		return "nf46j8p6^mkhsv6";
	}

	if (stringMatchWithWildcard($channel, "solas/kashike*")) {
		return "adorablekitties";
	}

	if (stringMatchWithWildcard($channel, "rizon/comkid'*")) {
		return "kittens_in_mittens";
	}

	return "NO_KEY";
}

function shouldSkipNetworkRender($network) {
	$some_array = glob(SBNC_ROOT_DIR . "logs/$network/*", GLOB_ONLYDIR);

	if ($network == "rizon") {
		if (count($some_array) == 1 && $some_array[0] == "/data1/apps/ircd/sbnc/logs/rizon/comkid'") {
			return true;
		}
	}

	if ($network == "drtshock" || $network == "zarthus") {
		return true;
	}

	return false;
}

function shouldSkipChannelRender($network) {
	if ($network ==  "solas/kashike") {
		return true;
	}

	return false;
}

function stringMatchWithWildcard($source, $pattern) {
	$pattern = preg_quote($pattern, '/');
	$pattern = str_replace( '\*' , '.*', $pattern);
	return preg_match('/^' . $pattern . '$/i' , $source);
}

function getLast30Lines($file_path, $tail_length) {
	$result = [];
	$file = file($file_path);
	$count = count($file);
	for ($i = $count - $tail_length; $i < $count; $i++) {
		$result[] .= $file[$i];
	}

	return $result;
}

/**
 * @param $line string Line to parse.
 *
 * @return string HTML parsed line
 */
function lineParse($line) {
    $map = getCharacterMap();
    $enabledMap = array();

    // ---
    $visibility = '';
    if (preg_match("/\[\d\d:\d\d:\d\d\] .+\* .+ \(.+\) has joined.+/", $line) ||
		preg_match("/\[\d\d:\d\d:\d\d\] .+\* .+ \(.+\) has quit.+/", $line) ||
		preg_match("/\[\d\d:\d\d:\d\d\] .+\* .+ \(.+\) has left.+/", $line) ||
		preg_match("/\[\d\d:\d\d:\d\d\] .+\* .+ is now known as .+/", $line) ||
		preg_match("/\[\d\d:\d\d:\d\d\] .+\* .* sets mode.*/", $line)) {
			$visibility = ' class="can-hide" ';
	}
    // ---
 
    foreach ($map as $key => $value) {
        $enabledMap[$key] = 0;
    }
 
    $line_escape = htmlentities($line, ENT_QUOTES);
    $line_parsed = '';
    $len = strlen($line_escape);
 
    for ($charPos = 0; $charPos < $len; $charPos++) {
        $character = $line_escape[$charPos];
 
        // It's in the map!
        if (in_array($character, $map)) {
 
            if (isBold($character)) {
                $line_parsed .= wrapInElement($map['bold'], $visibility, $enabledMap['bold'] == 1);
                $enabledMap['bold'] = $enabledMap['bold'] == 0 ? 1 : 0;
 
                continue;
            }
 
            if (isColor($character)) {
                // Check characters after the color code.
                if ($len > $charPos + 1 && is_numeric($line_escape[$charPos + 1])) {
                    if ($len > $charPos + 2 && is_numeric($line_escape[$charPos + 2])) {
                        $line_parsed .= wrapInElement($line_escape[$charPos + 1] . $line_escape[$charPos + 2], $visibility);
 
                        $enabledMap['color']++;
                        $charPos += 2;
                        continue;
                    }
 
                    $line_parsed .= wrapInElement($line_escape[$charPos + 1], $visibility);
 
                    $enabledMap['color']++;
                    $charPos++;
                    continue;
                }
 
                // No colors exceeding it. clear all colors.
                if ($enabledMap['color'] > 0) {
                    for ($i = 0; $i < $enabledMap['color']; $i++) {
                        $line_parsed .= wrapInelement($map['color'], $visibility, true);
                    }

                    $enabledMap['color'] = 0;
                    continue;
                }
 
                // We don't know what to do with it.
                continue;
            }
 
            if (isClear($character)) {
                foreach ($enabledMap as $key => $value) {
                    while ($enabledMap[$key] > 0) {
                        $line_parsed .= wrapInElement($map[$key], $visibility, $enabledMap[$key]-- > 0);
                    }
                }
 
                continue;
            }
 
            if (isReverseTv($character)) {
                $line_parsed .= wrapInElement($map['reversetv'], $visibility, $enabledMap['reversetv'] == 1);
                $enabledMap['reversetv'] = $enabledMap['reversetv'] == 0 ? 1 : 0;
 
                continue;
            }
 
            if (isItalic($character)) {
                $line_parsed .= wrapInElement($map['italic'], $visibility, $enabledMap['italic'] == 1);
                $enabledMap['italic'] = $enabledMap['italic'] == 0 ? 1 : 0;
 
                continue;
            }
 
            if (isUnderline($character)) {
                $line_parsed .= wrapInElement($map['underline'], $visibility, $enabledMap['underline'] == 1);
                $enabledMap['underline'] = $enabledMap['underline'] == 0 ? 1 : 0;
 
                continue;
            }
        }
 
        $line_parsed .= $character;
    }
 
    foreach ($enabledMap as $key => $value) {
        while ($enabledMap[$key] > 0) {
            $line_parsed .= wrapInElement($map[$key], $visibility, $enabledMap[$key]-- > 0);
        }
    }

    if (true) { // $visibility == ' class="can-hide" '
    	preg_match("/\[\d\d:\d\d:\d\d\] /", $line_parsed, $time_matches);
    	$line_parsed = str_replace($time_matches[0], '<span' . $visibility . '>' . $time_matches[0] . '</span>', $line_parsed);
    }

    return $line_parsed . '<br' . $visibility . '/>';
}
 
/**
 * @param $array_item string|null character code of item to return
 *
 * @return array|char|null Character map of all markup codes, or char if $array_item is set and found, null if not found.
 */
function getCharacterMap($array_item = null) {
    $map = array
    (
        'bold' => chr(2),
        'color' => chr(3),
        'clear' => chr(15),
        'reversetv' => chr(22),
        'italic' => chr(29),
        'underline' => chr(31),
    );
 
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
 
/**
 * @param $color_code int Colour code
 * @param $close bool Close the tag or not.
 *
 * @return string HTML styled colour code.
 */
function wrapInElement($color_code, $visibility, $close = false) {
	if (isBold($color_code)) {
		return !$close ? '<strong>' : '</strong>';
	}
 
	if (isReverseTv($color_code)) {
        // NOTE: getColorCode() always returns black text. We force white here.
		return !$close ? '<span' . $visibility . ' style="color: white; background-color: ' . getColorCode(1) . ';">' : '</span>';
	}
 
	if (isItalic($color_code)) {
		return !$close ? '<i>' : '</i>';
	}
 
	if (isUnderline($color_code)) {
		return !$close ? '<u>' : '</u>';
	}
 
	return !$close ? '<span' . $visibility . ' style="color: ' . getColorCode($color_code) . ';">' : '</span>';
}
 
/* the following functions all return booleans; they are helper functions. */
function isBold($color_code) {
	return $color_code == getCharacterMap('bold');
}
 
function isColor($color_code) {
    return $color_code == getCharacterMap('color');
}
 
function isClear($color_code) {
	return $color_code == getCharacterMap('clear');
}
 
function isReverseTv($color_code) {
	return $color_code == getCharacterMap('reversetv');
}
 
function isItalic($color_code) {
	return $color_code == getCharacterMap('italic');
}
 
function isUnderline($color_code) {
	return $color_code == getCharacterMap('underline');
}
 
/**
 * owner = 07
 * admin = 06
 * op = 03
 * hop = 10
 * voice = 02
 * normal = 05
 */
function getColorCode($input) {
    if (is_int($input)) {
        if ($input >= 10) {
            $input = strval($input);
        } else {
            $input = '0' . strval($input);
        }
    }
 
	switch($input) {
		case "00":
		case "01":
			return "#000000";
		case "02":
			return "#000080";
		case "03":
			return "#8000FF";
		case "04":
			return "#FF0000";
		case "05":
			return "#A52A2A"; // 804040 // 980ee8
		case "06":
			return "#8000FF";
		case "07":
			return "#808000";
		case "08":
			return "#FFFF00";
		case "09":
			return "#00FF00";
		case "10":
			return "#008080";
		case "11":
			return "#00FFFF";
		case "12":
			return "#0000FF";
		case "13":
			return "#FF00FF";
		case "14":
			return "#808080";
		case "15":
			return "#C0C0C0";
		case "16":
			return "#FFFFFF";
		default:
			return $input;
	}
}

function getColor($part, $start) {
	if (strlen($part) > $start - 1 && is_numeric($part[$start])) {
		if (strlen($part) >= $start && is_numeric($part[$start + 1])) {
			return 2;
		}

		return 1;
	}

	return 0;
}
