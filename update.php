<?php
include_once 'dbConnection.php';
session_start();
$email = $_SESSION['email'] ?? '';

// delete feedback
if (isset($_SESSION['key']) && $_SESSION['key'] == 'kweez1234567890') {
    if (!empty($_GET['fdid'])) {
        $id = $_GET['fdid'];
        $result = pg_query_params($con, "DELETE FROM feedback WHERE id = $1", array($id));
        if (!$result) die('Error deleting feedback');
        header("location:dashboard.php?q=3");
        exit();
    }
}

// delete user
if (isset($_SESSION['key']) && $_SESSION['key'] == 'kweez1234567890') {
    if (!empty($_GET['demail'])) {
        $demail = $_GET['demail'];
        $r1 = pg_query_params($con, "DELETE FROM rank WHERE email = $1", array($demail));
        if (!$r1) die('Error deleting from rank');

        $r2 = pg_query_params($con, "DELETE FROM history WHERE email = $1", array($demail));
        if (!$r2) die('Error deleting from history');

        $result = pg_query_params($con, "DELETE FROM \"user\" WHERE email = $1", array($demail));  // user is a reserved keyword, so quoted
        if (!$result) die('Error deleting user');

        header("location:dashboard.php?q=1");
        exit();
    }
}

// remove quiz
if (isset($_SESSION['key']) && $_SESSION['key'] == 'kweez1234567890') {
    if (!empty($_GET['q']) && $_GET['q'] === 'rmquiz') {
        $eid = $_GET['eid'] ?? '';
        if (!$eid) die('Quiz id missing');

        $result = pg_query_params($con, "SELECT qid FROM questions WHERE eid = $1", array($eid));
        if (!$result) die('Error fetching questions');

        while ($row = pg_fetch_assoc($result)) {
            $qid = $row['qid'];
            $r1 = pg_query_params($con, "DELETE FROM options WHERE qid = $1", array($qid));
            if (!$r1) die('Error deleting options');

            $r2 = pg_query_params($con, "DELETE FROM answer WHERE qid = $1", array($qid));
            if (!$r2) die('Error deleting answers');
        }

        $r3 = pg_query_params($con, "DELETE FROM questions WHERE eid = $1", array($eid));
        $r4 = pg_query_params($con, "DELETE FROM quiz WHERE eid = $1", array($eid));
        $r5 = pg_query_params($con, "DELETE FROM history WHERE eid = $1", array($eid));
        if (!$r3 || !$r4 || !$r5) die('Error deleting quiz related data');

        header("location:dashboard.php?q=5");
        exit();
    }
}

// add quiz
if (isset($_SESSION['key']) && $_SESSION['key'] == 'kweez1234567890') {
    if (!empty($_GET['q']) && $_GET['q'] === 'addquiz') {
        $name = $_POST['name'] ?? '';
        $name = ucwords(strtolower($name));
        $total = (int)($_POST['total'] ?? 0);
        $sahi = (int)($_POST['right'] ?? 0);
        $wrong = (int)($_POST['wrong'] ?? 0);
        $desc = $_POST['desc'] ?? '';
        $id = uniqid();

        $q3 = pg_query_params($con, "INSERT INTO quiz (eid, title, sahi, wrong, total, description, date) VALUES ($1, $2, $3, $4, $5, $6, CURRENT_TIMESTAMP)", 
            array($id, $name, $sahi, $wrong, $total, $desc));
        if (!$q3) die('Error inserting quiz');

        header("location:dashboard.php?q=4&step=2&eid=$id&n=$total");
        exit();
    }
}

// add question
if (isset($_SESSION['key']) && $_SESSION['key'] == 'kweez1234567890') {
    if (!empty($_GET['q']) && $_GET['q'] === 'addqns') {
        $n = (int)($_GET['n'] ?? 0);
        $eid = $_GET['eid'] ?? '';
        $ch = $_GET['ch'] ?? '';

        for ($i = 1; $i <= $n; $i++) {
            $qid = uniqid();
            $qns = $_POST['qns' . $i] ?? '';

            $q3 = pg_query_params($con, "INSERT INTO questions (eid, qid, qns, ch, sn) VALUES ($1, $2, $3, $4, $5)", 
                array($eid, $qid, $qns, $ch, $i));
            if (!$q3) die('Error inserting question');

            $oaid = uniqid();
            $obid = uniqid();
            $ocid = uniqid();
            $odid = uniqid();

            $a = $_POST[$i . '1'] ?? '';
            $b = $_POST[$i . '2'] ?? '';
            $c = $_POST[$i . '3'] ?? '';
            $d = $_POST[$i . '4'] ?? '';

            $qa = pg_query_params($con, "INSERT INTO options (qid, option, optionid) VALUES ($1, $2, $3)", array($qid, $a, $oaid));
            $qb = pg_query_params($con, "INSERT INTO options (qid, option, optionid) VALUES ($1, $2, $3)", array($qid, $b, $obid));
            $qc = pg_query_params($con, "INSERT INTO options (qid, option, optionid) VALUES ($1, $2, $3)", array($qid, $c, $ocid));
            $qd = pg_query_params($con, "INSERT INTO options (qid, option, optionid) VALUES ($1, $2, $3)", array($qid, $d, $odid));

            if (!$qa || !$qb || !$qc || !$qd) die('Error inserting options');

            $e = $_POST['ans' . $i] ?? 'a';
            switch ($e) {
                case 'a': $ansid = $oaid; break;
                case 'b': $ansid = $obid; break;
                case 'c': $ansid = $ocid; break;
                case 'd': $ansid = $odid; break;
                default: $ansid = $oaid;
            }

            $qans = pg_query_params($con, "INSERT INTO answer (qid, ansid) VALUES ($1, $2)", array($qid, $ansid));
            if (!$qans) die('Error inserting answer');
        }
        header("location:dashboard.php?q=0");
        exit();
    }
}

