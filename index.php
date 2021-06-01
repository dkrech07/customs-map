<html>
    <head>
        <link rel="stylesheet" href="libs/dropzone.css">
        <link rel="stylesheet" href="libs/dropzone.css">
        <script src="libs/dropzone.js"></script>
    </head>
    <body>
        <main>
        <form method="post" action="index.php" enctype="multipart/form-data">
            <input type="hidden" name="MAX_FILE_SIZE" value="2000000" />
            <input type="file" name="uploadfile">
            <input type="submit" value="Загрузить файл">
        </form>
        </main>
    </body>
</html>


<?php
print_r($_FILES);

$file_name = $_FILES['uploadfile']['name'];
$file_path = 'uploads/';
$file_url = 'uploads/' . $file_name;
move_uploaded_file($_FILES['userpic-file-photo']['tmp_name'], $file_path . $file_name);