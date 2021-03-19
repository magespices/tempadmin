<?php
/**
 * Created by Q-Solutions Studio
 *
 * @category    Magespices
 * @package     Magespices_TempAdmin
 * @author      Sebastian Strojwas <sebastian@qsolutionsstudio.com>
 */

namespace Magespices\TempAdmin\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class AdminAccessTemp
 * @package Magespices\TempAdmin\Model
 */
class AdminAccessTemp extends AbstractModel
{
    public function _construct(): void
    {
        $this->_init(ResourceModel\AdminAccessTemp::class);
    }
}
