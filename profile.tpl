<h1>
	[@template_title]
</h1>
<div class='content-wrapper'>
	<div class='content'><div>[@label_user_name]</div><div>:</div><div>[@user_name]</div></div>
	<div class='content'><div>[@label_last_login]</div><div>:</div><div>[@last_login]</div></div>
	<div class='content'>&nbsp;</div>
	<div class='content'><div>[@label_password_change]</div><div>:</div>
		<div>
			<form method='post' action='index.php'>
				<input type='hidden' name='action' value='user'>
				<input type='password' placeholder='[@label_password_old]' name='login_password_old'>
				&nbsp;
				<input type='password' placeholder='[@label_password_new]' name='login_password_new'>
				<br />
				<input type='submit' value='[@label_save]'>
			</form>
		</div>
	</div>
</div>
