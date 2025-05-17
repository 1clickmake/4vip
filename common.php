<?php
define('_CMBOARD_', true);

// XSS 방지를 위한 함수
function clean_xss_tags($str) {
    $str = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $str);
    return $str;
}

require_once $_SERVER['DOCUMENT_ROOT'] .'/data/config.php';
session_start();

$scheme = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http');
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
$domain_url = $scheme . '://' . $host;

define('CM_PATH', $_SERVER['DOCUMENT_ROOT']);
define('CM_URL', $domain_url);

require_once(CM_PATH.'/config.php');//상수 선언
require_once(CM_LIB_PATH.'/common.lib.php'); // 공통 라이브러리

// common.php 파일을 수정할 필요가 없도록 확장합니다.
$extend_file = array();
$tmp = dir(CM_EXTEND_PATH);
while ($entry = $tmp->read()) {
    // php 파일만 include 함
    if (preg_match("/(\.php)$/i", $entry))
        $extend_file[] = $entry;
}

if(!empty($extend_file) && is_array($extend_file)) {
    natsort($extend_file);

    foreach($extend_file as $file) {
        include_once(CM_EXTEND_PATH.'/'.$file);
    }
    unset($file);
}
unset($extend_file);

//관리자 환경설정
$sql = "SELECT * FROM `cm_config` WHERE `id` = :id";
$params = [':id' => 1]; // ':이름' => 값 형태
$config = sql_fetch($sql, $params);

$is_admin = false;
$is_member = false;
if(isset($_SESSION['user_id']) && $_SESSION['user_id'] !==""){
	$sql = "SELECT * FROM `cm_users` WHERE `user_id` = :user_id";
	$params = [':user_id' => $_SESSION['user_id']]; // ':이름' => 값 형태
	$member = sql_fetch($sql, $params);
	
	$is_member = true;
	
	if($config['admin_id'] == $member['user_id']){
		$is_admin = true;
	}
}

//템플릿 관련 상수
define('CM_TEMPLATE_PATH', CM_PATH.'/template/'.$config['template_id']);
define('CM_TEMPLATE_URL', CM_URL.'/template/'.$config['template_id']);

//쇼핑몰 템플릿 상수
define('CM_SHOP_TEMPLATE_PATH', CM_PATH.'/shop/template/'.$config['shop_template_id']);
define('CM_SHOP_TEMPLATE_URL', CM_URL.'/shop/template/'.$config['shop_template_id']);
?>