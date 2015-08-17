<?php

use Korobi\WebBundle\Console\MaintenanceCommand;

class MaintenanceFileTests extends \PHPUnit_Framework_TestCase {

    /**
     * @const string - Ensure we do not enable maintenance mode during tests on accident by appending
     *        to the file string.
     */
    const MAINTENANCE_FILE = MaintenanceCommand::MAINTENANCE_FILE . '_tests';

    public function testMaintenanceModeFilePermissions() {
        $this->assertFileDoesNotExist();
        $this->assertFileCanBeCreated();
        $this->assertFileReadable();
        $this->assertFileUnlinkable();
        $this->assertFileDoesNotExist();
    }

    public function assertFileCanBeCreated() {
        $this->assertTrue(touch(self::MAINTENANCE_FILE));
    }

    public function assertFileReadable() {
        $this->assertTrue(is_readable(self::MAINTENANCE_FILE));
    }

    public function assertFileUnlinkable() {
        $this->assertTrue(unlink(self::MAINTENANCE_FILE));
    }

    public function assertFileDoesNotExist() {
        $this->assertFalse(file_exists(self::MAINTENANCE_FILE));
    }
}
