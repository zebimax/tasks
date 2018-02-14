<?php

namespace Ajax\Tasks\Core;

class DB
{
    /** @var self */
    private static $db;
    /** @var \PDO */
    private $connection;

    /**
     * @param $host
     * @param $port
     * @param $user
     * @param $password
     * @param $dbname
     */
    private function __construct($host, $port, $user, $password, $dbname)
    {
        $this->connection = new \PDO(
            "mysql:host={$host};port={$port};dbname={$dbname}",
            $user,
            $password,
            [\PDO::ATTR_PERSISTENT => false]
        );
    }

    /**
     * @param array $configs
     *
     * @return DB
     */
    public static function getDb(array $configs)
    {
        if (!self::$db) {
            self::$db = new self(
                isset($configs['host']) ? $configs['host'] : '',
                isset($configs['port']) ? $configs['port'] : '',
                isset($configs['user']) ? $configs['user'] : '',
                isset($configs['password']) ? $configs['password'] : '',
                isset($configs['db_name']) ? $configs['db_name'] : ''
            );
        }

        return self::$db;
    }

    /**
     * @return \PDO
     */
    public function getConnection()
    {
        return $this->connection;
    }
}
