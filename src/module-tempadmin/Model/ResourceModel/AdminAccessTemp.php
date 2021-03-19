<?php
/**
 * Created by Q-Solutions Studio
 *
 * @category    Magespices
 * @package     Magespices_TempAdmin
 * @author      Sebastian Strojwas <sebastian@qsolutionsstudio.com>
 */

namespace Magespices\TempAdmin\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class AdminAccessTemp
 * @package Magespices\TempAdmin\Model\ResourceModel
 */
class AdminAccessTemp extends AbstractDb
{
    public function _construct(): void
    {
        $this->_init('admin_access_temp', 'id');
    }
}

