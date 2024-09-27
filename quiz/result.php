<?php
require_once "config.php";
$pdo = getDBConnection();

$userQuestionIDs = array_keys($_POST["answers"]);
$userAnswers = array_values($_POST["answers"]);
$questionNumber = 1;
$totalQuestionNumber = 10;
$numberCorrect = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Results</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            padding-top: 50px;
            background-image: url('quizlogo.jpeg'); /* Background image */
            background-repeat: repeat; /* Repeat the background image */
        }

        .container {
            background-color: #ffffff;
            border-radius: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-width: 600px;
            width: 100%;
            margin: auto; /* Center the container horizontally */
            position: absolute; /* Position the container */
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%); /* Adjust for centering */
        }

        .correct-answer {
            color: dodgerblue;
        }

        .wrong-answer {
            color: red;
        }

        .score {
            font-weight: bold;
            margin-top: 20px;
        }

        .btn {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <?php
    $questionNumber = 1;
    $totalQuestionNumber = 10;
    $numberCorrect = 0;

    foreach ($userQuestionIDs as $questionID) {
        $sql = "SELECT * FROM answers WHERE question_id = :questionID AND is_answer = 'true'";
        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(':questionID', $questionID);
            if ($stmt->execute()) {
                $row = $stmt->fetch();
                if ($row) {
                    $correctAnswer = $row['text'];

                    if (in_array($questionID, $userQuestionIDs)) {
                        $userAnswerIndex = array_search($questionID, $userQuestionIDs);
                        $userAnswer = $userAnswers[$userAnswerIndex];
                        if ($userAnswer === $correctAnswer) {
                            ?>
                            <p class="correct-answer">You got question <?=$questionNumber?> correct!</p>
                            <?php
                            $numberCorrect++;
                        } else {
                            ?>
                            <p class="wrong-answer">You got question <?=$questionNumber ?> wrong! The correct answer is: <?= $correctAnswer ?></p>
                            <?php
                        }
                    }
                }
            }
        }

        $questionNumber++;
    }

    ?>
    <p class="score">Total Score: <?= round(($numberCorrect/$totalQuestionNumber) * 100) ?>%</p>

    <!-- Button to take another quiz -->
    <a href="chooseQuiz.php" class="btn btn-primary">Take Another Quiz</a>
</div>
</body>
</html>