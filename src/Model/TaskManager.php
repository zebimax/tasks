<?php

namespace Ajax\Tasks\Model;

use Ajax\Tasks\Core\Request;

class TaskManager
{
    /** @var TaskTable */
    private $taskTable;

    /** @var FileHandler */
    private $fileHandler;

    /** @var int */
    private $perPage;

    /**
     * @param TaskTable   $taskTable
     * @param FileHandler $fileHandler
     * @param             $perPage
     */
    public function __construct(TaskTable $taskTable, FileHandler $fileHandler, $perPage)
    {
        $this->taskTable = $taskTable;
        $this->fileHandler = $fileHandler;
        $this->perPage = $perPage;
    }

    /**
     * @param Request $request
     *
     * @return array
     * @throws \Ajax\Tasks\Core\Exception\DBException
     */
    public function getTasks(Request $request)
    {
        $sort = $request->getGet(Request::SORT);
        $page = $request->getPage();
        $data = $this->taskTable->getData($page, $this->perPage, $sort);
        $data['data'] = $this->fileHandler->findPictures($data['data']);

        return $data;
    }

    /**
     * @param Request $request
     *
     * @throws \Ajax\Tasks\Core\Exception\FileException
     * @throws \Ajax\Tasks\Core\Exception\DBException
     * @throws \Exception
     */
    public function handleCreate(Request $request)
    {
        $task['username'] =  $request->getPost('username', '');
        $task['email'] = $request->getPost('email', '');
        $task['description'] = $request->getPost('description', '');
        $task['status'] = 'new';

        $id = $this->taskTable->insert($task);

        if ($id) {
            $this->fileHandler->moveUploadedFile($request, $id, 'picture');
        }
    }

    /**
     * @param int     $id
     * @param Request $request
     *
     * @throws \Ajax\Tasks\Core\Exception\DBException
     */
    public function handleUpdate($id, Request $request)
    {
        $data['description'] = $request->getPost('description', '');
        $data['status'] = $request->getPost('status', TaskTable::STATUS_NEW);
        $this->taskTable->update($id, $data);
    }

    /**
     * @param $id
     *
     * @return array|false
     * @throws \Ajax\Tasks\Core\Exception\DBException
     */
    public function getById($id)
    {
        return $this->taskTable->getById($id);
    }
}
