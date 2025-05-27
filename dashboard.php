<?php
session_start();
include_once 'dbConnection.php';

if (!isset($_SESSION['email']) || !isset($_SESSION['name'])) {
    header("Location: index.php");
    exit();
}

$email = $_SESSION['email'];
$name = $_SESSION['name'];
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
          if($(window).scrollTop() >= $(".logo").height()) {
               $(".navbar").addClass("navbar-fixed-top");
          } else {
               $(".navbar").removeClass("navbar-fixed-top");
          }
      });
  });
  </script>
</head>

<body>
<nav class="navbar">
<h1>Dashboard</h1>
    <span class="navigation"> 
    <li <?php if(@$_GET['q']==0) echo 'class="active"'; ?>><a href="dashboard.php?q=0">Home</a></li>
    <li <?php if(@$_GET['q']==1) echo 'class="active"'; ?>><a href="dashboard.php?q=1">User</a></li>
    <li <?php if(@$_GET['q']==2) echo 'class="active"'; ?>><a href="dashboard.php?q=2">Ranking</a></li>
    <li <?php if(@$_GET['q']==4 || @$_GET['q']==5) echo 'class="active"'; ?>><a href="dashboard.php?q=4">Create Quiz</a></li>
    <li <?php if(@$_GET['q']==4 || @$_GET['q']==5) echo 'class="active"'; ?>><a href="dashboard.php?q=5">Remove Quiz</a></li>
    </span>
    
    <span class="nav-right">
        <p><?= htmlspecialchars((string)$name) ?></p>&nbsp;&nbsp;<p><a href="logout.php?q=dashboard.php">&nbsp;Signout</a></p>
    </span>
</nav>

<div class="main-container">

<?php if (@$_GET['q'] == 0) {
    $stmt = $con->prepare("SELECT * FROM quiz ORDER BY date DESC");
    $stmt->execute();
    $quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo '<div class="table-container"><table class="table">
    <tr><th><p>No.</p></th><th><p>Quiz</p></th><th><p>Total Questions</p></th><th><p>Marks</p></th><th></th></tr>';

    $c = 1;
    foreach ($quizzes as $row) {
        $title = htmlspecialchars($row['title']);
        $total = (int)$row['total'];
        $sahi = (int)$row['sahi'];
        $eid = $row['eid'];

        $stmt2 = $con->prepare("SELECT score FROM history WHERE eid = ? AND email = ?");
        $stmt2->execute([$eid, $email]);
        $rowcount = $stmt2->rowCount();

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

if (@$_GET['q'] == 2) {
    $stmt = $con->prepare("SELECT * FROM rank ORDER BY score DESC");
    $stmt->execute();
    $ranks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo '<div class="table-container"><table class="table"><tr><th><p>Rank</p></th><th><p>Name</p></th><th><p>Score</p></th></tr>';
    $c = 0;

    foreach ($ranks as $row) {
        $e = $row['email'];
        $s = $row['score'];

        $stmt2 = $con->prepare("SELECT name FROM user WHERE email = ?");
        $stmt2->execute([$e]);
        $user = $stmt2->fetch(PDO::FETCH_ASSOC);
        $nameRank = $user ? htmlspecialchars((string)$user['name']) : '';

        $c++;
        echo '<tr><td><p>' . $c . '</p></td><td>' . $nameRank . '</td><td>' . $s . '</td></tr>';
    }
    echo '</table></div>';
}

if (@$_GET['q'] == 1) {
    $stmt = $con->prepare("SELECT * FROM user");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo '<div class="table-container"><table class="table"><tr><th><p>S.N.</p></th><th><p>Name</p></th><th><p>Email</p></th><th></th></tr>';
    $c = 1;
    foreach ($users as $row) {
        $nameUser = htmlspecialchars((string)$row['name']);
        $emailUser = htmlspecialchars((string)$row['email']);
        echo '<tr><td>' . $c++ . '</td><td>' . $nameUser . '</td><td>' . $emailUser . '</td>
        <td><a title="Delete User" style="text-decoration: none;" href="update.php?demail=' . urlencode($emailUser) . '"><b><span class="remove">Delete</span></b></a></td></tr>';
    }
    echo '</table></div>';
}
?>

<!-- Create Quiz -->
<?php if (@$_GET['q'] == 4 && !isset($_GET['step'])): ?>
<div class="quiz-container">
  <span class="q-title"><b>Create Quiz</b></span><br /><br />
  <form class="quiz-maker" action="update.php?q=addquiz" method="POST">
    <input name="name" placeholder="Enter Quiz title" class="input" type="text" required>
    <input name="total" placeholder="Total number of questions" class="input" type="number" required>
    <input name="right" placeholder="Marks for right answer" class="input" type="number" min="0" required>
    <input name="wrong" placeholder="Minus marks (no sign)" class="input" type="number" min="0" required>
    <textarea class="tarea" name="desc" rows="6" placeholder="Write description here..."></textarea>
    <button type="submit" class="qm-button">Submit</button>
  </form>
</div>
<?php endif; ?>

<!-- Add Questions -->
<?php if (@$_GET['q'] == 4 && @$_GET['step'] == 2): ?>
<div class="quiz-container">
  <span class="q-title"><b>Add Questions</b></span><br /><br />
  <form class="quiz-maker" action="update.php?q=addqns&n=<?= $_GET['n'] ?>&eid=<?= $_GET['eid'] ?>&ch=4" method="POST">
    <?php for ($i = 1; $i <= $_GET['n']; $i++): ?>
      <label><b>Question <?= $i ?>:</b></label><br />
      <textarea name="qns<?= $i ?>" class="tarea" rows="3" placeholder="Enter Question <?= $i ?>"></textarea>
      <input name="<?= $i ?>1" placeholder="Option 1" class="input" type="text">
      <input name="<?= $i ?>2" placeholder="Option 2" class="input" type="text">
      <input name="<?= $i ?>3" placeholder="Option 3" class="input" type="text">
      <input name="<?= $i ?>4" placeholder="Option 4" class="input" type="text">
      <label>Answer:</label>
      <select name="ans<?= $i ?>" class="select">
        <option value="">Select</option>
        <option value="a">Option 1</option>
        <option value="b">Option 2</option>
        <option value="c">Option 3</option>
        <option value="d">Option 4</option>
      </select><br><br>
    <?php endfor; ?>
    <button type="submit" class="qm-button">Submit</button>
  </form>
</div>
<?php endif; ?>

<!-- Remove Quiz -->
<?php if (@$_GET['q'] == 5):
    try {
        $stmt = $con->query("SELECT * FROM quiz ORDER BY date DESC");
        $quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "<p>Error: " . $e->getMessage() . "</p>";
        $quizzes = [];
    }
?>
<div class="table-container">
  <table class="table">
    <tr><th>No.</th><th>Quiz</th><th>Total Questions</th><th>Marks</th><th></th></tr>
    <?php $c = 1;
    foreach ($quizzes as $quiz):
      $title = $quiz['title'];
      $total = $quiz['total'];
      $sahi = $quiz['sahi'];
      $eid = $quiz['eid'];
    ?>
    <tr>
      <td><?= $c++ ?></td>
      <td><?= htmlspecialchars($title) ?></td>
      <td><?= $total ?></td>
      <td><?= $sahi * $total ?></td>
      <td><a href="update.php?q=rmquiz&eid=<?= $eid ?>"><span class="remove"><b>Remove</b></span></a></td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>
<?php endif; ?>    

</div> <!-- main-container end -->

</body>
</html>
