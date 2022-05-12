<?php

namespace hrustbb2\Migrations\Interfaces;

interface IMigrationCreator {
    public function setMigrationPath(string $path): void;
    public function create(string $name): void;
}