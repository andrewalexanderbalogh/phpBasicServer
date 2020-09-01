<?php
    $rawComposerJSON = file_get_contents(__DIR__ . '/../composer.json');
    $composerJSON = json_decode($rawComposerJSON);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo $composerJSON->name; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <style type="text/css">
    #message {
    font-weight: bold;
            margin-bottom: 1rem;
        }

        .highlight {
    color: darkgreen;
}

        body {
    margin: auto;
    text-align: center;
        }
    </style>
</head>




<body>
<h2 id="message">Welcome to the <span class="highlight"><?php echo $composerJSON->name; ?></span> API</h2>
    <div>
        <a class="btn" href="<?php echo $composerJSON->homepage; ?>">Find the repository on GitHub</a>
    </div>
</body>

</html>
