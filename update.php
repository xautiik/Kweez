<?php
include_once 'dbConnection.php';
session_start();
$email = $_SESSION['email'] ?? '';

function redirect($url) {
    header("Location: $url");
    exit();
}

// Delete feedback
if ($_SESSION['key'] ?? '' === 'kweez1234567890' && isset($_GET['fdid'])) {
    $stmt = $pdo->prepare("DELETE FROM feedback WHERE id = ?");
    $stmt->execute([$_GET['fdid']]);
    redirect("dashboard.php?q=3");
}

// Delete user
if ($_SESSION['key'] ?? '' === 'kweez1234567890' && isset($_GET['demail'])) {
    $demail = $_GET['demail'];
    $pdo->prepare("DELETE FROM rank WHERE email = ?")->execute([$demail]);
    $pdo->prepare("DELETE FROM history WHERE email = ?")->execute([$demail]);
    $pdo->prepare("DELETE FROM \"user\" WHERE email = ?")->execute([$demail]);
    redirect("dashboard.php?q=1");
}

// Remove quiz
if ($_SESSION['key'] ?? '' === 'kweez1234567890' && ($_GET['q'] ?? '') === 'rmquiz') {
    $eid = $_GET['eid'] ?? '';
    if (!$eid) die("Quiz ID missing");

    $stmt = $pdo->prepare("SELECT qid FROM questions WHERE eid = ?");
    $stmt->execute([$eid]);
    $qids = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($qids as $qid) {
        $pdo->prepare("DELETE FROM options WHERE qid = ?")->execute([$qid]);
        $pdo->prepare("DELETE FROM answer WHERE qid = ?")->execute([$qid]);
    }

    $pdo->prepare("DELETE FROM questions WHERE eid = ?")->execute([$eid]);
    $pdo->prepare("DELETE FROM quiz WHERE eid = ?")->execute([$eid]);
    $pdo->prepare("DELETE FROM history WHERE eid = ?")->execute([$eid]);

    redirect("dashboard.php?q=5");
}

// Add quiz
if ($_SESSION['key'] ?? '' === 'kweez1234567890' && ($_GET['q'] ?? '') === 'addquiz') {
    $id = uniqid();
    $name = ucwords(strtolower($_POST['name'] ?? ''));
    $total = (int) ($_POST['total'] ?? 0);
    $sahi = (int) ($_POST['right'] ?? 0);
    $wrong = (int) ($_POST['wrong'] ?? 0);
    $desc = $_POST['desc'] ?? '';

    $stmt = $pdo->prepare("INSERT INTO quiz (eid, title, sahi, wrong, total, description, date) VALUES (?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)");
    $stmt->execute([$id, $name, $sahi, $wrong, $total, $desc]);

    redirect("dashboard.php?q=4&step=2&eid=$id&n=$total");
}

// Add questions
if ($_SESSION['key'] ?? '' === 'kweez1234567890' && ($_GET['q'] ?? '') === 'addqns') {
    $n = (int) ($_GET['n'] ?? 0);
    $eid = $_GET['eid'] ?? '';
    $ch = $_GET['ch'] ?? '';

    for ($i = 1; $i <= $n; $i++) {
        $qid = uniqid();
        $qns = $_POST["qns$i"] ?? '';

        $pdo->prepare("INSERT INTO questions (eid, qid, qns, ch, sn) VALUES (?, ?, ?, ?, ?)")
            ->execute([$eid, $qid, $qns, $ch, $i]);

        $options = [
            ['text' => $_POST[$i . '1'], 'id' => uniqid()],
            ['text' => $_POST[$i . '2'], 'id' => uniqid()],
            ['text' => $_POST[$i . '3'], 'id' => uniqid()],
            ['text' => $_POST[$i . '4'], 'id' => uniqid()]
        ];

        foreach ($options as $opt) {
            $pdo->prepare("INSERT INTO options (qid, option, optionid) VALUES (?, ?, ?)")
                ->execute([$qid, $opt['text'], $opt['id']]);
        }

        $ansLetter = $_POST['ans' . $i] ?? 'a';
        $answerMap = ['a' => 0, 'b' => 1, 'c' => 2, 'd' => 3];
        $ansid = $options[$answerMap[$ansLetter]]['id'] ?? $options[0]['id'];

        $pdo->prepare("INSERT INTO answer (qid, ansid) VALUES (?, ?)")
            ->execute([$qid, $ansid]);
    }

    redirect("dashboard.php?q=0");
}

// Handle quiz answers
if (($_GET['q'] ?? '') === 'quiz' && ($_GET['step'] ?? '') == 2) {
    $eid = $_GET['eid'] ?? '';
    $sn = (int) ($_GET['n'] ?? 0);
    $total = (int) ($_GET['t'] ?? 0);
    $ans = $_POST['ans'] ?? '';
    $qid = $_GET['qid'] ?? '';

    $stmt = $pdo->prepare("SELECT ansid FROM answer WHERE qid = ?");
    $stmt->execute([$qid]);
    $correct = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT sahi, wrong FROM quiz WHERE eid = ?");
    $stmt->execute([$eid]);
    $quiz = $stmt->fetch(PDO::FETCH_ASSOC);
    $sahiPoint = (int) ($quiz['sahi'] ?? 0);
    $wrongPoint = (int) ($quiz['wrong'] ?? 0);

    if ($sn == 1) {
        $pdo->prepare("INSERT INTO history (email, eid, score, level, sahi, wrong, date) VALUES (?, ?, 0, 0, 0, 0, CURRENT_TIMESTAMP)")
            ->execute([$email, $eid]);
    }

    $stmt = $pdo->prepare("SELECT score, sahi, wrong FROM history WHERE eid = ? AND email = ?");
    $stmt->execute([$eid, $email]);
    $history = $stmt->fetch(PDO::FETCH_ASSOC);
    $score = (int) $history['score'];
    $sahi = (int) $history['sahi'];
    $wrong = (int) $history['wrong'];

    if ($ans === $correct) {
        $sahi++;
        $score += $sahiPoint;
        $pdo->prepare("UPDATE history SET score = ?, level = ?, sahi = ?, date = CURRENT_TIMESTAMP WHERE email = ? AND eid = ?")
            ->execute([$score, $sn, $sahi, $email, $eid]);
    } else {
        $wrong++;
        $score -= $wrongPoint;
        $pdo->prepare("UPDATE history SET score = ?, level = ?, wrong = ?, date = CURRENT_TIMESTAMP WHERE email = ? AND eid = ?")
            ->execute([$score, $sn, $wrong, $email, $eid]);
    }

    if ($sn == $total) {
        redirect("result.php?eid=$eid");
    } else {
        $next = $sn + 1;
        redirect("quiz.php?q=quiz&step=2&eid=$eid&n=$next&t=$total");
    }
}
