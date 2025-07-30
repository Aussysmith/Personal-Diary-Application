<?php
    session_start();

    if(array_key_exists("id", $_COOKIE)) {
        $_SESSION['id'] = $_COOKIE['id'];
    }

    if(array_key_exists("id", $_SESSION)) {
        echo "<p class='fw-bold fixed-top m-5'><a href='index.php?logout=1' class='btn btn-primary'>Log out</a></p>";
    } else {
        header("Location: index.php");
    }

   if(array_key_exists("content", $_POST)) {
    $link = mysqli_connect("localhost", "root", "", "diary_db");
    
    $query = "UPDATE users SET diary = '" .
        mysqli_real_escape_string($link, $_POST['content']) . 
        "' WHERE id = " . mysqli_real_escape_string($link, $_SESSION['id']) . " LIMIT 1";

    mysqli_query($link, $query);
    exit(); 
    }

    $diaryContent = "";
    $link = mysqli_connect("localhost", "root", "", "diary_db");
    $query = "SELECT diary FROM users WHERE id = " . 
    mysqli_real_escape_string($link, $_SESSION['id']) . " LIMIT 1";

    $result = mysqli_query($link, $query);
    $row = mysqli_fetch_array($result);

    if($row) {
        $diaryContent = $row['diary'];
    }

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="jQuery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
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

            #diary {
                width: 100%;
                height: 90%;
                background: #add8e6;
                opacity: 50%;
                font-weight: bold;
            }
        </style>
    </head>

    <body>

        <div class="container">
            
            <nav class="navbar bg-primary bg-gradient">
                <div class="container-fluid">
                    <h2 class="text-center w-100 py-2">My Diary</h2>
                </div>
            </nav>
            <textarea id="diary" name="diaryContent" class="form-control"><?php echo htmlspecialchars($diaryContent); ?></textarea>

        </div>

        <script type="text/javascript">
            $(document).ready(function() {
                $("#diary").on('input', function() {
                    $.ajax({
                        method: "POST",
                        url: "diary.php",
                        data: { content: $("#diary").val() }
                    });
                });
            });
        </script>
    </body>
</html>