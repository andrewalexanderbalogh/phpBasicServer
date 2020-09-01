<?php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Not Found</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <style type="text/css">
        #message {
            font-weight: bold;
            margin-bottom: 1rem;
        }

        .highlight {
            color: darkred;
        }

        body {
            margin: auto;
            text-align: center;
        }
    </style>
</head>

<body>
<h2 id="message">Resource for <span class="highlight"><?php echo $_SERVER['PATH_INFO']; ?></span> not found</h2>
</body>

</html>

