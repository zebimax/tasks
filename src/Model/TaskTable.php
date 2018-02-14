<?php

namespace Ajax\Tasks\Model;

use Ajax\Tasks\Core\DBTable;
use Ajax\Tasks\Core\DB;

class TaskTable extends DBTable
{
    const STATUS_NEW       = 'new';
    const STATUS_COMPLETED = 'completed';

    /** @var string */
    protected $tableName;

    /** @var array */
    protected $columns;

    /**
     * @param DB     $db
     * @param string $tableName
     * @param array  $columns
     */
    public function __construct(DB $db, $tableName, array $columns)
    {
        parent::__construct($db);
        $this->tableName = $tableName;
        $this->columns   = $columns;
    }

    /**
     * @return array
     */
    protected function getColumns()
    {
        return $this->columns;
    }

    /**
     * @return string
     */
    protected function getTableName()
    {
        return $this->tableName;
    }
}
