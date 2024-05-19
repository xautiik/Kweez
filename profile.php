<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta charset="UTF-8">
  <title>Kweez - Profile</title>
  <link rel="stylesheet" href="account.css"> 
  <link rel="stylesheet" href="style.css"> 

<?php if(@$_GET['w'])
{echo'<script>alert("'.@$_GET['w'].'");</script>';}
?>

</head>
<?php
include_once 'dbConnection.php';
?>
<body>

<nav class="navbar">
    <h1>Kweez</h1>
    <span class="navigation"> 
    <li <?php if(@$_GET['q']==1) echo'class="active"'; ?> ><a href="profile.php?q=1"><span class="glyphicon glyphicon-home" aria-hidden="true"></span>&nbsp;Home</a></li>
    <li <?php if(@$_GET['q']==2) echo'class="active"'; ?>><a href="profile.php?q=2"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span>&nbsp;History</a></li>
		<li <?php if(@$_GET['q']==3) echo'class="active"'; ?>><a href="profile.php?q=3"><span class="glyphicon glyphicon-stats" aria-hidden="true"></span>&nbsp;Ranking</a></li>
    </span>
          <?php
      include_once 'dbConnection.php';
      session_start();
        if(!(isset($_SESSION['email']))){
      header("location:index.php");
      }
      else
      {
      $name = $_SESSION['name'];
      $email=$_SESSION['email'];

      include_once 'dbConnection.php';
      echo '<span class="nav-right"><p>'.$name.'</p><p><a href="logout.php?q=profile.php">Log out</a></p></span>';
      }?>
</nav>

<div class="main-container">
<!--<div class="table-container">-->

<?php if(@$_GET['q']==1) {

$result = mysqli_query($con,"SELECT * FROM quiz ORDER BY date DESC") or die('Error');
echo  '<div class="table-container"><table class="table">
<tr><th><p>No.</p></th><th><p>Quiz</p></th><th><p>Total Questions</p></th><th><p>Result</p></th><th></th></tr>';
$c=1;
while($row = mysqli_fetch_array($result)) {
	$title = $row['title'];
	$total = $row['total'];
	$sahi = $row['sahi'];
	$eid = $row['eid'];
$q12=mysqli_query($con,"SELECT score FROM history WHERE eid='$eid' AND email='$email'" )or die('Error98');
$rowcount=mysqli_num_rows($q12);	
if($rowcount == 0){
	echo '<tr><td>'.$c++.'</td><td>'.$title.'</td><td>'.$total.'</td><td>'.$sahi*$total.'</td>
	<td><b><a style="text-decoration: none;" href="profile.php?q=quiz&step=2&eid='.$eid.'&n=1&t='.$total.'">&nbsp;<span class="start"><b>Start</b></span></a></b></td></tr>';
}
else
{
echo '<tr><td>'.$c++.'</td><td>'.$title.'&nbsp;<span title="This quiz is already solve by you"></span></td><td>'.$total.'</td><td>'.$sahi*$total.'</td>
	<td style="font-size: 1rem; font-weight: 600;"><b><a style="text-decoration: none;" href="update.php?q=quizre&step=25&eid='.$eid.'&n=1&t='.$total.'"></span><span class="restart"><b>Retake</b></span></a></b></td></tr>';
}
}
$c=0;
echo '</table></div>';

}?>

<!--</div>-->
<!--home closed-->


<!--quiz start-->
<?php
if(@$_GET['q']== 'quiz' && @$_GET['step']== 2) {
$eid=@$_GET['eid'];
$sn=@$_GET['n'];
$total=@$_GET['t'];
$q=mysqli_query($con,"SELECT * FROM questions WHERE eid='$eid' AND sn='$sn' " );
echo '<div class="quiz">';
while($row=mysqli_fetch_array($q) )
{
$qns=$row['qns'];
$qid=$row['qid'];
echo '<p class="question" style="color: #512da8; font-size: 1.25rem; font-weight: 600; margin-bottom:10px;">Question &nbsp;'.$sn.'</p><br /><p class="quest-r"style="color: #000; font-size: 1.2rem; font-weight: 600;">&nbsp'.$qns.'</p><br /><br />';
}
$q=mysqli_query($con,"SELECT * FROM options WHERE qid='$qid' " );
echo '<form class="q-form" action="update.php?q=quiz&step=2&eid='.$eid.'&n='.$sn.'&t='.$total.'&qid='.$qid.'" method="POST" style="
font-size: 1rem;
font-weight: 550;
margin: 0 15px 0 5px;
">
<br />';

while($row=mysqli_fetch_array($q) )
{
$option=$row['option'];
$optionid=$row['optionid'];
echo'<input class="radio" type="radio" name="ans" value="'.$optionid.'"><p class="option">'.$option.'</p><br /><br />';
}
echo'<br /><button class="q-submit" type="submit"></span>Submit</button></form></div>';
}

