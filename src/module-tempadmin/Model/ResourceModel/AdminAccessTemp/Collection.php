<?php
/**
 * Copyright © Q-Solutions Studio: eCommerce Nanobots. All rights reserved.
 *
 * @category    Nanobots
 * @package     Nanobots_TempAdmin
 * @author      Sebastian Strojwas <sebastian@qsolutionsstudio.com>
 */

namespace Nanobots\TempAdmin\Model\ResourceModel\AdminAccessTemp;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Nanobots\TempAdmin\Model\AdminAccessTemp;

/**
 * Class Collection
 * @package Nanobots\TempAdmin\Model\ResourceModel\AdminAccessTemp
 */
class Collection extends AbstractCollection
{
    public function _construct(): void
    {
        $this->_init(
            AdminAccessTemp::class,
            \Nanobots\TempAdmin\Model\ResourceModel\AdminAccessTemp::class
        );
    }
}
