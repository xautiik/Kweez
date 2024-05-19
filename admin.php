<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta charset="UTF-8">
  <title>accopy</title>
  <link rel="stylesheet" href="admin.css"> 
<?php if(@$_GET['w'])
{echo'<script>alert("'.@$_GET['w'].'");</script>';}
?>
</head>
<?php
include_once 'dbConnection.php';
?>
<body>
<div class="container" id="container">
<form role="form" method="post" action="adlogin.php?q=index.php">
<h1>Admin</h1>
<input type="text" name="uname" maxlength="20"  placeholder="Username" class="input"/> 
<input type="password" name="password" maxlength="15" placeholder="Password" class="input"/>
<button type="submit" name="login" value="Login" class="button">Login</button>
</form>
</div>
</body>
</html>