<?php


namespace Korobi\WebBundle\Deployment;


class TestOutputParser {

    public static function parseLine($line) {
        $data = ["incomplete" => 0, "passed" => 0, "failures" => 0, "assertions" => 0, "status" => "Pass"];
        $regex = '/(?J)(?|Tests: (?P<tests>[0-9]+), Assertions: (?P<assertions>[0-9]+), Failures: (?P<failures>[0-9]+)(?:, Incomplete: (?P<incomplete>[0-9]+))?)|(?|OK \((?P<tests>[0-9]+) tests, (?P<assertions>[0-9]+) assertions\))|(?|Tests: (?P<tests>[0-9]+), Assertions: (?P<assertions>[0-9]+), Incomplete: (?P<incomplete>[0-9]+)\.)/';
        $matches = [];
        preg_match($regex, $line, $matches);


        $data = array_merge($data, $matches);
        foreach ($data as $key => $arr) {
            if (is_int($key)) {
                unset($data[$key]);
            }
        }
        if ($data['incomplete'] > 0) {
            $data['status'] = "Tentative pass";
        }

        if ($data['failures'] > 0) {
            $data['status'] = "Fail";
        }

        return $data;
    }

}
