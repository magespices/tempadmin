<?php
/**
 * Created by Q-Solutions Studio
 *
 * @category    Magespices
 * @package     Magespices_TempAdmin
 * @author      Sebastian Strojwas <sebastian@qsolutionsstudio.com>
 */

namespace Magespices\TempAdmin\Cron;

use Magento\User\Model\UserFactory;
use Magespices\TempAdmin\Model\AdminAccessTempFactory;

class InvalidateTempAdmin
{
    /** @var AdminAccessTempFactory  */
    protected AdminAccessTempFactory $adminAccessTempFactory;

    /** @var UserFactory  */
    protected UserFactory $userFactory;

    /**
     * InvalidateTempAdmin constructor.
     * @param AdminAccessTempFactory $adminAccessTempFactory
     * @param UserFactory $userFactory
     */
    public function __construct(
        AdminAccessTempFactory $adminAccessTempFactory,
        UserFactory $userFactory
    ) {
        $this->adminAccessTempFactory = $adminAccessTempFactory;
        $this->userFactory = $userFactory;
    }
    public function execute(): InvalidateTempAdmin
    {
        $now = new \DateTime();
        $tempAccounts = $this->adminAccessTempFactory->create();
        $tempAccountsCollection = $tempAccounts->getCollection()
            ->addFieldToFilter('valid_until', ['lteq' => $now->format('Y-m-d H:i:s')]);

        foreach ($tempAccountsCollection as $tempAccount) {
            $user = $this->userFactory->create()->load($tempAccount->getAdminId());
            $user->setIsActive(0)->save();
            $tempAccount->delete();
        }

        return $this;
    }
}