// quiz start
if (!empty($_GET['q']) && $_GET['q'] === 'quiz' && !empty($_GET['step']) && $_GET['step'] == 2) {
    $eid = $_GET['eid'] ?? '';
    $sn = (int)($_GET['n'] ?? 0);
    $total = (int)($_GET['t'] ?? 0);
    $ans = $_POST['ans'] ?? '';
    $qid = $_GET['qid'] ?? '';

    // get correct answer
    $res = pg_query_params($con, "SELECT ansid FROM answer WHERE qid = $1", array($qid));
    if (!$res) die('Error fetching answer');
    $row = pg_fetch_assoc($res);
    $ansid = $row['ansid'] ?? '';

    if ($ans == $ansid) {
        $q = pg_query_params($con, "SELECT sahi FROM quiz WHERE eid = $1", array($eid));
        $row = pg_fetch_assoc($q);
        $sahi = (int)($row['sahi'] ?? 0);

        if ($sn == 1) {
            $ins = pg_query_params($con, "INSERT INTO history (email, eid, score, level, sahi, wrong, date) VALUES ($1, $2, 0, 0, 0, 0, CURRENT_TIMESTAMP)", array($email, $eid));
            if (!$ins) die('Error inserting history');
        }

        $h = pg_query_params($con, "SELECT score, sahi FROM history WHERE eid = $1 AND email = $2", array($eid, $email));
        $row = pg_fetch_assoc($h);
        $s = (int)($row['score'] ?? 0);
        $r = (int)($row['sahi'] ?? 0);

        $r++;
        $s += $sahi;

        $up = pg_query_params($con, "UPDATE history SET score = $1, level = $2, sahi = $3, date = CURRENT_TIMESTAMP WHERE email = $4 AND eid = $5", array($s, $sn, $r, $email, $eid));
        if (!$up) die('Error updating history');
    } else {
        $q = pg_query_params($con, "SELECT wrong FROM quiz WHERE eid = $1", array($eid));
        $row = pg_fetch_assoc($q);
        $wrong = (int)($row['wrong'] ?? 0);

        if ($sn == 1) {
            $ins = pg_query_params($con, "INSERT INTO history (email, eid, score, level, sahi, wrong, date) VALUES ($1, $2, 0, 0, 0, 0, CURRENT_TIMESTAMP)", array($email, $eid));
            if (!$ins) die('Error inserting history');
        }

        $h = pg_query_params($con, "SELECT score, wrong FROM history WHERE eid = $1 AND email = $2", array($eid, $email));
        $row = pg_fetch_assoc($h);
        $s = (int)($row['score'] ?? 0);
        $w = (int)($row['wrong'] ?? 0);

        $w++;
        $s -= $wrong;

        $up = pg_query_params($con, "UPDATE history SET score = $1, level = $2, wrong = $3, date = CURRENT_TIMESTAMP WHERE email = $4 AND eid = $5", array($s, $sn, $w, $email, $eid));
        if (!$up) die('Error updating history');
    }

    if ($sn == $total) {
        $q = pg_query_params($con, "SELECT score FROM history WHERE email = $1 AND eid = $2", array($email, $eid));
        $row = pg_fetch_assoc($q);
        $score = $row['score'] ?? 0;

        $q = pg_query_params($con, "SELECT * FROM rank WHERE email = $1", array($email));
        $num = pg_num_rows($q);

        if ($num == 0) {
            $q2 = pg_query_params($con, "INSERT INTO rank (email, score, time) VALUES ($1, $2, CURRENT_TIMESTAMP)", array($email, $score));
            if (!$q2) die('Error inserting rank');
        } else {
            $q2 = pg_query_params($con, "SELECT score FROM rank WHERE email = $1", array($email));
            $row = pg_fetch_assoc($q2);
            $sun = $row['score'] ?? 0;

            if ($score > $sun) {
                $q3 = pg_query_params($con, "UPDATE rank SET score = $1, time = CURRENT_TIMESTAMP WHERE email = $2", array($score, $email));
                if (!$q3) die('Error updating rank');
            }
        }
        header("location:dashboard.php?q=result&eid=$eid");
        exit();
    } else {
        $sn++;
        header("location:dashboard.php?q=quiz&step=2&eid=$eid&n=$sn&t=$total");
        exit();
    }
}

// reset quiz
if (!empty($_GET['q']) && $_GET['q'] == 'quizre' && !empty($_GET['eid'])) {
    $eid = $_GET['eid'];
    $del = pg_query_params($con, "DELETE FROM history WHERE eid = $1 AND email = $2", array($eid, $email));
    if (!$del) die('Error resetting quiz');
    header("location:dashboard.php?q=quiz&step=1&eid=$eid");
    exit();
}
?>
