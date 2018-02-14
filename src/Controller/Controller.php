<?php

namespace Ajax\Tasks\Controller;

use Ajax\Tasks\Container;

abstract class Controller
{
    /** @var Container */
    protected $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $name
     * @param array  $context
     *
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function render($name, array $context = [])
    {
        $twig = $this->container->getTwig();

        return $twig->render($name, $context);
    }

    /**
     * @param string $path
     */
    protected function redirect($path)
    {
        $request = $this->container->getRequest();
        $httpScheme = $request->getHttpScheme();
        $host = $request->getHttpHost();
        header(sprintf('Location: %s://%s%s', $httpScheme, $host, $path));
    }

    /**
     * @return bool
     */
    protected function isUserLogged()
    {
        $request = $this->container->getRequest();
        $session = $request->getSession();

        return isset($session['TUID']);
    }

}
