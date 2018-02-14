<?php

namespace Ajax\Tasks\Model;

use Ajax\Tasks\Core\Exception\FileException;
use Ajax\Tasks\Core\Request;
use Gumlet\ImageResize;

class FileHandler
{
    /** @var string */
    private $picturesDir;

    /** @var array */
    private $imageDimensions;

    /**
     * @param string $picturesDir
     * @param array  $imageDimensions
     */
    public function __construct($picturesDir, array $imageDimensions)
    {
        $this->picturesDir     = $picturesDir;
        $this->imageDimensions = $imageDimensions;
    }

    /**
     * @param array $tasks
     *
     * @return array
     */
    public function findPictures(array $tasks)
    {
        $data = [];
        foreach ($tasks as $item) {
            if (file_exists($this->picturesDir . DIRECTORY_SEPARATOR . $item['id'])) {
                $item['picture'] = $item['id'];
            }
            $data[] = $item;
        }

        return $data;
    }

    /**
     * @param Request $request
     * @param int     $taskId
     * @param string  $key
     *
     * @throws \Gumlet\ImageResizeException
     */
    public function moveUploadedFile(Request $request, $taskId, $key)
    {
        $file = isset($request->getFiles()[$key]) ? $request->getFiles()[$key] : null;
        if (!$file || !isset($file['tmp_name']) || !$file['tmp_name']) {
            return;
        }
        $image      = new ImageResize($file['tmp_name']);
        $origWidth  = $image->getSourceWidth();
        $origHeight = $image->getSourceHeight();
        if ($origWidth > $this->imageDimensions['width'] || $origHeight > $this->imageDimensions['height']) {
            $image->resizeToBestFit($this->imageDimensions['width'], $this->imageDimensions['height']);
        }
        $fileDest = $this->picturesDir . DIRECTORY_SEPARATOR . $taskId;
        $image->save($fileDest);
    }
}
