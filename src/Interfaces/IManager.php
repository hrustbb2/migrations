<?php

namespace hrustbb2\Migrations\Interfaces;

interface IManager {
    public function setDriver(string $driver): void;
    public function setDbHost(string $host): void;
    public function setDbName(string $dbName): void;
    public function setDbPassword(string $password): void;
    public function setDbUser(string $user): void;
    public function setDbCharset(string $charset): void;
    public function setMigrationPath(string $path): void;
    public function setSettings(array $settings): void;
    public function init(): void;
}