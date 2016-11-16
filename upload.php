<?php
$target_dir = "uploads/";
$message = "";
$target_files = $_FILES["fileToUpload"]["name"];
//var_dump(count($target_files));
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
    else if ($_FILES["fileToUpload"]["size"][$i] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }
// Allow certain file formats
    else if($fileType != "csv"&&$fileType != "CSV") {
        echo "Sorry, only csv files are allowed.";
        $uploadOk = 0;
    }
// Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
//        continue;
        exit;
// if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"][$i], $target_file)) {
            $message .= "The file ". basename( $target_files[$i]). " has been uploaded.</br>";
            import($target_file);
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
}
}
header("Location: ./conference.php");
exit;

function import($fileaddr){
    //连接数据库文件 www.111cn.net
    $connect=mysql_connect("mysql.comp.polyu.edu.hk","16027789x","jsiyppoo") or die("链接数据库失败！");
//连接数据库(test)
    mysql_select_db("16027789x",$connect) or die (mysql_error());
    $file=fopen($fileaddr,"r");
    var_dump($file);
    $i=0;
    while(!feof($file)){
        $data=fgetcsv($file);
        if($data[0])$temp[$i]=$data;
        $i+=1;
    }
    fclose($file);
//$q="insert into Speaker(speakerID) values('1000');";
//mysql_query($q) or die(mysql_error());
//$temp=fgetcsv($file);//连接EXCEL文件,格式为了.csv
    for($i=0;$i<count($temp);$i++){
        $tmp_info=$temp[$i];
        if($tmp_info[0]=="PresentationTitle"){
            while($i<count($temp)){
                $i+=1;
                if($i<count($temp))$tmp_info=$temp[$i];
                else break;
                if($tmp_info[0]=="EventTitle")break;
                $sql="select SpeakerID from Speaker where '$tmp_info[5]'=firstname and '$tmp_info[6]'=lastname;";
                $result=mysql_query($sql) or die(mysql_error());
                $test=mysql_fetch_array($result);
                if(!$test){$sql="insert into Speaker(firstname,lastname,photo,affliation,type) values('$tmp_info[5]','$tmp_info[6]','$tmp_info[7]','$tmp_info[8]','$tmp_info[9]');";
                    mysql_query($sql) or die(mysql_error());
                    if(mysql_error()){
                        echo"导入失败！";
                    }
                }
            }
        }
    }
    $current_line=1;
    $Conference_info=$temp[$current_line];
    $sql="insert into Conference(name,date,duration) values('$Conference_info[0]','$Conference_info[1]','$Conference_info[2]');";
    mysql_query($sql) or die(mysql_error());
    if(mysql_error()){
        echo"导入失败！";
    }
    $sql="select count(*) from Conference;";
    $ConferenceID_t=mysql_query($sql) or die(mysql_error());
    $ConferenceID=mysql_fetch_array($ConferenceID_t,MYSQLI_NUM);
    if(mysql_error()){
        echo"导入失败！";
    }
    $current_line=3;
    while($current_line<count($temp)){
        $Event_info=$temp[$current_line];
        $sql="insert into Event(ConferenceID,title,date,beginning_time,ending_time,venue) values('$ConferenceID[0]','$Event_info[0]','$Event_info[1]','$Event_info[2]','$Event_info[3]','$Event_info[4]');";
        mysql_query($sql) or die(mysql_error());
        if(mysql_error()){
            echo"导入失败！";
        }
        $sql="select count(*) from Event";
        $EventID_t=mysql_query($sql) or die(mysql_error());
        $EventID=mysql_fetch_array($EventID_t,MYSQLI_NUM);
        $current_line+=2;
        while($current_line<count($temp)){
            $temp_info=$temp[$current_line];
            //echo $temp_info[0];
            if($temp_info[0]=="EventTitle")break;
            $Presentation_info=$temp[$current_line];
            $first_name=$Presentation_info[5];
            $last_name=$Presentation_info[6];
            $sql="select speakerID from Speaker where '$first_name'=firstname and '$last_name'=lastname;";
            $speaker_ID_t=mysql_query($sql) or dir(mysql_error());
            $speaker_ID=mysql_fetch_array($speaker_ID_t,MYSQLI_NUM);
            //echo $speaker_ID[0];
            if(mysql_error())echo"导入失败！";
            $sql="insert into Presentation(title,speakerID,eventID,beginning_time,ending_time,biography,abstract) values('$Presentation_info[0]','$speaker_ID[0]','$EventID[0]','$Presentation_info[1]','$Presentation_info[2]','$Presentation_info[3]','$Presentation_info[4]');";
            mysql_query($sql) or die(mysql_error());
            if(mysql_error()){
                echo"导入失败！";
            }
            $current_line+=1;
        }
        $current_line+=1;
    }
    mysql_close($connect);
}
?>