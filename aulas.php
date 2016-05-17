<?php

function getInfoAula($alu_codigo, $aul_dia) {
    $query  = sprintf(" SELECT a.aul_dia, a.aul_hor_ini, a.aul_hor_fim, a.aul_preco ");
    $query .= sprintf(" FROM   aulas a ");
    $query .= sprintf(" WHERE  1 = 1 ");
    
    if (empty($alu_codigo)) {
        $query .= sprintf(" AND  1 = 2 ");
    } else {
        $query .= sprintf(" AND  a.aul_aluno = " . $alu_codigo);
    }
    
    if (empty($aul_dia)) {
        $query .= sprintf(" AND  1 = 2 ");
    } else {
        $query .= sprintf(" AND  a.aul_dia = " . $aul_dia);
    }
    
    $query .= sprintf(" ORDER  BY a.aul_dia ");
    $aulas = executeQuery($query);
    
    return $aulas;
}

function validateTime($hora) {
    $ok = true;
    if ( preg_match('/^[0-9]{2}:[0-9]{2}$/', $hora) ) {
        if (substr($hora,0,2) > "23") {
            $ok = false;
        }
        if (substr($hora,3,2) > "59") {
            $ok = false;
        }
    } else {
        $ok = false;
    }
    return $ok;
}

function validateFloat($number) {
    if ( preg_match( '/^[\-+]?[0-9]*\,?[0-9]+$/', $number) ) {
        return true;
    } else {
        return false;
    }
}

function validateEmail($email) {
    if ( filter_var($email, FILTER_VALIDATE_EMAIL) ) {
        return true;
    } else {
        return false;
    }
}

function validateDate($date) {
    if ( preg_match( '/^(((0[1-9]|[12]\d|3[01])\/(0[13578]|1[02])\/((19|[2-9]\d)\d{2}))|((0[1-9]|[12]\d|30)\/(0[13456789]|1[012])\/((19|[2-9]\d)\d{2}))|((0[1-9]|1\d|2[0-8])\/02\/((19|[2-9]\d)\d{2}))|(29\/02\/((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))))$/', $date) ) {
        return true;
    } else {
        return false;
    }
}

function diasemana($data) {
    $dia =  substr("$data", 0, 2);
    $mes =  substr("$data", 3, 2);
    $ano =  substr("$data", 6, 4);
    
    $diasemana = date("w", mktime(0,0,0,$mes,$dia,$ano) );
    
    switch($diasemana) {
        case "0": $diasemana = "Dom"; break;
        case "1": $diasemana = "Seg"; break;
        case "2": $diasemana = "Ter"; break;
        case "3": $diasemana = "Qua"; break;
        case "4": $diasemana = "Qui"; break;
        case "5": $diasemana = "Sex"; break;
        case "6": $diasemana = "Sáb"; break;
    }
    
    return $diasemana;
}

function dataformatosql($data) {
    $dia =  substr("$data", 0, 2);
    $mes =  substr("$data", 3, 2);
    $ano =  substr("$data", 6, 4);

    $diasql = date("Y-m-d", mktime(0,0,0,$mes,$dia,$ano) );

    return $diasql;
}