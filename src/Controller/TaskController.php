<?php

namespace Ajax\Tasks\Controller;

use Ajax\Tasks\Core\Exception\SecurityException;
use Ajax\Tasks\Core\Request;

class TaskController extends Controller
{
    /**
     * Index action
     * @throws \Twig_Error_Syntax
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Loader
     * @throws \Ajax\Tasks\Core\Exception\DBException
     */
    public function indexAction()
    {
        $request      = $this->container->getRequest();
        $tasksManager = $this->container->getTaskManager();
        $perPage      = $this->container->getTaskPerPage();
        $result       = $tasksManager->getTasks($request);

        echo $this->render(
            'index.html.twig',
            [
                'tasks'       => $result['data'],
                Request::PAGE => $request->getPage(),
                'count'       => $result['count'],
                'perPage'     => $perPage,
            ]
        );
    }

    /**
     * @throws \Ajax\Tasks\Core\Exception\FileException
     * @throws \Ajax\Tasks\Core\Exception\DBException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws \Exception
     */
    public function createAction()
    {
        $request = $this->container->getRequest();

        if ($request->getRequestMethod() === 'POST') {
            $tasksManager = $this->container->getTaskManager();
            $tasksManager->handleCreate($request);
            $this->redirect('/');
        }

        echo $this->render('task/create.html.twig');
    }

    /**
     * @param int $id
     *
     * @throws \Exception
     */
    public function updateAction($id)
    {
        if (!$this->isUserLogged()) {
            throw new SecurityException('Access Denied');
        }
        $request      = $this->container->getRequest();
        $tasksManager = $this->container->getTaskManager();

        if ($request->getRequestMethod() === 'POST') {

            $tasksManager->handleUpdate($id, $request);

            $this->redirect('/task/update/' . $id);
        }
        $result = $tasksManager->getById($id);

        echo $this->render('task/update.html.twig', ['task' => $result]);
    }

    /**
     *
     * @throws \Twig_Error_Syntax
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Loader
     */
    public function preViewAction()
    {
        $task['username']    = isset($_POST['username']) ? $_POST['username'] : '';
        $task['email']       = isset($_POST['email']) ? $_POST['email'] : '';
        $task['description'] = isset($_POST['description']) ? $_POST['description'] : '';
        $task['status']      = 'new';
        if (isset($_FILES['file-0']['size']) && $_FILES['file-0']['size']) {
            $task['pictureContent'] = 'data:image/png;base64,' . base64_encode(file_get_contents($_FILES['file-0']['tmp_name']));
        }
        echo $this->render('task/preView.html.twig', ['task' => $task]);
    }
}
