<?php

namespace Sprinix\Skip2FAByIP\Plugin;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Store\Model\ScopeInterface;
use Magento\TwoFactorAuth\Model\TfaSession;
use Magento\Framework\App\RequestInterface;

class SkipTwoFactorAuthPlugin
{
    protected $scopeConfig;
    protected $remoteAddress;
    protected $request;

    /**
     * SkipTwoFactorAuthPlugin constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param RemoteAddress $remoteAddress
     * @param RequestInterface $request
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        RemoteAddress $remoteAddress,
        RequestInterface $request

    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->remoteAddress = $remoteAddress;
        $this->request = $request;
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
     * @return string[]
     */
    protected function getIpFromKey()
    {
        return $this->scopeConfig->getValue('twofactorauth/general/get_ip_from_key',
            ScopeInterface::SCOPE_STORE);
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
        $clientIp = $this->getClientIp();

        return in_array($clientIp, $allowedIps);
    }

    /**
     * @return string
     */
    public function getClientIp()
    {
        $server = $this->request->getServer();
        $key = $this->getIpFromKey();
        if (isset($server['HTTP_CDN_LOOP']) && $server['HTTP_CDN_LOOP'] == 'cloudflare'
            && isset($server['HTTP_CF_CONNECTING_IP'])) {
            return $server['HTTP_CF_CONNECTING_IP'];
        } elseif (isset($key) && isset($server[(string)$key])) {
            return $server[(string)$key];
        } elseif (isset($server['HTTP_X_FORWARDED_FOR'])) {
            return $server['HTTP_X_FORWARDED_FOR'][0];
        } else {
            return $this->remoteAddress->getRemoteAddress();
        }
    }
}

