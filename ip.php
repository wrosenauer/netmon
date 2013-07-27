<?php
	require_once('runtime.php');
	require_once(ROOT_DIR.'/lib/classes/core/Ip.class.php');
	require_once(ROOT_DIR.'/lib/classes/core/Networklist.class.php');
	require_once(ROOT_DIR.'/lib/classes/core/Networkinterface.class.php');
	require_once(ROOT_DIR.'/lib/classes/core/Router.class.php');
	
	if($_GET['section']=='add') {
		$smarty->assign('message', Message::getMessage());
		
		$networklist = new Networklist();
		$smarty->assign('networklist', $networklist->getNetworklist());
		
		$networkinterface = new Networkinterface((int)$_GET['interface_id']);
		$networkinterface->fetch();
		$smarty->assign('networkinterface', $networkinterface);
		
		$router = new Router((int)$_GET['router_id']);
		$router->fetch();
		$smarty->assign('router', $router);
		
		$smarty->display("header.tpl.php");
		$smarty->display("ip_add.tpl.php");
		$smarty->display("footer.tpl.php");
	} elseif ($_GET['section']=='insert_add') {
		$ip = new Ip(false, (int)$_GET['interface_id'], (int)$_POST['network_id'], $_POST['ip']);
		if($ip->store()) {
			$message[] = array('Die IP '.$_POST['ip'].' wurde angelegt.', 1);
			Message::setMessage($message);
		} else {
			$message[] = array('Die IP '.$_POST['ip'].' konnte nicht angelegt werden.', 2);
			Message::setMessage($message);
		}
		header('Location: ./router_config.php?router_id='.$_GET['router_id']);
	} elseif ($_GET['section']=='delete') {
		$ip = new Ip((int)$_GET['ip_id']);
		$ip->fetch();
		$ip->delete();
		
		$message[] = array('Die IP '.$ip->getIp().'/'.$ip->getNetwork()->getNetmask().' wurde gelöscht.', 1);
		Message::setMessage($message);
		
		header('Location: ./router_config.php?router_id='.$_GET['router_id']);
	}
?>