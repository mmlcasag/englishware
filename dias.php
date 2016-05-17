<?php 
header('Content-Type: text/html; charset=utf-8');

ini_set('default_charset', 'UTF-8');
ini_set('display_errors', 'Off');

require_once 'aulas.php';
require_once 'messages/functions.php';
require_once 'database/functions.php';

startDatabase();

// Variaveis
$cont = 0;
$total = 0;

// Parâmetros
$p_search = "";
$p_fil_aluno = "";
$p_fil_dtini = "";
$p_fil_dtfim = "";
$p_fil_status = "";

if ($_REQUEST) {
    $p_fil_aluno = addslashes(trim($_REQUEST["p_fil_aluno"]));
    $p_fil_dtini = addslashes(trim($_REQUEST["p_fil_dtini"]));
    $p_fil_dtfim = addslashes(trim($_REQUEST["p_fil_dtfim"]));
    $p_fil_status = addslashes(trim($_REQUEST["p_fil_status"]));
}

// ------------------- Query para montar o combo dos alunos ------------------- 
$query  = sprintf(" SELECT a.alu_codigo, a.alu_nome ");
$query .= sprintf(" FROM   alunos a ");
$query .= sprintf(" ORDER  BY a.alu_nome ");
$alunos = executeQuery($query);

// ------------------- Query da consulta principal da tela ------------------- 
$query = sprintf(" SELECT * FROM projecao_mensal ");

if (empty($p_fil_aluno) && empty($p_fil_dtini) && empty($p_fil_dtfim)) {
    $query .= sprintf(" WHERE 1 = 2 ");
} else {
    $query .= sprintf(" WHERE 1 = 1 ");
    
    if (!empty($p_fil_aluno)) {
        $query .= sprintf(" AND pjm_aluno = " . $p_fil_aluno);
    }
    if (!empty($p_fil_dtini)) {
        if (validateDate($p_fil_dtini)) {
            $query .= " AND pjm_data_aula >= STR_TO_DATE('" . $p_fil_dtini . "','%d/%m/%Y')";
        } else {
            showMessage(9,"ESSA DATA (" . $p_fil_dtini . ") TA INVÁLIDA PORRA! UTILIZE O FORMATO DD/MM/YYYY!","javascript:history.go(-1);");
            die();
        }
    }
    if (!empty($p_fil_dtfim)) {
        if (validateDate($p_fil_dtfim)) {
            $query .= " AND pjm_data_aula <= STR_TO_DATE('" . $p_fil_dtfim . "','%d/%m/%Y')";
        } else {
            showMessage(9,"ESSA DATA (" . $p_fil_dtfim . ") TA INVÁLIDA PORRA! UTILIZE O FORMATO DD/MM/YYYY!","javascript:history.go(-1);");
            die();
        }
    }
}
if (!empty($p_fil_status)) {
    $query .= sprintf(" AND pjm_revisado = " . $p_fil_status);
}

$query .= sprintf(" ORDER BY pjm_data_aula, pjm_hor_ini, pjm_hor_fim, pjm_aluno_nome ");
$consulta = executeQuery($query);

// ------------------- Query para buscar os valores de acréscimo e descontos ------------------- 
$query  = sprintf(" SELECT distinct p.pjm_acrescimos, p.pjm_descontos ");
$query .= sprintf(" FROM   projecao_mensal p ");
$query .= sprintf(" WHERE  p.pjm_ano = " . substr($p_fil_dtini,6,4));
$query .= sprintf(" AND    p.pjm_mes = " . substr($p_fil_dtini,3,2));

if (!empty($p_fil_aluno)) {
	$query .= sprintf(" AND    p.pjm_aluno = " . $p_fil_aluno);
}

$valores = executeQuery($query);

// Faz fetch no resultset
while ($row = mysql_fetch_assoc($valores)) {
    $acrescimos = number_format($row["pjm_acrescimos"],2,',','.');
    $descontos  = number_format($row["pjm_descontos"],2,',','.');
}

?>

<html>

<head>
    <title>:: ENGLISHWARE ::</title>
    <link rel="stylesheet" type="text/css" href="style.css"/>
</head>

<script language="javascript">
    function inverteGeral() {
        var elements = document.getElementsByClassName("p_cancelar");
        for(var i = 0; i < elements.length; i++) {
            elements[i].value = elements[i].value == 1 ? 9 : 1;
        }
    }
