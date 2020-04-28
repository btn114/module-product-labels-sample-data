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

namespace Mageplaza\ProductLabelsSampleData\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use Magento\Framework\Setup\SampleData\Executor;
use Mageplaza\ProductLabelsSampleData\Setup\Installer;

/**
 * Class InstallProductLabelsSampleData
 * @package Mageplaza\ProductLabelsSampleData\Setup\Patch\Data
 */
class InstallProductLabelsSampleData implements DataPatchInterface, PatchVersionInterface
{
    /**
     * @var Executor
     */
    protected $executor;

    /**
     * @var Installer
     */
    protected $installer;

    /**
     * InstallProductLabelsSampleData constructor.
     *
     * @param Executor $executor
     * @param Installer $installer
     */
    public function __construct(
        Executor $executor,
        Installer $installer
    ) {
        $this->executor  = $executor;
        $this->installer = $installer;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->executor->exec($this->installer);
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getVersion()
    {
        return '1.0.0';
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
