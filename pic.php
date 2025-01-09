<?php
if (isset($_POST['ok'])) {

    if (isset($_FILES['test']) && $_FILES['test']['error'] === 0) {
        $image = $_FILES['test'];
        var_dump($image);
    } else {
        echo "Aucun fichier n'a été téléchargé ou une erreur s'est produite.";
    }

    echo $_FILES['test']['name'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload File</title>
</head>
<body>
    <form action="" method="POST" enctype="multipart/form-data">

        <input type="file" name="test">
        <input type="submit" name="ok" value="Envoyer">
    </form>
</body>
</html>