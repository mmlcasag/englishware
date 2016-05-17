<?php 
header('Content-Type: text/html; charset=utf-8');

ini_set('default_charset', 'UTF-8');
ini_set('display_errors', 'Off');

require_once 'aulas.php';
require_once 'database/functions.php';

startDatabase();

$operacao = "I";

$alu_codigo = "";
$alu_nome = "";
$alu_email = "";
$alu_fone = "";
$niv_codigo = "";
$niv_descricao = "";

if ($_GET) {
    $alu_codigo = addslashes(trim($_GET["p_alu_codigo"]));
    
    if (empty($alu_codigo)) {
        $operacao = "I";
    } else {
        $operacao = "A";
    }
}

$p_fil_dtini = "01/" . date("m/Y");
$p_fil_dtfim = date("t",strtotime("today")) . "/" . date("m/Y");

// ------------------- Query para montar o combo dos níveis ------------------- 
$query  = sprintf(" SELECT n.niv_codigo, n.niv_descricao ");
$query .= sprintf(" FROM   niveis n ");
$query .= sprintf(" ORDER  BY n.niv_codigo ");
$niveis = executeQuery($query);

// ------------------- Query da consulta principal da tela ------------------- 
$query  = sprintf(" SELECT a.alu_codigo, a.alu_nome, a.alu_email, a.alu_fone, n.niv_codigo, n.niv_descricao ");
$query .= sprintf(" FROM   alunos a ");
$query .= sprintf(" JOIN   niveis n ON n.niv_codigo = a.alu_nivel ");

if (empty($alu_codigo))
    $query .= sprintf(" WHERE  1 = 2 ");
else
    $query .= sprintf(" WHERE  a.alu_codigo = " . $alu_codigo);
	
$consulta = executeQuery($query);

while ($row = mysql_fetch_assoc($consulta)) {
    $alu_codigo = $row['alu_codigo'];
    $alu_nome = $row['alu_nome'];
    $alu_email = $row['alu_email'];
    $alu_fone = $row['alu_fone'];
    $niv_codigo = $row['niv_codigo'];
    $niv_descricao = $row['niv_descricao'];
}

?>

<html>

