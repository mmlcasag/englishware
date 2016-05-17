<?php
header("Content-Type: text/plain");

ini_set('default_charset', 'UTF-8');
ini_set('display_errors', 'Off');

require_once 'messages/functions.php';
require_once 'sendmail/functions.php';
require_once 'database/functions.php';

startDatabase();

$query  = sprintf("SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED");
$temp   = executeQuery($query);

$ano    = date("Y");
$mes    = date("m");
$per    = date("m/Y");
$codigo = "";
$nome   = "";
$mail   = "";

foreach ($_POST["p_arr_alunos"] as $aluno) {
    
    set_time_limit(1000);
    
    $query  = sprintf(" select distinct pjm_aluno, pjm_aluno_nome, pjm_aluno_email
                        from   projecao_mensal 
                        where  pjm_ano   = " . $ano . "
                        and    pjm_mes   = " . $mes . "
                        and    pjm_aluno = " . $aluno );
    
    $dados = executeQuery($query);
    
    while ($row = mysql_fetch_assoc($dados)) {
        $codigo = $row['pjm_aluno'];
        $nome   = $row['pjm_aluno_nome'];
        $mail   = $row['pjm_aluno_email'];
    }
    
    $email  = '<html><head><title>:: ENGLISHWARE ::</title></head><body><table border="0" class="barra" cellspacing="0" cellpadding="20" width="100%"><tr><td width="170px" nowrap><b><font type="Tahoma" style="font-size:24px;" color="blue">E</font><font type="Tahoma" style="font-size:24px;" color="red">n</font><font type="Tahoma" style="font-size:24px;" color="orange">g</font><font type="Tahoma" style="font-size:24px;" color="blue">l</font><font type="Tahoma" style="font-size:24px;" color="green">i</font><font type="Tahoma" style="font-size:24px;" color="red">s</font><font type="Tahoma" style="font-size:24px;" color="blue">h</font><font type="Tahoma" style="font-size:24px;" color="red">w</font><font type="Tahoma" style="font-size:24px;" color="orange">a</font><font type="Tahoma" style="font-size:24px;" color="blue">r</font><font type="Tahoma" style="font-size:24px;" color="green">e</font></b></td><td><br></td></tr></table></form><table border="0" cellspacing="0" cellpadding="20" width="100%"><tr><td width="170px" align="left" bgcolor="#DEDEDE" valign="top"><sub><b>Fabiana Branchini</b><br><br>Celular: (54) 9974-6881<br>Residencial: (54) 3201-1616<br>E-mail: fabibr@gmail.com<br>CPF: 699.616.030.87<br><br><b>Banco Itaú</b><br>Agência: 3249<br>C.Corrente: 22.536-2<br><br><b> Banco Santander</b><br>Agência: 0189<br>C.Corrente: 01.030573.1<br></sub></td><td align="left" valign="top"><font type="Tahoma" style="font-size:20px;" color="black"><b>Lembrete de Pagamento<br></b></font><br>';
    $email .= '<font face="tahoma" size="2">';
    $email .= '<br/>Olá ' . $nome . '!<br><br>Este é apenas um lembrete de que o pagamento do mês ' . $per . ' ainda não foi efetuado.<br/><br/><ul>';
    $email .= '</font><br><br><br><br><br><br><br>';
    $email .= '<font face="Tahoma" color="red" size="2"><b>Esta mensagem foi gerada automaticamente. Favor não responder este e-mail.</b></font>';
    $email .= '</td></tr></table></body></html>';
    
    if (!empty($mail)) {
        email($mail, 'Lembrete de Pagamento - ' . $per . ' - ' . $nome, $email);
        sleep(1);
        flush();
    }
    
}

showMessage(1, "Operação realizada com sucesso", "index.php");