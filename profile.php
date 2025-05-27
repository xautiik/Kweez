<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta charset="UTF-8">
  <title>Kweez - Profile</title>
  <link rel="stylesheet" href="account.css"> 
  <link rel="stylesheet" href="style.css"> 

<?php 
if (@$_GET['w']) {
    echo '<script>alert("'.htmlspecialchars($_GET['w']).'");</script>';
}
?>

</head>
<?php
include_once 'dbConnection.php';
session_start();
?>
<body>

<nav class="navbar">
    <h1>Kweez</h1>
    <span class="navigation"> 
    <li <?php if (@$_GET['q']==1) echo 'class="active"'; ?> >
        <a href="profile.php?q=1">
          <span class="glyphicon glyphicon-home" aria-hidden="true"></span>&nbsp;Home
        </a>
    </li>
    <li <?php if (@$_GET['q']==2) echo 'class="active"'; ?>>
        <a href="profile.php?q=2">
          <span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span>&nbsp;History
        </a>
    </li>
    <li <?php if (@$_GET['q']==3) echo 'class="active"'; ?>>
        <a href="profile.php?q=3">
          <span class="glyphicon glyphicon-stats" aria-hidden="true"></span>&nbsp;Ranking
        </a>
    </li>
    </span>
    <?php
    if (!(isset($_SESSION['email']))) {
        header("location:index.php");
        exit();
    } else {
        $name = $_SESSION['name'];
        $email = $_SESSION['email'];
        echo '<span class="nav-right"><p>'.htmlspecialchars($name).'</p><p><a href="logout.php?q=profile.php">Log out</a></p></span>';
    }
    ?>
</nav>

<div class="main-container">

<?php 
if (@$_GET['q'] == 1) {
    $result = pg_query($conn, "SELECT * FROM quiz ORDER BY date DESC");
    if (!$result) { die('Error: ' . pg_last_error()); }

    echo '<div class="table-container"><table class="table">
    <tr><th><p>No.</p></th><th><p>Quiz</p></th><th><p>Total Questions</p></th><th><p>Result</p></th><th></th></tr>';

    $c = 1;
    while ($row = pg_fetch_assoc($result)) {
        $title = htmlspecialchars($row['title']);
        $total = (int)$row['total'];
        $sahi = (int)$row['sahi'];
        $eid = htmlspecialchars($row['eid']);

        $esc_email = pg_escape_string($conn, $email);
        $esc_eid = pg_escape_string($conn, $eid);

        $q12 = pg_query_params($conn, "SELECT score FROM history WHERE eid = $1 AND email = $2", [$esc_eid, $esc_email]);
        if (!$q12) { die('Error: ' . pg_last_error()); }

        $rowcount = pg_num_rows($q12);

        if ($rowcount == 0) {
            echo '<tr><td>'.$c++.'</td><td>'.$title.'</td><td>'.$total.'</td><td>'.$sahi * $total.'</td>
            <td><b><a style="text-decoration: none;" href="profile.php?q=quiz&step=2&eid='.$eid.'&n=1&t='.$total.'">&nbsp;<span class="start"><b>Start</b></span></a></b></td></tr>';
        } else {
            echo '<tr><td>'.$c++.'</td><td>'.$title.'&nbsp;<span title="This quiz is already solved by you"></span></td><td>'.$total.'</td><td>'.$sahi * $total.'</td>
            <td style="font-size: 1rem; font-weight: 600;"><b><a style="text-decoration: none;" href="update.php?q=quizre&step=25&eid='.$eid.'&n=1&t='.$total.'"><span class="restart"><b>Retake</b></span></a></b></td></tr>';
        }
    }
    echo '</table></div>';
}
?>

<!-- quiz start -->
<?php
if (@$_GET['q'] == 'quiz' && @$_GET['step'] == 2) {
    $eid = pg_escape_string($conn, $_GET['eid']);
    $sn = (int)$_GET['n'];
    $total = (int)$_GET['t'];

    $q = pg_query_params($conn, "SELECT * FROM questions WHERE eid = $1 AND sn = $2", [$eid, $sn]);
    if (!$q) { die('Error: ' . pg_last_error()); }

    echo '<div class="quiz">';
    while ($row = pg_fetch_assoc($q)) {
        $qns = htmlspecialchars($row['qns']);
        $qid = $row['qid'];
        echo '<p class="question" style="color: #512da8; font-size: 1.25rem; font-weight: 600; margin-bottom:10px;">Question &nbsp;'.$sn.'</p><br /><p class="quest-r" style="color: #000; font-size: 1.2rem; font-weight: 600;">&nbsp'.$qns.'</p><br /><br />';
    }

    $q_opts = pg_query_params($conn, "SELECT * FROM options WHERE qid = $1", [$qid]);
    if (!$q_opts) { die('Error: ' . pg_last_error()); }

    echo '<form class="q-form" action="update.php?q=quiz&step=2&eid='.$eid.'&n='.$sn.'&t='.$total.'&qid='.$qid.'" method="POST" style="font-size: 1rem; font-weight: 550; margin: 0 15px 0 5px;">
    <br />';

    while ($row = pg_fetch_assoc($q_opts)) {
        $option = htmlspecialchars($row['option']);
        $optionid = $row['optionid'];
        echo '<input class="radio" type="radio" name="ans" value="'.$optionid.'"><p class="option">'.$option.'</p><br /><br />';
    }
    echo '<br /><button class="q-submit" type="submit">Submit</button></form></div>';
}

