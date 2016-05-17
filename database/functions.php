<?php

function startDatabase() {
    // Conecta com o database
    $link = mysql_connect('mysql.hostinger.com.br','u187390300_root','u187390300');
    if (!$link) {
        die('Not connected to MySQL: ' . mysql_error());
    }
    
    // Seleciona, abre e entra no database correto
    $db_selected = mysql_select_db('u187390300_engli', $link);
    if (!$db_selected) {
        die ('Not connected to Englishware database: ' . mysql_error());
    }
}

function executeQuery($query) {
    $resultset = mysql_query($query);
    
    // Trata erro
    if (!$resultset) {
        $mensagem  = 'Invalid query: ' . mysql_error() . "\n";
        $mensagem .= 'Whole query: ' . $query;
    }
    
    return $resultset;
}