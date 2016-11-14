<?php
$target_dir = "uploads/";
$message = "";
$target_files = $_FILES["fileToUpload"]["name"];
for($i=0;$i<count($target_files);$i++){
    $target_file = $target_dir . basename($target_files[$i]);
    $uploadOk = 1;
    $fileType = pathinfo($target_file,PATHINFO_EXTENSION);

// Check if file already exists
    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }
// Check file size
    if ($_FILES["fileToUpload"]["size"][$i] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }
// Allow certain file formats
    if($fileType != "csv"&&$fileType != "CSV") {
        echo "Sorry, only csv files are allowed.";
        $uploadOk = 0;
    }
// Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"][$i], $target_file)) {
            $message .= "The file ". basename( $_FILES["fileToUpload"]["name"][$i]). " has been uploaded.</br>";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
}
}
echo $message;
?>