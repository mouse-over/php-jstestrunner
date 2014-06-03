<?php
/**
 * This file is part of IntelliJ IDEA
 *
 * @author Vaclav Prokes (vprokes@mouse-over.net)
 */


namespace MouseOver\JsTester;

use Nette\Latte\Engine;
use Nette\Templating\FileTemplate;
use Nette\Utils\FileSystem;
use Nette\Utils\Finder;


/**
 * Class TestRunner
 * @package JsTester
 */
class TestRunner
{

    /** @var string */
    private $outputDir;
    /** @var  string */
    private $templateFile;
    /** @var  string */
    private $outputFileName;
    /** @var  string */
    private $vendorPath;
    /** @var  string[] */
    private $testFiles = array();

    public function __construct($outputDir = NULL)
    {
        if ($outputDir) {
            $this->setOutputDir($outputDir);
        }
    }

    /**
     * @param array $parameters
     * @return string Test runner html path
     */
    public function generate($parameters = array())
    {
        $templateFile = $this->getTemplateFile();
        $testRunnerFile = $this->getOutputDir() . '/' . $this->getOutputFileName();

        //- prepare template
        $template = new FileTemplate($templateFile);
        $template->registerFilter(new Engine);
        $parameters = array_merge($parameters, $this->getDefaultParameters());
        $template->setParameters($parameters);

        //- write test runner html
        FileSystem::write($testRunnerFile, (string)$template);

        return $testRunnerFile;
    }

    /**
     * @return string
     */
    public function getTemplateFile()
    {
        return $this->templateFile ? : __DIR__ . 'templates/TestRunner.latte';
    }

    /**
     * @param string $templateFile
     */
    public function setTemplateFile($templateFile)
    {
        $this->templateFile = $templateFile;
    }

    /**
     * @return string
     */
    public function getOutputDir()
    {
        return $this->outputDir;
    }

    /**
     * @param string $tempDir
     */
    public function setOutputDir($tempDir)
    {
        $this->outputDir = $tempDir;
    }

    /**
     * @return string
     */
    public function getOutputFileName()
    {
        return $this->outputFileName ? : 'TestRunner.html';
    }

    /**
     * @param string $outputFile
     */
    public function setOutputFileName($outputFile)
    {
        $this->outputFileName = $outputFile;
    }

    protected function getDefaultParameters()
    {
        return array(
            'vendorPath' => $this->getVendorPath(),
            'testFiles' => $this->getTestFiles(),
            'outputDir' => $this->getOutputDir(),
        );
    }

    /**
     * @return string
     */
    public function getVendorPath()
    {
        return $this->vendorPath ? : realpath(__DIR__."/../../../vendor/");
    }

    /**
     * @param string $vendorPath
     */
    public function setVendorPath($vendorPath)
    {
        $this->vendorPath = $vendorPath;
    }

    public function getTestFiles()
    {
        return $this->testFiles;
    }

    /**
     * @param string[]|\SplFileInfo[]|Finder $files
     */
    public function addTestFiles($files)
    {
        foreach ($files as $file) {
            $this->addTestFile($file);
        }
    }

    /**
     * @param string|\SplFileInfo $file
     */
    public function addTestFile($file)
    {
        $this->testFiles[] = $file instanceof \SplFileInfo ? $file->getRealPath() : $file;
    }

    public function clearOutputDir()
    {
        $dir = $this->getOutputDir();
        if (is_dir($dir)) {
            FileSystem::delete($dir);
        }
    }

    public function checkOutputDir()
    {
        $dir = $this->getOutputDir();
        if (!is_dir($dir)) {
            FileSystem::createDir($dir);
        }
    }

}