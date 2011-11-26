<?php 
/**
 * Crystal Newton
 * 
 * @author Matteo Vigoni <mattevigo@gmail.com>
 * @version 2.0.1
 * @copyright by Crystal Newton
 * 
 * Default template for Crystal Newton Official Website
 */
defined("_ENTRY_") or die("Restricted Access!");

define( "TEMPLATE", "default" );
define( "LOCAL_ROOT", WEB_ROOT . "/templates/default/" );
define( "LOCAL_VIEWS", TEMPLATES_DIR.DS.TEMPLATE.DS."views" );

import('core.site.Page');
import('core.site.Model');

import("includes.debug");

$model = $this->getModel();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title><?php echo SITE_NAME; ?></title>

<link type="text/css" rel="stylesheet" href="<?php echo LOCAL_ROOT; ?>css/style-default.css" />

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js" type="text/javascript"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/jquery-ui.min.js"></script>    

</head>
<body>
	
	<!-- Container -->
	<div id="container">
		<div id="main-container">
			<div id="main">
				<?php load_view( $model->getView(), &$model );?>
			</div>
			
			<div id="sidebar">
			
				<div class="widget"></div>
				
				<div class="widget"></div>
			</div>
			
			<div class="cleared"></div>
			
		</div>
		
		<div id="footer"></div>
	</div> 

</body>
</html>