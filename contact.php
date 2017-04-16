<?php
    ini_set('display_errors', 1); //Turn on error reporting, ONLY FOR DEVELOPMENT SHOULD BE DELETED ON RELEASE


    //htmlspecialchars() for html output 
    $contact_store = "/Users/gilesholdsworth/Dropbox/programming/github/PHP-contact/"; //Location to store contact files, full path or DOC_ROOT prepended if you want
    $required_fields = array("name", "email", "subject", "message");//All fields are required, optional fields should be included in this list, and send a dummy value if empty

    $was_error = false;
    $error_text = "";

    $name = "";
    $email = "";
    $subject = "";
    $message = "";

    

    function error($text) {
        global $was_error, $error_text;
        $was_error = true;
        $error_text .= "Error: " . $text . "<br>\n";
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

    
    


    //Main logic
    if (posts_are_set($_POST, $required_fields)) {
        $name = $_POST["name"];
        $email = $_POST["email"];
        $subject = $_POST["subject"];
        $message = $_POST["message"];
        
        
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
                        echo($error_text);
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
        
        <?php //Testing section
            echo($name);
            echo("<br>");
            echo($email);
            echo("<br>");
            echo($subject);
            echo("<br>");
            echo($message);
        ?>
	</body>
</html>
