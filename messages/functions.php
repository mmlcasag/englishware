<?php
header('Content-Type: text/html; charset=utf-8');

ini_set('default_charset', 'UTF-8');    
ini_set('display_errors', 'Off');

function showMessage($type, $message, $url) {
    
    echo '
	<html>
        
	<head>
	  <title>:: ENGLISHWARE ::</title>
	  <link rel="stylesheet" type="text/css" href="style.css"/>
	</head>
        
	<body>
	  <center><br/><br/>';
	    switch ($type) {
                case 1:
                    echo '<img src="images/success.jpg"/>';
                    echo '<br/>';
                    echo '<br/>';
                    echo '<br/>';
                    echo '<font color="green"><h2>' . $message . '</h2></font>';
                    break;
                case 4:
                    echo '<img src="images/batore.jpg"/>';
                    echo '<br/>';
                    echo '<br/>';
                    echo '<br/>';
                    echo '<font color="green"><h2>' . $message . '</h2></font>';
                    break;
                case 8:
                    echo '<img src="images/bug.jpg"/>';
                    echo '<br/>';
                    echo '<br/>';
                    echo '<br/>';
                    echo '<font color="blue"><h2>' . $message . '</h2></font>';
                    break;
                case 9:
                    echo '<img src="images/error.jpg"/>';
                    echo '<br/>';
                    echo '<br/>';
                    echo '<br/>';
                    echo '<font color="red"><h2>' . $message . '</h2></font>';
                    break;
            }
    
    echo '
        <br/>
        <br/>
    <a href="' . $url . '">Voltar</a>
  </center>
</body>

</html>';

}