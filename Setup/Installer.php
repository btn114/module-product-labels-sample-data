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

namespace Mageplaza\ProductLabelsSampleData\Setup;

use Exception;
use Magento\Framework\Setup;
use Mageplaza\ProductLabelsSampleData\Model\ProductLabels;

/**
 * Class Installer
 * @package Mageplaza\ProductLabelsSampleData\Setup
 */
class Installer implements Setup\SampleData\InstallerInterface
{
    /**
     * @var ProductLabels
     */
    private $abandonedCart;

    /**
     * Installer constructor.
     *
     * @param ProductLabels $abandonedCart
     */
    public function __construct(
        ProductLabels $abandonedCart
    ) {
        $this->abandonedCart = $abandonedCart;
    }

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function install()
    {
        $this->abandonedCart->install(['Mageplaza_ProductLabelsSampleData::fixtures/mageplaza_productlabels_rule.csv']);
    }
}
