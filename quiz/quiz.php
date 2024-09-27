<?php
require_once "config.php";
$pdo = getDBConnection();
$chosenQuizID = $_GET["quiz"];
$sql = "Select * from questions
         where quiz_id = :quiz_id
         order by RAND()
         LIMIT 10";

if($stmt = $pdo->prepare($sql)){
    $stmt->bindParam(":quiz_id", $chosenQuizID);
    if($stmt->execute()){
        $rows = $stmt->fetchAll();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #ffffff;
            border-radius: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-width: 600px;
            width: 100%;
            overflow-y: auto; /* Allow vertical scrolling */
            max-height: 70vh; /* Set maximum height */
        }

        h2 {
            color: #007bff;
            text-align: center;
        }

        ol {
            list-style-type: none;
            padding-left: 0;
            margin-top: 0; /* Remove default margin */
            counter-reset: question-counter; /* Reset counter */
        }

        li {
            margin-bottom: 20px; /* Add margin between questions */
        }

        .choices {
            margin-left: 20px; /* Add margin to the choices */
        }

        li.question::before {
            content: counter(question-counter) ". ";
            counter-increment: question-counter; /* Increment counter */
            font-weight: bold;
        }

        input[type="radio"] {
            margin-right: 5px;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
            transition: background-color 0.3s;
            display: block;
            margin: 20px auto;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Quiz</h2>
    <form action="result.php" method="post">
        <ol>
            <?php $counter = 1; ?>
            <?php foreach($rows as $row): ?>
                <li class="question">
                    <span><?= $row["text"] ?></span>
                    <ol class="choices" type="a"> <!-- Added class "choices" here -->
                        <?php
                        $answersql = "Select * from answers where question_id = :question_id";
                        if($answerstmt = $pdo->prepare($answersql)){
                            $answerstmt->bindParam(":question_id", $row["id"]);
                            if($answerstmt->execute()){
                                $answers = $answerstmt->fetchAll();
                                foreach($answers as $answer)
                                {
                                    ?>
                                    <li>
                                        <input type="radio" name="answers[<?= $row["id"] ?>]" value="<?= $answer["text"] ?>">
                                        <?= $answer["text"]?>
                                    </li>
                                    <?php
                                }
                            }
                        }
                        ?>
                    </ol>
                </li>
                <?php $counter++; ?>
            <?php endforeach; ?>
        </ol>
        <input type="submit" value="Find Your Grade">
    </form>
</div>
</body>
</html>
