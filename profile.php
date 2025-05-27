<?php
include_once 'dbConnection.php';
session_start();

if (!isset($_SESSION['email'])) {
    header("location:index.php");
    exit();
}

$name = $_SESSION['name'];
$email = $_SESSION['email'];
?>
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
if (isset($_GET['w'])) {
    echo '<script>alert("' . htmlspecialchars($_GET['w']) . '");</script>';
}
?>

</head>
<body>

<nav class="navbar">
    <h1>Kweez</h1>
    <span class="navigation"> 
    <li <?php if (isset($_GET['q']) && $_GET['q'] == 1) echo 'class="active"'; ?> >
        <a href="profile.php?q=1">
          <span class="glyphicon glyphicon-home" aria-hidden="true"></span>&nbsp;Home
        </a>
    </li>
    <li <?php if (isset($_GET['q']) && $_GET['q'] == 2) echo 'class="active"'; ?> >
        <a href="profile.php?q=2">
          <span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span>&nbsp;History
        </a>
    </li>
    <li <?php if (isset($_GET['q']) && $_GET['q'] == 3) echo 'class="active"'; ?> >
        <a href="profile.php?q=3">
          <span class="glyphicon glyphicon-stats" aria-hidden="true"></span>&nbsp;Ranking
        </a>
    </li>
    </span>
    <?php
        echo '<span class="nav-right"><p>'.htmlspecialchars($name).'</p><p><a href="logout.php?q=profile.php">Log out</a></p></span>';
    ?>
</nav>

<div class="main-container">

<?php
// Helper function to safely get GET params
function getParam($key) {
    return isset($_GET[$key]) ? $_GET[$key] : null;
}

$q = getParam('q');

// Home - show quizzes
if ($q == 1) {
    $stmt = $con->prepare("SELECT * FROM quiz ORDER BY date DESC");
    if (!$stmt->execute()) {
        die('Error: ' . implode(' | ', $stmt->errorInfo()));
    }
    $quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo '<div class="table-container"><table class="table">
    <tr><th><p>No.</p></th><th><p>Quiz</p></th><th><p>Total Questions</p></th><th><p>Result</p></th><th></th></tr>';

    $c = 1;
    foreach ($quizzes as $row) {
        $title = htmlspecialchars($row['title']);
        $total = (int)$row['total'];
        $sahi = (int)$row['sahi'];
        $eid = $row['eid'];

        // Check if user already took this quiz
        $stmt2 = $con->prepare("SELECT score FROM history WHERE eid = ? AND email = ?");
        if (!$stmt2->execute([$eid, $email])) {
            die('Error: ' . implode(' | ', $stmt2->errorInfo()));
        }
        $rowcount = $stmt2->rowCount();

        if ($rowcount == 0) {
            echo '<tr><td>'.$c++.'</td><td>'.$title.'</td><td>'.$total.'</td><td>'.($sahi * $total).'</td>
            <td><b><a style="text-decoration: none;" href="profile.php?q=quiz&step=2&eid='.$eid.'&n=1&t='.$total.'">&nbsp;<span class="start"><b>Start</b></span></a></b></td></tr>';
        } else {
            echo '<tr><td>'.$c++.'</td><td>'.$title.'&nbsp;<span title="This quiz is already solved by you"></span></td><td>'.$total.'</td><td>'.($sahi * $total).'</td>
            <td style="font-size: 1rem; font-weight: 600;"><b><a style="text-decoration: none;" href="update.php?q=quizre&step=25&eid='.$eid.'&n=1&t='.$total.'"><span class="restart"><b>Retake</b></span></a></b></td></tr>';
        }
    }
    echo '</table></div>';
}

