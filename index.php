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
	require_once('./includes/files.php');
	// Load session management
	// Session variables : USER_NAME, USER_PASSWORD
	$config_session = new session;
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
	// Check for login POST variables or current session login variables
	$login_username = '';
	if (isset($_POST['login_username'])) {
		$login_username = $_POST['login_username'];
		$config_session->set('USER_NAME', $login_username);
	}
	if ($config_session->exist('login_username'))
		$login_username = $config_session->get('USER_NAME');
	$login_password = '';
	if (isset($_POST['login_password'])) {
		$login_password = $_POST['login_password'];
		$config_session->set('USER_PASSWORD', $login_password);
	}
	if ($config_session->exist('login_password'))
		$login_password = $config_session->get('USER_PASSWORD');
	// Login validate or log out
	$config_user = new ini;
	if ($config_user->open('.'.$config_application->get('PATHS', 'USERS').$config_session->get('USER_NAME').'.ini.php') && $config_user->read()) {
		if (!(hash($config_application->get('SECURITY', 'HASH'), $config_session->get('USER_PASSWORD')) == $config_user->get('VALIDATION','USER_PASSWORD'))) {
			$config_log = new ini;
			$config_log->open('.'.$config_application->get('LOG', 'INFO'));
			$config_log->read();
			$config_log->set('LOGIN', strtotime('now'), $config_language->get('ERROR', 'INVALID_PASSWORD')." [".$login_username."@".$login_password."/".$_SERVER['REMOTE_ADDR']."]");
			$config_log->write();
			$config_session->erase_session();
			header("Location: login.php?action=error_password");
			die();
		} else {
			if (isset($_POST['login_username']) && isset($_POST['login_password'])) {
				$config_user->set('LOG', 'LAST_LOGIN', strtotime('now'));
				$config_user->write();
				$config_log = new ini;
				$config_log->open('.'.$config_application->get('LOG', 'INFO'));
				$config_log->read();
				$config_log->set('LOGIN', strtotime('now'), $config_language->get('STRING', 'LOGIN')." [".$login_username."/".$_SERVER['REMOTE_ADDR']."]");
				$config_log->write();
			}
		}
	} else {
		$config_session->erase_session();
			$config_log = new ini;
			$config_log->open('.'.$config_application->get('LOG', 'INFO'));
			$config_log->read();
			$config_log->set('LOGIN', strtotime('now'), $config_language->get('ERROR', 'INVALID_USERNAME')." [".$login_username."@".$login_password."/".$_SERVER['REMOTE_ADDR']."']");
			$config_log->write();
			$config_session->erase_session();
			header("Location: login.php?action=error_user");
			die();
	}
	// Initialize actions
	$action = '';
	if (isset($_POST['action']))
		$action = $_POST['action'];
	else
		if (isset($_GET['action']))
			$action = $_GET['action'];
	// Initialize templates
	$config_page_template->open('.'.$config_application->get('PATHS', 'TEMPLATES').'default.tpl');
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
	$config_page_template->set('menu_product_title', $config_application->get('METATAGS', 'DEPLOYMENT')."<span>".$config_application->get('METATAGS', 'PRODUCT')."</span>");
	$config_page_template->set('menu_product_version', "<a href='index.php'>".$config_language->get('STRING', 'VERSION')."&nbsp;".$config_application->get('METATAGS', 'VERSION')."</a>");
	$config_page_template->set('menu_configuration', "<a href='index.php?action=configuration'>".$config_language->get('STRING', 'CONFIGURATION')."</a>");
	$config_page_template->set('menu_user_profile', "<a href='index.php?action=user'>".$config_language->get('STRING', 'PROFILE')."</a>");
	$config_page_template->set('menu_status', "<a href='index.php?action=status'>".$config_language->get('STRING', 'STATUS')."</a>");
	$config_page_template->set('menu_logout', "<a href='login.php'>".$config_language->get('STRING', 'LOGOUT')."</a>");
	$config_page_template->set('menu_modal_window', '');
	$config_page_template->set('page_content', '');
	$config_content_template->open('.'.$config_application->get('PATHS', 'TEMPLATES').'blank.tpl');
	if ($action=='status') {
		$config_content_template->open('.'.$config_application->get('PATHS', 'TEMPLATES').'status.tpl');
		$config_content_template->set('template_title', $config_language->get('STRING', 'STATUS'));
		$config_content_template->set('label_document_root', $config_language->get('STRING', 'DOCUMENT_ROOT'));
		$config_content_template->set('document_root', $_SERVER['DOCUMENT_ROOT']);
		$config_content_template->set('label_http_host', $config_language->get('STRING', 'HTTP_HOST'));
		$config_content_template->set('http_host', $_SERVER['HTTP_HOST']);
		$config_content_template->set('label_server_signature', $config_language->get('STRING', 'SERVER_SIGNATURE'));
		$config_content_template->set('server_signature', $_SERVER['SERVER_SIGNATURE']);
		$config_content_template->set('label_http_user_agent', $config_language->get('STRING', 'HTTP_USER_AGENT'));
		$config_content_template->set('http_user_agent', $_SERVER['HTTP_USER_AGENT']);
		$config_files = new files;
		$config_content_template->set('label_free_space', $config_language->get('STRING', 'FREE_SPACE'));
		$config_content_template->set('free_space', $config_files->convert(disk_free_space('/'))." (".number_format(((disk_free_space('/')) / disk_total_space('/') * 100), 2, '.', ',')."%)");
		$config_content_template->set('label_used_space', $config_language->get('STRING', 'USED_SPACE'));
		$config_content_template->set('used_space', $config_files->convert(disk_total_space('/') - disk_free_space('/'))." (".number_format(((disk_total_space('/') - disk_free_space('/')) / disk_total_space('/') * 100), 2, '.', ',')."%)");
		$config_content_template->set('label_total_space', $config_language->get('STRING', 'TOTAL_SPACE'));
		$config_content_template->set('total_space', $config_files->convert(disk_total_space('/')));
		$config_page_template->set('page_content', $config_content_template->get());
	} else if ($action=='user') {
		$login_password_old = '';
		if (isset($_POST['login_password_old']))
			$login_password_old = $_POST['login_password_old'];
		else
			if (isset($_GET['login_password_old']))
				$login_password_old = $_GET['login_password_old'];
		$login_password_new = '';
		if (isset($_POST['login_password_new']))
			$login_password_new = $_POST['login_password_new'];
		else
			if (isset($_GET['login_password_new']))
				$login_password_new = $_GET['login_password_new'];
		$config_content_template->open('.'.$config_application->get('PATHS', 'TEMPLATES').'profile.tpl');
		$config_content_template->set('template_title', $config_language->get('STRING', 'PROFILE'));
		$config_content_template->set('label_user_name', $config_language->get('STRING', 'USERNAME'));
		$config_content_template->set('user_name', $config_user->get('VALIDATION', 'USER_NAME'));
		$config_content_template->set('label_last_login', $config_language->get('STRING', 'LAST_LOGIN'));
		$config_content_template->set('last_login', gmdate($config_application->get('STYLE', 'DATETIME'), $config_user->get('LOG', 'LAST_LOGIN'))." (".$config_user->get('LOG', 'LAST_LOGIN').")");
		$config_content_template->set('label_password_change', $config_language->get('STRING', 'PASSWORD_CHANGE'));
		$config_content_template->set('label_save', $config_language->get('STRING', 'SAVE'));
		$config_content_template->set('label_password_old', $config_language->get('STRING', 'PASSWORD_OLD'));
		$config_content_template->set('label_password_new', $config_language->get('STRING', 'PASSWORD_NEW'));
		if ($login_password_old != '' && $login_password_new != '' && $login_password_old != $login_password_new) {
			$config_user->set('VALIDATION', 'USER_PASSWORD', hash($config_application->get('SECURITY', 'HASH'), $login_password_new));
			$config_user->write();
			$config_session->set('USER_PASSWORD', $login_password_new);
			$config_log = new ini;
			$config_log->open('.'.$config_application->get('LOG', 'INFO'));
			$config_log->read();
			$config_log->set('LOGIN', strtotime('now'), $config_language->get('STRING', 'PASSWORD_CHANGE')." [".$config_session->get('USER_NAME')."/".$_SERVER['REMOTE_ADDR']."]");
			$config_log->write();
			$config_popup_template->open('.'.$config_application->get('PATHS', 'TEMPLATES').'popup_alert.tpl');
			$config_popup_template->set('alert_message', $config_language->get('MESSAGE', 'SUCCESS_PASSWORD_CHANGE'));
			$config_page_template->set('menu_modal_window', $config_popup_template->get());
		}
		$config_page_template->set('page_content', $config_content_template->get());

		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
	} else if ($action=='configuration') {
		$config_content_template->open('.'.$config_application->get('PATHS', 'TEMPLATES').'text.tpl');
		$config_content_template->set('template_title', $config_language->get('STRING', 'CONFIGURATION'));
		$config_page_template->set('text', "
			<ul class='tree'><font color='green'>Feature Model Configuration</font>
				<ul><font color='green'>Infrastructure</font>
					<ul><font color='green'>Network</font>
						<ul><font color='green'>Cable</font>
							<ul><font color='green'>Public network</font>
								<ul><font color='green'>Broadband</font></ul>
								<ul><font color='red'>P2P</font></ul>
							</ul>
							<ul><font color='red'>Private network</font>
								<ul><font color='red'>VPN</font></ul>
								<ul><font color='red'>LAN</font></ul>
							</ul>
						</ul>
						<ul><font color='red'>Wireless</font>
							<ul><font color='red'>Radio</font>
								<ul><font color='red'>Wifi</font></ul>
								<ul><font color='red'>Bluetooth</font></ul>
							</ul>
							<ul><font color='red'>Cellular</font>
								<ul><font color='red'>2G</font></ul>
								<ul><font color='red'>3G</font></ul>
								<ul><font color='red'>4G</font></ul>
							</ul>
						</ul>
					</ul>
					<ul><font color='green'>Storage</font>
						<ul><font color='red'>Network Attach Storage</font>
							<ul><font color='red'>Distributed nodes</font>
								<ul><font color='red'>Shared-disk file system</font></ul>
								<ul><font color='red'>Distributed file system</font></ul>
							</ul>
							<ul><font color='red'>Scale storage</font>
								<ul><font color='red'>Scale-up storage</font></ul>
								<ul><font color='red'>Scale out storage</font></ul>
							</ul>
						</ul>
						<ul><font color='green'>Direct Attach Storage</font>
							<ul><font color='green'>Volatile</font>
								<ul><font color='red'>ATA</font></ul>
								<ul><font color='green'>SATA</font></ul>
								<ul><font color='red'>eSATA</font></ul>
								<ul><font color='red'>NVMe</font></ul>
								<ul><font color='red'>SCSI</font></ul>
								<ul><font color='red'>SAS</font></ul>
								<ul><font color='green'>USB</font></ul>
								<ul><font color='red'>IEEE1394</ul>
								<ul><font color='red'>Fibre channel</ul>
							</ul>
							<ul><font color='red'>Non volatile</font>
								<ul><font color='red'>SRAM</font></ul>
								<ul><font color='red'>DRAM</font></ul>
							</ul>
						</ul>
					</ul>
					<ul><font color='green'>Data sources</font>
						<ul><font color='red'>Hardware</font>
							<ul><font color='red'>Sensors</font>
								<ul><font color='red'>Acoustic, sound, vibration</font></ul>
								<ul><font color='red'>Automotive, transportation</font></ul>
								<ul><font color='red'>Chemical</font></ul>
								<ul><font color='red'>Electric current, electric potential, megnetic, radio</font></ul>
								<ul><font color='red'>Flow, fluid velocity</font></ul>
								<ul><font color='red'>Ionizing radiation, subatomic particles</font></ul>
								<ul><font color='red'>Navigation instruments</font></ul>
								<ul><font color='red'>Position, angle, displacement, distance, speed, acceleration</font></ul>
								<ul><font color='red'>Optical, light, imaging, photon</font></ul>
								<ul><font color='red'>Pressure</font></ul>
							</ul>
							<ul><font color='red'>Embedded</font>
								<ul><font color='red'>RFID tag</font></ul>
								<ul><font color='red'>Embedded mobile</font></ul>
								<ul><font color='red'>Mobile phone</font></ul>
								<ul><font color='red'>Constrained device with radio</font></ul>
							</ul>
							<ul><font color='red'>Actuators</font>
								<ul><font color='red'>Electric linear</font></ul>
								<ul><font color='red'>Electric rotary</font></ul>
								<ul><font color='red'>Fluid power linear</font></ul>
								<ul><font color='red'>Fluid power rotary</font></ul>
								<ul><font color='red'>Linear chain</font></ul>
								<ul><font color='red'>Manual linear</font></ul>
								<ul><font color='red'>Manual rotary</font></ul>
							</ul>
						</ul>
						<ul><font color='green'>Software</font>
							<ul><font color='red'>Applications</font>
								<ul><font color='red'>Internal logs</font></ul>
								<ul><font color='red'>External logs</font></ul>
							</ul>
							<ul><font color='red'>Agents</font>
								<ul><font color='red'>Monitors</font></ul>
								<ul><font color='red'>Bulk collectors</font></ul>
							</ul>
							<ul><font color='green'>Web data</font>
								<ul><font color='green'>Scrappers</font></ul>
								<ul><font color='green'>Crawlers</font></ul>
							</ul>
						</ul>
					</ul>
				</ul>
				<ul><font color='green'>Services</font>
					<ul><font color='green'>Collecting</font>
						<ul><font color='red'>Streaming</font>
							<ul><font color='red'>Real time</font>
								<ul><font color='red'>Real time processing</font></ul>
								<ul><font color='red'>Stream processing</font></ul>
							</ul>
							<ul><font color='red'>Batch</font>
								<ul><font color='red'>Periodic batch processing</font></ul>
								<ul><font color='red'>Non-periodic batch processing</font></ul>
							</ul>
						</ul>
						<ul><font color='green'>Static</font>
							<ul><font color='red'>Push/Pull</font>
								<ul><font color='red'>Push-based</font></ul>
								<ul><font color='red'>Pull-based</font></ul>
							</ul>
							<ul><font color='green'>On demand</font>
								<ul><font color='green'>Search-in-place based</font></ul>
								<ul><font color='red'>Pull on demand</font></ul>
							</ul>
						</ul>
					</ul>
					
					
					
					
					
					
					
					
					
					
					<ul>Storing
						<ul>Relational database
							<ul>RDBMS
								<ul>Client-server model</ul>
								<ul>App-resident SQL libraries</ul>
								<ul>Highly distributed SQL environment</ul>
							</ul>
							<ul>Hierarchical DBMS
								<ul>Tree structure</ul>
								<ul>XML</ul>
							</ul>
							<ul>Object-Oriented DB
								<ul>Complex object model</ul>
								<ul>Semantic data model</ul>
							</ul>
							<ul>Network DBMS
								<ul>Graph structure</ul>
								<ul>Navigational structure</ul>
							</ul>
						</ul>
						<ul>Non relational database
							<ul>Key-Value
								<ul>Key-Value eventually consistent</ul>
								<ul>Key-Value ordered</ul>
								<ul>Key-Value RAM</ul>
								<ul>Key-Value rotating disk</ul>
							</ul>
							<ul>Column
								<ul>Row-oriented</ul>
								<ul>Column-oriented</ul>
							</ul>
							<ul>Document
								<ul>Collections organization</ul>
								<ul>Tags and non-visible metadata organization</ul>
								<ul>Directory hierarchies organization</ul>
							</ul>
							<ul>Graph
								<ul>Single-graph</ul>
								<ul>Multi-graph</ul>
								<ul>Hyper-graph</ul>
							</ul>
						</ul>
					</ul>
					<ul>Preprocessing
						<ul>Data transfer
							<ul>Information extraction
								<ul>Multiple file formats</ul>
								<ul>Arcane formats extraction from legacy systems</ul>
							</ul>
							<ul>Load
								<ul>One historic load</ul>
								<ul>Periodic data ingestion</ul>
								<ul>Retention period of data</ul>
								<ul>Data replication for critical data</ul>
							</ul>
							<ul>Distribute
								<ul>Data center distribution</ul>
								<ul>Disaster recovery</ul>
							</ul>
						</ul>
						<ul>Data compression
							<ul>Lossless
								<ul>Run-length</ul>
								<ul>Huffman</ul>
								<ul>Lempel Ziv</ul>
							</ul>
							<ul>Lossy
								<ul>JPEG</ul>
								<ul>MPEG</ul>
								<ul>MP3</ul>
							</ul>
						</ul>
						<ul>Data preparation
							<ul> OR Data structure
								<ul>Structured data</ul>
								<ul>Semi-structured data</ul>
								<ul>Unstructured data</ul>
							</ul>
							<ul> OR Quality
								<ul>Validity</ul>
								<ul>Accuracy</ul>
								<ul>Completeness</ul>
								<ul>Consistency</ul>
								<ul>Uniformity</ul>
								<ul>Integrity</ul>
							</ul>
							<ul>Cleaning
								<ul>Statistical cleaning</ul>
								<ul>Database cleaning methods</ul>
								<ul>Data normalization</ul>
							</ul>
							<ul>Replication
								<ul>Operational synchronization</ul>
								<ul>Analytical synchronization</ul>
							</ul>
							<ul>Transformation
								<ul>Map</ul>
								<ul>Reduce</ul>
							</ul>
						</ul>
					</ul>
					<ul>Processing
						<ul>Analytics
							<ul>Processing model
								<ul>Real-time analysis</ul>
								<ul>Near-time analysis</ul>
								<ul>Batch analysis</ul>
							</ul>
							<ul>Analytics model
								<ul>Descriptive</ul>
								<ul>Predictive</ul>
								<ul>Prescriptive</ul>
							</ul>
							<ul>Analytics services
								<ul>Simulation</ul>
								<ul>Optimization</ul>
							</ul>
						</ul>
						<ul>Analytics techniques
							<ul>Machine learning
								<ul>Neural networks</ul>
								<ul>SVM</ul>
								<ul>Naive bayes</ul>
								<ul>Random forests</ul>
								<ul>Genetic algorithms</ul>
								<ul>LDA</ul>
								<ul>K-nearest neighbor</ul>
							</ul>
							<ul>Regression
								<ul>Linear</ul>
								<ul>Decision trees</ul>
								<ul>Time-series</ul>
							</ul>
							<ul>Other
								<ul>Expert systems</ul>
								<ul>Statistics</ul>
							</ul>
						</ul>
					</ul>
					<ul>Interface
						<ul>Visualization
							<ul>Interaction
								<ul>Archivist</ul>
								<ul>End user</ul>
								<ul>Data export</ul>
							</ul>
							<ul>Representation
								<ul>1D/Linear</ul>
								<ul>2D/Planar</ul>
								<ul>3D/Volumetric</ul>
								<ul>Temporal</ul>
								<ul>nD/Multidimensional</ul>
								<ul>Tree/Hierarchucal</ul>
								<ul>Node/Link/Matrix</ul>
							</ul>
						</ul>
						
						
						
						
						
						
						
						
						
						<ul><font color='green'>Platform</font>
							<ul><font color='red'>Desktop</font>
								<ul><font color='red'>Linux</font></ul>
								<ul><font color='red'>OSX</font></ul>
								<ul><font color='red'>Windows</font></ul>
							</ul>
							<ul><font color='red'>Mobile</font>
								<ul><font color='red'>Android</font></ul>
								<ul><font color='red'>iOS</font></ul>
								<ul><font color='red'>Windows mobile</font></ul>
							</ul>
							<ul><font color='green'>Web-based</font>
							</ul>
						</ul>
					</ul>
				</ul>
			</ul>");
		$config_page_template->set('page_content', $config_content_template->get());
	} else {
		/* ==========================================================================================
		 * SCRAP USER INFO FROM TOPCODER
		 * ==========================================================================================
		require_once('./includes/scrapper.php');
		$config_scrapper = new scrapper;
		$conn = mysqli_connect("127.0.0.1", "root", "kokoriko", "topcoder");
		if ($conn) {
			$sql = "SELECT * FROM users WHERE json_info='';";
			$result = $conn->query($sql);
			$i = 0;
			while($row = $result->fetch_assoc()) {
				echo 'Processing '.$i.' ...';
				$content = $config_scrapper->get('http://api.topcoder.com/v2/users/'.$row['reg_name'], 'HTML');
				$sql = "UPDATE users SET json_info='".$content."' WHERE reg_name='".$row['reg_name']."';";
				if ($conn->query($sql) === TRUE) {
					echo 'OK!<br>';
				} else {
					echo "Error updating record ".$i.": " . $conn->error;
				}
				$i = $i + 1;
			}
			$config_page_template->set('page_content', '');
		}		
		========================================================================================== */
		


		/* ==========================================================================================
		 * ORGANIZE SCRAPPED USER INFO FROM TOPCODER
		 * ==========================================================================================
		$conn = mysqli_connect("127.0.0.1", "root", "kokoriko", "topcoder");
		if ($conn) {
			$sql = "SELECT * FROM users;";
			$result = $conn->query($sql);
			while($row = $result->fetch_assoc()) {
				$user_data = json_decode($row['json_info'], true);
				for ($i = 0; $i <= 11; $i++) {
					if (isset($user_data['ratingSummary'][$i]['name'])) {
						if ($user_data['ratingSummary'][$i]['rating']!=0) {
							if ($user_data['ratingSummary'][$i]['name'] == 'Algorithm') {
								$sql = "UPDATE `users` SET `Algorithm`='".$user_data['ratingSummary'][$i]['rating']."' WHERE `reg_name`='".$row['reg_name']."';";
								if ($conn->query($sql) === TRUE) { echo 'OK!<br>'; }
							} else
							if ($user_data['ratingSummary'][$i]['name'] == 'Architecture') {
								$sql = "UPDATE `users` SET `Architecture`='".$user_data['ratingSummary'][$i]['rating']."' WHERE `reg_name`='".$row['reg_name']."';";
								if ($conn->query($sql) === TRUE) { echo 'OK!<br>'; }
							} else
							if ($user_data['ratingSummary'][$i]['name'] == 'Assembly') {
								$sql = "UPDATE `users` SET `Assembly`='".$user_data['ratingSummary'][$i]['rating']."' WHERE `reg_name`='".$row['reg_name']."';";
								if ($conn->query($sql) === TRUE) { echo 'OK!<br>'; }
							} else
							if ($user_data['ratingSummary'][$i]['name'] == 'Conceptualization') {
								$sql = "UPDATE `users` SET `Conceptualization`='".$user_data['ratingSummary'][$i]['rating']."' WHERE `reg_name`='".$row['reg_name']."';";
								if ($conn->query($sql) === TRUE) { echo 'OK!<br>'; }
							} else
							if ($user_data['ratingSummary'][$i]['name'] == 'Content Creation') {
								$sql = "UPDATE `users` SET `Content_Creation`='".$user_data['ratingSummary'][$i]['rating']."' WHERE `reg_name`='".$row['reg_name']."';";
								if ($conn->query($sql) === TRUE) { echo 'OK!<br>'; }
							} else
							if ($user_data['ratingSummary'][$i]['name'] == 'Design') {
								$sql = "UPDATE `users` SET `Design`='".$user_data['ratingSummary'][$i]['rating']."' WHERE `reg_name`='".$row['reg_name']."';";
								if ($conn->query($sql) === TRUE) { echo 'OK!<br>'; }
							} else
							if ($user_data['ratingSummary'][$i]['name'] == 'Development') {
								$sql = "UPDATE `users` SET `Development`='".$user_data['ratingSummary'][$i]['rating']."' WHERE `reg_name`='".$row['reg_name']."';";
								if ($conn->query($sql) === TRUE) { echo 'OK!<br>'; }
							} else
							if ($user_data['ratingSummary'][$i]['name'] == 'Marathon Match') {
								$sql = "UPDATE `users` SET `Marathon_Match`='".$user_data['ratingSummary'][$i]['rating']."' WHERE `reg_name`='".$row['reg_name']."';";
								if ($conn->query($sql) === TRUE) { echo 'OK!<br>'; }
							} else
							if ($user_data['ratingSummary'][$i]['name'] = 'RIA Build') {
								$sql = "UPDATE `users` SET `RIA_Build`='".$user_data['ratingSummary'][$i]['rating']."' WHERE `reg_name`='".$row['reg_name']."';";
								if ($conn->query($sql) === TRUE) { echo 'OK!<br>'; }
							} else
							if ($user_data['ratingSummary'][$i]['name'] == 'Specification') {
								$sql = "UPDATE `users` SET `Specification`='".$user_data['ratingSummary'][$i]['rating']."' WHERE `reg_name`='".$row['reg_name']."';";
								if ($conn->query($sql) === TRUE) { echo 'OK!<br>'; }
							} else
							if ($user_data['ratingSummary'][$i]['name'] == 'Test Scenarios') {
								$sql = "UPDATE `users` SET `Test_Scenarios`='".$user_data['ratingSummary'][$i]['rating']."' WHERE `reg_name`='".$row['reg_name']."';";
								if ($conn->query($sql) === TRUE) { echo 'OK!<br>'; }
							} else
							if ($user_data['ratingSummary'][$i]['name'] == 'Test Suites') {
								$sql = "UPDATE `users` SET `Test_Suites`='".$user_data['ratingSummary'][$i]['rating']."' WHERE `reg_name`='".$row['reg_name']."';";
								if ($conn->query($sql) === TRUE) { echo 'OK!<br>'; }
							} else
							if ($user_data['ratingSummary'][$i]['name'] == 'UI Prototypes') {
								$sql = "UPDATE `users` SET `UI_Prototypes`='".$user_data['ratingSummary'][$i]['rating']."' WHERE `reg_name`='".$row['reg_name']."';";
								if ($conn->query($sql) === TRUE) { echo 'OK!<br>'; }
							}
							echo $sql;
						}
					}
				}
			}
		}

-Algorithm
-Architecture
-Assembly
-Conceptualization
-Content_Creation
-Design
-Development
-Marathon_Match
-RIA_Build
-Specification
-Test Scenarios
-Test Suites
-UI Prototypes

========================================================================================== */







		
		
		
		/*
		require_once('./includes/scrapper.php');
		$config_scrapper = new scrapper;
		
		$content = $config_scrapper->get('http://dblp.uni-trier.de/search/publ/api?q=TEST&h=1000&format=xml', 'HTML');
		
		print_r($content);

		print_r(array_values(array_unique($matches[0])));
		*/

		//if (filter_var($url, FILTER_VALIDATE_URL) === TRUE) {
			//die('Not a valid URL');
		//}

		//echo "<br><br>";
		//echo implode("<br>", array_values(array_unique($matches[0])));
		
		//$config_page_template->set('page_content', '');
		
		//require_once('./includes/scrapper.php');
		//$config_scrapper = new scrapper;
		//$config_page_template->set('menu_modal_window', $config_scrapper->get('https://uowm.gr/en/', 'HTML'));
		
		//1.https://www.booking.com/searchresults.html?ss=agnanti
		//2.<div class="destination_name"><a class="item_name_link" href="/searchresults.el.html?label=gen173nr-1DCAQoggJCDnNlYXJjaF9hZ25hbnRpSDNiBW5vcmVmaFyIAQGYAQjCAQN4MTHIAQ_YAQPoAQH4AQOSAgF5qAID;sid=41083c4c6249cb1b8bb19c6a6f5bfcf4;class_interval=1;dest_id=900039557;dest_type=city;group_adults=2;group_children=0;highlighted_hotels=95484;label_click=undef;mih=0;no_rooms=1;offset=0;qrhpp=14825df473f468a43554c5710cf8dbd3-hotel-2;room1=A%2CA;sb_price_type=total;search_selected=0;ss=agnanti;ssb=empty;srpos=3;origin=search">Paros Agnanti Hotel</a></div>
		//  pairno to link
		//3.
		//
		
		//https://www.booking.com/searchresults.html?label=gen173nr-1DCAQoggJCDnNlYXJjaF9hZ25hbnRpSDNiBW5vcmVmaFyIAQGYATHCAQN4MTHIAQ_YAQPoAQH4AQKSAgF5qAID;sid=41083c4c6249cb1b8bb19c6a6f5bfcf4;class_interval=1;dest_id=-819398;dest_type=city;dtdisc=0;group_adults=2;group_children=0;highlighted_hotels=358874;inac=0;index_postcard=0;label_click=undef;mih=0;no_rooms=1;offset=0;postcard=0;qrhpp=422f5d8ab832c01c1ef3415adadf2458-hotel-0;room1=A%2CA;sb_price_type=total;search_selected=0;ss=agnanti;ss_all=0;ssb=empty;sshis=0;origin=search;srpos=1
		
		//https://www.booking.com/searchresults.html?label=gen173nr-1DEgZzZWFyY2goggJCAlhYSDNiBW5vcmVmaFyIAQGYATHCAQN4MTHIAQ_YAQPoAQH4AQKSAgF5qAID;sid=41083c4c6249cb1b8bb19c6a6f5bfcf4;dest_id=-825694;dest_type=city;highlighted_hotels=428850;qrhpp=c78cace823224a731035314bc8d993f6-hotel-1;search_selected=0;ss=agnanti;srpos=2;origin=search
		
		//searchresults.el.html?label=gen173nr-1DEgZzZWFyY2goggJCAlhYSDNiBW5vcmVmaFyIAQGYATHCAQN4MTHIAQ_YAQPoAQH4AQKSAgF5qAID;sid=41083c4c6249cb1b8bb19c6a6f5bfcf4;class_interval=1;dest_id=-825694;dest_type=city;dtdisc=0;group_adults=2;group_children=0;highlighted_hotels=428850;inac=0;index_postcard=0;label_click=undef;mih=0;no_rooms=1;offset=0;postcard=0;raw_dest_type=city;room1=A%2CA;sb_price_type=total;ss=agnanti;ss_all=0;ssb=empty;sshis=0;=;lang=el;lang_click=top;cdl=en-us
		
		//https://www.booking.com/hotel/gr/agnantiparga.html
		

		
		
		
		
		
		
		
	}
	
	

	
	
	echo $config_page_template->get();
?>

