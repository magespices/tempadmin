<?php
/**
 * Copyright © Q-Solutions Studio: eCommerce Nanobots. All rights reserved.
 *
 * @category    Nanobots
 * @package     Nanobots_TempAdmin
 * @author      Sebastian Strojwas <sebastian@qsolutionsstudio.com>
 */

namespace Nanobots\TempAdmin\Cron;

use Magento\User\Model\ResourceModel\User as UserResource;
use Magento\User\Model\UserFactory;
use Nanobots\TempAdmin\Model\AdminAccessTempFactory;
use Nanobots\TempAdmin\Model\ResourceModel\AdminAccessTemp as AdminAccessTempResource;

class InvalidateTempAdmin
{
    protected AdminAccessTempFactory $adminAccessTempFactory;
    protected UserFactory $userFactory;
    protected UserResource $userResource;
    protected AdminAccessTempResource $adminAccessTempResource;

    /**
     * InvalidateTempAdmin constructor.
     * @param AdminAccessTempFactory $adminAccessTempFactory
     * @param AdminAccessTempResource $adminAccessTempResource
     * @param UserFactory $userFactory
     * @param UserResource $userResource
     */
    public function __construct(
        AdminAccessTempFactory  $adminAccessTempFactory,
        AdminAccessTempResource $adminAccessTempResource,
        UserFactory             $userFactory,
        UserResource            $userResource
    ) {
        $this->adminAccessTempFactory = $adminAccessTempFactory;
        $this->userFactory = $userFactory;
        $this->userResource = $userResource;
        $this->adminAccessTempResource = $adminAccessTempResource;
    }
    public function execute(): InvalidateTempAdmin
    {
        $now = new \DateTime();
        $tempAccounts = $this->adminAccessTempFactory->create();
        $tempAccountsCollection = $tempAccounts->getCollection()
            ->addFieldToFilter('valid_until', ['lteq' => $now->format('Y-m-d H:i:s')]);

        foreach ($tempAccountsCollection as $tempAccount) {
            $user = $this->userFactory->create();
            $this->userResource->load($user, $tempAccount->getAdminId());
            $this->userResource->save($user->setIsActive(0));
            $this->adminAccessTempResource->delete($tempAccount);
        }

        return $this;
    }
}
