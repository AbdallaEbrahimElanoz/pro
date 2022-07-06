<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Document</title>
    <style>
        form,input{
            margin:auto;
            textalign:center;
        } 


 </style>
</head>
   
<body>
<?php
$host    = "127.0.0.1";
$port    = 25003;
// $message = "Hello Server123";
// echo "Message To server :".$message;
if(isset($_POST['btn'])){
    $message =$_REQUEST['textmsg'];
    // create socket
    $socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");
    // connect to server
    $result = socket_connect($socket, $host, $port) or die("Could not connect to server\n");  
    // send string to server
    socket_write($socket, $message, strlen($message)) or die("Could not send data to server\n");
    // get server response
    $result = socket_read ($socket, 1024) or die("Could not read server response\n");
    $result =trim($result);
    $result = "Reply From Server  :\t".$result;
   
    // close socket
   // socket_close($socket);

}
?>
    <form method='POST'>
    <input type="text" name="textmsg"/>
    <input type="submit" name="btn" value="send"/>
    <textarea rows="10" cols="30"><?php  echo $result; ?> </textarea>
</form>    

</div>  
</body>
</html>









