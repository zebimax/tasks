<?php

namespace Ajax\Tasks\Twig;

use Ajax\Tasks\Core\Exception\BadConfigException;
use Ajax\Tasks\Core\Request;
use Ajax\Tasks\Model\LoginManager;
use Ajax\Tasks\Model\PaginationLinksMaker;
use Ajax\Tasks\Model\TaskTable;

class TaskExtension extends \Twig_Extension
{
    /** @var Request */
    private $request;

    /** @var LoginManager */
    private $loginManager;

    /**
     * @param Request $request
     */
    public function __construct(Request $request, LoginManager $loginManager)
    {
        $this->request      = $request;
        $this->loginManager = $loginManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                'ajax_task_is_status_completed',
                [$this, 'isStatusCompleted']
            ),
            new \Twig_SimpleFunction(
                'ajax_task_get_pagination_links',
                [$this, 'getPaginationLinks']
            ),
            new \Twig_SimpleFunction(
                'ajax_task_render_link',
                [$this, 'renderLink']
            ),
            new \Twig_SimpleFunction(
                'ajax_task_base_url',
                [$this, 'getBaseUrl']
            ),
            new \Twig_SimpleFunction(
                'ajax_task_url',
                [$this, 'generateUrl']
            ),
            new \Twig_SimpleFunction(
                'ajax_task_is_user_logged',
                [$this, 'isUserLogged']
            ),
        ];
    }

    /**
     * @return bool
     */
    public function isUserLogged()
    {
        return $this->loginManager->isLogged($this->request);
    }

    /**
     * @param $status
     *
     * @return bool
     * @throws \Ajax\Tasks\Core\Exception\BadConfigException
     */
    public function isStatusCompleted($status)
    {
        if ($status === TaskTable::STATUS_NEW) {
            return false;
        }
        if ($status === TaskTable::STATUS_COMPLETED) {
            return true;
        }
        throw new BadConfigException('Unknown Status');
    }

    /**
     * @param int $count
     * @param int $current
     * @param int $perPage
     *
     * @return array
     */
    public function getPaginationLinks($count, $current, $perPage)
    {
        $paginatorLinks = new PaginationLinksMaker();

        return $paginatorLinks($count, $current, $perPage);
    }

    /**
     * @param array $linkData
     *
     * @return string
     */
    public function renderLink(array $linkData)
    {
        $uri      = $this->getFullUri();
        $page     = $linkData['link_id'] ? '?page=' . $linkData['link_id'] : '';
        $show     = $linkData['link_show'];
        $active   = $linkData['active'] ? ' active' : '';
        $disabled = $linkData['disabled'] ? ' disabled' : '';

        return sprintf(
            '<a href="%s" class="%s%s">%s</a>',
            $uri . $page,
            $active,
            $disabled,
            $show
        );
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return sprintf('%s://%s', $this->request->getHttpScheme(), $this->request->getHttpHost());
    }

    /**
     * @param mixed $param
     * @param bool  $removeIfEqual
     *
     * @return string
     */
    public function generateUrl($param, $removeIfEqual = false)
    {
        $scheme = $this->request->getHttpScheme();
        $host   = $this->request->getHttpHost();
        $uri = $this->request->getRequestUri();
        $start = strpos($uri, '?');
        $query = substr(
            $uri,
            $start ? $start + 1 : 0,
            strlen($uri)
        );
        $query = ltrim($query, '/');
        parse_str($query, $queryParams);

        if ($removeIfEqual && isset($queryParams[$param['key']]) && $queryParams[$param['key']] === $param['value']) {
            unset($queryParams[$param['key']]);
        } else {
            $queryParams[$param['key']] = $param['value'];
        }

        $url = sprintf('%s://%s/', $scheme, $host);
        if ($queryParams) {
            $url .= '?' . http_build_query($queryParams);
        }

        return $url;
    }

    /**
     * @return string
     */
    private function getFullUri()
    {
        $requestUri = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '?'));

        return sprintf('http://%s%s', $_SERVER['HTTP_HOST'], $requestUri);
    }
}
