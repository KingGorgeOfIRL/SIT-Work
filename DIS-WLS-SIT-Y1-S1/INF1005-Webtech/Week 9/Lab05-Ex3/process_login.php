<?php
session_start();
?>
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
                // Additional check to make sure e-mail address is well-formed.
                if (!filter_var($email, FILTER_VALIDATE_EMAIL))
                {
                    $errorMsg .= "Invalid email format.";
                    $success = false;
                }
            }

            if ($success) {
                authenticateUser();
            }

            if ($success) {
                echo '<h4>Login successful!</h4>';
                echo '<p>Welcome back, '. $_SESSION["fname"] .' '. $_SESSION["lname"] .'</p>';
                echo '<div class="mb-3"><a class="btn btn-primary" href="index.php" id="success-button">Home</a></div>';
            }else{
                echo '<h4>An error was detected:</h4>';
                echo '<p>' . $errorMsg . '</p>';
                echo '<div class="mb-3"><a class="btn btn-secondary" href="login.php">Return to Login</a></div>';
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

            function authenticateUser()
            {
                global $fname, $lname, $email, $pwd_hashed, $errorMsg, $success;
                // Create database connection.
                $config = parse_ini_file('/var/www/private/db-config.ini');
                if (!$config)
                {
                    $errorMsg = "Failed to read database config file.";
                    $success = false;
                }
                else
                {
                    $conn = new mysqli(
                        $config['servername'],
                        $config['username'],
                        $config['password'],
                        $config['dbname']
                    );
                    // Check connection
                    if ($conn->connect_error)
                    {
                        $errorMsg = "Connection failed: " . $conn->connect_error;
                        $success = false;
                    }
                    else
                    {
                        // Prepare the statement:
                        $stmt = $conn->prepare("SELECT * FROM world_of_pets_members WHERE email=?");
                        // Bind & execute the query statement:
                        $stmt->bind_param("s", $email);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        if ($result->num_rows > 0)
                        {
                            // Note that email field is unique, so should only have one row.
                            $row = $result->fetch_assoc();
                            $fname = $row["fname"];
                            $lname = $row["lname"];
                            $pwd_hashed = $row["password"];
                            // Check if the password matches:
                            if (password_verify($_POST["pwd"], $pwd_hashed))
                            {
                                $_SESSION["fname"] = $fname;
                                $_SESSION["lname"] = $lname;
                                $_SESSION["email"] = $email;
                            }
                            else
                            {
                                // Don’t tell hackers which one was wrong, keep them guessing...
                                $errorMsg = "Email not found or password doesn't match...";
                                $success = false;
                            }
                        }
                        else
                        {
                            $errorMsg = "Email not found or password doesn't match...";
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
