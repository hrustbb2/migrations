<?php

namespace hrustbb2\Migrations;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use hrustbb2\Migrations\Interfaces\IMigrationCreator;

class MigrationCreator implements IMigrationCreator {

    protected string $migrationPath = '';

    public function setMigrationPath(string $path): void
    {
        $this->migrationPath = $path;
    }

    public function create(string $name): void
    {
        $className = ucfirst($name) . '_' . time();
        $class = ClassType::class($className);
        $namespace = new PhpNamespace('');
        $namespace->addUse('hrustbb2\Migrations\AbstractMigrate');
        $namespace->addUse('Phinx\Db\Table');
        $class->addExtend('AbstractMigrate');

        $methodUp = $class->addMethod('up');
        $methodUp->setPublic();
        $methodUp->addParameter('settings')->setType('array');
        $methodUp->setReturnType('void');

        $methodDown = $class->addMethod('down');
        $methodDown->setPublic();
        $methodDown->addParameter('settings')->setType('array');
        $methodDown->setReturnType('void');

        $namespace->add($class);

        $printer = new Printer();
        $printer->setTypeResolving(false);
        $str = "<?php\n\n" . $printer->printNamespace($namespace);
        file_put_contents($this->migrationPath . '/' . $className . '.php', $str);
    }

}