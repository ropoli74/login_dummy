<?php require_once(dirname(__FILE__).'/classes/User.php'); ?>
<?php $is_logged_in = User::is_logged_in(); ?>
<html>
	<head>
		<title>LOGIN DUMMY</title>
	</head>
	<body>
		<h1>Willkommen</h1>
		<?php if ($is_logged_in): ?>
			<?php $user = User::factory_from_session(); ?>
			Hallo <?php echo $user->get_firstname(); ?>, willkommen zurück!<br />
			Hier gehts zur <a href="secured_site.php">sicheren Seite</a>.
		<?php else: ?>
			Bitte melden dich an:<br /><br />

			<form action="login.php" method="post">
				E-Mail:<br />
				<input type="text" name="username"><br />
				Passwort:<br />
				<input type="password" name="password"><br /><br />
				Anmeldung speichern <input type="checkbox" name="remember" value="1"><br /><br />
				<input type="submit" value="Anmelden">
			</form>
		<?php endif; ?>
	</body>
</html>