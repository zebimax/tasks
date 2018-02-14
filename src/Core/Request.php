<?php

namespace Ajax\Tasks\Core;

class Request
{
    const SORT = 'sort';
    const PAGE = 'page';

    const USERNAME = 'username';
    const PASSWORD = 'password';

    /** @var array */
    private $get;

    /** @var array */
    private $post;

    /** @var array */
    private $server;

    /** @var array */
    private $session;

    /** @var array */
    private $files;

    public function __construct(array $get, array $post, array $server, array $files, array $session)
    {
        $this->get     = $get;
        $this->post    = $post;
        $this->server  = $server;
        $this->files   = $files;
        $this->session = $session;
    }

    public function getRequestMethod()
    {
        return $this->server['REQUEST_METHOD'];
    }

    public function getHttpScheme()
    {
        return isset($this->server['HTTP_SCHEME']) ? $this->server['HTTP_SCHEME'] : 'http';
    }

    public function getHttpHost()
    {
        return $this->server['HTTP_HOST'];
    }

    public function getRequestUri()
    {
        return $this->server['REQUEST_URI'];
    }

    public function getGet($key, $default = null)
    {
        return isset($this->get[$key]) ? $this->get[$key] : $default;
    }

    public function find($key, $default = null)
    {
        $search = array_merge($this->post, $this->get);

        return isset($search[$key]) ? $search[$key] : $default;
    }


    public function getPost($key, $default = null)
    {
        return isset($this->post[$key]) ? $this->post[$key] : $default;
    }

    public function getPage($default = 1)
    {
        return (int)$this->getGet(self::PAGE, $default);
    }

    /**
     * @return array
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * @return array
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @return array
     */
    public function getFiles()
    {
        return $this->files;
    }
}
