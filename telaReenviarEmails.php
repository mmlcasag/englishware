<?php
header('Content-Type: text/html; charset=utf-8');

ini_set('default_charset', 'UTF-8');
ini_set('display_errors', 'Off');

require_once 'database/functions.php';

startDatabase();

$p_ano = date("Y");
$p_mes = date("m");

// ------------------- Query do combobox dos alunos ------------------- 
$query  = sprintf(" SELECT a.alu_codigo, a.alu_nome ");
$query .= sprintf(" FROM   alunos a ");
$query .= sprintf(" ORDER  BY a.alu_nome ");

$consulta = executeQuery($query);

?>

<html>

<head>
    <title>:: ENGLISHWARE ::</title>
    <link rel="stylesheet" type="text/css" href="style.css"/>
    <script language="javascript" type="text/javascript">
        function reenviarEmails() {
            if (confirm("Você tem certeza que deseja reenviar os emails para todos os alunos selecionados?")) {
                document.main.submit();
            }
            return false;
        }
    </script>
</head>

<body>
    <form action="telaConsulta.php" method="post" name="search" enctype="multipart/form-data">
        <table border="0" class="barra" cellspacing="0" cellpadding="20" width="100%">
            <tr>
                <td width="170px" nowrap>
                    <b>
                        <font face="Tahoma" style="font-size:20px;" color="blue">E</font>
                        <font face="Tahoma" style="font-size:20px;" color="red">n</font>
                        <font face="Tahoma" style="font-size:20px;" color="orange">g</font>
                        <font face="Tahoma" style="font-size:20px;" color="blue">l</font>
                        <font face="Tahoma" style="font-size:20px;" color="green">i</font>
                        <font face="Tahoma" style="font-size:20px;" color="red">s</font>
                        <font face="Tahoma" style="font-size:20px;" color="blue">h</font>
                        <font face="Tahoma" style="font-size:20px;" color="red">w</font>
                        <font face="Tahoma" style="font-size:20px;" color="orange">a</font>
                        <font face="Tahoma" style="font-size:20px;" color="blue">r</font>
                        <font face="Tahoma" style="font-size:20px;" color="green">e</font>
                    </b>
                </td>
                <td>
                    <input type="text" size="80" name="p_search" style="height:25px;" value=""/>
                    <input type="submit" name="p_submit" value="Pesquisa Englishware" class="groovybutton" />
                </td>
            </tr>
        </table>
    </form>
    
    <form action="reenviar_emails.php" method="post" name="main" enctype="multipart/form-data">
        <table border="0" cellspacing="0" cellpadding="20" width="100%">
            <tr>
                <td width="170px" align="left" valign="top">
                    <font face="tahoma" size="3"><a href="javascript:reenviarEmails();">Reenviar E-mails</a></font>
                    <br/>
                    <br/>
                    <br/>
                    <br/>
                    <font face="tahoma" size="2"><a href="javascript:history.go(-1);">Voltar</a></font>
                </td>
                <td align="left" valign="top">
                    <table border="0" width="100%">
                        <tr>
                            <td width="75px" valign="top">
                                <font face="tahoma" size="2" color="black"><b>Alunos</b></font>
                                <font face="tahoma" size="2" color="red"><b>*</b></font>
                            </td>
                            <td>
                                <select multiple name="p_arr_alunos[]" style="width: 400px; height: 325px;">
                                <?php
                                    while ($row = mysql_fetch_assoc($consulta)) {
                                        echo '<option value="' . $row['alu_codigo'] . '">' . $row['alu_nome']. '</option>';
                                    }
                                ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><br/></td>
                            <td>
                                <font face="tahoma" size="2" color="red"><b>* Campos obrigatórios<br /></b></font>
                                <input type="submit" style="visibility: hidden;"/>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </form>
  
</body>

</html>