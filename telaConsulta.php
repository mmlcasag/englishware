<?php 
header('Content-Type: text/html; charset=utf-8');

ini_set('default_charset', 'UTF-8');
ini_set('display_errors', 'Off');

require_once 'database/functions.php';

startDatabase();

// Parâmetros
$p_search = "";

if ($_POST) {
    $p_search = addslashes(trim($_POST["p_search"]));
}

$p_fil_dtini = "01/" . date("m/Y");
$p_fil_dtfim = date("t",strtotime("today")) . "/" . date("m/Y");

// ------------------- Query da consulta principal da tela ------------------- 
$query  = sprintf(" SELECT a.alu_codigo, a.alu_nome, a.alu_email, a.alu_fone, n.niv_descricao ");
$query .= sprintf(" FROM   alunos a ");
$query .= sprintf(" JOIN   niveis n ON n.niv_codigo = a.alu_nivel ");
$query .= sprintf(" WHERE  1 = 1 ");

if (!empty($p_search)) {
    $query .= sprintf(" AND ( ");
    $query .= sprintf("       LOWER(a.alu_nome)      LIKE LOWER('%%" . $p_search . "%%') ");
    $query .= sprintf("   OR  LOWER(a.alu_email)     LIKE LOWER('%%" . $p_search . "%%') ");
    $query .= sprintf("   OR  LOWER(a.alu_fone)      LIKE LOWER('%%" . $p_search . "%%') ");
    $query .= sprintf("   OR  LOWER(n.niv_descricao) LIKE LOWER('%%" . $p_search . "%%') ");
    $query .= sprintf(" ) ");
}

$query .= sprintf(" ORDER  BY a.alu_nome ");
$consulta = executeQuery($query);

?>

<html>

<head>
    <title>:: ENGLISHWARE ::</title>
    <link rel="stylesheet" type="text/css" href="style.css"/>
    <script language="javascript" type="text/javascript">
        function enviarEmailGeral() {
            if (confirm("Você tem certeza que deseja enviar e-mail para TODOS os alunos?\n\nPode ser que este processo demore alguns minutos.\nPor favor, não pire o cabeção."))
                location.href="email_geral.php";
        }
        function calculaDias() {
            if (confirm("Você tem certeza que deseja calcular os dias de aula no mês para todos os alunos?\nSe você já calculou para algum aluno nesse mês, essa operação poderá sobrescrever alterações já feitas.\nDeseja continuar?"))
                location.href="calcula_dias.php";
        }
    </script>
</head>

<body>
    
    <form action="telaConsulta.php" method="post" name="search" enctype="multipart/form-data">
    
    <table border="0" class="barra" cellpadding="20" cellspacing="0" width="100%">
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
                <input type="text" size="80" name="p_search" style="height:25px;" value="<?php echo $p_search; ?>"/>
                <input type="submit" name="p_submit" value="Pesquisa Englishware" class="groovybutton" onMouseOver="goLite(this.form.name,this.name)" onMouseOut="goDim(this.form.name,this.name)"/>
            </td>
        </tr>
    </table>
    
    </form>
    
    <table border="0" cellspacing="0" cellpadding="20" width="100%">
        <tr>
            <td width="170px" align="left" valign="top">
                <font face="tahoma" size="3"><a href="/englishware/aluno.php">Adicionar Aluno</a></font>
                <br/>
                <br/>
                <br/>
                <font face="tahoma" size="3"><a href="javascript:;" onclick="calculaDias();">Calcular Aulas Mês</a></font>
                <br/>
                <br/>
                <?php
                    $query  = sprintf(" SELECT COUNT(*) AS qtd ");
                    $query .= sprintf(" FROM   projecao_mensal ");
                    $query .= sprintf(" WHERE  pjm_revisado <> 9 ");
                    $query .= sprintf(" AND    pjm_ano = " . date('Y'));
                    $query .= sprintf(" AND    pjm_mes = " . date('m'));
                    $dias   = executeQuery($query);
                    
                    while ($dia = mysql_fetch_assoc($dias)) {
                        if ($dia["qtd"] > 0) {
                            echo '<font face="tahoma" size="3"><a href="/englishware/dias.php?p_fil_aluno=&p_fil_dtini=' . $p_fil_dtini . '&p_fil_dtfim=' . $p_fil_dtfim . '&p_fil_status=">Alterar Aulas Mês</a></font>';
                            echo '<br/>';
                            echo '<br/>';
                            echo '<font face="tahoma" size="3"><a href="/englishware/email.php">Enviar E-mails Mês</a></font>';
                            echo '<br/>';
                            echo '<br/>';
                        }
                    }
                ?>
                <br/>
                <br/>
                <font face="tahoma" size="2"><a href="/englishware/index.php">Voltar</a></font>
            </td>
            <td align="left" valign="top">
                <table border="0" cellspacing="0" cellpadding="0" width="100%">
                <?php
                    while ($row = mysql_fetch_assoc($consulta)) {
                        echo '<tr>';
                        echo '  <td align="left">';
                        echo '    <font face="tahoma" size="3"><a href="/englishware/aluno.php?p_alu_codigo=' . $row['alu_codigo'] . '">' . $row['alu_nome'] . '</a></font>';
                        echo '    <br/>';
                        echo '    <font face="tahoma" size="2" color="green">' . $row['alu_email'] . '</font>&nbsp;&nbsp;<font face="tahoma" size="2" color="#88AAFF">' . $row['alu_fone'] . '</font>';
                        echo '    <br/>';
                        echo '    <font face="tahoma" size="2" color="black">' . $row['niv_descricao'] . '</font>';
                        echo '    <br/>';
                        echo '    <br/>';
                        echo '  </td>';
                        echo '</tr>';
                    }
                    if (mysql_num_rows($consulta) == 0) {
                        echo '<tr>';
                        echo '  <td align="left">';
                        echo '    <font face="tahoma" size="2" color="black">';
                        echo ' Sua pesquisa não encontrou nenhum documento correspondente.<br/><br/>Sugestões:<br/><br/>';
                        echo ' <ul>';
                        echo '   <li>Certifique-se de que todas as palavras estejam escritas corretamente.</li>';
                        echo '   <li>Tente palavras-chave diferentes.</li>';
                        echo '   <li>Tente palavras-chave mais genéricas.</li>';
                        echo ' </ul>';
                        echo '    </font>';
                        echo '  </td>';
                        echo '</tr>';
                    } else {
                        echo '<tr>';
                        echo '  <td align="left">';
                        echo '    <font face="tahoma" size="2" color="red">';
                        echo '       <br/> * ' . mysql_num_rows($consulta) . ' registro(s) em 0.0053 segundos. Quer mais rápido? Clique <a href="http://www.formula1.com/default.html" target="_blank">aqui</a>!';
                        echo '    </font>';
                        echo '  </td>';
                        echo '</tr>';
                    }
                ?>
                </table>
            </td>
        </tr>
    </table>
    
</body>

</html>