<head>
    <title>:: ENGLISHWARE ::</title>
    <link rel="stylesheet" type="text/css" href="style.css"/>
    <script language="javascript" type="text/javascript">
        function excluiAluno() {
            if (confirm("Você tem certeza que deseja excluir este aluno?"))
                location.href="exclui_aluno.php?p_alu_codigo=" + document.main.p_alu_codigo.value;
        }
        function calculaDias() {
            if (confirm("Você tem certeza que deseja calcular os dias de aula no mês para este aluno?\nSe você já calculou para este aluno nesse mês, essa operação poderá sobrescrever alterações já feitas.\nDeseja continuar?"))
                location.href="calcula_dias.php?p_alu_codigo=" + document.main.p_alu_codigo.value;
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
                    <input type="submit" name="p_submit" value="Pesquisa Englishware" class="groovybutton" onMouseOver="goLite(this.form.name,this.name)" onMouseOut="goDim(this.form.name,this.name)"/>
                </td>
            </tr>
        </table>
    </form>
    
    <form action="grava_aluno.php" method="post" name="main" enctype="multipart/form-data">
        <table border="0" cellspacing="0" cellpadding="20" width="100%">
            <tr>
                <td width="170px" align="left" valign="top">
                    <?php 
                        if ($operacao == "A") {
                            echo '<font face="tahoma" size="3"><a href="/englishware/aluno.php">Adicionar Aluno</a></font>';
                            echo '<br/>';
                            echo '<br/>';
                            echo '<br/>';
                        }
                    ?>
                    <font face="tahoma" size="3"><a href="javascript:document.main.submit();">Salvar Alterações</a></font>
                    <br/>
                    <br/>
                    <?php 
                        if ($operacao == "A") {
                            echo '<font face="tahoma" size="3"><a href="javascript:;" onclick="excluiAluno();">Excluir Aluno</a></font>';
                            echo '<br/>';
                            echo '<br/>';
                            echo '<br/>';
                            echo '<font face="tahoma" size="3"><a href="javascript:;" onclick="calculaDias();">Calcular Aulas Mês</a></font>';
                            echo '<br/>';
                            echo '<br/>';
                            
                            // Verifica que já foram calculados os dias para este aluno
                            $query  = sprintf(" SELECT COUNT(*) AS qtd ");
                            $query .= sprintf(" FROM   projecao_mensal ");
                            $query .= sprintf(" WHERE  pjm_revisado <> 9 ");
                            $query .= sprintf(" AND    pjm_aluno = $alu_codigo ");
                            $query .= sprintf(" AND    pjm_ano = " . date('Y'));
                            $query .= sprintf(" AND    pjm_mes = " . date('m'));
                            $dias   = executeQuery($query);
                            
                            while ($dia = mysql_fetch_assoc($dias)) {
                                if ($dia["qtd"] > 0) {
                                    echo '<font face="tahoma" size="3"><a href="/englishware/dias.php?p_fil_aluno=' . $alu_codigo . '&p_fil_dtini=' . $p_fil_dtini . '&p_fil_dtfim=' . $p_fil_dtfim . '&p_fil_status=">Alterar Aulas Mês</a></font>';
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo '<font face="tahoma" size="3"><a href="/englishware/email.php?p_alu_codigo=' . $alu_codigo . '">Enviar E-mails Mês</a></font>';
                                    echo '<br/>';
                                    echo '<br/>';
                                }
                            }
                        }
                    ?>
                    <br/>
                    <br/>
                    <font face="tahoma" size="2"><a href="javascript:history.go(-1);">Voltar</a></font>
                </td>
                <td align="left" valign="top">
                    <table border="0" width="100%">
                        <tr>
                            <td width="75px">
                                <font face="tahoma" size="2" color="black"><b>Nome</b></font>
                                <font face="tahoma" size="2" color="red"><b>*</b></font>
                            </td>
                            <td>
                                <input type="text" name="p_alu_nome" size="50" maxlength="50" value="<?php echo $alu_nome; ?>"/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <font face="tahoma" size="2" color="black"><b>Nível</b></font>
                                <font face="tahoma" size="2" color="red"><b>*</b></font>
                            </td>
                            <td>
                                <select name="p_alu_nivel" style="width: 326px;">
                                <?php
                                    if (empty($niv_codigo)) 
                                        echo '<option selected value=""></option>';
                                    else
                                        echo '<option value=""></option>';
                                    
                                    // Faz fetch no resultset
                                    while ($row = mysql_fetch_assoc($niveis)) {
                                        if ($niv_codigo == $row['niv_codigo'])
                                            echo '<option selected value="' . $row['niv_codigo'] . '">' . $row['niv_descricao']. '</option>';
                                        else
                                            echo '<option value="' . $row['niv_codigo'] . '">' . $row['niv_descricao']. '</option>';
                                    }
                                ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><font face="tahoma" size="2" color="black"><b>E-mail</b></font></td>
                            <td><input type="text" name="p_alu_email" size="50" maxlength="50" value="<?php echo $alu_email; ?>"/></td>
                        </tr>
                        <tr>
                            <td><font face="tahoma" size="2" color="black"><b>Fone</b></font></td>
                            <td><input type="text" name="p_alu_fone" size="50" maxlength="50" value="<?php echo $alu_fone; ?>"/></td>
                        </tr>
                        <tr>
                            <td><br/></td>
                            <td>
                                <font face="tahoma" size="2" color="red"><b>* Campos obrigatórios</b></font>
                                <input type="hidden" name="p_alu_codigo" value="<?php echo $alu_codigo; ?>"/>
                                <input type="submit" style="visibility: hidden;"/>
                            </td>
                        </tr>
                    </table>

                    <table border="0" width="410px">
                        <tr>
                            <td align="left" colspan="4"><br/><font face="Tahoma" size="2"><b>Aulas</b></font><br/><br/></td>
                        </tr>
                        <tr>
                            <td align="left"   width="40%"><font face="tahoma" size="2"><b>Dia da Semana</b></font></td>
                            <td align="center" width="20%"><font face="tahoma" size="2"><b>Início</b></font>&nbsp;<font color="red" size="1"><sub>00:00</sub></font></td>
                            <td align="center" width="20%"><font face="tahoma" size="2"><b>Final</b></font>&nbsp;<font color="red" size="1"><sub>00:00</sub></font></td>
                            <td align="center" width="20%"><font face="tahoma" size="2"><b>Preço</b></font>&nbsp;<font color="red" size="1"><sub>R$ 0,00</sub></font></td>
                        </tr>
                        <?php
                            for ($i = 1; $i <= 7; $i++) {
                                // busca dados da aula no dia
                                $aul_hor_ini = "";
                                $aul_hor_fim = "";
                                $aul_preco   = "";
                                
                                $aulas = getInfoAula($alu_codigo, $i);
                                
                                while ($row = mysql_fetch_assoc($aulas)) {
                                    $aul_hor_ini = date('H:i',strtotime($row['aul_hor_ini']));
                                    $aul_hor_fim = date('H:i',strtotime($row['aul_hor_fim']));
                                    $aul_preco   = number_format($row['aul_preco'],2,',','.');
                                }
                                
                                switch ($i) {
                                    case 1: $dia_semana = "Domingo"; break;
                                    case 2: $dia_semana = "Segunda-feira"; break;
                                    case 3: $dia_semana = "Terça-feira"; break;
                                    case 4: $dia_semana = "Quarta-feira"; break;
                                    case 5: $dia_semana = "Quinta-feira"; break;
                                    case 6: $dia_semana = "Sexta-feira"; break;
                                    case 7: $dia_semana = "Sábado"; break;
                                }
                                
                                echo '<tr>';
                                echo '	<td><input type="hidden" name="p_aul_dia[' . $i . ']" value="' . $i . '"/><font face="tahoma" size="2">' . $dia_semana . '</font></td> ';
                                echo '	<td><input type="text"   name="p_aul_hor_ini[' . $i . ']" maxlenght="5" style="width:100%; text-align:center;" value="' . $aul_hor_ini . '"/></td> ';
                                echo '	<td><input type="text"   name="p_aul_hor_fim[' . $i . ']" maxlenght="5" style="width:100%; text-align:center;" value="' . $aul_hor_fim . '"/></td> ';
                                echo '	<td><input type="text"   name="p_aul_preco[' . $i . ']"   maxlenght="5" style="width:100%; text-align:right; " value="' . $aul_preco   . '"/></td> ';
                                echo '</tr>';
                            }
                        ?>
                    </table>
                </td>
            </tr>
        </table>
    </form>
  
</body>

</html>