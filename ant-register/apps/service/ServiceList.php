<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2016/11/1
 * Time: 09:51
 */

namespace service;

use common\LoadClass;
use entity;

class ServiceList extends Base
{
    /**
     * @var \dao\Base
     */
    protected $dao;
    
    public function __construct()
    {
        $this->dao = LoadClass::getDao('ServiceList');
    }

    /**
     * @param $serviceName
     * @param $serviceIp
     * @param $servicePort
     * @return entity\ServiceList|mixed
     * @desc 服务注册
     */
    public function register($serviceName, $serviceIp, $servicePort)
    {
        $serviceInfo = $this->dao->fetchOne([
            'ip = ' => "'{$serviceIp}'",
            'port = ' => $servicePort
        ]);

        if (empty($serviceInfo)) {
            $serviceInfo = new entity\ServiceList();
            $serviceInfo->name = $serviceName;
            $serviceInfo->ip = $serviceIp;
            $serviceInfo->port = $servicePort;
            $serviceInfo->status = 1;
            $serviceInfo->startTime = time();
            $id = $this->dao->add($serviceInfo);
            $serviceInfo->id = $id;
        } else if (empty($serviceInfo->status)) {
            if ($this->dao->update(['status' => 1], ['id=' => $serviceInfo->id])) {
                $serviceInfo->status = 1;
            }
        }
        return $serviceInfo;
    }

    /**
     * @param $serviceIp
     * @param $servicePort
     * @return mixed
     * @desc 服务摘除
     */
    public function drop($serviceIp, $servicePort)
    {
        $serviceInfo = $this->dao->fetchOne([
            'ip = ' => "'{$serviceIp}'",
            'port = ' => $servicePort
        ]);

        if (!empty($serviceInfo->status)) {
            if ($this->dao->update(['status' => 0], ['id=' => $serviceInfo->id])) {
                $serviceInfo->status = 0;
            }
        }
        return $serviceInfo;
    }

    /**
     * @param $serviceName
     * @return int
     * @desc 移除某服务所有机器
     */
    public function dropAll($serviceName)
    {
        return $this->dao->update(['status'=>0], ['name='=>$serviceName]);
    }

    /**
     * @param $serviceName
     * @return mixed
     * @desc 获取服务列表
     */
    public function getServiceList($serviceName)
    {
        $serviceList = LoadClass::getDao('ServiceList')->fetchAll([
            'name=' => "'{$serviceName}'"
        ]);
        return $serviceList;
    }

}