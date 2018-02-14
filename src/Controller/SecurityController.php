<?php

namespace Ajax\Tasks\Controller;

class SecurityController extends Controller
{
    /**
     * Log in action
     * @throws \Exception
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function loginAction()
    {
        $request = $this->container->getRequest();
        if ($request->getRequestMethod() === 'POST') {
            $loginManager = $this->container->getLoginManager();
            $loginManager->handleLogin($request);
            $this->redirect('/');
        }
        echo $this->render('login.html.twig');
    }

    /**
     * Log out action
     */
    public function logoutAction()
    {
        $loginManager = $this->container->getLoginManager();
        $loginManager->handleLogout();
        $this->redirect('/');
    }
}
