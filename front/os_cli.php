<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="css/styles.termo.css" rel="stylesheet" type="text/css">
<link href="css/styles.css" rel="stylesheet" type="text/css">
</head>
<?php 
include ('../../../inc/includes.php');
include ('../../../config/config.php');
global $DB;
Session::checkLoginUser();
Html::header('OS Remix', "", "plugins", "os");
echo Html::css($CFG_GLPI["root_doc"]."/css/styles.css");
if (isset($_SESSION["glpipalette"])) {
	echo Html::css($CFG_GLPI["root_doc"]."/css/palettes/".$_SESSION["glpipalette"].".css");
}
$SelPlugin = "SELECT * FROM glpi_plugin_os_config";
$ResPlugin = $DB->query($SelPlugin);
$Plugin = $DB->fetch_assoc($ResPlugin);
$EmpresaPlugin = $Plugin['name'];
$EnderecoPlugin = $Plugin['address'];
$TelefonePlugin = $Plugin['phone'];
$CidadePlugin = $Plugin['city'];
$CorPlugin = $Plugin['color'];
$CorTextoPlugin = $Plugin['textcolor'];
$SelTicket = "SELECT * FROM glpi_tickets WHERE id = '".$_GET['id']."'";
$ResTicket = $DB->query($SelTicket);
$Ticket = $DB->fetch_assoc($ResTicket);
$OsId = $_GET['id'];
$OsNome = $Ticket['name'];
$SelDataInicial = "SELECT date,date_format(date, '%d/%m/%Y %H:%i') AS DataInicio FROM glpi_tickets WHERE id = '".$_GET['id']."'";
$ResDataInicial = $DB->query($SelDataInicial);
$DataInicial = $DB->fetch_assoc($ResDataInicial);
$OsData = $DataInicial['DataInicio'];
$OsDescricao = $Ticket['content'];
$SelDataFinal = "SELECT time_to_resolve,date_format(solvedate, '%d/%m/%Y %H:%i') AS DataFim FROM glpi_tickets WHERE id = '".$_GET['id']."'";
$ResDataFinal = $DB->query($SelDataFinal);
$DataFinal = $DB->fetch_assoc($ResDataFinal);
$OsDataEntrega = $DataFinal['DataFim'];
$OsSolucao = $Ticket['solution'];
$SelTicketUsers = "SELECT * FROM glpi_tickets_users WHERE tickets_id = '".$OsId."'";
$ResTicketUsers = $DB->query($SelTicketUsers);
$TicketUsers = $DB->fetch_assoc($ResTicketUsers);
$OsUserId = $TicketUsers['users_id'];
$SelIdOsResponsavel = "SELECT users_id FROM glpi_tickets_users WHERE tickets_id = '".$OsId."' AND type = 2";
$ResIdOsResponsavel = $DB->query($SelIdOsResponsavel);
$OsResponsavel = "";
while ($IdOsResponsavel = $DB->fetch_assoc($ResIdOsResponsavel)) {
	$SelOsResponsavelName = "SELECT * FROM glpi_users WHERE id = '".$IdOsResponsavel['users_id']."'";
	$ResOsResponsavelName = $DB->query($SelOsResponsavelName);
	$OsResponsavelFull = $DB->fetch_assoc($ResOsResponsavelName);
	$OsResponsavel .= $OsResponsavelFull['firstname']. " " .$OsResponsavelFull['realname']. ", ";
}
if(strlen($OsResponsavel)>2){
	$OsResponsavel = substr($OsResponsavel, 0, strlen($OsResponsavel)-2);
}
$SelAtendimento = "select max(date_format(date_mod, '%d/%m/%Y %H:%i')) as date_mod from glpi_logs where itemtype like 'Ticket' and id_search_option=12 and new_value=15 and items_id=".$OsId;
$ResDtAtendimento = $DB->query($SelAtendimento);
if($ResDtAtendimento){
	$dtatend = $DB->fetch_assoc($ResDtAtendimento);
	if($dtatend){
		$OsDataAtendimento = $dtatend['date_mod'];
	}	
}
$EntidadeId = $Ticket['entities_id'];
$SelEmpresa = "SELECT * FROM glpi_entities WHERE id = '".$EntidadeId."'";
$ResEmpresa = $DB->query($SelEmpresa);
$Empresa = $DB->fetch_assoc($ResEmpresa);
$EntidadeName = $Empresa['name'];
$EntidadeCep = $Empresa['postcode'];
$EntidadeEndereco = $Empresa['address'];
$EntidadeEmail = $Empresa['email'];
$EntidadePhone = $Empresa['phonenumber'];
$EntidadeCnpj = $Empresa['comment'];
$SelEmail = "SELECT * FROM glpi_useremails WHERE users_id = '".$OsUserId."'";
$ResEmail = $DB->query($SelEmail);
$Email = $DB->fetch_assoc($ResEmail);
$UserEmail = $Email['email'];
$SelCustoLista = "SELECT actiontime, sec_to_time(actiontime) AS Hora,name,cost_time,cost_fixed,cost_material,FORMAT(cost_time,2,'de_DE') AS cost_time2, FORMAT(cost_fixed,2,'de_DE') AS cost_fixed2, FORMAT(cost_material,2,'de_DE') AS cost_material2, SUM(cost_material + cost_fixed + cost_time * actiontime/3600) AS CustoItem FROM glpi_ticketcosts WHERE tickets_id = '".$OsId."' GROUP BY id";
$ResCustoLista = $DB->query($SelCustoLista);
$SelCusto = "SELECT SUM(cost_material + cost_fixed + cost_time * actiontime/3600) AS SomaTudo FROM glpi_ticketcosts WHERE tickets_id = '".$OsId."'";
$ResCusto = $DB->query($SelCusto);
$Custo = $DB->fetch_assoc($ResCusto);
$CustoTotal =  $Custo['SomaTudo'];
$CustoTotalFinal = number_format($CustoTotal, 2, ',', ' ');
$SelTicketUsers = "SELECT * FROM glpi_tickets_users WHERE tickets_id = '".$OsId."'";
$ResTicketUsers = $DB->query($SelTicketUsers);
$TicketUsers = $DB->fetch_assoc($ResTicketUsers);
$OsUserId = $TicketUsers['users_id'];
$SelUsers = "SELECT * FROM glpi_users WHERE id = '".$OsUserId."'";
$ResUsers = $DB->query($SelUsers);
$Users = $DB->fetch_assoc($ResUsers);
$UserName = $Users['firstname']. " " .$Users['realname'];
$UserCpf = $Users['registration_number'];
$UserTelefone = $Users['mobile'];
$UserEndereco = $Users['comment'];
$UserCep = $Users['phone2'];
$SelEmail = "SELECT * FROM glpi_useremails WHERE users_id = '".$OsUserId."'";
$ResEmail = $DB->query($SelEmail);
$Email = $DB->fetch_assoc($ResEmail);
$UserEmail = $Email['email'];
$SelTempoTotal = "SELECT SUM(actiontime) AS TempoTotal FROM glpi_ticketcosts WHERE tickets_id = '".$OsId."'";
$ResTempoTotal = $DB->query($SelTempoTotal);
$TempoTotal = $DB->fetch_assoc($ResTempoTotal);
$seconds = $TempoTotal['TempoTotal'];
$hours = floor($seconds / 3600);
$seconds -= $hours * 3600;
$minutes = floor($seconds / 60);
$seconds -= $minutes * 60;
?>
<body>
<!-- inicio dos botoes -->
<div id="botoes" style="width:50%; background:#fff; margin:auto; padding-bottom:10px;"> 
	<!--<input type="button" class="botao" name="configurar" value="Configurar" onclick="window.location.href='./index.php'"> -->
	<p></p>
	<form action="os_cli.php" method="get">	
	<input type="text" name="id" value="ID Ordem de Serviço" onfocus="if (this.value=='ID Ordem de Serviço') this.value='';" onblur="if (this.value=='') this.value='ID Ordem de Serviço'" />
	<input class="submit" type="submit" value="Enviar">
	</form>
	<p></p>
	<a href='os_cli_pdf.php?id=<?php echo $OsId; ?>' target="_blank" class="vsubmit"> Imprimir </a>
	<a href='os_cli.php?id=<?php echo $OsId; ?>' class="vsubmit"> Usuário </a>
	<a href='os.php?id=<?php echo $OsId; ?>' class="vsubmit"> Entidade </a>
	<a href="index.php" class="vsubmit" style="float:right;"> Configurar </a>
	<p></p>
