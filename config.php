<?php

require_once('./config/runtime.inc.php');
require_once('./lib/classes/core/install.class.php');

if ($_GET['section']=="edit") {
	//INSTALLATION-LOCK
	$smarty->assign('installed', $GLOBALS['installed']);

	//WEBSERVER
	$smarty->assign('url_to_netmon', $GLOBALS['url_to_netmon']);
	
	//MYSQL
	$smarty->assign('mysql_host', $GLOBALS['mysql_host']);
	$smarty->assign('mysql_db', $GLOBALS['mysql_db']);
	$smarty->assign('mysql_user', $GLOBALS['mysql_user']);
	$smarty->assign('mysql_password', $GLOBALS['mysql_password']);
	
	//JABBER
	$smarty->assign('jabber_server', $GLOBALS['jabber_server']);
	$smarty->assign('jabber_username', $GLOBALS['jabber_username']);
	$smarty->assign('jabber_password', $GLOBALS['jabber_password']);
	
	//MAIL
	$smarty->assign('mail_sending_type', $GLOBALS['mail_sending_type']);
	$smarty->assign('mail_sender_adress', $GLOBALS['mail_sender_adress']);
	$smarty->assign('mail_sender_name', $GLOBALS['mail_sender_name']);
	$smarty->assign('mail_smtp_server', $GLOBALS['mail_smtp_server']);
	$smarty->assign('mail_smtp_username', $GLOBALS['mail_smtp_username']);
	$smarty->assign('mail_smtp_password', $GLOBALS['mail_smtp_password']);
	$smarty->assign('mail_smtp_login_auth', $GLOBALS['mail_smtp_login_auth']);
	$smarty->assign('mail_smtp_ssl', $GLOBALS['mail_smtp_ssl']);
	
	//NETWORK
	$smarty->assign('net_prefix', $GLOBALS['net_prefix']);
	$smarty->assign('community_name', $GLOBALS['community_name']);
	$smarty->assign('community_website', $GLOBALS['community_website']);
	$smarty->assign('enable_network_policy', $GLOBALS['enable_network_policy']);
	$smarty->assign('networkPolicy', $GLOBALS['networkPolicy']);
	
	//VPNKEYS
	$smarty->assign('expiration', $GLOBALS['expiration']);

	//PROJEKT
	$smarty->assign('days_to_keep_mysql_crawl_data', $GLOBALS['days_to_keep_mysql_crawl_data']);

	//GOOGLEMAPSAPIKEY
	$smarty->assign('google_maps_api_key', $GLOBALS['google_maps_api_key']);
	
	//CRAWLER
	$smarty->assign('crawl_cycle', $GLOBALS['crawl_cycle']);
	$smarty->assign('crawler_ping_timeout', $GLOBALS['crawler_ping_timeout']);
	$smarty->assign('crawler_curl_timeout', $GLOBALS['crawler_curl_timeout']);


	$smarty->assign('message', Message::getMessage());
	$smarty->display("header.tpl.php");
	$smarty->display("config.tpl.php");
	$smarty->display("footer.tpl.php");
} elseif ($_GET['section']=="insert_edit") {
		$config_path = "./config/config.local.inc.php";

		$file = Install::getFileLineByLine($config_path);
		if ($_POST['installed'])
			$configs[0] = '$GLOBALS[\'installed\'] = true;';
		else
			$configs[0] = '$GLOBALS[\'installed\'] = false;';
		$file = Install::changeConfigSection('//INSTALLATION-LOCK', $file, $configs);
		Install::writeEmptyFileLineByLine($config_path, $file);
		unset($configs);

		$file = Install::getFileLineByLine($config_path);
		$configs[0] = '$GLOBALS[\'url_to_netmon\'] = "'.$_POST['url_to_netmon'].'";';
		$file = Install::changeConfigSection('//WEBSERVER', $file, $configs);
		Install::writeEmptyFileLineByLine($config_path, $file);
		unset($configs);

		$file = Install::getFileLineByLine($config_path);
		$configs[0] = '$GLOBALS[\'mysql_host\'] = "'.$_POST['mysql_host'].'";';
		$configs[1] = '$GLOBALS[\'mysql_db\'] = "'.$_POST['mysql_db'].'";';
		$configs[2] = '$GLOBALS[\'mysql_user\'] = "'.$_POST['mysql_user'].'";';
		$configs[3] = '$GLOBALS[\'mysql_password\'] = "'.$_POST['mysql_password'].'";';
		$file = Install::changeConfigSection('//MYSQL', $file, $configs);
		Install::writeEmptyFileLineByLine($config_path, $file);
		unset($configs);

		$file = Install::getFileLineByLine($config_path);
		$configs[0] = '$GLOBALS[\'jabber_server\'] = "'.$_POST['jabber_server'].'";';
		$configs[1] = '$GLOBALS[\'jabber_username\'] = "'.$_POST['jabber_username'].'";';
		$configs[2] = '$GLOBALS[\'jabber_password\'] = "'.$_POST['jabber_password'].'";';
		$file = Install::changeConfigSection('//JABBER', $file, $configs);
		Install::writeEmptyFileLineByLine($config_path, $file);
		unset($configs);

		$file = Install::getFileLineByLine($config_path);
		$configs[0] = '$GLOBALS[\'mail_sending_type\'] = "'.$_POST['mail_sending_type'].'";';
		$configs[1] = '$GLOBALS[\'mail_sender_adress\'] = "'.$_POST['mail_sender_adress'].'";';
		$configs[2] = '$GLOBALS[\'mail_sender_name\'] = "'.$_POST['mail_sender_name'].'";';
		$configs[3] = '$GLOBALS[\'mail_smtp_server\'] = "'.$_POST['mail_smtp_server'].'";';
		$configs[4] = '$GLOBALS[\'mail_smtp_username\'] = "'.$_POST['mail_smtp_username'].'";';
		$configs[5] = '$GLOBALS[\'mail_smtp_password\'] = "'.$_POST['mail_smtp_password'].'";';
		$configs[6] = '$GLOBALS[\'mail_smtp_login_auth\'] = "'.$_POST['mail_smtp_login_auth'].'";';
		$configs[7] = '$GLOBALS[\'mail_smtp_ssl\'] = "'.$_POST['mail_smtp_ssl'].'";';
		$file = Install::changeConfigSection('//MAIL', $file, $configs);
		Install::writeEmptyFileLineByLine($config_path, $file);
		unset($configs);

		$file = Install::getFileLineByLine($config_path);
		$configs[0] = '$GLOBALS[\'net_prefix\'] = "'.$_POST['net_prefix'].'";';
		$configs[1] = '$GLOBALS[\'community_name\'] = "'.$_POST['community_name'].'";';
		$configs[2] = '$GLOBALS[\'community_website\'] = "'.$_POST['community_website'].'";';
		if ($_POST['enable_network_policy'])
			$configs[3] = '$GLOBALS[\'enable_network_policy\'] = true;';
		else
			$configs[3] = '$GLOBALS[\'enable_network_policy\'] = false;';
		$configs[4] = '$GLOBALS[\'networkPolicy\'] = "'.$_POST['networkPolicy'].'";';
		$file = Install::changeConfigSection('//NETWORK', $file, $configs);
		Install::writeEmptyFileLineByLine($config_path, $file);
		unset($configs);

		$file = Install::getFileLineByLine($config_path);
		$configs[0] = '$GLOBALS[\'expiration\'] = '.$_POST['expiration'].';';
		$file = Install::changeConfigSection('//VPNKEYS', $file, $configs);
		Install::writeEmptyFileLineByLine($config_path, $file);
		unset($configs);

		$file = Install::getFileLineByLine($config_path);
		$configs[0] = '$GLOBALS[\'days_to_keep_mysql_crawl_data\'] = '.$_POST['days_to_keep_mysql_crawl_data'].';';
		$file = Install::changeConfigSection('//PROJEKT', $file, $configs);
		Install::writeEmptyFileLineByLine($config_path, $file);
		unset($configs);

		$file = Install::getFileLineByLine($config_path);
		$configs[0] = '$GLOBALS[\'google_maps_api_key\'] = "'.$_POST['google_maps_api_key'].'";';
		$file = Install::changeConfigSection('//GOOGLEMAPSAPIKEY', $file, $configs);
		Install::writeEmptyFileLineByLine($config_path, $file);
		unset($configs);

		$file = Install::getFileLineByLine($config_path);
		$configs[0] = '$GLOBALS[\'crawl_cycle\'] = '.$_POST['crawl_cycle'].';';
		$configs[1] = '$GLOBALS[\'crawler_ping_timeout\'] = '.$_POST['crawler_ping_timeout'].';';
		$configs[2] = '$GLOBALS[\'crawler_curl_timeout\'] = '.$_POST['crawler_curl_timeout'].';';
		$file = Install::changeConfigSection('//CRAWLER', $file, $configs);
		Install::writeEmptyFileLineByLine($config_path, $file);
		unset($configs);

		$message[] = array('Die Konfiguration wurde geändert.', 1);
		Message::setMessage($message);

		header('Location: ./config.php?section=edit');
}

?>