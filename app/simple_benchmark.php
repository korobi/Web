<?php

use Korobi\WebBundle\Parser\IRCTextParser;

require 'autoload.php';

echo "Benchmarker, please wait...\n";

$testString = "abc\x02d\x031,2efg\x02hijk\x02lm\x03nopq\x031rst\x03,3uvwxyz";
$runningTotal = 0;
for ($i = 1; $i <= 1000; $i++) {
    $start = microtime(true);
    for ($i = 1; $i <= 5000; $i++) {
        IRCTextParser::parse($testString);
    }
    $end = microtime(true);
    $runningTotal += $end - $start;
}

echo "We parsed 5000 identical chat lines with complex formatting and repeated the entire process 1000 times.\n";
echo "The average time for the 5000 lines to be parsed was:\n";
echo 1000 * (($runningTotal) / 100.0) . " ms\n";
