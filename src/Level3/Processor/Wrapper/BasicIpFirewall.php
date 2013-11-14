<?php

namespace Level3\Processor\Wrapper;

use Level3\Messages\Request;
use Level3\Processor\Wrapper;
use Level3\Exceptions\Forbidden;

use Closure;
use UnexpectedValueException;

class BasicIpFirewall extends Wrapper
{
    const CIDR_SEPARATOR = '/';

    private $blacklist = [];
    private $whitelist = [];

    public function addIpToBlacklist($ipOrCIDR)
    {
        $this->addIpTo('blacklist', $ipOrCIDR);
    }

    public function getBlacklist()
    {
        return $this->blacklist;
    }

    public function addIpToWhitelist($ipOrCIDR)
    {
        $this->addIpTo('whitelist', $ipOrCIDR);
    }

    public function getWhitelist()
    {
        return $this->whitelist;
    }

    public function addIpTo($list, $ipOrCIDR)
    {
        $oposite = $list == 'whitelist'?'blacklist':'whitelist';
        if (count($this->$oposite) != 0) {
            throw new UnexpectedValueException(
                'Conflict cannot set at same time Blacklist and Whilelist'
            );
        }

        if ($this->isValidIPv4($ipOrCIDR)) {
            $this->{$list}[] = $ipOrCIDR;

            return;
        }

        if ($this->isValidCIDR($ipOrCIDR)) {
            $range = $this->getCIDRRange($ipOrCIDR);
            $this->$list = array_merge($this->$list, $range);

            return;
        }

        throw new UnexpectedValueException('Malformed IP/CIDR');
    }

    public function error(Closure $execution, Request $request)
    {
        return $execution($request);
    }

    protected function processRequest(Closure $execution, Request $request, $method)
    {
        $ip = $request->getClientIp();
        if (!$this->isAuthorizedIp($ip)) {
            throw new Forbidden();
        }

        return $execution($request);
    }

    protected function isAuthorizedIp($ip)
    {
        if ($this->whitelist) {
            return $this->isIpInWhitelist($ip);
        }

        if ($this->blacklist) {
            return !$this->isIpInBlacklist($ip);
        }

        return true;
    }

    protected function isIpInWhitelist($ip)
    {
        $list = array_flip($this->whitelist);

        return isset($list[$ip]);
    }

    protected function isIpInBlacklist($ip)
    {
        $list = array_flip($this->blacklist);

        return isset($list[$ip]);
    }

    private function isValidCIDR($cidr)
    {
        $tmp = explode(self::CIDR_SEPARATOR, $cidr);
        if (count($tmp) != 2) {
            return false;
        }

        return true;
    }

    private function isValidIPv4($ip)
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return false;
        }

        return true;
    }

    private function getCIDRRange($cidr)
    {
        $cidrParts = $this->getCIDRComponents($cidr);

        $length = $this->getCIDRLength($cidr);
        $range = [];

        $start = ip2long($cidrParts[0]);
        for ($i=0;$i < $length;$i++) {
            $range[] = long2ip($start + $i);
        }

        return $range;
    }

    private function getCIDRComponents($cidr)
    {
        return explode(self::CIDR_SEPARATOR, $cidr);
    }

    private function getCIDRLength($cidr)
    {
        $cidrParts = $this->getCIDRComponents($cidr);

        return 1 << (32 - $cidrParts[1]);
    }
}
