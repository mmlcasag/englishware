<?php 
header('Content-Type: text/html; charset=utf-8');

ini_set('default_charset', 'UTF-8');
ini_set('display_errors', 'Off');
?>

<html>

<head>
    <title>:: ENGLISHWARE ::</title>
    <link rel="stylesheet" type="text/css" href="style.css"/>
    <script>
        function telaConsulta() {
            location.href = "/englishware/telaConsulta.php";
        }
        function telaReenviarEmails() {
            location.href = "/englishware/telaReenviarEmails.php";
        }
    </script>
</head>

<body>
    <center>
        <br/>
        <br/>
        <br/>
        <br/>
        <font face="Tahoma" style="font-size:82px;" color="blue">E</font>
        <font face="Tahoma" style="font-size:68px;" color="red">n</font>
        <font face="Tahoma" style="font-size:68px;" color="orange">g</font>
        <font face="Tahoma" style="font-size:68px;" color="blue">l</font>
        <font face="Tahoma" style="font-size:68px;" color="green">i</font>
        <font face="Tahoma" style="font-size:68px;" color="red">s</font>
        <font face="Tahoma" style="font-size:68px;" color="blue">h</font>
        <font face="Tahoma" style="font-size:68px;" color="red">w</font>
        <font face="Tahoma" style="font-size:68px;" color="orange">a</font>
        <font face="Tahoma" style="font-size:68px;" color="blue">r</font>
        <font face="Tahoma" style="font-size:68px;" color="green">e</font>
        <br/>
        <br/>
        <br/>
        <form action="telaConsulta.php" method="post" name="search" enctype="multipart/form-data">
              <input type="text" size="80" name="p_search" style="height:25px;"/>
              <br/>
              <br/>
              <input type="submit" name="p_submit" class="groovybutton" value="Pesquisa Englishware" />
              <input type="button" name="p_submit" class="groovybutton" value="Estou Com Paciência" onclick="telaConsulta();"/>
              <input type="button" name="p_submit" class="groovybutton" value="Reenviar E-mails" onclick="telaReenviarEmails();"/>
        </form>
    </center> 
</body>

</html>