//result display
if(@$_GET['q']== 'result' && @$_GET['eid']) 
{
$eid=@$_GET['eid'];
$q=mysqli_query($con,"SELECT * FROM history WHERE eid='$eid' AND email='$email' " )or die('Error157');
echo  '<div class="table-container">
<center><h1 style="color: #512da8">Result</h1><center><br /><table class="table">';

while($row=mysqli_fetch_array($q) )
{
$s=$row['score'];
$w=$row['wrong'];
$r=$row['sahi'];
$qa=$row['level'];
echo '<tr style="color: #512da8; font-size: 1rem; font-weight: 550;"><td>Total Questions</td><td>'.$qa.'</td></tr>
      <tr style="color: #337233; font-size: 1rem; font-weight: 550;"><td>Correct Answers</td><td>'.$r.'</td></tr> 
	    <tr style="color: #e21010; font-size: 1rem; font-weight: 550;"><td>Wrong Answers</td><td>'.$w.'</td></tr>
	    <tr style="color: #512da8; font-size: 1rem; font-weight: 550;"><td>Score</td><td>'.$s.'</td></tr>';
}
$q=mysqli_query($con,"SELECT * FROM rank WHERE  email='$email' " )or die('Error157');
while($row=mysqli_fetch_array($q) )
{
$s=$row['score'];
echo '<tr style="color: #512da8; font-size: 1rem; font-weight: 550;"><td>Overall Score</td><td>'.$s.'</td></tr>';
}
echo '</table></div>';

}
?>
<!--quiz end-->

<?php
//history start
if(@$_GET['q']== 2) 
{
$q=mysqli_query($con,"SELECT * FROM history WHERE email='$email' ORDER BY date DESC " )or die('Error197');
echo  '<div class="table-container">
<table class="table" >
<tr><th><p>No.</p></th><th><p>Quiz</p></th><th><p>Question Solved</p></th><th><p>Right</p></th><th><p>Wrong<b></th><th><p>Score</p></th>';
$c=0;
while($row=mysqli_fetch_array($q) )
{
$eid=$row['eid'];
$s=$row['score'];
$w=$row['wrong'];
$r=$row['sahi'];
$qa=$row['level'];
$q23=mysqli_query($con,"SELECT title FROM quiz WHERE  eid='$eid' " )or die('Error208');
while($row=mysqli_fetch_array($q23) )
{
$title=$row['title'];
}
$c++;
echo '<tr><td>'.$c.'</td><td>'.$title.'</td><td>'.$qa.'</td><td>'.$r.'</td><td>'.$w.'</td><td>'.$s.'</td></tr>';
}
echo'</table></div>';
}

//ranking start
if(@$_GET['q']== 3) 
{
$q=mysqli_query($con,"SELECT * FROM rank  ORDER BY score DESC " )or die('Error223');
echo  '<div class="table-container">
<table class="table" >
<tr><th><p>Rank</p></th><th><p>Name</p></th><th><p>Score</p></th></tr>';
$c=0;
while($row=mysqli_fetch_array($q) )
{
$e=$row['email'];
$s=$row['score'];
$q12=mysqli_query($con,"SELECT * FROM user WHERE email='$e' " )or die('Error231');
while($row=mysqli_fetch_array($q12) )
{
$name=$row['name'];

}
$c++;
echo '<tr><td><p>'.$c.'</p></td><td>'.$name.'</td><td>'.$s.'</td>';
}
echo '</table></div>';}
?>
</div>
<footer>
<span class="foot"><h2>Kayodee</h2></span>
</footer>

</body>
</html>
