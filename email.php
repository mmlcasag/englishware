<?php 
header('Content-Type: text/html; charset=utf-8');

ini_set('default_charset', 'UTF-8');
ini_set('display_errors', 'Off');
?>

<html>

<head>
    <title>:: ENGLISHWARE ::</title>
</head>

<body>

<table align="center" border="1" cellspacing="1" cellpadding="4" width="100%">

<tr>
    <td align="center" colspan="5"><b>Relação de Valores a Receber</b></td>
</tr>

<tr>
    <td align="left"><b>Nome do Aluno</b></td>
    <td align="right"><b>Total de Aulas</b></td>
    <td align="right"><b>Acréscimos</b></td>
    <td align="right"><b>Descontos</b></td>
    <td align="right"><b>Total Geral</b></td>
</tr>

<?php

require_once 'messages/functions.php';
require_once 'database/functions.php';
require_once 'sendmail/functions.php';

$aluno 	     = 0;
$aluno_ant   = 0;
$aul_preco   = 0;
$qtd_aulas   = 0;
$vlr_aluno   = 0;
$vlr_total   = 0;
$tot_acres   = 0;
$tot_desc    = 0;
$nome  	     = "";
$mail 	     = "";
$email       = "";
$aul_hor_ini = "";
$aul_hor_fim = "";
$alu_codigo  = "";

if ($_GET) {
    $alu_codigo = addslashes(trim($_GET["p_alu_codigo"]));
}

startDatabase();
set_time_limit(1000);

$query  = sprintf("SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED");
$temp   = executeQuery($query);

// ------------------- Query da consulta principal da tela ------------------- 
$query  = sprintf(" SELECT pjm_ano, pjm_mes, pjm_aluno, alu_nome as pjm_aluno_nome, alu_email as pjm_aluno_email ");
$query .=         " ,      DATE_FORMAT(pjm_data_aula,'%d/%m/%Y') as pjm_data_aula, pjm_dia_semana, pjm_hor_ini ";
$query .= sprintf(" ,      pjm_hor_fim, pjm_vlr_aula, pjm_revisado, pjm_acrescimos, pjm_descontos ");
$query .= sprintf(" FROM   projecao_mensal, alunos ");
$query .= sprintf(" WHERE  alu_codigo = pjm_aluno ");
$query .= sprintf(" AND    pjm_ano    = " . date('Y'));
$query .= sprintf(" AND    pjm_mes    = " . date('m'));
$query .= sprintf(" AND    pjm_revisado <> 9 ");

if (!empty($alu_codigo)) {
    $query .= sprintf(" AND pjm_aluno = " . $alu_codigo);
}

$query .= sprintf(" ORDER  BY pjm_ano, pjm_mes, alu_nome, pjm_data_aula, pjm_hor_ini, pjm_hor_fim");
$alunos = executeQuery($query);

