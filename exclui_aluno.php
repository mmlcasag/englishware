<?php 
header('Content-Type: text/html; charset=utf-8');

ini_set('default_charset', 'UTF-8');
ini_set('display_errors', 'Off');

require_once 'messages/functions.php';
require_once 'database/functions.php';

// Parâmetros
if ($_GET) {
    $alu_codigo = addslashes(trim($_GET["p_alu_codigo"]));
} else {
    showMessage(9,"O QUE É QUE VOCÊ ESTÁ FAZENDO AQUI???","javascript:history.go(-1);");
    die;
}

// Variáveis
$erro = false;
$mensagem = "";

if (!$erro && empty($alu_codigo)) {
    $erro = true;
    $mensagem = "INFORME O CÓDIGO DO ALUNO PORRA!!!";
}

if ($erro) {
    showMessage(9,$mensagem,"javascript:history.go(-1);");
} else {
    startDatabase();
    
    $query = " delete from projecao_mensal where pjm_aluno = '$alu_codigo' ";
    $consulta = executeQuery($query);
    
    if (!$consulta) {
        showMessage(8,"Ocorreu um erro ao excluir os dias de aula calculados para o aluno!","javascript:history.go(-1);");
    } else {
        $query = " delete from aulas where aul_aluno = '$alu_codigo' ";
        $consulta = executeQuery($query);
        
        if (!$consulta) {
            showMessage(8,"Ocorreu um erro ao excluir as aulas do aluno!","javascript:history.go(-1);");
        } else {
            $query = " delete from alunos where alu_codigo = '$alu_codigo' ";
            $consulta = executeQuery($query);
            
            if (!$consulta)
                showMessage(8,"Ocorreu um erro ao excluir aluno do banco de dados!","javascript:history.go(-1);");
            else
                showMessage(1,"O aluno foi excluído do sistema com sucesso!","telaConsulta.php");
        }
    }
}