<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kweez</title>
    <link rel="stylesheet" href="style.css"> 
<?php if(@$_GET['w'])
{echo'<script>alert("'.@$_GET['w'].'");</script>';}
?>
<script>
    function validateForm() {var y = document.forms["form"]["name"].value;	var letters = /^[A-Za-z]+$/;if (y == null || y == "") {alert("Name must be filled out.");return false;}var x = document.forms["form"]["email"].value;var atpos = x.indexOf("@");
    var dotpos = x.lastIndexOf(".");if (atpos<1 || dotpos<atpos+2 || dotpos+2>=x.length) {alert("Not a valid e-mail address.");return false;}var a = document.forms["form"]["password"].value;if(a == null || a == ""){alert("Password must be filled out");return false;}if(a.length<5 || a.length>25){alert("Passwords must be 5 to 25 characters long.");return false;}
    var b = document.forms["form"]["cpassword"].value;if (a!=b){alert("Passwords must match.");return false;}}
    </script>
</head>
<body>
    <div class="container" id="container">
        <div class="form-container sign-up">
            <form action="register.php?q=profile.php" onSubmit="return validateForm()" method="POST">
                <h1 style="color: #512da8; font-size: 2em; margin-bottom: 10px;">Register</h1>
                <input type="text" name="name" id="name" placeholder="Name">
                <input type="email" name="email" id="email" placeholder="Email">
                <span class="pass">
                <input type="password" name="password" id="password" placeholder="Password">
                <input type="password" name="cpassword" id="cpassword" placeholder="Confirm Password">
                </span>
                <button type="submit" class="button">Register</button>
            </form>
        </div>

        <div class="form-container sign-in">
            <form action="login.php?q=index.php" method="POST">
                <h1 style="color: #512da8; font-size: 2em; margin-bottom: 10px;">Login</h1>
                <input type="email" name="email" id="email" placeholder="Email">
                <input type="password" name="password" id="password" placeholder="Password">
                <button type="submit" class="button">Login</button>
            </form>
        </div>

        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>Kweez</h1>
                    <p>Create and take engaging quizzes to test and enhance your knowledge!</p>
                    <button class="hidden" id="login">Login</button>
                </div>

                <div class="toggle-panel toggle-right">
                    <h1>Kweez</h1>
                    <p>Create and take engaging quizzes to test and enhance your knowledge!</p>
                    <button class="hidden" id="register">Register</button>
                </div>
            </div>
        </div>
    </div>  
    <script src="script.js"></script>    
</body>
</html>