<?php
	require_once('DbAccessor.php');
	require_once('TokenManager.php');

	// continue session
	session_start();

	$tokenManager = new TokenManager();
	$validToken = $tokenManager->verifyToken('form1');


	if ($validToken) {

		$dbAccessor = new DbAccessor();

		// get post parameters
		$user = $_POST['theuser'];
		$pass = $_POST['password'];
		$doodle = $_POST['doodle'];


		// add user
		$success = false;
		$userExists = $dbAccessor->userExists($user);
		if (!$userExists) {
			$success = $dbAccessor->addUser($user, $pass, $doodle);
			//$success = true;
		}


	}


if ($validToken) : ?>

<html>
	<head>
		<link rel="stylesheet" type="text/css" href="doodleStyle.css">
	</head>
	<body>
		<div id="titleBar">
			<div class="blue left"><img class="titleImg" src="doodle.svg"></div>
			<span id="vs">VS</span>
			<div class="red right"><img class="titleImg" src="password.svg"></div>
		</div>

		<div id="inside">
			<div class="liteBox">
				<?php
				// TITLE
				if ($userExists or !$success) {
					echo "<h1>Error</h1>";
				}
				else if ($success) {
					echo "<h1>Success</h1>";
				}
				?>
			</div>
			<div class="liteBox">
				<?php
				// DESCRIPTION
				echo "<p>";
				if ($userExists) {
					echo "An account already exists with that email address.";
				} else if (!$success) {
					echo "An error occurred with submitting your information to the database. Please contact mpl934@mocs.utc.edu for help.";
				} else if ($success) {
					echo "Congratulations! You have successfully registered!<br><br>You will be emailed in 1 week to login using the same credentials. Please do not write them down.";

					// remove this to avoid users logging in immediately
					echo "<a href='login.php'><button type='button'>Go to login</button></a>";
				}
				echo "</p>";
				?>
			</div>
		</div>
	</body>
</html>

<?php endif; ?>
