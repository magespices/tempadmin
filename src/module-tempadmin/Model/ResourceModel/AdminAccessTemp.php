<?php
/**
 * Copyright © Q-Solutions Studio: eCommerce Nanobots. All rights reserved.
 *
 * @category    Nanobots
 * @package     Nanobots_TempAdmin
 * @author      Sebastian Strojwas <sebastian@qsolutionsstudio.com>
 */

namespace Nanobots\TempAdmin\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class AdminAccessTemp
 * @package Nanobots\TempAdmin\Model\ResourceModel
 */
class AdminAccessTemp extends AbstractDb
{
    public function _construct(): void
    {
        $this->_init('admin_access_temp', 'id');
    }
}