</script>

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
                <input type="text" size="80%" name="p_search" style="height:25px;" value="<?php echo $p_search; ?>"/>
                <input type="submit" name="p_submit" value="Pesquisa Englishware" class="groovybutton" onMouseOver="goLite(this.form.name,this.name)" onMouseOut="goDim(this.form.name,this.name)"/>
            </td>
        </tr>
    </table>
    
    </form>
    
    <table border="0" cellspacing="0" cellpadding="20" width="100%">
        <tr>
            <td width="170px" align="left" valign="top">
                <font face="tahoma" size="3"><a href="/englishware/aula.php">Adicionar Aula</a></font>
                <br/>
                <br/>
                <br/>
                <font face="tahoma" size="3"><a href="javascript:document.main.submit();">Salvar Alterações</a></font>
                <br/>
                <br/>
                <br/>
                <br/>
                <font face="tahoma" size="2"><a href="/englishware/index.php">Voltar</a></font>
            </td>
            
            <td align="left" valign="top">
                
                <form action="dias.php" method="post" name="filtros" enctype="multipart/form-data">
                
                <table align="center" border="0" cellspacing="1" cellpadding="2" width="100%">
                    
                    <tr>
                      <th align="left"><font face="tahoma" size="2">Período</font></th>
                      <td align="left" colspan="6">
                        <font face="tahoma" size="2">
                          <input type="text" name="p_fil_dtini" size="10" maxlength="10" value="<?php echo $p_fil_dtini;?>"> até <input type="text" name="p_fil_dtfim" size="10" maxlength="10" value="<?php echo $p_fil_dtfim;?>">
                        </font>
                      </td>
                    </tr>
                    
                    <tr>
                        <th align="left" width="100px"><font face="tahoma" size="2">Aluno</font></th>
                        <td align="left" colspan="6">
                            <font face="tahoma" size="2">
                            <select name="p_fil_aluno" style="width:326px;">
                                <?php
                                    if (empty($p_fil_aluno)) 
                                        echo '<option selected value="">Todos</option>';
                                    else
                                        echo '<option value="">Todos</option>';

                                    // Faz fetch no resultset
                                    while ($row = mysql_fetch_assoc($alunos)) {
                                        if ($p_fil_aluno == $row['alu_codigo'])
                                            echo '<option selected value="' . $row['alu_codigo'] . '">' . $row['alu_nome']. '</option>';
                                        else
                                            echo '<option value="' . $row['alu_codigo'] . '">' . $row['alu_nome']. '</option>';
                                    }
                                ?>
                            </select>
                            </font>
                        </td>
                    </tr>
                    
                    <tr>
                        <th align="left"><font face="tahoma" size="2">Situação</font></th>
                        <td align="left" colspan="6">
                            <font face="tahoma" size="2">
                                <select name="p_fil_status" style="width:326px;">
                                <?php 
                                    if (empty($p_fil_status)) {
                                        echo '<option selected value="">Todas</option>';
                                        echo '<option value="1">Aula Normal</option>';
                                        echo '<option value="9">Desmarcada</option>';
                                    } else if ($p_fil_status == 1) {
                                        echo '<option value="">Todas</option>';
                                        echo '<option selected value="1">Aula Normal</option>';
                                        echo '<option value="9">Desmarcada</option>';
                                    } else if ($p_fil_status == 9) {
                                        echo '<option value="">Todas</option>';
                                        echo '<option value="1">Aula Normal</option>';
                                        echo '<option selected value="9">Desmarcada</option>';
                                    }
                                ?>
                                </select>
                            </font>
                        </td>
                    </tr>
                    
                    <tr>
                        <th align="left"><br><input type="submit" value="Consultar"></th>
                    </tr>
                    
                </table>
                
                </form>
                
                <br>
                
                <form action="grava_dias.php" method="post" name="main" enctype="multipart/form-data">

                <table align="center" border="0" cellspacing="1" cellpadding="2" width="100%">

                    <tr>
                        <th align="left">  <font face="tahoma" size="2">Aluno</font></th>
                        <th align="center"><font face="tahoma" size="2">Data Aula</font></th>
                        <th align="center"><font face="tahoma" size="2">Dia Semana</font></th>
                        <th align="center"><font face="tahoma" size="2">Hora Início</font></th>
                        <th align="center"><font face="tahoma" size="2">Hora Fim</font></th>
                        <th align="right"> <font face="tahoma" size="2">Valor Aula</font></th>
                        <th align="center" width="110px"><font face="tahoma" size="2">Situação</font></th>
                    </tr>

                    <?php
                        while ($row = mysql_fetch_assoc($consulta)) {
                            $cont  = $cont  + 1;
                            $total = $total + $row["pjm_vlr_aula"];			
                            
                            echo '<input type="hidden" name="p_aluno[' . $cont . ']" value="' . $row["pjm_aluno"] . '">';
                            echo '<input type="hidden" name="p_data[' . $cont . ']"  value="' . $row["pjm_data_aula"] . '">';
                            
                            echo '<tr>';
                            echo '  <td align="left">  <font face="tahoma" size="2">' . $row["pjm_aluno_nome"] . '</font></td>';
                            echo '  <td align="center"><font face="tahoma" size="2">' . date('d/m/Y',strtotime($row["pjm_data_aula"])) . '</font></td>';
                            echo '  <td align="center"><font face="tahoma" size="2">' . $row["pjm_dia_semana"] . '</font></td>';
                            echo '  <td align="center"><font face="tahoma" size="2">' . date('H:i',strtotime($row["pjm_hor_ini"])) . '</font></td>';
                            echo '  <td align="center"><font face="tahoma" size="2">' . date('H:i',strtotime($row["pjm_hor_fim"])) . '</font></td>';
                            echo '  <td align="right"> <font face="tahoma" size="2">' . number_format($row["pjm_vlr_aula"],2,',','.') . '</font></td>';
                            echo '  <td align="center"> ';
                            echo '    <font face="tahoma" size="2"> ';
                            echo '      <select class="p_cancelar" name="p_cancelar[' . $cont . ']"> ';
                            
                            if ($row["pjm_revisado"] == 1) {
                                echo ' <option selected value="1">Aula Normal</option> ';
                                echo ' <option value="9">Desmarcada</option> ';
                            } else {
                                echo ' <option value="1">Aula Normal</option> ';
                                echo ' <option selected value="9">Desmarcada</option> ';
                            }
                            
                            echo '      </select> ';
                            echo '    </font> ';
                            echo '  </td> ';
                            echo '</tr> ';
                        }
                        
                        echo '<tr>';
                        echo '  <th align="center" colspan="5"><font face="tahoma" size="2"></font></th>';
                        echo '  <th align="right"  colspan="1"><font face="tahoma" size="2">' . number_format($total,2,',','.') . '</font></th>';
                        echo '  <th align="center" colspan="5"><font face="tahoma" size="2"></font></th>';
                        echo '</tr>';
                        
                        echo '<tr>';
                        echo '  <th align="left" colspan="7"><font face="tahoma" size="2"><input type="button" value="Inverter Geral" onclick="inverteGeral();"></font></th>';
                        echo '</tr>';
                        
                        echo '</table>';
                        echo '<br />';
                        echo '<table align="center" border="0" cellspacing="1" cellpadding="2" width="100%">';
						
						echo '<tr>';
						echo '  <th align="left" width="100px"><font face="tahoma" size="2">Acréscimos:</font></th>';
						echo '  <td align="left"><font face="tahoma" size="2"><input type="text" name="p_acrescimos" size="7" maxlenght="7" value="' . $acrescimos . '"></font></td>';
						echo '</tr>';
						
						echo '<tr>';
						echo '  <th align="left" width="100px"><font face="tahoma" size="2">Descontos:</font></th>';
						echo '  <td align="left"><font face="tahoma" size="2"><input type="text" name="p_descontos" size="7" maxlenght="7" value="' . $descontos . '"></font></td>';
						echo '</tr>';
						
                        if (mysql_num_rows($consulta) == 0) {
                            echo '<tr>';
                            echo '  <td align="left" colspan="2">';
                            echo '    <br><font face="tahoma" size="2" color="black">';
                            echo ' Sua pesquisa não encontrou nenhum registro correspondente.<br/><br/>Sugestões:<br/><br/>';
                            echo ' <ul>';
                            echo '   <li>Certifique-se de que você preencheu os filtros acima de forma que traga algum resultado válido.</li>';
                            echo '   <li>Certifique-se de que você já calculou os dias de aulas para o(s) aluno(s) ou período(s) desejado(s).</li>';
                            echo ' </ul>';
                            echo '    </font>';
                            echo '  </td>';
                            echo '</tr>';
                        } else {
                            echo '<tr>';
                            echo '  <td align="left" colspan="2"><font face="tahoma" size="2" color="red"><br>* ' . mysql_num_rows($consulta) . ' registro(s) em 0.0472 segundos. Quer mais rápido? Clique <a href="http://www.formula1.com/default.html" target="_blank">aqui</a></font></td>';
                            echo '</tr>';
                        }
                    ?>
                </table>
                
                <input type="submit" style="visibility: hidden;"/>
                
                </form>
            </td>
        </tr>
    </table>
    
</body>

</html>