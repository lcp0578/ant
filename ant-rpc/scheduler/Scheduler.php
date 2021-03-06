<?php

 namespace scheduler;

 use ZPHP\Core\Config as ZConfig;

/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2016/11/17
 * Time: 15:29
 * @desc 服务调度器
 */
class Scheduler
{
    /**
     * @param $serviceName
     * @return array [$ip, $port]
     * @desc 根据服务名获名一个可用的ip:port
     */
    public static function getService($serviceName)
    {
        $soaConfig = ZConfig::get('soa');
        if (!empty($soaConfig)) {
            //@TODO 配合ant-config, 不走网络请求
            $rpcClient = new Tcp($soaConfig['ip'], $soaConfig['port'], $soaConfig['timeOut']);
            $data = $rpcClient->setApi('main')->call('getList', [
                'serviceName' => $serviceName
            ]);
            if ($data) {
                //@TODO 跟据投票，选出最合理的服务
                $serverList =  \json_decode($data, true);
                shuffle($serverList);
                $current = current($serverList);
                return [
                    $current->ip,
                    $current->port
                ];
            }
        }
    }

    /**
     * @param $ip
     * @param $port
     * @desc rpc调用成功，成功投票+1
     */
    public static function voteGood($ip, $port)
    {

    }

    /**
     * @param $ip
     * @param $port
     * @desc rpc调用失败，失败投票 +1
     */
    public static function voteBad($ip, $port)
    {

    }

}