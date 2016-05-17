<?php 
header('Content-Type: text/html; charset=utf-8');

ini_set('default_charset', 'UTF-8');
ini_set('display_errors', 'Off');

require_once 'aulas.php';
require_once 'messages/functions.php';
require_once 'database/functions.php';

// Parâmetros
if ($_POST) {
    $alu_codigo = addslashes(trim($_POST["p_alu_codigo"]));
    $alu_nome = addslashes(trim($_POST["p_alu_nome"]));
    $alu_email = addslashes(trim($_POST["p_alu_email"]));
    $alu_fone = addslashes(trim($_POST["p_alu_fone"]));
    $alu_nivel = addslashes(trim($_POST["p_alu_nivel"]));

    // quando é array não deve colocar addslashes e trim
    $aul_dia = $_POST["p_aul_dia"];
    $aul_hor_ini = $_POST["p_aul_hor_ini"];
    $aul_hor_fim = $_POST["p_aul_hor_fim"];
    $aul_preco = $_POST["p_aul_preco"];
} else {
    showMessage(9,"Você não deveria estar aqui!","javascript:history.go(-1);");
}

// Variáveis
$erro = false;
$mensagem = "";

startDatabase();

if (!$erro && empty($alu_nome)) {
    $erro = true;
    $mensagem = "INFORME O NOME DO ALUNO PORRA!!!";
}

if (!$erro && empty($alu_nivel)) {
    $erro = true;
    $mensagem = "INFORME O NÍVEL DO ALUNO PORRA!!!";
}

if (!$erro && !empty($alu_email)) {
    if ( !validateEmail($alu_email) ) {
        $erro = true;
        $mensagem = "INFORME UM EMAIL VÁLIDO SEU MERDA!!!";
    }
}

for ($i = 1; $i <= count($aul_dia); $i++ ) {
    $dia = addslashes(trim($aul_dia[$i]));
    $ini = addslashes(trim($aul_hor_ini[$i]));
    $fim = addslashes(trim($aul_hor_fim[$i]));
    $vlr = addslashes(trim($aul_preco[$i]));
  
    // Checa se os três campos da linha estão corretos
    $ok  = true;
    // Checa se os três campos da linha estão em branco (aí desconsidera)
    if ( empty($ini) && empty($fim) && empty($vlr) ) {
        $ok = true;
    } else {
        // Checa se os três campos da linha estão preenchidos (aí grava)
        if ( !empty($ini) && !empty($fim) && !empty($vlr) ) {
            $ok = true;
            // Condição de erro. Apresentar mensagem para o usuário
            } else {
                $ok = false;
            }
    }
    if (!$erro && !$ok) {
        $erro = true;
        $mensagem = "INFORME OS 3 CAMPOS PARA CADASTRAR A AULA PORRA!!!";
    }
    
    // Valida a hora inicial
    if (!$erro && !empty($ini) && !validateTime($ini)) {
        $erro = true;
        $mensagem = "ESSA HORA " . $ini . " TA FORA DA CASINHA!!!";
    }
    
    // Valida a hora final
    if (!$erro && !empty($fim) && !validateTime($fim)) {
        $erro = true;
        $mensagem = "ESSA HORA " . $fim . " TA FORA DA CASINHA!!!";
    }
    
    // Valida o valor da aula
    if (!$erro && !empty($vlr) && !validateFloat($vlr)) {
        $erro = true;
        $mensagem = "ESSA PREÇO DE R$ " . $vlr . " TA FORA DA CASINHA!!!";
    } 
    
    // Verifica se há alguma outra aula no mesmo horário dessa
    if (!$erro && !empty($ini) && !empty($fim)) {
        $query  = sprintf(" SELECT COUNT(*) qtd ");
        $query .= sprintf(" FROM   aulas ");
        
        if (empty($alu_codigo))
            $query .= sprintf(" WHERE  1 = 1 ");
        else
            $query .= sprintf(" WHERE  aul_aluno <> $alu_codigo ");
        
        $query .= sprintf(" AND    aul_dia    = $dia ");
        $query .= sprintf(" AND  ( ADDTIME('$ini','00:00:01') BETWEEN aul_hor_ini AND aul_hor_fim ");
        $query .= sprintf(" OR     SUBTIME('$fim','00:00:01') BETWEEN aul_hor_ini AND aul_hor_fim ) ");
        
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
}

if ($erro) {
    showMessage(9,$mensagem,"javascript:history.go(-1);");
} else {
    if (empty($alu_codigo)) {
        // atribui um código para o aluno
        $query = " select max(alu_codigo) alu_codigo from alunos ";
        $id = executeQuery($query);
        
        while ($row = mysql_fetch_assoc($id)) {
            try {
                $alu_codigo = $row['alu_codigo'] + 1;
            } catch(Exception $e) {
                $alu_codigo = 1;
            }
        }
        
        // insere aluno
        $query = " insert into alunos 
                     ( alu_codigo, alu_nome, alu_email, alu_fone, alu_nivel ) 
                   values 
                     ( '$alu_codigo', '$alu_nome','$alu_email','$alu_fone','$alu_nivel' ) ";
        
        $consulta = executeQuery($query);
        
        if (!$consulta) {
            $erro = true;
            showMessage(8,"Ocorreu um erro ao tentar inserir um novo aluno!","javascript:history.go(-1);");
        }
    } else {
	// atualiza cadastro do aluno
	$query = " update alunos 
	           set    alu_nome   = '$alu_nome'
                   ,      alu_email  = '$alu_email'
                   ,      alu_fone   = '$alu_fone'
                   ,      alu_nivel  = '$alu_nivel'
                   where  alu_codigo = '$alu_codigo' ";
        
        $consulta = executeQuery($query);
	
	if (!$consulta) {
            $erro = true;
            showMessage(8,"Ocorreu um erro ao tentar atualizar cadastro do aluno!","javascript:history.go(-1);");
        }
    }
    
    // remove aulas do aluno...
    if (!$erro) {
        $query = " delete from aulas where aul_aluno = '$alu_codigo' ";
        $consulta = executeQuery($query);
        
        if (!$consulta) {
            $erro = true;
            showMessage(8,"Ocorreu um erro ao excluir as aulas do aluno!","javascript:history.go(-1);");
        }
    }
    
    // ... e as inclui novamente, com as atualizações da tela
    if (!$erro) {
        // varre dias da semana
        for ($i = 1; $i <= count($aul_dia); $i++ ) {
            $dia = addslashes(trim($aul_dia[$i]));
            $ini = addslashes(trim($aul_hor_ini[$i]));
            $fim = addslashes(trim($aul_hor_fim[$i]));
            $vlr = str_replace(',','.',addslashes(trim($aul_preco[$i])));
            
            // apenas adiciona as linhas onde todas as informações estão preenchidas
            if (!$erro && !empty($ini) && !empty($fim) && !empty($vlr)) {
                // insere aula
                $query = " insert into aulas
                             ( aul_aluno, aul_dia, aul_hor_ini, aul_hor_fim, aul_preco ) 
                           values 
                             ( '$alu_codigo', '$dia','$ini','$fim','$vlr' ) ";
                
                $consulta = executeQuery($query);
                
                if (!$consulta) {
                    $erro = true;
                    showMessage(8,"Ocorreu um erro ao tentar inserir as aulas do aluno!","javascript:history.go(-1);");
                }
            }
        }
    }
    
    if (!$erro) {
        showMessage(1,"Operação realizada com sucesso!","telaConsulta.php");
    }
}