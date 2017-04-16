<?php
	// CHANGE HERE
	$contact_store = "/Users/gilesholdsworth/Dropbox/programming/github/PHP-contact/contacts/";
	$required_fields = array("name", "email", "subject", "message");
	// TO HERE as necessary
	$was_error = false;
	$error_text = "";
	$was_success = false;
	$success_text = "";
	$name = "";
	$email = "";
	$subject = "";
	$message = "";
	$file_text = "";
	function error($text) {
		global $was_error, $error_text;
		$was_error = true;
		$error_text .= "Error: " . $text . "<br>\n";
	}
	function success($text) {
		global $was_success, $success_text;
		$was_success = true;
		$success_text .= "Success: " . $text . "<br>\n";
	}
	function posts_are_set($post, $names) {//return true if string variables are set and have some characters, false if not
		$return_bool = false;
		foreach ($names as $name) {
			$return_bool = (isset($post[$name]) && strlen($post[$name]) > 0);
			if (!$return_bool) {
				error(ucfirst($name) . " field not filled out.");
				break;
			}
		}
		return $return_bool;
	}
	function format_file_text($name, $email, $subject, $message) {
		return $name . "\n" . $email . "\n" .  $subject . "\n" . $message;
	}
	if (posts_are_set($_POST, $required_fields)) {
		$name = $_POST["name"];
		$email = $_POST["email"];
		$subject = $_POST["subject"];
		$message = $_POST["message"];
		
		$file_text = format_file_text($name, $email, $subject, $message);
		$file_name = tempnam($contact_store, "contact-");
		if ($file_name !== false && file_put_contents($file_name, $file_text) =!== false) {
				throw new Exception("error");
			}
			success("Message sent.");
		} else {
			error("Couldn't complete contacting process, please try again later.");
		}

	}
?>

<!DOCTYPE html>
<html>
	<head></head>
	<body>

		<form action="" method="POST">
			<p id="error-message" style="color: red">
				<?php
					if ($was_error) {
						echo $error_text;
					}
				?>
			</p>
			<p id="success-message" style="color: green">
				<?php
					if ($was_success) {
						echo $success_text;
					}
				?>
			</p>
			<label for="name">Name</label><br>
			<input type="text" id="name" name="name" placeholder="Your name...">
			<br>
			<label for="email">Email</label><br>
			<input type="text" id="email" name="email" placeholder="Your email...">
			<br>
			<label for="subject">Subject</label><br>
			<input type="text" id="subject" name="subject" placeholder="Subject...">
			<br>
			<label for="subject">Message</label><br>
			<textarea id="message" name="message" placeholder="Your message..." rows="8" cols="50"></textarea>
			<br>
			<input type="submit" value="Submit">
		</form>
	</body>
</html>

