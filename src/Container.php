<?php

namespace Ajax\Tasks;

use Ajax\Tasks\Core\DB;
use Ajax\Tasks\Core\Exception\CircularReferenceException;
use Ajax\Tasks\Core\Request;
use Ajax\Tasks\Model\FileHandler;
use Ajax\Tasks\Model\LoginManager;
use Ajax\Tasks\Model\TaskManager;
use Ajax\Tasks\Model\TaskTable;
use Ajax\Tasks\Twig\A;
use Ajax\Tasks\Twig\B;
use Ajax\Tasks\Twig\C;
use Ajax\Tasks\Twig\TaskExtension;

class Container
{
    /** @var Configs */
    private $configs;

    /** @var array */
    private $services = [];

    /** @var array */
    private $loading = [];

    /**
     * @param Configs $configs
     * @param Request $request
     */
    public function __construct(Configs $configs, Request $request)
    {
        $this->configs                  = $configs;
        $this->services[Request::class] = $request;
    }

    /**
     * @return \Twig_Environment
     */
    public function getTwig()
    {
        if (!isset($this->services[\Twig_Environment::class])) {
            $loader = new \Twig_Loader_Filesystem($this->configs->getViewsDir());
            $twig   = new \Twig_Environment($loader);
            $loginManager = $this->getLoginManager();
            $request = $this->getRequest();
            $twig->addExtension(new TaskExtension($request, $loginManager));
            $this->services[\Twig_Environment::class] = $twig;
        }

        return $this->services[\Twig_Environment::class];
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->services[Request::class];
    }

    /**
     * @return LoginManager
     */
    public function getLoginManager()
    {
        if (!isset($this->services[LoginManager::class])) {
            $admUsername                         = $this->getRequest()->getPost('username');
            $admPassword                         = $this->getRequest()->getPost('password');
            $this->services[LoginManager::class] = new LoginManager($admUsername, $admPassword);
        }

        return $this->services[LoginManager::class];
    }

    /**
     * @return TaskManager
     */
    public function getTaskManager()
    {
        if (!isset($this->services[TaskManager::class])) {
            $dbConfig      = $this->configs->getDbConf();
            $taskColumns   = $this->configs->getTaskColumns();
            $taskTableName = $this->configs->getTaskTableName();

            $db                                 = DB::getDb($dbConfig);
            $taskTable                          = new TaskTable($db, $taskTableName, $taskColumns);
            $fileHandler = new FileHandler($this->configs->getPicturesDir(), $this->configs->getImageDimensions());
            $perPage                       = $this->configs->getTasksPerPage();
            $this->services[TaskManager::class] = new TaskManager($taskTable, $fileHandler, $perPage);
        }

        return $this->services[TaskManager::class];
    }

    /**
     * @return int
     */
    public function getTaskPerPage()
    {
        return $this->configs->getTasksPerPage();
    }
}
