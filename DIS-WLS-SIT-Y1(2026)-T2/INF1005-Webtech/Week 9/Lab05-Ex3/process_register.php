<!DOCTYPE html>
<html lang="en">
    <?php include "inc/head.inc.php";?>
    <body>
        <?php include "inc/nav.inc.php";?>
        <main class="container">
            <?php
            $email = $errorMsg = "";
            $success = true;
            if (empty($_POST["email"]) || empty($_POST["pwd"])){
                $errorMsg .= "Email and Password is required.<br>";
                $success = false;
            }else{
                $email = sanitize_input($_POST["email"]);
                $password = $_POST["pwd"];
                $password_confirm = $_POST["pwd_confirm"];
                $fname = sanitize_input($_POST["fname"]);
                $lname = sanitize_input($_POST["lname"]);
                // Additional check to make sure e-mail address is well-formed.
                if (!filter_var($email, FILTER_VALIDATE_EMAIL))
                {
                    $errorMsg .= "Invalid email format.";
                    $success = false;
                }
                if ($password != $password_confirm){
                    $errorMsg .= "Passwords do not match.";
                    $success = false;
                }
                $pwd_hashed = password_hash($_POST["pwd"],PASSWORD_DEFAULT);
                saveMemberToDB();
            }
            if ($success) {
                echo '
                <h4>Registration successful!</h4>
                <p>Thank you for signing up, '.$fname.' '.$lname.'</p>
                <div class="mb-3"><a class="btn btn-primary" href="login.php" id="success-button">Log-in</a></div>
                ';
            }else{
                echo '
                <h4>An input error was detected:</h4>
                <p>' . $errorMsg . '</p>
                <div class="mb-3"><a class="btn btn-secondary" href="/register.php">Return to Sign Up</a></div>
                ';
            }

            /*
            * Helper function that checks input for malicious or unwanted content.
            */
            function sanitize_input($data){
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
            }
            function saveMemberToDB(){
                global $fname, $lname, $email, $pwd_hashed, $errorMsg, $success;
                // Create database connection.
                $config = parse_ini_file('/var/www/private/db-config.ini');
                if (!$config){
                    $errorMsg = "Failed to read database config file.";
                    $success = false;
                }
                else{
                    $conn = new mysqli(
                    $config['servername'],
                    $config['username'],
                    $config['password'],
                    $config['dbname']
                    );
                    // Check connection
                    if ($conn->connect_error){
                        $errorMsg = "Connection failed: " . $conn->connect_error;
                        $success = false;
                    }
                    else{
                        // Prepare the statement:
                        $stmt = $conn->prepare("INSERT INTO world_of_pets_members
                        (fname, lname, email, password) VALUES (?, ?, ?, ?)");
                        // Bind & execute the query statement:
                        $stmt->bind_param("ssss", $fname, $lname, $email, $pwd_hashed);
                        if (!$stmt->execute()){
                            $errorMsg = "Execute failed: (" . $stmt->errno . ") " .
                            $stmt->error;
                            $success = false;
                        }
                        $stmt->close();
                        }
                    $conn->close();
                }
            }
            ?>
        </main>
        <?php
        include "inc/footer.inc.php";
        ?>
    </body>
</html>
