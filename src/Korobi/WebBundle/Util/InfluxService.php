<?php

namespace Korobi\WebBundle\Util;


use crodas\InfluxPHP\Client;

class InfluxService {

    /**
     * @var Client
     */
    private $client;
    private $host;
    private $port;
    private $user;
    private $password;
    private $database;

    /**
     * @param $host
     * @param $port
     * @param $user
     * @param $password
     * @param $database
     */
    public function __construct($host, $port, $user, $password, $database) {
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->password = $password;
        $this->database = $database;
        $this->connect();
    }

    protected function connect() {
        $this->client = new Client($this->host, $this->port, $this->user, $this->password);
    }

    public function getDatabase() {
        return $this->client->getDatabase($this->database);
    }

}