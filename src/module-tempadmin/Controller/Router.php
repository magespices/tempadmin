<?php
/**
 * Copyright © Q-Solutions Studio: eCommerce Nanobots. All rights reserved.
 *
 * @category    Nanobots
 * @package     Nanobots_TempAdmin
 * @author      Sebastian Strojwas <sebastian@qsolutionsstudio.com>
 */

namespace Nanobots\TempAdmin\Controller;

use Magento\Framework\App\Action\Forward;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;
use Magento\Framework\UrlInterface;
use Nanobots\TempAdmin\Helper\Data as TempAdminHelper;

class Router implements RouterInterface
{
    /** @var ActionFactory  */
    protected ActionFactory $actionFactory;

    /** @var TempAdminHelper  */
    protected TempAdminHelper $helper;

    /**
     * Router constructor.
     * @param ActionFactory $actionFactory
     * @param TempAdminHelper $helper
     */
    public function __construct(
        ActionFactory $actionFactory,
        TempAdminHelper $helper
    ) {
        $this->actionFactory = $actionFactory;
        $this->helper = $helper;
    }

    /**
     * @param RequestInterface $request
     * @return ActionInterface|null
     */
    public function match(RequestInterface $request): ?ActionInterface
    {
        if (!$this->helper->isTempAdminModuleEnabled()) {
            return null;
        }

        $currentPath = trim($request->getPathInfo(), '/');

        $tempAdminPath = $this->helper->getTempAdminUrlPath();
        $tempAdminPaths = [
            $tempAdminPath,
            $tempAdminPath . '/index',
            $tempAdminPath . '/index/index',
        ];

        if (!in_array($currentPath, $tempAdminPaths)) {
            return null;
        }

        $request->setAlias(
            UrlInterface::REWRITE_REQUEST_PATH_ALIAS,
            $currentPath
        );
        $request->setPathInfo('/temp_admin/index/index');

        return $this->actionFactory->create(
            Forward::class
        );
    }
}
