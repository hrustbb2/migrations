<?php

namespace hrustbb2\Migrations;

use Phinx\Db\Adapter\AdapterInterface;

abstract class AbstractMigrate {

    protected AdapterInterface $adapter;

    public function setAdatter(AdapterInterface $adapter): void
    {
        $this->adapter = $adapter;
    }

    abstract function up(array $settings): void;

    abstract function down(array $settings): void;

}