<?php
/**
 * Copyright © Q-Solutions Studio: eCommerce Nanobots. All rights reserved.
 *
 * @category    Nanobots
 * @package     Nanobots_TempAdmin
 * @author      Sebastian Strojwas <sebastian@qsolutionsstudio.com>
 */

namespace Nanobots\TempAdmin\Helper;

use Exception;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Zend_Mail_Exception;

/**
 * Class Data
 * @package Nanobots\TempAdmin\Helper
 */
class Data extends AbstractHelper
{
    public const TEMP_ADMIN_ENABLED_XPATH = 'temp_admin/general/enabled';
    public const TEMP_ADMIN_URI_XPATH = 'temp_admin/general/secret_uri';
    public const PASSWORD_LENGTH_XPATH = 'temp_admin/general/password_length';
    public const WHITELIST_ENABLED_XPATH = 'temp_admin/general/whitelist_enabled';
    public const WHITELIST_IPS_XPATH = 'temp_admin/general/whitelist_ips';
    public const EMAIL_XPATH = 'temp_admin/general/email';
    public const GENERAL_CONTACT_EMAIL_XPATH = 'trans_email/ident_general/email';
    public const EMAIL_DOMAIN_XPATH = 'temp_admin/general/email_domain';

    protected StoreManagerInterface $storeManager;
    protected FormKey $formKey;
    protected Validator $formKeyValidator;
    protected ResourceConnection $resourceConnection;

    /**
     * Data constructor.
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param FormKey $formKey
     * @param Validator $formKeyValidator
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        FormKey $formKey,
        Validator $formKeyValidator,
        ResourceConnection $resourceConnection
    ) {
        $this->storeManager = $storeManager;
        $this->formKey = $formKey;
        $this->formKeyValidator = $formKeyValidator;
        $this->resourceConnection = $resourceConnection;

        parent::__construct($context);
    }

    /**
     * @return bool
     */
    public function isTempAdminModuleEnabled(): bool
    {
        try {
            return $this->scopeConfig->isSetFlag(
                static::TEMP_ADMIN_ENABLED_XPATH,
                ScopeInterface::SCOPE_STORE,
                $this->storeManager->getStore()->getId()
            );
        } catch (Exception $exception) {
            $this->_logger->error($exception->getMessage());
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isWhitelistEnabled(): bool
    {
        try {
            return $this->scopeConfig->isSetFlag(
                static::WHITELIST_ENABLED_XPATH,
                ScopeInterface::SCOPE_STORE,
                $this->storeManager->getStore()->getId()
            );
        } catch (Exception $exception) {
            $this->_logger->error($exception->getMessage());
        }
        return false;
    }

    /**
     * @return array
     */
    public function getWhitelistIPs(): array
    {
        try {
            return explode(PHP_EOL,
                $this->scopeConfig->getValue(
                static::WHITELIST_IPS_XPATH,
                ScopeInterface::SCOPE_STORE,
                $this->storeManager->getStore()->getId()
            ));
        } catch (Exception $exception) {
            $this->_logger->error($exception->getMessage());
        }
        return [];
    }

    /**
     * @return string
     */
    public function getTempAdminUrlPath(): string
    {
        return $this->scopeConfig->getValue(static::TEMP_ADMIN_URI_XPATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return int
     */
    public function getPasswordLength(): int
    {
        return (int) $this->scopeConfig->getValue(static::PASSWORD_LENGTH_XPATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->scopeConfig->getValue(static::EMAIL_XPATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getGeneralContactEmail(): string
    {
        return $this->scopeConfig->getValue(static::GENERAL_CONTACT_EMAIL_XPATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getEmailDomain(): string
    {
        return $this->scopeConfig->getValue(static::EMAIL_DOMAIN_XPATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getFormKey() : string
    {
        $formKey = '';

        try {
            $formKey = $this->formKey->getFormKey();
        } catch (LocalizedException $exception) {
            $this->_logger->error($exception->getMessage());
        }

        return $formKey;
    }

    /**
     * @return array
     */
    public function getUserRoles(): array
    {
        $userRoles = [];

        $connection = $this->resourceConnection->getConnection();
        $table = $connection->getTableName('authorization_role');
        $query = "SELECT role_id, role_name FROM " . $table . " WHERE parent_id = 0 ORDER BY role_id ASC";

        $result = $connection->fetchAll($query);

        foreach ($result as $userRole) {
            $userRoles[$userRole['role_id']] = $userRole['role_name'];
        }

        return $userRoles;
    }

    /**
     * @param $duration
     * @return int
     */
    public function getValidUntilTimestamp($duration): int
    {
        switch ($duration) {
            case 1:
                $numberOfDays = 1;
                break;
            case 2:
                $numberOfDays = 7;
                break;
            case 3:
            default:
                $numberOfDays = date('t');
                break;
        }

        return time() + ($numberOfDays * 24 * 60 * 60);
    }

    /**
     * @param int $roleId
     * @return bool
     */
    public function validateUserRole(int $roleId): bool
    {
        $connection = $this->resourceConnection->getConnection();
        $table = $connection->getTableName('authorization_role');
        $query = "SELECT role_id FROM " . $table . " WHERE parent_id = 0 ORDER BY role_id ASC";

        $roleIds = $connection->fetchCol($query);

        return in_array($roleId, $roleIds);
    }

    /**
     * @return string
     */
    public function generateRandomPassword(): string
    {
        // intentionally without 0 and O
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ123456789';
        $pass = array();
        $alphaLength = strlen($alphabet) - 1;
        $includesNumericCharacter = false;
        $includesAlphabeticCharacter = false;

        for ($i = 0; $i < $this->getPasswordLength(); $i++) {
            $n = rand(0, $alphaLength);

            if (is_numeric($alphabet[$n])) {
                $includesNumericCharacter = true;
            } else {
                $includesAlphabeticCharacter = true;
            }

            $pass[] = $alphabet[$n];
        }

        if (!$includesNumericCharacter) {
            $pass[0] = rand(1, 9);
        } else if (!$includesAlphabeticCharacter) {
            $n = rand(0, $alphaLength - 9);
            $key = rand(0, $this->getPasswordLength() - 1);
            $pass[$key] = $alphabet[$n];
        }

        return implode($pass);
    }

    /**
     * @param $request
     * @return bool
     */
    public function validateFormKey($request): bool
    {
        return $this->formKeyValidator->validate($request);
    }

    /**
     * @param $data
     * @throws Zend_Mail_Exception
     */
    public function sendNotification($data)
    {
        $body = '<ul>';
        foreach ($data as $k => $v) {
            $body .= '<li>' . $k . ": " . $v . '</li>';
        }
        $body .= '</ul>';

        $email = new \Zend_Mail();

        $email->setSubject("Temp Admin Account Created");
        $email->setFrom($this->getGeneralContactEmail(), 'Magento Notification');
        $email->setBodyHtml($body);
        $email->addTo($this->getEmail());
        $email->send();
    }

    /**
     * @param string $message
     */
    public function logError(string $message)
    {
        $this->_logger->error($message);
    }
}
