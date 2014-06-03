<?php

namespace MouseOver\JsTester\WebLoader;


/**
 * Class LocalJsLoader
 * Helper class to get js files list from webloader
 */
class JavaScriptLoaderFiles extends \WebLoader\Nette\JavaScriptLoader
{

    /**
     * mimic render
     * return string[]
     */
    public function getFiles()
    {
        $hasArgs = func_num_args() > 0;

        $files = array();
        $compiler = $this->getCompiler();
        if ($hasArgs) {
            $backup = $compiler->getFileCollection();
            $newFiles = new \WebLoader\FileCollection($backup->getRoot());
            $newFiles->addFiles(func_get_args());
            $compiler->setFileCollection($newFiles);
        }

        // remote files
        foreach ($compiler->getFileCollection()->getRemoteFiles() as $file) {
            $files[] = $file;
        }

        foreach ($compiler->generate(FALSE) as $file) {
            $files[] = $this->getGeneratedFilePath($file);
        }

        if ($hasArgs) {
            $compiler->setFileCollection($backup);
        }
        return $files;
    }

    protected function getGeneratedFilePath($file)
    {
        return $this->getTempPath() . '/' . $file->file;
    }
}