// Quiz start
if ($q == 'quiz' && getParam('step') == 2) {
    $eid = getParam('eid');
    $sn = (int)getParam('n');
    $total = (int)getParam('t');

    $stmt = $con->prepare("SELECT * FROM questions WHERE eid = ? AND sn = ?");
    if (!$stmt->execute([$eid, $sn])) {
        die('Error: ' . implode(' | ', $stmt->errorInfo()));
    }

    echo '<div class="quiz">';
    $question = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($question) {
        $qns = htmlspecialchars($question['qns']);
        $qid = $question['qid'];
        echo '<p class="question" style="color: #512da8; font-size: 1.25rem; font-weight: 600; margin-bottom:10px;">Question &nbsp;'.$sn.'</p><br /><p class="quest-r" style="color: #000; font-size: 1.2rem; font-weight: 600;">&nbsp'.$qns.'</p><br /><br />';
    }

    $stmt_opts = $con->prepare("SELECT * FROM options WHERE qid = ?");
    if (!$stmt_opts->execute([$qid])) {
        die('Error: ' . implode(' | ', $stmt_opts->errorInfo()));
    }

    echo '<form class="q-form" action="update.php?q=quiz&step=2&eid='.$eid.'&n='.$sn.'&t='.$total.'&qid='.$qid.'" method="POST" style="font-size: 1rem; font-weight: 550; margin: 0 15px 0 5px;">
    <br />';

    $options = $stmt_opts->fetchAll(PDO::FETCH_ASSOC);
    foreach ($options as $opt) {
        $option = htmlspecialchars($opt['option']);
        $optionid = $opt['optionid'];
        echo '<input class="radio" type="radio" name="ans" value="'.$optionid.'"><p class="option">'.$option.'</p><br /><br />';
    }
    echo '<br /><button class="q-submit" type="submit">Submit</button></form></div>';
}

// Result display
if ($q == 'result' && getParam('eid')) {
    $eid = getParam('eid');

    $stmt = $con->prepare("SELECT * FROM history WHERE eid = ? AND email = ?");
    if (!$stmt->execute([$eid, $email])) {
        die('Error: ' . implode(' | ', $stmt->errorInfo()));
    }

    echo '<div class="table-container">
    <center><h1 style="color: #512da8">Result</h1></center><br /><table class="table">';

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $s = (int)$row['score'];
        $w = (int)$row['wrong'];
        $r = (int)$row['sahi'];
        $qa = (int)$row['level'];

        echo '<tr style="color: #512da8; font-size: 1rem; font-weight: 550;"><td>Total Questions</td><td>'.$qa.'</td></tr>
              <tr style="color: #337233; font-size: 1rem; font-weight: 550;"><td>Correct Answers</td><td>'.$r.'</td></tr> 
              <tr style="color: #e21010; font-size: 1rem; font-weight: 550;"><td>Wrong Answers</td><td>'.$w.'</td></tr>
              <tr style="color: #512da8; font-size: 1rem; font-weight: 550;"><td>Score</td><td>'.$s.'</td></tr>';
    }

    $stmt_rank = $con->prepare("SELECT * FROM rank WHERE email = ?");
    if (!$stmt_rank->execute([$email])) {
        die('Error: ' . implode(' | ', $stmt_rank->errorInfo()));
    }

    while ($row = $stmt_rank->fetch(PDO::FETCH_ASSOC)) {
        $s = (int)$row['score'];
        echo '<tr style="color: #512da8; font-size: 1rem; font-weight: 550;"><td>Overall Score</td><td>'.$s.'</td></tr>';
    }

    echo '</table></div>';
}

// History
if ($q == 2) {
    $stmt = $con->prepare("SELECT * FROM history WHERE email = ? ORDER BY date DESC");
    if (!$stmt->execute([$email])) {
        die('Error: ' . implode(' | ', $stmt->errorInfo()));
    }

    echo '<div class="table-container"><table class="table">
          <tr><th><p>No.</p></th><th><p>Quiz</p></th><th><p>Question Solved</p></th><th><p>Right</p></th><th><p>Wrong</p></th><th><p>Score</p></th><th><p>Date</p></th></tr>';

    $c = 1;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo '<tr>
              <td>'.$c++.'</td>
              <td>'.htmlspecialchars($row['eid']).'</td>
              <td>'.$row['level'].'</td>
              <td>'.$row['sahi'].'</td>
              <td>'.$row['wrong'].'</td>
              <td>'.$row['score'].'</td>
              <td>'.$row['date'].'</td>
              </tr>';
    }
    echo '</table></div>';
}

// Ranking
if ($q == 3) {
    $stmt = $con->prepare("SELECT * FROM rank ORDER BY score DESC");
    if (!$stmt->execute()) {
        die('Error: ' . implode(' | ', $stmt->errorInfo()));
    }

    echo '<div class="table-container"><table class="table">
          <tr><th><p>Rank</p></th><th><p>Name</p></th><th><p>Email</p></th><th><p>Score</p></th></tr>';

    $c = 1;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo '<tr>
              <td>'.$c++.'</td>
              <td>'.htmlspecialchars($row['name']).'</td>
              <td>'.htmlspecialchars($row['email']).'</td>
              <td>'.$row['score'].'</td>
              </tr>';
    }
    echo '</table></div>';
}
?>

</div>
</body>
</html>
