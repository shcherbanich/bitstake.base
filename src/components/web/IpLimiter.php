<?php

namespace shcherbanich\bitstake\components\web;

use Yii;
use yii\filters\RateLimitInterface;

class IpLimiter implements RateLimitInterface {


    public $rateLimit = 5;

    public $ipWhiteList = [
        '127.0.0.1',
        '::1'
    ];

    /**
     * @param string $ip the IP address
     * @return boolean whether the rule applies to the IP address
     */
    protected function matchIP($ip)
    {
        if (empty($this->ipWhiteList)) {
            return true;
        }
        foreach ($this->ipWhiteList as $rule) {
            if ($rule === '*' || $rule === $ip || (($pos = strpos($rule, '*')) !== false && !strncmp($ip, $rule, $pos))) {
                return true;
            }
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getRateLimit($request, $action)
    {
        return [$this->rateLimit, 1];
    }

    /**
     * @inheritdoc
     */
    public function loadAllowance($request, $action)
    {

        $cache = Yii::$app->cache;

        $userIp = md5(Yii::$app->request->getUserIP());

        $key = "RateLimit:{$action->id}_{$userIp}";

        $data = $cache->get($key);

        if ($data && isset($data['allowance']) && isset($data['allowance_updated_at'])) {

            return [$data['allowance'], $data['allowance_updated_at']];
        }

        return [$this->rateLimit, time()];
    }

    /**
     * @inheritdoc
     */
    public function saveAllowance($request, $action, $allowance, $timestamp)
    {

        $ip = Yii::$app->request->getUserIP();

        if(!$this->matchIP($ip)) {

            $data = [];

            $data['allowance'] = $allowance;

            $data['allowance_updated_at'] = $timestamp;

            $cache = Yii::$app->cache;

            $userIp = md5($ip);

            $key = "RateLimit:{$action->id}_{$userIp}";

            $cache->set($key, $data, 600);
        }
    }
}