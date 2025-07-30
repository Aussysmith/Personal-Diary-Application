<?php

session_start();
$error = "";


    if(array_key_exists("logout", $_GET)) {
        session_unset();
        setcookie("id", "", time() - 60 * 60);
        $_COOKIE["id"] = "";
    }
    else if(array_key_exists("id", $_SESSION) OR array_key_exists("id", $_COOKIE)) {
        //go to the loggedinpage if you're still logged in
        header("Location: diary.php");
    }//end test for logout query string 

    // start check for sumbit and all info/initial connection
    if(array_key_exists("submit", $_POST)) {
    
        $link = mysqli_connect("localhost", "root", "", "diary_db");

        if(mysqli_connect_error()) {
            die("Data Connection Error");
        }
    
        if($error != "") {
            $error .= "<p>There were error(s) in your form!</p>" . $error;
        } else {
            $emailAddress = mysqli_real_escape_string($link, $_POST['email']);
            $password = mysqli_real_escape_string($link, $_POST['password']); 
            $password = password_hash($password, PASSWORD_DEFAULT);
            
            if($_POST['submit'] == 'signUp') {
                $query  = "SELECT id FROM users WHERE email = '" . $emailAddress . "' LIMIT 1";
                
                $result = mysqli_query($link, $query);
    
                if(mysqli_num_rows($result) > 0) {
                    $error = "<div class='alert alert-danger'>That email address is taken.</div>";
                } else {
                    $query = "INSERT INTO users (email, password) VALUES ('" . $emailAddress . "', '" . $password . "')";
    
                    if(!mysqli_query($link, $query)) {
                        $error .= "<p class='alert alert-danger'>Could not sign you up - Please try again later.</p>";
                        $error .= "<p class='alert alert-danger'>" . mysqli_error($link) . "</p>";
                    } else {
                        $id = mysqli_insert_id($link);
    
                        $_SESSION['id'] = $id;
    
                        if(isset($_POST['checkbox'])) {
                            setcookie("id", $id, time() + 60 * 60 * 24 * 365);
                        }
    
                        header("Location: diary.php");
    
                    }//end if for successful/failed sign up
                }//end if mysqli_num_rows test
            } else {
                $query = "SELECT * FROM users WHERE email = '" . $emailAddress . "'";
                $result = mysqli_query($link, $query);
                $row = mysqli_fetch_array($result);
                $password = mysqli_real_escape_string($link, $_POST['password']);

                if(isset($row) AND array_key_exists("password", $row)) {
                    $passwordMatches = password_verify($password, $row['password']);

                    if($passwordMatches) {
                        $_SESSION['id'] = $row['id'];
                        if(isset($_POST['checkbox'])) {
                            setcookie("id", $row['id'], time() + 60 * 60 * 24 * 365);
                        }

                        header("Location: diary.php");
                    } else {
                        $error = "<div class='alert alert-danger'>That email/password combination could not be found.</div>";
                    }//end else - password matches or doesn't
                } else {
                    $error = "<div class='alert alert-danger'>That email/password combination could not be found.</div>";
                }
            }//end if-else for signUp == 1 or 0
        }//end of error existing check
    }//end if the submit exists

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="jQuery.min.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body {
                min-height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                margin: 0; 
                background: url(images/notebook.jpg) center/cover no-repeat;
            }
        </style>
    </head>

    <body>
        
        
        <div class="container w-75 text-center">
            <div id="error"><?php echo $error; ?></div>
           
            <h1>Secret Diary</h1>
            <p>Store your thoughts permanently and securely</p>
            
            <form method="post" class="w-50 mx-auto">
                
                <input type="email" name="email" placeholder="Your email" class="form-control mb-2" required>
                <input type="password" name="password" placeholder="Password" class="form-control mb-2" required>
                
                <div class="form-check mb-3">
                    <label class="form-check-label" for="checkbox">Stay logged in</label>
                    <input class="form-check-input align-center" type="checkbox" name="checkbox" id="checkbox">
                </div>

                <button type="submit" name="submit" class="btn btn-success" value="signUp">Sign up</button>
                <button type="submit" name="submit" class="btn btn-primary" value="loggedIn">Log in</button>
                
            </form>
        </div>
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
       
    </body>
</html>