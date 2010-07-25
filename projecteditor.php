<?php

  require_once('./config/runtime.inc.php');
  require_once('./lib/classes/core/helper.class.php');
  require_once('./lib/classes/core/projecteditor.class.php');

    if ($_GET['section'] == "new") {
		if (UserManagement::checkPermission(32)) {
			$smarty->assign('message', Message::getMessage());
			$smarty->display("header.tpl.php");
			$smarty->display("project_new.tpl.php");
			$smarty->display("footer.tpl.php");
		} else {
			$message[] = array("Nur Administratoren dürfen Projekte anlegen!", 2);
			Message::setMessage($message);
			header('Location: ./login.php');
		}
    }
    if ($_GET['section'] == "insert_new") {
		if (UserManagement::checkPermission(32)) {
			$project_result = ProjectEditor::createNewProject();
			header('Location: ./project.php?project_id='.$project_result['project_id']);
		} else {
			$message[] = array("Nur Administratoren dürfen Projekte anlegen!", 2);
			Message::setMessage($message);
			header('Location: ./login.php');
		}
    }

?>