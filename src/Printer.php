<?php

namespace hrustbb2\Migrations;

use Nette\PhpGenerator\Printer as BasePrinter;

class Printer extends BasePrinter {
    public $indentation = "    ";
    public $linesBetweenProperties = 1;
    public $linesBetweenMethods = 1;
}