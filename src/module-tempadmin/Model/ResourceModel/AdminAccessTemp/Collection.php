<?php
/**
 * Created by Q-Solutions Studio
 *
 * @category    Magespices
 * @package     Magespices_TempAdmin
 * @author      Sebastian Strojwas <sebastian@qsolutionsstudio.com>
 */

namespace Magespices\TempAdmin\Model\ResourceModel\AdminAccessTemp;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magespices\TempAdmin\Model\AdminAccessTemp;

/**
 * Class Collection
 * @package Magespices\TempAdmin\Model\ResourceModel\AdminAccessTemp
 */
class Collection extends AbstractCollection
{
    public function _construct(): void
    {
        $this->_init(
            AdminAccessTemp::class,
            \Magespices\TempAdmin\Model\ResourceModel\AdminAccessTemp::class
        );
    }
}
