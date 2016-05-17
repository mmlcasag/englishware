<?php 
header('Content-Type: text/html; charset=utf-8');

ini_set('default_charset', 'UTF-8');
ini_set('display_errors', 'Off');

require_once 'aulas.php';
require_once 'database/functions.php';

startDatabase();

// ------------------- Query para montar o combo dos alunos ------------------- 
$query  = sprintf(" SELECT a.alu_codigo, a.alu_nome ");
$query .= sprintf(" FROM   alunos a ");
$query .= sprintf(" ORDER  BY a.alu_nome ");
$alunos = executeQuery($query);

?>

<html>

<head>
  <title>:: ENGLISHWARE ::</title>
  <link rel="stylesheet" type="text/css" href="style.css"/>
</head>

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
					<input type="text" size="80%" name="p_search" style="height:25px;" value=""/>
					<input type="submit" name="p_submit" value="Pesquisa Englishware" class="groovybutton" onMouseOver="goLite(this.form.name,this.name)" onMouseOut="goDim(this.form.name,this.name)"/>
			  </td>
			</tr>
			
	  </table>
  </form>
  
  <form action="grava_aula.php" method="post" name="main" enctype="multipart/form-data">
	<table border="0" cellspacing="0" cellpadding="20" width="100%">
		
	  <tr>
		<td width="170px" align="left" valign="top">
		  <font face="tahoma" size="3"><a href="javascript:document.main.submit();">Salvar Alterações</a></font>
		  <br/>
		  <br/>
		  <br/>
		  <br/>
		  <font face="tahoma" size="2"><a href="javascript:history.go(-1);">Voltar</a></font>
	    </td>
			  
		<td align="left" valign="top">
		  <table border="0" width="100%">

			<tr>
			  <td width="125px"><font face="tahoma" size="2" color="black"><b>Aluno</b></font><font face="tahoma" size="2" color="red"><b>*</b></font></td>
			  <td>
			    <select name="p_aluno" style="width:356px;">
				  <option selected value=""></option>
				  <?php
				  while ($row = mysql_fetch_assoc($alunos)) {
				    echo '<option value="' . $row["alu_codigo"] . '">' . $row["alu_nome"] . '</option>';
				  }
				  ?>
				</select>
			  </td>
			</tr>
				
			<tr>
			  <td><font face="tahoma" size="2" color="black"><b>Data da Aula</b></font><font face="tahoma" size="2" color="red"><b>*</b></font></td>
			  <td><input type="text" name="p_dat_aula" size="10" maxlength="10"/><font face="tahoma" size="2" color="red"><sub>&nbsp;&nbsp;dd/mm/yyyy</sub></font></td>
			</tr>
				
			<tr>
			  <td><font face="tahoma" size="2" color="black"><b>Hora Início</b></font><font face="tahoma" size="2" color="red"><b>*</b></font></td>
			  <td><input type="text" name="p_hor_ini" size="5" maxlength="5"/><font face="tahoma" size="2" color="red"><sub>&nbsp;&nbsp;00:00</sub></font></td>
			</tr>
				
			<tr>
			  <td><font face="tahoma" size="2" color="black"><b>Hora Término</b></font><font face="tahoma" size="2" color="red"><b>*</b></font></td>
			  <td><input type="text" name="p_hor_fim" size="5" maxlength="5"/><font face="tahoma" size="2" color="red"><sub>&nbsp;&nbsp;00:00</sub></font></td>
			</tr>
			
			<tr>
			  <td><font face="tahoma" size="2" color="black"><b>Valor Aula</b></font><font face="tahoma" size="2" color="red"><b>*</b></font></td>
			  <td><input type="text" name="p_vlr_aula" size="5" maxlength="5"/><font face="tahoma" size="2" color="red"><sub>&nbsp;&nbsp;R$ 0,00</sub></font></td>
			</tr>
			  
			<tr>
			  <td><br/></td>
			  <td><font face="tahoma" size="2" color="red"><b><br>* Campos obrigatórios</b></font><input type="submit" style="visibility: hidden;"/></td>
			</tr>
			  
		  </table>
		</td>
	  </tr>
	  
    </table>
  </form>
  
</body>

</html>