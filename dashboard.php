<?php
session_start();
include_once 'dbConnection.php';

$email = $_SESSION['email'] ?? null;

if (!$email) {
    header("Location: index.php");
    exit();
}

$name = $_SESSION['name'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta charset="UTF-8">
  <title>dashboard</title>
  <link rel="stylesheet" href="account.css">
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="input.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
  $(function () {
      $(document).on('scroll', function(){
          console.log('scroll top : ' + $(window).scrollTop());
          if($(window).scrollTop() >= $(".logo").height())
          {
               $(".navbar").addClass("navbar-fixed-top");
          }

          if($(window).scrollTop() < $(".logo").height())
          {
               $(".navbar").removeClass("navbar-fixed-top");
          }
      });
  });
  </script>
</head>

<body>
<!-- admin start-->

<!--navigation menu-->
<nav class="navbar">
<h1>Dashboard</h1>
    <span class="navigation"> 
    <li <?php if(@$_GET['q']==0) echo 'class="active"'; ?>><a href="dashboard.php?q=0">Home</a></li>
    <li <?php if(@$_GET['q']==1) echo 'class="active"'; ?>><a href="dashboard.php?q=1">User</a></li>
    <li <?php if(@$_GET['q']==2) echo 'class="active"'; ?>><a href="dashboard.php?q=2">Ranking</a></li>
    <li <?php if(@$_GET['q']==4 || @$_GET['q']==5) echo 'class="active"'; ?>><a href="dashboard.php?q=4">Create Quiz</a></li>
    <li <?php if(@$_GET['q']==4 || @$_GET['q']==5) echo 'class="active"'; ?>><a href="dashboard.php?q=5">Remove Quiz</a></li>
    </span>
    
<?php
include_once 'dbConnection.php';
session_start();

$email = $_SESSION['email'] ?? null;

if (!$email) {
    header("location:index.php");
    exit();
} else {
    $name = $_SESSION['name'];
    
    echo '<span class="nav-right"><p>' . htmlspecialchars($name) . '</p>&nbsp;&nbsp;<p><a href="logout.php?q=dashboard.php">&nbsp;Signout</a></p></span>';
}
?>
</nav>
<!--navigation menu closed-->

<div class="main-container">
<!--home start-->

<?php if (@$_GET['q'] == 0) {

    $result = pg_query($con, "SELECT * FROM quiz ORDER BY date DESC") or die('Error');

    echo '<div class="table-container"><table class="table">
    <tr><th><p>No.</p></th><th><p>Quiz</p></th><th><p>Total Questions</p></th><th><p>Marks</p></th><th></th></tr>';

    $c = 1;
    while ($row = pg_fetch_assoc($result)) {
        $title = htmlspecialchars($row['title']);
        $total = (int)$row['total'];
        $sahi = (int)$row['sahi'];
        $eid = $row['eid'];

        
        $q12 = pg_query_params($con, "SELECT score FROM history WHERE eid=$1 AND email=$2", array($eid, $email)) or die('Error98');
        $rowcount = pg_num_rows($q12);

        if ($rowcount == 0) {
            echo '<tr><td>' . $c++ . '</td><td>' . $title . '</td><td>' . $total . '</td><td>' . ($sahi * $total) . '</td>
            <td><b><a style="text-decoration: none;" href="profile.php?q=quiz&step=2&eid=' . urlencode($eid) . '&n=1&t=' . $total . '">&nbsp;<span class="start"><b>Start</b></span></a></b></td></tr>';
        } else {
            echo '<tr><td>' . $c++ . '</td><td>' . $title . '&nbsp;<span title="This quiz is already solved by you"></span></td><td>' . $total . '</td><td>' . ($sahi * $total) . '</td>
            <td style="font-size: 1rem; font-weight: 600;"><b><a style="text-decoration: none;" href="update.php?q=quizre&step=25&eid=' . urlencode($eid) . '&n=1&t=' . $total . '"><span class="restart"><b>Restart</b></span></a></b></td></tr>';
        }
    }
    echo '</table></div>';

}

//ranking start
if (@$_GET['q'] == 2) {
    $q = pg_query($con, "SELECT * FROM rank ORDER BY score DESC") or die('Error223');

    echo '<div class="table-container"><table class="table"><tr><th><p>Rank</p></th><th><p>Name</p></th><th><p>Score</p></th></tr>';
    $c = 0;

    while ($row = pg_fetch_assoc($q)) {
        $e = $row['email'];
        $s = $row['score'];

        $q12 = pg_query_params($con, "SELECT * FROM user WHERE email=$1", array($e)) or die('Error231');
        $name = '';
        if ($row2 = pg_fetch_assoc($q12)) {
            $name = htmlspecialchars($row2['name']);
        }
        $c++;
        echo '<tr><td><p>' . $c . '</p></td><td>' . $name . '</td><td>' . $s . '</td></tr>';
    }
    echo '</table></div>';
}
?>
<!--home closed-->

<!--users start-->
<?php if (@$_GET['q'] == 1) {
    $result = pg_query($con, "SELECT * FROM user") or die('Error');

    echo '<div class="table-container"><table class="table"><tr><th><p>S.N.</p></th><th><p>Name</p></th><th><p>Email</p></th><th></th></tr>';
    $c = 1;
    while ($row = pg_fetch_assoc($result)) {
        $name = htmlspecialchars($row['name']);
        $email = htmlspecialchars($row['email']);
        echo '<tr><td>' . $c++ . '</td><td>' . $name . '</td><td>' . $email . '</td>
        <td><a title="Delete User" style="text-decoration: none;" href="update.php?demail=' . urlencode($email) . '"><b><span class="remove">Delete</span></b></a></td></tr>';
    }
    echo '</table></div>';
}
?>
<!--user end-->

<!--add quiz start-->
<?php
if (@$_GET['q'] == 4 && !(@$_GET['step'])) {
    echo '
    <div class="quiz-container">
    <span class="q-title"><b>Create Quiz</b></span><br /><br />
    <form class="quiz-maker" name="form" action="update.php?q=addquiz" method="POST">
      <input id="name" name="name" placeholder="Enter Quiz title" class="input" type="text">
      <input id="total" name="total" placeholder="Enter total number of questions" class="input" type="number">
      <input id="right" name="right" placeholder="Enter marks on right answer" class="input" min="0" type="number">
      <input id="wrong" name="wrong" placeholder="Enter minus marks on wrong answer without sign" class="input" min="0" type="number">
      <textarea class="tarea" rows="6" cols="8" name="desc" placeholder="Write description here..."></textarea>  
      <button type="submit" class="qm-button">Submit</button>
    </form>
    </div>';
}
?>
<!--add quiz end-->

<!--add quiz step2 start-->
<?php
if (@$_GET['q'] == 4 && (@$_GET['step']) == 2) {
    echo '
    <div class="quiz-container">
    <span class="q-title"><b>Add Questions</b></span><br /><br />
    <form class="quiz-maker" name="form" action="update.php?q=addqns&n=' . intval(@$_GET['n']) . '&eid=' . htmlspecialchars(@$_GET['eid']) . '&ch=4" method="POST">';

    for ($i = 1; $i <= intval(@$_GET['n']); $i++) {
        echo '<b class="title-1">Question No.&nbsp;' . $i . '&nbsp;:</b><br />
        <textarea rows="3" cols="5" name="qns' . $i . '" class="tarea" placeholder="Write question number ' . $i . ' here..."></textarea>  
        <input id="' . $i . '1" name="' . $i . '1" placeholder="Enter Option 1" class="input" type="text">
        <input id="' . $i . '2" name="' . $i . '2" placeholder="Enter Option 2" class="input" type="text">
        <input id="' . $i . '3" name="' . $i . '3" placeholder="Enter Option 3" class="input" type="text">
        <input id="' . $i . '4" name="' . $i . '4" placeholder="Enter Option 4" class="input" type="text">
        <br />
        <b>Answer</b>:<br />
        <select id="ans' . $i . '" name="ans' . $i . '" class="select" >
          <option>Select Answer for Question ' . $i . '</option>
          <option value="a">Option 1</option>
          <option value="b">Option 2</option>
          <option value="c">Option 3</option>
          <option value="d">Option 4</option>
        </select><br /><br />';
    }
        
    echo '
        <button type="submit" class="qm-button">Submit</button>
    </form></div>';
}
?>
<!--add quiz step 2 end-->

<!--remove quiz-->
<?php if (@$_GET['q'] == 5) {
    $result = pg_query($con, "SELECT * FROM quiz ORDER BY date DESC") or die('Error');
    echo '<div class="table-container"><table class="table">
    <tr><th><p>No.</p></th><th><p>Quiz</p></th><th><p>Total Questions</p></th><th><p>Marks</p></th><th></th></tr>';
    $c = 1;
    while ($row = pg_fetch_assoc($result)) {
        $title = htmlspecialchars($row['title']);
        $total = (int)$row['total'];
        $sahi = (int)$row['sahi'];
        $eid = $row['eid'];

        echo '<tr><td>' . $c++ . '</td><td>' . $title . '</td><td>' . $total . '</td><td>' . ($sahi * $total) . '</td>
        <td><a title="Delete Quiz" href="update.php?q=rmquiz&eid=' . urlencode($eid) . '"><b><span class="remove">Delete</span></b></a></td></tr>';
    }
    echo '</table></div>';
}
?>
<!--remove quiz-->

</div> <!-- main-container end -->

</body>
</html>

