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
$arr_alu = "";
$arr_dat = "";
$arr_sit = "";

$acr = 0;
$dsc = 0;

// Quando é array não deve colocar addslashes e trim
if ($_POST) {
    $arr_alu = $_POST["p_aluno"];
    $arr_dat = $_POST["p_data"];
    $arr_sit = $_POST["p_cancelar"];
    
    $acr = addslashes(trim($_POST["p_acrescimos"]));
    $dsc = addslashes(trim($_POST["p_descontos"]));
} else {
    showMessage(9,"Você não deveria estar aqui!","javascript:history.go(-1);");
    die();
}

if (!$erro && empty($acr)) {
    $erro = true;
    $mensagem = "INFORME O VALOR DE ACRÉSCIMO, MESMO QUE SEJA R$ 0,00!!!";
}
    
if (!$erro && !empty($acr) && !validateFloat($acr)) {
    $erro = true;
    $mensagem = "ESSE VALOR DE ACRÉSCIMO DE R$ " . $acr . " TA FORA DA CASINHA!!!";
}

if (!$erro && empty($dsc)) {
    $erro = true;
    $mensagem = "INFORME O VALOR DE DESCONTO, MESMO QUE SEJA R$ 0,00!!!";
}
    
if (!$erro && !empty($dsc) && !validateFloat($dsc)) {
    $erro = true;
    $mensagem = "ESSE VALOR DE DESCONTO DE R$ " . $dsc . " TA FORA DA CASINHA!!!";
}

if ($erro) {
    showMessage(9,$mensagem,"javascript:history.go(-1);");
} else {
    if (count($arr_alu) > 0) {
        for ($i = 1; $i <= count($arr_alu); $i++) {
            $alu = addslashes(trim($arr_alu[$i]));
            $dat = addslashes(trim($arr_dat[$i]));
            $sit = addslashes(trim($arr_sit[$i]));
            
            $acr = str_replace(',','.',$acr);
            $dsc = str_replace(',','.',$dsc);
            
            $query = " update projecao_mensal 
                       set    pjm_revisado   = '$sit'
                       ,      pjm_acrescimos = '$acr'
                       ,      pjm_descontos  = '$dsc'
                       where  pjm_aluno      = '$alu'
                       and    pjm_data_aula  = '$dat' ";

            $consulta  =  executeQuery($query);
        }
    }

    showMessage(1,"Operação realizada com sucesso","telaConsulta.php");
}