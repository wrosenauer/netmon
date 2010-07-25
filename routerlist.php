<?php
  require_once('./config/runtime.inc.php');
  require_once('./lib/classes/core/router.class.php');
  
  $routerlist = Router::getRouterList();
  $smarty->assign('routerlist', $routerlist);

  $smarty->display("header.tpl.php");
  $smarty->display("routerlist.tpl.php");
  $smarty->display("footer.tpl.php");
?>