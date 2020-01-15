<!DOCTYPE html>
<html>
	<head>
		<title>[@template_title]</title>
		<meta name='product'					content='[@meta_product]' />
		<meta name='version'					content='[@meta_version]' />
		<meta name='copyright'					content='[@meta_copyright]' />
		<meta name='author'						content='[@meta_author]' />
		<meta name='contact'					content='[@meta_contact]' />
		<meta name='distribution'				content='[@meta_distribution]' />
		<meta name='robots'						content='[@meta_robots]' />
		<meta http-equiv='Content-Type'			content='[@meta_content_type]'/>
		<meta http-equiv='content-language'		content='[@meta_content_language]' />
		<meta http-equiv='content-style-type'	content='[@meta_content_style]' />
		<meta http-equiv='X-UA-Compatible'		content='[@meta_xua]'/>
		<link rel='stylesheet' type='text/css' href='[@meta_css]' media='all' />
	</head>
	<body>
		[@menu_modal_window]
		<div class='body'></div>
		<div class='grad'></div>
		<div class='header'>
			<div>[@label_product]</div>
		</div>
		<div class='footer'>[@label_version]</div>
		<div class='login'>
			<form method='post' action='index.php'>
				<input type='text' placeholder='[@label_username]' name='login_username'><br>
				<input type='password' placeholder='[@label_password]' name='login_password'><br>
				<input type='submit' value='[@label_login]'>
			</form>
		</div>
	</body>
</html>
