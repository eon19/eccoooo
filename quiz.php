<?php
include('quizdb.php');
session_start();

/* =========================
   START QUIZ (SET NAME)
========================= */
if (isset($_POST['start_quiz']) && !empty($_POST['username'])) {
    $_SESSION['username'] = htmlspecialchars($_POST['username']);

    // Reset everything when starting
    $_SESSION['score'] = 0;
    $_SESSION['current_index'] = 0;

    // Load RANDOM questions
    $result = $conn->query("SELECT * FROM questions ORDER BY RAND()");
    $_SESSION['quiz_questions'] = $result->fetch_all(MYSQLI_ASSOC);

    // Extra shuffle for stronger randomness
    shuffle($_SESSION['quiz_questions']);
}

/* =========================
   RESET QUIZ
========================= */
if (isset($_POST['end_quiz'])) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

/* =========================
   QUIZ LOGIC (ONLY IF STARTED)
========================= */
if (isset($_SESSION['username']) && isset($_SESSION['quiz_questions'])) {

    $total_questions = count($_SESSION['quiz_questions']);

    /* HANDLE ANSWER */
    if (isset($_POST['option']) && $_SESSION['current_index'] < $total_questions) {

        $current_question = $_SESSION['quiz_questions'][$_SESSION['current_index']];
        $selected_option = intval($_POST['option']);

        if ($selected_option === intval($current_question['correct_option'])) {
            $_SESSION['score']++;
        }

        $_SESSION['current_index']++;
    }

    $quiz_finished = $_SESSION['current_index'] >= $total_questions;

} else {
    $total_questions = 0;
    $quiz_finished = false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Quiz Game</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: #f4f4f4;
}

.quiz-header {
    background: #4801e1;
    color: white;
    padding: 20px;
    font-size: 20px;
    font-weight: bold;
    text-align: center;
}

.quiz-container {
    max-width: 700px;
    margin: 60px auto;
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 0 15px rgba(0,0,0,0.1);
}

.question-image {
    width: 100%;
    max-height: 300px;
    object-fit: contain;
    margin: 20px 0;
}

.option-btn {
    font-size: 18px;
    padding: 15px;
}
</style>
</head>
<body>

<!-- HEADER -->
<div class="quiz-header">
    <?php if (isset($_SESSION['username'])): ?>
        Player: <?= $_SESSION['username']; ?> |
        Score: <?= $_SESSION['score']; ?>
    <?php else: ?>
        Quiz Game
    <?php endif; ?>
</div>

<div class="container">
<div class="quiz-container">

<?php if (!isset($_SESSION['username'])): ?>

    <!-- NAME INPUT -->
    <div class="text-center">
        <h3 class="mb-4">Enter Your Name</h3>

        <form method="POST">
            <input type="text" name="username" class="form-control mb-3"
                   placeholder="Your Name" required>

            <button type="submit" name="start_quiz" class="btn btn-success">
                Start Quiz
            </button>
        </form>
    </div>

<?php else: ?>

    <?php if (!$quiz_finished && $total_questions > 0): ?>

        <?php 
            $question = $_SESSION['quiz_questions'][$_SESSION['current_index']];
            $current_number = $_SESSION['current_index'] + 1;
        ?>

        <h5 class="text-center">
            Question <?= $current_number ?> of <?= $total_questions ?>
        </h5>

        <h4 class="text-center mt-3">
            <?= htmlspecialchars($question['question']); ?>
        </h4>

        <img src="<?= htmlspecialchars($question['image_url']); ?>" 
             class="question-image" 
             alt="Question Image">

        <form method="POST">
            <div class="row g-3 mt-2">

                <div class="col-6">
                    <button type="submit" name="option" value="1"
                        class="btn btn-primary w-100 option-btn">
                        <?= htmlspecialchars($question['option_1']); ?>
                    </button>
                </div>

                <div class="col-6">
                    <button type="submit" name="option" value="2"
                        class="btn btn-primary w-100 option-btn">
                        <?= htmlspecialchars($question['option_2']); ?>
                    </button>
                </div>

                <div class="col-6">
                    <button type="submit" name="option" value="3"
                        class="btn btn-primary w-100 option-btn">
                        <?= htmlspecialchars($question['option_3']); ?>
                    </button>
                </div>

                <div class="col-6">
                    <button type="submit" name="option" value="4"
                        class="btn btn-primary w-100 option-btn">
                        <?= htmlspecialchars($question['option_4']); ?>
                    </button>
                </div>

            </div>
        </form>

    <?php elseif ($total_questions == 0): ?>

        <div class="text-center">
            <h4>No questions found in database.</h4>
        </div>

    <?php else: ?>

        <!-- FINISHED -->
        <div class="text-center">
            <h3>Quiz Finished 🎉</h3>
            <h4>
                <?= $_SESSION['username']; ?>, your score is 
                <?= $_SESSION['score']; ?> / <?= $total_questions; ?>
            </h4>
        </div>

    <?php endif; ?>

    <!-- RESTART -->
    <form method="POST" class="text-center mt-4">
        <button type="submit" name="end_quiz" class="btn btn-danger">
            Restart Quiz
        </button>
    </form>

    <!-- PROGRESS -->
    <div class="text-center mt-3">
        <?php if (!$quiz_finished && $total_questions > 0): ?>
            Progress: <?= $_SESSION['current_index']; ?> / <?= $total_questions; ?>
        <?php elseif ($total_questions > 0): ?>
            Completed: <?= $total_questions; ?> / <?= $total_questions; ?>
        <?php endif; ?>
    </div>

<?php endif; ?>

</div>
</div>

</body>
</html>