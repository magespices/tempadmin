<?php
/**
 * Copyright © Q-Solutions Studio: eCommerce Nanobots. All rights reserved.
 *
 * @category    Nanobots
 * @package     Nanobots_TempAdmin
 * @author      Sebastian Strojwas <sebastian@qsolutionsstudio.com>
 */

declare(strict_types=1);

namespace Nanobots\TempAdmin\Controller\Index;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Nanobots\TempAdmin\Helper\Data as TempAdminHelper;
use Magento\User\Model\UserFactory;
use Nanobots\TempAdmin\Model\AdminAccessTempFactory;

/**
 * Class Index
 */
class Create implements HttpGetActionInterface
{
    /** @var RequestInterface  */
    protected RequestInterface $request;

    /** @var UserFactory  */
    protected UserFactory $userFactory;

    /** @var AdminAccessTempFactory  */
    protected AdminAccessTempFactory $adminAccessTempFactory;

    /** @var ResultFactory  */
    protected ResultFactory $resultFactory;

    /** @var JsonHelper  */
    protected JsonHelper $jsonHelper;

    /** @var TempAdminHelper  */
    protected TempAdminHelper $tmpAdminHelper;

    /**
     * Create constructor.
     * @param RequestInterface $request
     * @param UserFactory $userFactory
     * @param AdminAccessTempFactory $adminAccessTempFactory
     * @param ResultFactory $resultFactory
     * @param JsonHelper $jsonHelper
     * @param TempAdminHelper $tmpAdminHelper
     */
    public function __construct(
        RequestInterface $request,
        UserFactory $userFactory,
        AdminAccessTempFactory $adminAccessTempFactory,
        ResultFactory $resultFactory,
        JsonHelper $jsonHelper,
        TempAdminHelper $tmpAdminHelper
    ) {
        $this->request = $request;
        $this->userFactory = $userFactory;
        $this->adminAccessTempFactory = $adminAccessTempFactory;
        $this->resultFactory = $resultFactory;
        $this->jsonHelper = $jsonHelper;
        $this->tmpAdminHelper = $tmpAdminHelper;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        if (!$this->request->isXmlHttpRequest()) {
            $responseData = [
                'status' => 0,
                'message' => 'Invalid request type.',
            ];
        } elseif (!$this->tmpAdminHelper->validateFormKey($this->request)) {
            $responseData = [
                'status' => 0,
                'message' => 'Invalid form key.',
            ];
        } else {
            $responseData = [
                'status' => 1,
                'message' => 'User has been created.',
            ];

            $randNumber = rand(11111111, 99999999);
            $email = sprintf('tempadmin+%s@%s', $randNumber, $this->tmpAdminHelper->getEmailDomain());
            $userName = sprintf('tempadmin%s', $randNumber);
            $roleId = (int) $this->request->getParam('role_id');

            if ($this->tmpAdminHelper->validateUserRole($roleId)) {
                $adminInfo = [
                    'username'          => $userName,
                    'firstname'         => 'temp',
                    'lastname'          => 'admin',
                    'email'             => $email,
                    'password'          => $this->tmpAdminHelper->generateRandomPassword(),
                    'interface_locale'  => 'en_US',
                    'is_active'         => 1
                ];

                $userModel = $this->userFactory->create();
                $userModel->setData($adminInfo);
                $userModel->setRoleId($roleId);

                try{
                    $userModel->save();

                    $adminAccessTempInfo = [
                        'admin_id'      => $userModel->getId(),
                        'valid_until'   => $this->tmpAdminHelper->getValidUntilTimestamp($this->request->getParam('duration')),
                    ];

                    $adminAccessTempModel = $this->adminAccessTempFactory->create();
                    $adminAccessTempModel->setData($adminAccessTempInfo)->save();

                    $notificationData = [
                        'email' => $adminInfo['email'],
                        'user' => $adminInfo['username'],
                        'password' => $adminInfo['password']
                    ];

                    $this->tmpAdminHelper->sendNotification($notificationData);
                } catch (\Exception $exception) {
                    $this->tmpAdminHelper->logError($exception->getMessage());
                    $responseData = [
                        'status' => 0,
                        'message' => $exception->getMessage(),
                    ];
                }
            } else {
                $responseData = [
                    'status' => 0,
                    'message' => 'Invalid Admin Role.',
                ];
            }
        }

        $response = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $response->setHeader('Content-type', 'text/plain');

        return $response->setContents(
            $this->jsonHelper->jsonEncode($responseData)
        );
    }
}
