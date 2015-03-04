<?php


namespace Korobi\WebBundle\Deployment;


class TestOutputParser {

    public static function parseLine($line) {
        $data = ["passed" => 0, "failed" => 0, "assertions" => 0];
        $regex = '/(?J)(?|Tests: (?P<tests>[0-9]+), Assertions: (?P<assertions>[0-9]+), Failures: (?P<failures>[0-9]+)(?:, Incomplete: (?P<incomplete>[0-9]+))?)|(?|OK \((?P<tests>[0-9]+) tests, (?P<assertions>[0-9]+) assertions\))|(?|Tests: (?P<tests>[0-9]+), Assertions: (?P<assertions>[0-9]+), Incomplete: (?P<incomplete>[0-9]+)\.)/';
        $matches = [];
        preg_match($regex, $line, $matches);

        if (count($matches) === 4) {

        } else {

        }
        $data = array_merge($data, $matches);
        return $data;
    }

}
