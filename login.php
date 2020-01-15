<?php
	// Initialize
	ini_set('display_errors', 'On');
	error_reporting(E_ALL | E_STRICT);
	session_start();
	// Initialize error message variable
	$login_error = "";
	// Include application libraries
	require_once('./includes/ini.php');
	require_once('./includes/session.php');
	require_once('./includes/template.php');
	// Load session management
	// Session variables : USER_NAME, USER_PASSWORD
	$config_session = new session;
	if ($config_session->exist('USER_NAME'))
		$config_session->delete('USER_NAME');
	if ($config_session->exist('USER_PASSWORD'))
		$config_session->delete('USER_PASSWORD');
	// Load template management
	$config_page_template = new template;
	$config_content_template = new template;
	$config_popup_template = new template;
	// Load application configuration file
	$config_application = new ini;
	$config_application->open('./config/application.ini.php');
	$config_application->read();
	// Load language configuration file
	$config_language = new ini;
	$config_language->open('./config/language.ini.php');
	$config_language->read();
	// Initialize actions
	$action = '';
	if (isset($_POST['action']))
		$action = $_POST['action'];
	else
		if (isset($_GET['action']))
			$action = $_GET['action'];
	// Initialize templates
	$config_page_template->open('.'.$config_application->get('PATHS', 'TEMPLATES').'login.tpl');
	$config_page_template->set('template_title', $config_application->get('METATAGS', 'TITLE')."@".$config_application->get('METATAGS', 'DEPLOYMENT'));
	$config_page_template->set('meta_product', $config_application->get('METATAGS', 'PRODUCT'));
	$config_page_template->set('meta_version', $config_application->get('METATAGS', 'VERSION'));
	$config_page_template->set('meta_copyright', $config_application->get('METATAGS', 'COPYRIGHT'));
	$config_page_template->set('meta_author', $config_application->get('METATAGS', 'AUTHOR'));
	$config_page_template->set('meta_contact', $config_application->get('METATAGS', 'CONTACT'));
	$config_page_template->set('meta_distribution', $config_application->get('METATAGS', 'DISTRIBUTION'));
	$config_page_template->set('meta_robots', $config_application->get('METATAGS', 'ROBOTS'));
	$config_page_template->set('meta_content_type', $config_language->get('CONFIG', 'CHARSET'));
	$config_page_template->set('meta_content_language', $config_language->get('CONFIG', 'CODE'));
	$config_page_template->set('meta_content_style', $config_application->get('METATAGS', 'TYPE'));
	$config_page_template->set('meta_xua', $config_application->get('METATAGS', 'XUA'));
	$config_page_template->set('meta_css', $config_application->get('STYLE', 'CSS'));
	$config_page_template->set('menu_modal_window', '');
	$config_page_template->set('label_product', $config_application->get('METATAGS', 'DEPLOYMENT')."<span>".$config_application->get('METATAGS', 'PRODUCT')."</span>");
	$config_page_template->set('label_version', $config_application->get('METATAGS', 'TITLE')."&nbsp;v.".$config_application->get('METATAGS', 'VERSION'));
	$config_page_template->set('label_username', $config_language->get('STRING', 'USERNAME'));
	$config_page_template->set('label_password', $config_language->get('STRING', 'PASSWORD'));
	$config_page_template->set('label_login', $config_language->get('STRING', 'LOGIN'));
	if ($action=='error_user') {
		$config_popup_template->open('.'.$config_application->get('PATHS', 'TEMPLATES').'popup_alert.tpl');
		$config_popup_template->set('alert_message', $config_language->get('ERROR', 'INVALID_USERNAME'));
		$config_page_template->set('menu_modal_window', $config_popup_template->get());
	} else if ($action=='error_password') {
		$config_popup_template->open('.'.$config_application->get('PATHS', 'TEMPLATES').'popup_alert.tpl');
		$config_popup_template->set('alert_message', $config_language->get('ERROR', 'INVALID_PASSWORD'));
		$config_page_template->set('menu_modal_window', $config_popup_template->get());
	}
	echo $config_page_template->get();
?>