// for each student...
while ($row = mysql_fetch_assoc($alunos)) {
    // quando termina um aluno e começa outro...
    if ($aluno_ant != $row["pjm_aluno"]) {
        // na primeira vez que entra no loop não manda email
        if ($aluno_ant != 0) { 
            // monta rodapé do email
            $email .= '</ul>';
			$email .= 'Total de aulas: <b>' . $qtd_aulas . '</b>';
			$email .= '<br/>';
			if (($acrescimos > 0) || ($descontos > 0)) {
				$email .= 'Valor das aulas: <b>R$ ' . number_format($vlr_aluno,2,',','.') . '</b>';
				$email .= '<br/>';
			}
			if ($acrescimos > 0) {
				$email .= 'Valor de acréscimo: <b>R$ ' . $acrescimos . '</b>';
				$email .= '<br/>';
			}
			if ($descontos > 0) {
				$email .= 'Valor de desconto: <b>R$ ' . $descontos . '</b>';
				$email .= '<br/>';
			}
			$email .= 'Valor total a pagar: <b>R$ ' . number_format($vlr_aluno + $acrescimos - $descontos,2,',','.') . '</b>';
			$email .= '<br/>';
			$email .= '<font color="red">Pagando até o dia 05 via transferência (Itaú ou Santander): <b>R$ ' . number_format(($vlr_aluno * 0.95) + $acrescimos - $descontos,2,',','.') . '</b></font>';
			$email .= '<br/>';
			$email .= '<br/>';
			$email .= '</font>';
			$email .= '<b>Observações:</b>';
			$email .= '<br/>';
			$email .= 'Para pagamentos realizados ATÉ o dia 05 é possível pagar utilizando cartão de crédito. Quem tiver interesse, favor entrar em contato.';
			$email .= '<br/>';
			$email .= '<br/>';
			$email .= 'Dúvidas, alterações ou informações bancárias, verificar dados ao lado.';
			$email .= '</font>';
			$email .= '</td></tr></table></body></html>';
            
            $tot_acres = $tot_acres + $acrescimos;
            $tot_desc = $tot_desc + $descontos;
            
            // envia email
            if (!empty($mail)) {
                echo '<tr>
                        <td align="left">' . $nome . '</td>
                        <td align="right">R$ ' . number_format($vlr_aluno,2,',','.') . '</td>
                        <td align="right">R$ ' . $acrescimos . '</td>
                        <td align="right">R$ ' . $descontos . '</td>
                        <td align="right">R$ ' . number_format($vlr_aluno + $acrescimos - $descontos,2,',','.') . '</td>
                      </tr>';
                email($mail, 'Aulas do Mês - ' . date("m/Y") . ' - ' . $nome, $email);
                sleep(1);
                flush();
            }
        }
        
        // zera variáveis
        $vlr_aluno = 0;
        $qtd_aulas = 0;
        $aluno_ant = $row["pjm_aluno"];
        $aluno     = $row["pjm_aluno"];
        $nome      = $row["pjm_aluno_nome"];
        $mail      = $row["pjm_aluno_email"];
        
        // monta cabeçalho do email
        $email  = '<html><head><title>:: ENGLISHWARE ::</title></head><body><table border="0" class="barra" cellspacing="0" cellpadding="20" width="100%"><tr><td width="170px" nowrap><b><font type="Tahoma" style="font-size:24px;" color="blue">E</font><font type="Tahoma" style="font-size:24px;" color="red">n</font><font type="Tahoma" style="font-size:24px;" color="orange">g</font><font type="Tahoma" style="font-size:24px;" color="blue">l</font><font type="Tahoma" style="font-size:24px;" color="green">i</font><font type="Tahoma" style="font-size:24px;" color="red">s</font><font type="Tahoma" style="font-size:24px;" color="blue">h</font><font type="Tahoma" style="font-size:24px;" color="red">w</font><font type="Tahoma" style="font-size:24px;" color="orange">a</font><font type="Tahoma" style="font-size:24px;" color="blue">r</font><font type="Tahoma" style="font-size:24px;" color="green">e</font></b></td><td><br></td></tr></table></form><table border="0" cellspacing="0" cellpadding="20" width="100%"><tr><td width="170px" align="left" bgcolor="#DEDEDE" valign="top"><sub><b>Fabiana Branchini</b><br><br>Celular: (54) 9974-6881<br>Residencial: (54) 3201-1616<br>E-mail: fabibr@gmail.com<br>CPF: 699.616.030.87<br><br><b>Banco Itaú</b><br>Agência: 3249<br>C.Corrente: 22.536-2<br><br><b> Banco Santander</b><br>Agência: 0189<br>C.Corrente: 01.030573.1<br><br><b> Banco Bradesco</b><br>Agência: 1775<br>C.Corrente: 42175-8<br>Márcio Luis Casagrande <br></sub></td><td align="left" valign="top"><font type="Tahoma" style="font-size:20px;" color="black"><b>Demonstrativo das Aulas do Mês</b></font><br>';
        $email .= '<font face="tahoma" size="2">';
        $email .= '<br/>Olá ' . $nome . '!<br><br>Confirmando então as aulas para o mês ' . date("m/Y") . ':<br/><ul>';
    }
    
    $qtd_aulas   = $qtd_aulas + 1;
    $aul_hor_ini = date('H:i',strtotime($row['pjm_hor_ini']));
    $aul_hor_fim = date('H:i',strtotime($row['pjm_hor_fim']));
    $aul_preco   = number_format($row['pjm_vlr_aula'],2,',','.');
    $acrescimos  = number_format($row['pjm_acrescimos'],2,',','.');
    $descontos   = number_format($row['pjm_descontos'],2,',','.');
    $vlr_aluno   = $vlr_aluno + $aul_preco;
    $vlr_total   = $vlr_total + $aul_preco;
    $email      .= '<li>Dia <b>' . $row["pjm_data_aula"] . '</b> (' . $row["pjm_dia_semana"] . '), das <b>' . $aul_hor_ini . '</b> às <b>' . $aul_hor_fim . '</b></li>';
}

