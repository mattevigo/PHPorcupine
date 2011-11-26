<?php
/**
 * Default page for admin.php
 */
defined("_ENTRY_") or die("Restricted Access!");
defined("_ADMIN_") or header("Location:login.php");

import("core.site.Page");
import("core.site.Model");
import("core.framework.User");

$user = User::getUserFromId(getDB(), $_SESSION['uid']);

//echo "Default admin.php page (panel)<br />";
$model = new Model();
$panel_page = new Page(SITE_NAME . " - Admin (" . $user->getUsername() . ")", $model);
$panel_page->setTemplate('admin');

//var_dump($panel_page);
//load_template('admin', &$panel_page);
$panel_page->display();
?>