<?php
/**
 * Copyright © Q-Solutions Studio: eCommerce Nanobots. All rights reserved.
 *
 * @category    Nanobots
 * @package     Nanobots_TempAdmin
 * @author      Sebastian Strojwas <sebastian@qsolutionsstudio.com>
 */

namespace Nanobots\TempAdmin\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Nanobots\TempAdmin\Helper\Data as TempAdminHelper;

class Form extends Template
{
    /** @var TempAdminHelper  */
    protected TempAdminHelper $helper;

    /**
     * Form Constructor
     * @param Context  $context
     * @param TempAdminHelper $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        TempAdminHelper $helper,
        array $data = []
    ) {
        $this->helper = $helper;

        parent::__construct($context, $data);
    }

    /**
     * @return bool
     */
    public function checkIfFormShouldBeShown() : bool
    {
        return $this->helper->isTempAdminModuleEnabled();
    }

    /**
     * @return string
     */
    public function getFormAction(): string
    {
        return '/temp_admin/index/create';
    }

    /**
     * @return string
     */
    public function getFormKey() : string
    {
        return $this->helper->getFormKey();
    }

    /**
     * @return array
     */
    public function getUserRoles(): array
    {
        return $this->helper->getUserRoles();
    }
}
