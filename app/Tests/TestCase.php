<?php

namespace Korobi\Tests;

use Mockery;

class TestCase extends \Illuminate\Foundation\Testing\TestCase {

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication() {
        $app = require __DIR__ . '/../../bootstrap/app.php';

        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

        return $app;
    }

    public function mock($class) {
        $mock = Mockery::mock($class);

        $this->app->instance($class, $mock);

        return $mock;
    }

    public function tearDown() {
        Mockery::close();
    }

}
