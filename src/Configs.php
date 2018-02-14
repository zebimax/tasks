<?php

namespace Ajax\Tasks;

class Configs
{
    const ROWS_PER_PAGE = 3;

    /** @var array */
    private $dbConf = [
        'host'     => 'localhost',
        'user'     => 'root',
        'password' => null,
        'db_name'  => 'ax_tasks'
    ];

    /** @var array */
    private $taskColumns = [
        'username',
        'email',
        'description',
        'status'
    ];

    /** @var array */
    private $adminCredentials = [
        'username' => 'admin',
        'password' => '123',
    ];

    /** @var array */
    private $imageDimensions = [
        'width'  => 320,
        'height' => 240,
    ];

    /** @var string */
    private $webDir;

    /** @var string */
    private $taskTableName = 'task';

    /**
     * @param string $webDir
     */
    public function __construct($webDir)
    {
        $this->webDir = $webDir;
    }

    /**
     * @return array
     */
    public function getDbConf()
    {
        return $this->dbConf;
    }

    /**
     * @return array
     */
    public function getTaskColumns()
    {
        return $this->taskColumns;
    }

    /**
     * @return string
     */
    public function getSrcDir()
    {
        return $this->webDir . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src';
    }

    /**
     * @return string
     */
    public function getViewsDir()
    {
        return $this->getSrcDir() . DIRECTORY_SEPARATOR . 'views';
    }

    /**
     * @return array
     */
    public function getAdminCredentials()
    {
        return $this->adminCredentials;
    }

    /**
     * @return string
     */
    public function getTaskTableName()
    {
        return $this->taskTableName;
    }

    /**
     * @return int
     */
    public function getTasksPerPage()
    {
        return self::ROWS_PER_PAGE;
    }

    /**
     * @return string
     */
    public function getPicturesDir()
    {
        return $this->webDir . DIRECTORY_SEPARATOR . 'pictures';
    }

    /**
     * @return array
     */
    public function getImageDimensions()
    {
        return $this->imageDimensions;
    }
}