// result display
if (@$_GET['q'] == 'result' && @$_GET['eid']) {
    $eid = pg_escape_string($conn, $_GET['eid']);
    $esc_email = pg_escape_string($conn, $email);

    $q = pg_query_params($conn, "SELECT * FROM history WHERE eid = $1 AND email = $2", [$eid, $esc_email]);
    if (!$q) { die('Error: ' . pg_last_error()); }

    echo '<div class="table-container">
    <center><h1 style="color: #512da8">Result</h1></center><br /><table class="table">';

    while ($row = pg_fetch_assoc($q)) {
        $s = (int)$row['score'];
        $w = (int)$row['wrong'];
        $r = (int)$row['sahi'];
        $qa = (int)$row['level'];

        echo '<tr style="color: #512da8; font-size: 1rem; font-weight: 550;"><td>Total Questions</td><td>'.$qa.'</td></tr>
              <tr style="color: #337233; font-size: 1rem; font-weight: 550;"><td>Correct Answers</td><td>'.$r.'</td></tr> 
              <tr style="color: #e21010; font-size: 1rem; font-weight: 550;"><td>Wrong Answers</td><td>'.$w.'</td></tr>
              <tr style="color: #512da8; font-size: 1rem; font-weight: 550;"><td>Score</td><td>'.$s.'</td></tr>';
    }

    $q_rank = pg_query_params($conn, "SELECT * FROM rank WHERE email = $1", [$esc_email]);
    if (!$q_rank) { die('Error: ' . pg_last_error()); }

    while ($row = pg_fetch_assoc($q_rank)) {
        $s = (int)$row['score'];
        echo '<tr style="color: #512da8; font-size: 1rem; font-weight: 550;"><td>Overall Score</td><td>'.$s.'</td></tr>';
    }

    echo '</table></div>';
}
?>
<!-- quiz end -->

<?php
// history start
if (@$_GET['q'] == 2) {
    $esc_email = pg_escape_string($conn, $email);
    $q = pg_query_params($conn, "SELECT * FROM history WHERE email = $1 ORDER BY date DESC", [$esc_email]);
    if (!$q) { die('Error: ' . pg_last_error()); }

    echo '<div class="table-container"><table class="table">
    <tr><th><p>No.</p></th><th><p>Quiz</p></th><th><p>Question Solved</p></th><th><p>Right</p></th><th><p>Wrong</p></th><th><p>Score</p></th><th><p>Date</p></th></tr>';

    $c = 1;
    while ($row = pg_fetch_assoc($q)) {
        $eid = htmlspecialchars($row['eid']);
        $score = (int)$row['score'];
        $wrong = (int)$row['wrong'];
        $sahi = (int)$row['sahi'];
        $level = (int)$row['level'];
        $date = htmlspecialchars($row['date']);

        $qz = pg_query_params($conn, "SELECT title FROM quiz WHERE eid = $1", [$eid]);
        if ($qz) {
            $qz_row = pg_fetch_assoc($qz);
            $title = htmlspecialchars($qz_row['title']);
        } else {
            $title = "Unknown";
        }

        echo '<tr><td>'.$c++.'</td><td>'.$title.'</td><td>'.$level.'</td><td>'.$sahi.'</td><td>'.$wrong.'</td><td>'.$score.'</td><td>'.$date.'</td></tr>';
    }
    echo '</table></div>';
}
// history end
?>

<?php
// ranking start
if (@$_GET['q'] == 3) {
    $q = pg_query($conn, "SELECT * FROM rank ORDER BY score DESC");
    if (!$q) { die('Error: ' . pg_last_error()); }

    echo '<div class="table-container"><table class="table">
    <tr><th><p>Rank</p></th><th><p>Name</p></th><th><p>Score</p></th></tr>';

    $c = 1;
    while ($row = pg_fetch_assoc($q)) {
        $name = htmlspecialchars($row['name']);
        $score = (int)$row['score'];

        echo '<tr><td>'.$c++.'</td><td>'.$name.'</td><td>'.$score.'</td></tr>';
    }
    echo '</table></div>';
}
?>

</div> <!-- main container -->

</body>
</html>