</div>
<!-- inicio das tabelas -->
<table style="width:50%; background:#fff; margin:auto;" border="1" cellpadding="0" cellspacing="0"> 
<tr>
<td style="padding: 0px !important;" >
<table style="width:100%; background:#fff;" border="1">
<tr>
<td width="400" colspan="2">
<table style="width:100%;" border="0" cellpadding="0" cellspacing="0">
<!-- tabela do logotipo -->
<tr><td height="119" valign="middle" style="width:25%; text-align:center; margin:auto;"><img src="./img/logo_os.png" width="100" height="100" align="absmiddle"></td>
<!-- tabela do titulo -->
<td style="text-align:center;"><p><font size="4"><?php echo ($EmpresaPlugin);?></font></p>
<p><font size="2"><?php echo ("$EnderecoPlugin - $CidadePlugin - $TelefonePlugin"); ?></font></p>
<!-- tabela do titulo segunda linha -->
<p width="131" height="70"><font size="6"> OS Nº &nbsp;<b><?php echo $OsId;?> </font></b></p></tr>
<!-- fecha a tabela de titulo -->
</table></td>
<!-- segunda tabela -->
<tr><td colspan="2" style="background-color:<?php echo $CorPlugin; ?> !important"><center><b><font color="<?php echo $CorTextoPlugin; ?>">DADOS DO CLIENTE</font></b></center></td> </tr>
<tr><td width="50%"><b>Nome: </b><?php echo ($UserName) ?></td><td ><b>Telefone: </b><?php echo ($UserTelefone)?></td></tr>
<tr><td width="50%"><b>Endereço: </b><?php echo ($UserEndereco)?></td><td><b>E-Mail: </b><?php echo ($UserEmail)?></td></tr>
<tr><td width="50%"><b>CPF: </b><?php echo ($UserCpf)?></td><td ><b>CEP: </b><?php echo ($UserCep)?></td></tr>
<!-- tabela OS -->
<tr><td colspan="2" style="background-color:<?php echo $CorPlugin; ?> !important"><center><b><font color="<?php echo $CorTextoPlugin; ?>">DETALHES DA ORDEM DE SERVIÇO</font></b></center></td></tr>
<tr><td width="50%"><b>Título:</b> <?php echo $OsNome;?></td><td width="50%"><b>Responsável:</b> <?php echo $OsResponsavel;?></td></tr>
<tr><td width="50%"><b>Data/Hora de Início: </b><?php echo ($OsData);?></td><td><b>Data/Hora de Término: </b><?php echo ($OsDataEntrega);?>
<tr><td colspan="2" style="background-color:<?php echo $CorPlugin; ?> !important"><center><b><font color="<?php echo $CorTextoPlugin; ?>">DESCRIÇÃO</font></b></center></td></tr>
<tr><td height="150" colspan="2" valign="top" style="padding:10px;"><?php echo html_entity_decode($OsDescricao);?></td></tr>
<tr><td colspan="2" style="background-color:<?php echo $CorPlugin; ?> !important"><center><b><font color="<?php echo $CorTextoPlugin; ?>">SOLUÇÃO</font></b></center></td></tr>
<tr><td height="5" colspan="2" valign="top" style="padding:10px;">
<?php 
	if ( $OsSolucao == null ) {
		echo "<br><hr><br><hr><br><hr><br>";
	} else {
		echo html_entity_decode($OsSolucao);
	}
?>
</td></tr>

<table style="width:100%; background:#fff;" border="0">
<tr><td colspan="2" style="background-color:<?php echo $CorPlugin; ?> !important";><center><b><font color="<?php echo $CorTextoPlugin; ?>">ASSINATURAS</font></b></center></tr></td>
</table>
<br />
<br />
<br />
<br />
<br />
<table width="688" border="0" align="center" cellspacing="0">
<tr align="center"><td style="text-align:center;">____________________________________</td><td style="text-align:center;">_____________________________________</td></tr>
<tr align="center"><td style="text-align:center;" ><?php echo ($UserName);?></td><td style="text-align:center;" ><?php echo ($EmpresaPlugin);?></td></tr>
</table>
</table> 
<!-- estilo do botao para nao aparecer em impressao --> 
<style media="print">
</style>
</body>
</html>
<?php  
Html::footer();
?>