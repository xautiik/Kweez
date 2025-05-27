<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard</title>
  <link rel="stylesheet" href="admin.css" />
  <script>
    window.onload = function() {
      const urlParams = new URLSearchParams(window.location.search);
      const warning = urlParams.get('w');
      if (warning) {
        alert(warning);
      }
    };
  </script>
</head>
<body>
  <div class="container" id="container">
    <form role="form" method="post" action="adlogin.php?q=index.php" autocomplete="off">
      <h1>Admin</h1>
      <input
        type="text"
        name="uname"
        maxlength="20"
        placeholder="Username"
        class="input"
        autocomplete="username"
        required
      />
      <input
        type="password"
        name="password"
        maxlength="15"
        placeholder="Password"
        class="input"
        autocomplete="current-password"
        required
      />
      <button type="submit" name="login" value="Login" class="button">Login</button>
    </form>
  </div>
</body>
</html>
