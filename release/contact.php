<?php
	// CHANGE BETWEEN HERE
	$contact_store = "contacts/"; //Location to store contact files, full path or DOC_ROOT prepended if you want, DIRECTORY MUST EXIST
	$required_fields = array("name", "email", "subject", "message");//All fields are required, optional fields should be included in this list, and send a dummy value if empty
	$file_field_separator = "\n"; //This string will be inserted between each field in the file produced
	// AND HERE
	$was_error = false;
	$error_text = "";
	$was_success = false;
	$success_text = "";
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
	function values_are_set($dict, $names) {
		$return_bool = false;
		foreach ($names as $name) {
			$return_bool = (isset($dict[$name]) && strlen($dict[$name]) > 0);
			if (!$return_bool) {
				error(ucfirst($name) . " field not filled out.");
				break;
			}
		}
		return $return_bool;
	}
	function format_file_text($dict, $names, $file_field_separator) {
		$file_text = "";
		foreach ($names as $name) {
			$file_text .= $dict[$name] . $file_field_separator;
		}
		return $file_text;
	}
	if (values_are_set($_POST, $required_fields)) {
		$file_text = format_file_text($_POST, $required_fields, $file_field_separator);
		$file_name = tempnam($contact_store, "contact-");
		
		if ($file_name !== false && file_put_contents($file_name, $file_text) !== false) {
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
