<?php
require_once 'classes/Date_time.php';
$dt = new Date_time();
$notes = $dt->checkSubmit();
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Notes</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-3">
        <h1>Display Notes</h1>
        <p><a href="index.php">Add Note</a></p>

        <form method="post" action="">
            <div class="form-group">
                <label for="begDate">Beginning Date</label>
                <input type="date" class="form-control" id="begDate" name="begDate">
            </div>

            <div class="form-group">
                <label for="endDate">Ending Date</label>
                <input type="date" class="form-control" id="endDate" name="endDate">
            </div>

            <button type="submit" name="getNotes" class="btn btn-primary">Get Notes</button>
        </form>

        <?= $notes; ?>
    </div>
</body>

</html>