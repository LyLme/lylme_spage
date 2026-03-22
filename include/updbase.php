<?php

if (!defined("VERSION")) {
    return 0;
}

function get_vernum($version)
{
    // 移除版本号中的'v'前缀，并分割为数组
    $vn = explode('.', str_replace('v', '', (string)$version));
    
    // 确保数组至少有3个元素，避免未定义偏移错误
    $vn[0] = $vn[0] ?? 0;
    $vn[1] = $vn[1] ?? 0;
    $vn[2] = $vn[2] ?? 0;
    
    // 格式化版本号：主版本 + 两位次版本 + 两位修订版本
    return $vn[0] . sprintf("%02d", $vn[1]) . sprintf("%02d", $vn[2]);
}

// 确保配置存在且包含版本信息
if (!isset($conf['version']) || empty($conf['version'])) {
    return 0;
}

$sqlvn = get_vernum($conf['version']);  // 数据库版本
$filevn = get_vernum(constant("VERSION"));  // 文件版本

if ($sqlvn < $filevn) {
    // 文件版本大于数据库版本，执行更新
    $sql = '';
    $version = '';
    
    if ($sqlvn < 20200) {
        $version = 'v2.2.0';
         $sql .= '';
    }
    
    // 执行SQL语句
    if (!empty($sql)) {
        $sqlStatements = explode(';', $sql);
        
        foreach ($sqlStatements as $sqlStatement) {
            $sqlStatement = trim($sqlStatement);
            if (empty($sqlStatement)) {
                continue;
            }
            
            try {
                $DB->query($sqlStatement);
            } catch (Exception $e) {
                // 可以选择记录错误日志，但不中断升级流程
                error_log("SQL执行失败: " . $e->getMessage());
            }
        }
    }
    
    // 保存新版本号
    if (!empty($version)) {
        saveSetting('version', $version);
    }
}