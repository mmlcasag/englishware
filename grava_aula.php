<?php 
header('Content-Type: text/html; charset=utf-8');

ini_set('default_charset', 'UTF-8');
ini_set('display_errors', 'Off');

require_once 'aulas.php';
require_once 'messages/functions.php';
require_once 'database/functions.php';

startDatabase();

// Variáveis
$erro = false;
$mensagem = "";

// Parâmetros
if ($_POST) {
    $alu = addslashes(trim($_POST["p_aluno"]));
    $dat = addslashes(trim($_POST["p_dat_aula"]));
    $ini = addslashes(trim($_POST["p_hor_ini"]));
    $fim = addslashes(trim($_POST["p_hor_fim"]));
    $vlr = addslashes(trim($_POST["p_vlr_aula"]));
} else {
    showMessage(9,"Você não deveria estar aqui!","javascript:history.go(-1);");
}

if (!$erro && empty($alu)) {
    $erro = true;
    $mensagem = "INFORME O ALUNO PORRA!!!";
}

if (!$erro && empty($dat)) {
    $erro = true;
    $mensagem = "INFORME A DATA DA AULA PORRA!!!";
}

if (!$erro && !empty($dat) && !validateDate($dat)) {
    $erro = true;
    $mensagem = "ESSA DATA " . $dat . " TA FORA DA CASINHA!!!";
}

if (!$erro && empty($ini)) {
    $erro = true;
    $mensagem = "INFORME A HORA DE INÍCIO DA AULA PORRA!!!";
}

if (!$erro && !empty($ini) && !validateTime($ini)) {
    $erro = true;
    $mensagem = "ESSA HORA " . $ini . " TA FORA DA CASINHA!!!";
}

if (!$erro && empty($fim)) {
    $erro = true;
    $mensagem = "INFORME A HORA DE TÉRMINO DA AULA PORRA!!!";
}

if (!$erro && !empty($fim) && !validateTime($fim)) {
    $erro = true;
    $mensagem = "ESSA HORA " . $fim . " TA FORA DA CASINHA!!!";
}

if (!$erro && empty($vlr)) {
    $erro = true;
    $mensagem = "INFORME O PREÇO DA AULA PORRA!!!";
}
    
if (!$erro && !empty($vlr) && !validateFloat($vlr)) {
    $erro = true;
    $mensagem = "ESSE PREÇO DE R$ " . $vlr . " TA FORA DA CASINHA!!!";
}

// Verifica se há alguma outra aula no mesmo horário dessa
if (!$erro) {
    $query  = sprintf(" SELECT COUNT(*) qtd ");
    $query .= sprintf(" FROM   projecao_mensal ");
    $query .=         " WHERE  pjm_data_aula = STR_TO_DATE('$dat','%d/%m/%Y') ";
    $query .= sprintf(" AND  ( ADDTIME('$ini','00:00:01') BETWEEN pjm_hor_ini AND pjm_hor_fim ");
    $query .= sprintf(" OR     SUBTIME('$fim','00:00:01') BETWEEN pjm_hor_ini AND pjm_hor_fim ) ");
    $valida = executeQuery($query);
    
    /*
    while ($row = mysql_fetch_assoc($valida)) {
        if ($row['qtd'] > 0) {
            $erro = true;
            $mensagem = "VOCÊ JÁ TEM AULA COM UM MANÉ NESSE HORÁRIO!!!";
        }
    }
    */
}

// Carrega dados necessários para inserir na tabela
if (!$erro) {
    $query  = sprintf(" SELECT a.alu_nome, a.alu_email, n.niv_descricao ");
    $query .= sprintf(" FROM   alunos a ");
    $query .= sprintf(" JOIN   niveis n on n.niv_codigo = a.alu_nivel ");
    $query .= sprintf(" WHERE  a.alu_codigo = " . $alu);
    $valida = executeQuery($query);
    
    while ($row = mysql_fetch_assoc($valida)) {
        $ano  = date("Y");
        $mes  = date("m");
        $sem  = diasemana($dat);
        $nome = $row["alu_nome"];
        $mail = $row["alu_email"];
        $nivi = $row["niv_descricao"];
        $dat  = dataformatosql($dat);
        $vlr  = str_replace(',','.',$vlr);
    }
}

if ($erro) {
    showMessage(9,$mensagem,"javascript:history.go(-1);");
} else {
    // insere aula
    $query = "  insert into projecao_mensal
                  ( pjm_ano, pjm_mes, pjm_aluno, pjm_aluno_nome, pjm_aluno_email, pjm_aluno_nivel
                  , pjm_data_aula, pjm_dia_semana, pjm_hor_ini, pjm_hor_fim, pjm_vlr_aula, pjm_revisado 
                  ) 
                values 
                  ( '$ano', '$mes', '$alu', '$nome', '$mail', '$nivi'
                  , '$dat', '$sem', '$ini', '$fim', '$vlr', 1
                ) ";
    
    $consulta = executeQuery($query);
    
    if (!$consulta) {
        // altera aula
        $query = " update projecao_mensal
                   set    pjm_aluno_nome  = '$nome'
                   ,      pjm_aluno_email = '$mail'
                   ,      pjm_aluno_nivel = '$nivi'
                   ,      pjm_dia_semana  = '$sem'
                   ,      pjm_hor_ini     = '$ini'
                   ,      pjm_hor_fim     = '$fim'
                   ,      pjm_vlr_aula    = '$vlr'
                   ,      pjm_revisado    = 1
                   where  pjm_ano         = '$ano'
                   and    pjm_mes         = '$mes'
                   and    pjm_aluno       = '$alu'
                   and    pjm_data_aula   = '$dat' ";
        
        $consulta = executeQuery($query);
    }
    
    if (!$consulta) {
        $erro = true;
        showMessage(8,"Ocorreu um erro ao tentar inserir uma nova aula!","javascript:history.go(-1);");
    } else { 
        showMessage(1,"Operação realizada com sucesso!","telaConsulta.php");
    }
}