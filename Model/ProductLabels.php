<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_ProductLabelsSampleData
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ProductLabelsSampleData\Model;

use Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\File\Csv;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Module\Dir;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Setup\SampleData\Context as SampleDataContext;
use Magento\Framework\Setup\SampleData\FixtureManager;
use Magento\MediaStorage\Model\File\Uploader;
use Mageplaza\ProductLabels\Model\MetaFactory;

/**
 * Class ProductLabels
 * @package Mageplaza\ProductLabelsSampleData\Model
 */
class ProductLabels
{
    /**
     * @var FixtureManager
     */
    private $fixtureManager;

    /**
     * @var Csv
     */
    protected $csvReader;

    /**
     * @var File
     */
    private $file;
    /**
     * @var \Mageplaza\ProductLabels\Model\RuleFactory
     */
    private $ruleFactory;

    protected $idMapFields = [];

    protected $viewDir = '';
    /**
     * @var Reader
     */
    private $moduleReader;
    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    private $ioFile;

    protected $mediaDirectory;
    /**
     * @var \Mageplaza\ProductLabels\Helper\Data
     */
    private $helperData;
    /**
     * @var MetaFactory
     */
    private $metaFactory;
    /**
     * @var \Mageplaza\ProductLabels\Model\Indexer\RuleIndexer
     */
    private $ruleIndexer;

    /**
     * ProductLabels constructor.
     * @param SampleDataContext $sampleDataContext
     * @param File $file
     * @param Reader $moduleReader
     * @param Filesystem\Io\File $ioFile
     * @param Filesystem $filesystem
     * @param \Mageplaza\ProductLabels\Model\RuleFactory $salesPopFactory
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        SampleDataContext $sampleDataContext,
        File $file,
        Reader $moduleReader,
        \Magento\Framework\Filesystem\Io\File $ioFile,
        Filesystem $filesystem,
        \Mageplaza\ProductLabels\Model\RuleFactory $ruleFactory,
        \Mageplaza\ProductLabels\Model\Indexer\RuleIndexer $ruleIndexer,
        \Mageplaza\ProductLabels\Model\MetaFactory $metaFactory,
        \Mageplaza\ProductLabels\Helper\Data $helperData
    ) {
        $this->fixtureManager = $sampleDataContext->getFixtureManager();
        $this->csvReader = $sampleDataContext->getCsvReader();
        $this->file = $file;
        $this->ruleFactory = $ruleFactory;
        $this->moduleReader = $moduleReader;
        $this->ioFile = $ioFile;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->helperData = $helperData;
        $this->metaFactory = $metaFactory;
        $this->ruleIndexer = $ruleIndexer;
    }

    /**
     * @param array $fixtures
     *
     * @throws Exception
     */
    public function install(array $fixtures)
    {
        foreach ($fixtures as $fileName) {
            $fileName = $this->fixtureManager->getFixture($fileName);
            if (!$this->file->isExists($fileName)) {
                continue;
            }

            $rows = $this->csvReader->getData($fileName);

            $header = array_shift($rows);

            foreach ($rows as $row) {
                $data = [];
                foreach ($row as $key => $value) {
                    $data[$header[$key]] = $value;
                }

                $data = $this->prepareData($data);

                $rule = $this->ruleFactory->create()
                    ->addData($data)
                    ->save();
                $this->ruleIndexer->executeRow($rule->getId());
            }
        }
    }

    /**
     * @param array $data
     * @return array
     * @throws Exception
     */
    protected function prepareData($data)
    {
        unset($data['rule_id']);

        if ($data['label_image']) {
            $data['label_image'] = $this->copyImage($data['label_image']);
        }

        return $data;
    }

    /**
     * @param $path
     * @return string
     */
    protected function getFilePath($path)
    {
        if (!$this->viewDir) {
            $this->viewDir = $this->moduleReader->getModuleDir(
                Dir::MODULE_VIEW_DIR,
                'Mageplaza_ProductLabelsSampleData'
            );
        }

        return $this->viewDir . $path;
    }

    /**
     * @param $filePath
     * @return string
     * @throws Exception
     */
    protected function copyImage($filePath)
    {
        if (!$filePath) {
            return '';
        }
        $filePath = ltrim($filePath, '/');
        $pathInfo = $this->ioFile->getPathInfo($filePath);
        $fileName = $pathInfo['basename'];
        $dispersion = $pathInfo['dirname'];
        $file = $this->getFilePath('/files/image/' . $filePath);
        $this->ioFile->checkAndCreateFolder('pub/media/mageplaza/productlabels/product/' . $dispersion);
        $fileName = Uploader::getCorrectFileName($fileName);
        $fileName = Uploader::getNewFileName(
            $this->mediaDirectory->getAbsolutePath('mageplaza/productlabels/product/' . $dispersion . '/' . $fileName)
        );
        $destinationFile = $this->mediaDirectory->getAbsolutePath(
            'mageplaza/productlabels/product/' . $dispersion . '/' . $fileName
        );

        $destinationFilePath = $this->mediaDirectory->getAbsolutePath($destinationFile);
        $this->ioFile->cp($file, $destinationFilePath);

        return $fileName;
    }
}
