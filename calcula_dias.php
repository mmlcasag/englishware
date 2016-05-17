<?php 
header('Content-Type: text/html; charset=utf-8');

ini_set('default_charset', 'UTF-8');
ini_set('display_errors', 'Off');

require_once 'messages/functions.php';
require_once 'database/functions.php';

startDatabase();

if ($_GET)
    $alu_codigo = addslashes(trim($_GET["p_alu_codigo"]));
else
    $alu_codigo = "";

// ------------------- Query da consulta principal da tela ------------------- 
$query  = sprintf(" SELECT a.alu_codigo, a.alu_nome, a.alu_email, a.alu_fone, n.niv_codigo, n.niv_descricao ");
$query .= sprintf(" FROM   alunos a ");
$query .= sprintf(" JOIN   niveis n ON n.niv_codigo = a.alu_nivel ");
$query .= sprintf(" WHERE  EXISTS ( SELECT 1 FROM aulas x WHERE x.aul_aluno = a.alu_codigo ) ");
$query .= sprintf(" AND    a.alu_email IS NOT NULL ");

if (!empty($alu_codigo))
    $query .= sprintf(" AND  a.alu_codigo = " . $alu_codigo);

$query .= sprintf(" ORDER  BY a.alu_nome");
$alunos = executeQuery($query);

// for each student...
while ($row = mysql_fetch_assoc($alunos)) {
    $ano   = date("Y");
    $mes   = date("m");
    $aluno = $row["alu_codigo"];
    $nome  = $row["alu_nome"];
    $email = $row["alu_email"];
    $nivel = $row["niv_descricao"];
    
    $query = " delete from projecao_mensal 
               where  pjm_ano   = '$ano'
               and    pjm_mes   = '$mes'
               and    pjm_aluno = '$aluno' ";
    
    $consulta = executeQuery($query);
    
    // for each day...
    for ( $i = 1; $i <= date("t"); $i++ ) {
        $aul_data     = date("Y-m-d", mktime(0, 0, 0, date("m"), $i, date("Y"))); // holds the current date within the loop
        $current_date = date("d/m/Y", mktime(0, 0, 0, date("m"), $i, date("Y"))); // holds the current date within the loop
        $current_day  = date("w", mktime(0, 0, 0, date("m"), $i, date("Y"))) + 1; // holds the day within the week
        
        switch ($current_day) {
            case 1: $dia_semana = "Dom"; break;
            case 2: $dia_semana = "Seg"; break;
            case 3: $dia_semana = "Ter"; break;
            case 4: $dia_semana = "Qua"; break;
            case 5: $dia_semana = "Qui"; break;
            case 6: $dia_semana = "Sex"; break;
            case 7: $dia_semana = "Sáb"; break;
        }
        
        $query  = sprintf(" SELECT a.aul_hor_ini, a.aul_hor_fim, a.aul_preco ");
        $query .= sprintf(" FROM   aulas a ");
        $query .= sprintf(" WHERE  a.aul_aluno = " . $row["alu_codigo"]);
        $query .= sprintf(" AND    a.aul_dia   = " . $current_day);
        $aulas  = executeQuery($query);
        
        while ($aula = mysql_fetch_assoc($aulas)) {
            $aul_hor_ini = date('H:i',strtotime($aula['aul_hor_ini']));
            $aul_hor_fim = date('H:i',strtotime($aula['aul_hor_fim']));
            $aul_preco   = number_format($aula['aul_preco'],2,',','.');
            
            $query = " insert into projecao_mensal 
                         ( pjm_ano, pjm_mes, pjm_aluno, pjm_aluno_nome, pjm_aluno_email, pjm_aluno_nivel, pjm_data_aula, pjm_dia_semana, pjm_hor_ini, pjm_hor_fim, pjm_vlr_aula, pjm_revisado )
                       values
                         ( '$ano', '$mes', '$aluno', '$nome', '$email', '$nivel', '$aul_data', '$dia_semana', '$aul_hor_ini', '$aul_hor_fim', '$aul_preco', 1 ) ";
            
            $consulta = executeQuery($query);
            
            if (!$consulta) {
                showMessage(8,"Ocorreu um erro ao calcular os dias de aula no mês para o aluno " . $nome,"telaConsulta.php");
                die;
            }
        }
    }
}

showMessage(1,"Operação realizada com sucesso","telaConsulta.php");