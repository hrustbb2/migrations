<?php

include __DIR__ . '/../vendor/autoload.php';

use hrustbb2\Migrations\JSONManager;

$m = new JSONManager();
$m->setMigrationPath(__DIR__);
$m->setDbHost('host');
$m->setDbName('dbname');
$m->setDbUser('dbuser');
$m->setDbPassword('dbpassword');
$m->init();
$nm = $m->getNewMigrations();
// $m->migrate($nm);
$m->rollback();