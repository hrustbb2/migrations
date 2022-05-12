<?php

namespace hrustbb2\Migrations;

use hrustbb2\Migrations\Interfaces\IManager;
use Phinx\Db\Adapter\AdapterFactory;
use Phinx\Db\Adapter\AdapterInterface;

class JSONManager implements IManager {

    protected string $migrationsPath;

    protected string $driver = self::MYSQL_DRIVER;

    protected string $dbHost;

    protected string $dbName;

    protected string $dbPassword;

    protected string $dbUser;

    protected string $dbCharset = 'utf8';

    protected AdapterInterface $adapter;

    protected array $settings = [];

    protected array $migrations = [];

    public function setDriver(string $driver): void
    {
        $this->driver = $driver;
    }

    public function setDbHost(string $host): void
    {
        $this->dbHost = $host;
    }

    public function setDbName(string $dbName): void
    {
        $this->dbName = $dbName;
    }

    public function setDbPassword(string $password): void
    {
        $this->dbPassword = $password;
    }

    public function setDbUser(string $user): void
    {
        $this->dbUser = $user;
    }

    public function setDbCharset(string $charset): void
    {
        $this->dbCharset = $charset;
    }

    public function setMigrationPath(string $path): void
    {
        $this->migrationsPath = $path;
    }

    public function setSettings(array $settings): void
    {
        $this->settings = $settings;
    }

    public function init(): void
    {
        if(file_exists($this->migrationsPath . '/migrations.json')){
            $str = file_get_contents($this->migrationsPath . '/migrations.json');
            $this->migrations = json_decode($str, true);
        }else{
            $str = json_encode([], JSON_PRETTY_PRINT);
            file_put_contents($this->migrationsPath . '/migrations.json', $str);
        }
        $adapterFactory = AdapterFactory::instance();
        $options = [
            'host' => $this->dbHost,
            'name' => $this->dbName,
            'user' => $this->dbUser, 
            'pass' => $this->dbPassword,
        ];
        $this->adapter = $adapterFactory->getAdapter($this->driver, $options);
        $this->adapter->connect();
    }

    protected function getLastTimestamp():int
    {
        if(!$this->migrations){
            return 0;
        }
        $row = $this->migrations[count($this->migrations) - 1];
        return $row['timestamp'];
    }

    public function getNewMigrations():array
    {
        $lastTimestamp = $this->getLastTimestamp();
        $files = scandir($this->migrationsPath);
        $result = [];
        foreach($files as $file){
            $m = [];
            if(is_file($this->migrationsPath . '/' . $file) && preg_match('/^(.*)_(\d*).php$/', $file, $m) === 1){
                $timestamp = $m[2];
                if($timestamp <= $lastTimestamp){
                    continue;
                }
                $class = $m[1] . '_' . $m[2];
                require $this->migrationsPath . '/' . $file;
                $migration = new $class($this->adapter);
                $result[$timestamp] = [
                    'obj' => $migration,
                    'file' => $file,
                ];
            }
        }
        ksort($result);
        return $result;
    }

    public function migrate(array $migrations):void
    {
        foreach($migrations as $timestamp=>$migration){
            $migration['obj']->setAdatter($this->adapter);
            $migration['obj']->up($this->settings);
            $row = [
                'timestamp' => $timestamp,
                'file' => $migration['file'],
            ];
            $this->migrations[] = $row;
            $str = json_encode($this->migrations, JSON_PRETTY_PRINT);
            file_put_contents($this->migrationsPath . '/migrations.json', $str);
        }
    }

    public function rollback():void
    {
        $migration = array_pop($this->migrations);
        $file = $migration['file'];
        if(is_file($this->migrationsPath . '/' . $file) && preg_match('/^(.*)_(\d*).php$/', $file, $m) === 1){
            require $this->migrationsPath . '/' . $file;
            $class = $m[1] . '_' . $m[2];
            $migrationObj = new $class($this->adapter);
            $migrationObj->setAdatter($this->adapter);
            $migrationObj->down($this->settings);
            $str = json_encode($this->migrations, JSON_PRETTY_PRINT);
            file_put_contents($this->migrationsPath . '/migrations.json', $str);
        }else{
            echo $file . ' not found';
        }
    }

}