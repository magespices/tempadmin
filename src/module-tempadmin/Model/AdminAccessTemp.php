<?php
/**
 * Copyright © Q-Solutions Studio: eCommerce Nanobots. All rights reserved.
 *
 * @category    Nanobots
 * @package     Nanobots_TempAdmin
 * @author      Sebastian Strojwas <sebastian@qsolutionsstudio.com>
 */

namespace Nanobots\TempAdmin\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class AdminAccessTemp
 * @package Nanobots\TempAdmin\Model
 */
class AdminAccessTemp extends AbstractModel
{
    public function _construct(): void
    {
        $this->_init(ResourceModel\AdminAccessTemp::class);
    }
}
