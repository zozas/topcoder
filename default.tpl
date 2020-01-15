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
		<div class='container'>
			<div class='nav'>
				<div class='title'>
					<div>[@menu_product_title]</div>
				</div>
				<br />
				[@menu_product_version]
				<br />
				<br />
				[@menu_configuration]
				<br />
				<br />				
				[@menu_user_profile]
				<br />
				[@menu_status]
				<br />
				[@menu_logout]
			</div>
			<div class='article'>
			  [@page_content]
			</div>
		</div>
	</body>
</html>
