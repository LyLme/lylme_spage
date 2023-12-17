<?php

$site = new SITE($dbconfig['host'], $dbconfig['user'], $dbconfig['pwd'], $dbconfig['dbname'], $dbconfig['port']);
class SITE extends DB
{
    public function __construct($db_host, $db_user, $db_pass, $db_name, $db_port)
    {
        parent::__construct($db_host, $db_user, $db_pass, $db_name, $db_port);
    }
    /**
     * @Description 获取分组信息
     * @return object
     */
    public function getGroups()
    {
        return $this->query("SELECT * FROM `lylme_groups` WHERE `group_pwd` = 0 ORDER BY `group_order` ASC");
    }/**
     * 获取分组列表
     * @Description
     * @return object
     */
    public function getCategorys()
    {
        //获取分组信息
        return $this->query("SELECT * FROM `lylme_groups` WHERE `group_pwd` = 0 ORDER BY `group_order` ASC");
    }
    /**
     * 获取标签菜单
     * @Author: LyLme
     * @return object
     */
    public function getTags()
    {
        return $this->query("SELECT * FROM `lylme_tags` ORDER BY `lylme_tags`.`sort` ASC");
    }
    /**
     * 获取搜索引擎
     * @Author: LyLme
     * @return object
     */
    public function getSou()
    {
        return $this->query("SELECT * FROM `lylme_sou` WHERE `sou_st` = 1 ORDER BY `lylme_sou`.`sou_order` ASC");
    }

}
