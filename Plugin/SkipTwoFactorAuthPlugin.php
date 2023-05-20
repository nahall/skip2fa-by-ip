<?php

namespace Sprinix\Skip2FAByIP\Plugin;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Store\Model\ScopeInterface;
use Magento\TwoFactorAuth\Model\TfaSession;

class SkipTwoFactorAuthPlugin
{
    protected $scopeConfig;
    protected $remoteAddress;

    /**
     * SkipTwoFactorAuthPlugin constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param RemoteAddress $remoteAddress
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        RemoteAddress $remoteAddress
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->remoteAddress = $remoteAddress;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag('twofactorauth/general/enabled_skip_by_ip',
            ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string[]
     */
    protected function getAllowedIps()
    {
        $allowedIps = $this->scopeConfig->getValue('twofactorauth/general/allowed_ips',
            ScopeInterface::SCOPE_STORE);
        return explode(',', $allowedIps ?? '');
    }

    /**
     * @param TfaSession $subject
     * @param $result
     * @return bool
     */
    public function afterIsGranted(
        TfaSession $subject,
        $result
    ): bool
    {
        if ($this->isEnabled() && $this->isIpAllowed()) {
            return true;
        }
        return $result;
    }

    /**
     * @return bool
     */
    public function isIpAllowed(): bool
    {
        $allowedIps = $this->getAllowedIps();
        $clientIp = $this->remoteAddress->getRemoteAddress();

        return in_array($clientIp, $allowedIps);
    }
}
