<?php

namespace Ajax\Tasks\Model;

use Ajax\Tasks\Core\Request;

class LoginManager
{
    const SESSION_ID = 'TUID';
    /** @var string */
    private $adminUsername;

    /** @var string */
    private $adminPassword;

    /**
     * @param string $adminUsername
     * @param string $adminPassword
     */
    public function __construct($adminUsername, $adminPassword)
    {
        $this->adminUsername = $adminUsername;
        $this->adminPassword = $adminPassword;
    }

    /**
     * @param Request $request
     *
     * @throws \Exception
     */
    public function handleLogin(Request $request)
    {
        $username = $request->getPost(Request::USERNAME);
        $password = $request->getPost(Request::PASSWORD);
        if ($this->canLogin($username, $password)) {
            $_SESSION[self::SESSION_ID] = bin2hex(random_bytes(16));
        }
    }

    /**
     * @param Request $request
     */
    public function handleLogout(Request $request = null)
    {
        session_destroy();
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function isLogged(Request $request)
    {
        return isset($request->getSession()[self::SESSION_ID]);
    }

    /**
     * @param string $username
     * @param string $password
     *
     * @return bool
     */
    protected function canLogin($username, $password)
    {
        return $this->adminUsername === $username && $password === $this->adminPassword;
    }
}