// monta rodapé do email
if ($qtd_aulas > 0) {
    // monta rodapé do email
    $email .= '</ul>';
	$email .= 'Total de aulas: <b>' . $qtd_aulas . '</b>';
	$email .= '<br/>';
	if (($acrescimos > 0) || ($descontos > 0)) {
		$email .= 'Valor das aulas: <b>R$ ' . number_format($vlr_aluno,2,',','.') . '</b>';
		$email .= '<br/>';
	}
	if ($acrescimos > 0) {
		$email .= 'Valor de acréscimo: <b>R$ ' . $acrescimos . '</b>';
		$email .= '<br/>';
	}
	if ($descontos > 0) {
		$email .= 'Valor de desconto: <b>R$ ' . $descontos . '</b>';
		$email .= '<br/>';
	}
	$email .= 'Valor total a pagar: <b>R$ ' . number_format($vlr_aluno + $acrescimos - $descontos,2,',','.') . '</b>';
	$email .= '<br/>';
	$email .= '<font color="red">Pagando até o dia 05 via transferência (Itaú ou Santander): <b>R$ ' . number_format(($vlr_aluno * 0.95) + $acrescimos - $descontos,2,',','.') . '</b></font>';
	$email .= '<br/>';
	$email .= '<br/>';
	$email .= '</font>';
	$email .= '<b>Observações:</b>';
	$email .= '<br/>';
	$email .= 'Para pagamentos realizados ATÉ o dia 05 é possível pagar utilizando cartão de crédito. Quem tiver interesse, favor entrar em contato.';
	$email .= '<br/>';
	$email .= '<br/>';
	$email .= 'Dúvidas, alterações ou informações bancárias, verificar dados ao lado.';
	$email .= '</font>';
	$email .= '</td></tr></table></body></html>';
    
    $tot_acres = $tot_acres + $acrescimos;
    $tot_desc = $tot_desc + $descontos;
    
    // envia email
    if (!empty($mail)) {
        echo '<tr>
                <td align="left">' . $nome . '</td>
                <td align="right">R$ ' . number_format($vlr_aluno,2,',','.') . '</td>
                <td align="right">R$ ' . $acrescimos . '</td>
                <td align="right">R$ ' . $descontos . '</td>
                <td align="right">R$ ' . number_format($vlr_aluno + $acrescimos - $descontos,2,',','.') . '</td>
              </tr>';
        email($mail, 'Aulas do Mês - ' . date("m/Y") . ' - ' . $nome, $email);
        sleep(1);
        flush();
    }
}

?>

<tr>
    <td align="right"><br /></td>
    <td align="right"><b>R$ <?php echo number_format($vlr_total,2,',','.');?></b></td>
    <td align="right"><b>R$ <?php echo number_format($tot_acres,2,',','.');?></b></td>
    <td align="right"><b>R$ <?php echo number_format($tot_desc,2,',','.');?></b></td>
    <td align="right"><b>R$ <?php echo number_format($vlr_total + $tot_acres - $tot_desc,2,',','.');?></b></td>
</tr>

</table>

<br>
<center><a href="telaFim.php">[Avançar]</a></center>
<br>

</body>

</html>