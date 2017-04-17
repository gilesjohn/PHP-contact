<?php
	ini_set('display_errors', 1); //Turn on error reporting, ONLY FOR DEVELOPMENT SHOULD BE DELETED ON RELEASE

	// Implementation specific constants
	// REQUIRE SETTING DURING SETUP
	$contact_store = "contacts/"; //Location to store contact files, full path or DOC_ROOT prepended if you want, DIRECTORY MUST EXIST
	$required_fields = array("name", "email", "subject", "message");//All fields are required, optional fields should be included in this list, and send a dummy value if empty
	$file_field_separator = "\n"; //This string will be inserted between each field in the file produced
	$use_file = true;
	$use_email = false;
	$to_email = "gilesjohn@hotmail.co.uk";
	$use_captcha = true;
	$secret_key = "6LfPYB0UAAAAAEfUSQrFItBEBETrQvfbhcKm9bqy";
	$site_key = "6LfPYB0UAAAAANp4lOHzJLoX9ACUgujzNEuN3CPf"
	// NO MORE CODE IS NEEDED TO BE CHANGED DURING SETUP

	// Initialise variables with defaults
	$was_error = false;
	$error_text = "";
	$was_success = false;
	$success_text = "";


	// Function definitions

	/* error - Set was_error flag and error_text so that an error is displayed to client later in the code.
	 * 	Recommended that current block is escaped after calling this function so execution does not continue 
	 * after encountering an error.
	 *
	 * Param	$text	String to display giving client information about the error
	 */
	function error($text) {
		global $was_error, $error_text;
		$was_error = true;
		$error_text .= "Error: " . $text . "<br>\n";
	}

	/* success - Set was_success flag and success_text meaning that code successfully finished.
	 * 	Can be run even after a call to error function, but NOT RECOMMENDED, likely shows a flaw in your
	 * scripts logic
	 *
	 * Param	$text	String to display giving client confirmation of submission
	 */
	function success($text) {
		global $was_success, $success_text;
		$was_success = true;
		$success_text .= "Success: " . $text . "<br>\n";
	}

	/* values_are_set - Test if certain values in a dictionary style array are set and not empty strings
	 *
	 * Param	$dict	Associative array with string values
	 * Param	$names	Array of the keys to be checked in the $dict array
	 * Return	bool	Whether all specified values are set and not empty
	 */
	function values_are_set($dict, $names) {//return true if string variables are set and have some characters, false if not
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

	/* format_file_text - Create formatted text from certain values of an associative array
	 *
	 * Param	$dict	Associative array with string values
	 * Param	$names	Array of the keys to be checked in the $dict array
	 * Return	string	Formatted text
	 */
	function format_file_text($dict, $names, $file_field_separator) {
		$file_text = "";
		foreach ($names as $name) {
			$file_text .= $dict[$name] . $file_field_separator;
		}
		return $file_text;
	}

	/* send_email - Send $content to mail address specified in $to_email
	 *
	 * Param	$content	String to send
	 * Return	bool	False if failure
	 */
	function send_email($content) {
		global $to_email;
		$msg = $content;
		$msg = wordwrap($msg,70);
		return mail($to_email,"Contact Form Submission",$msg);
	}

	/* send_email - Send $content to mail address specified in $to_email
	 *
	 * Param	$content	String to send
	 * Return	bool	true only if success, probably false on failure but not guarranteed 
	 */
	function check_captcha() {
		global $secret_key;
		$url = "https://www.google.com/recaptcha/api/siteverify";
		$data = array("secret" => $secret_key, "response" => $_POST["g-recaptcha-response"], "remoteip" => $_SERVER['REMOTE_ADDR']);
		$options = array(
			'http' => array(
				'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
				'method'  => 'POST',
				'content' => http_build_query($data)
			)
		);
		$context  = stream_context_create($options);
		$result = file_get_contents($url, false, $context);
		if ($result === FALSE) { /* Handle error */ }
		$result = json_decode($result, true);
		if (!isset($result["success"])) {
			return false;
		}
		return $result["success"];
	}



    // Main logic
	
	/* Algorithm:
	 *	Check if all required post fields are set and not empty
	 *	Sort all required post fields into a single formatted string
	 *	Create a uniqure file in the $contact_store directory
	 *	Store the formatted string in the uniqure file
	 */
	if (count($_POST) > 0) {
		if ($use_captcha) {
			if (check_captcha() !== true) {
				exit("Failed bot detection test.");
			}
		}
		if (values_are_set($_POST, $required_fields)) {
			$file_text = format_file_text($_POST, $required_fields, $file_field_separator);
			
			if ($use_file) {
				$file_name = tempnam($contact_store, "contact-");
				if ($file_name === false) {
					error("Couldn't complete contacting process, please try again later.");
				} else if (file_put_contents($file_name, $file_text) === false) {
					error("Couldn't complete contacting process, please try again later.");
				}
			}
			
			if ($use_email) {
				if (send_email($file_text) === false) {
					error("Couldn't complete contacting process, please try again later.");
				}
			}
				
				
			if (!$was_error) {
				success("Message sent.");
			}
		}
	}
?>

<!DOCTYPE html>
<html>
	<head>
	
	<?php
		if ($use_captcha) {
			echo("<script src='https://www.google.com/recaptcha/api.js'></script>
			<script>
			   function onSubmit(token) {
				 document.getElementById('contact-form').submit();
			   }
     		</script>
			");
		}
	?>
	</head>
	<body>

		<form action="" method="POST" id="contact-form">
			<p id="error-message" style="color: red">
				<?php
					// Code to display error text in correct location if a problem occurred
					if ($was_error) {
						echo $error_text;
					}
				?>
			</p>
			<p id="success-message" style="color: green">
				<?php
					// Code to display success text in correct location if script succeeded
					
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
			
			<?php
			if ($use_captcha) {
				echo("<button
				class='g-recaptcha'
				data-sitekey='" . $site_key . "'
				data-callback='onSubmit'>
				Submit
				</button>");
			} else {
				echo('<input type="submit" value="Submit">');
			}
			?>
			
		</form>

		<?php //Testing section
			if (values_are_set($_POST, $required_fields)) {
				foreach ($required_fields as $name) {
					echo($_POST[$name] . "<br>");
				}
			}
		?>
	</body>
</html>
