<?php

namespace hrustbb2\Migrations\Interfaces;

interface IManager {
    const MYSQL_DRIVER = 'mysql';
    const PGSQL_DRIVER = 'pgsql';
    const SQLITE_DRIVER = 'sqlite';
    const SQLSRV_DRIVER = 'sqlsrv';
    public function setDriver(string $driver): void;
    public function setDbHost(string $host): void;
    public function setDbName(string $dbName): void;
    public function setDbPassword(string $password): void;
    public function setDbUser(string $user): void;
    public function setDbCharset(string $charset): void;
    public function setMigrationPath(string $path): void;
    public function setSettings(array $settings): void;
    public function init(): void;
    public function getNewMigrations():array;
    public function migrate(array $migrations):void;
    public function rollback():void;
}