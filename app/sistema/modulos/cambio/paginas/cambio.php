<?

/********************************************************************************
*****/
/* Funcao para pegar cotacao no Yahoo Finances						   										*/
/* Criada por: Frederick Moschkowich - www.fm-consultoria.com.br		 								*/
/* fm.consultoria@gmail.com														   */
/* Em: 09 de dezembro de 2008																						   */
/*																	   															  */
/* Exemplo de uso														 	  */
/* $dtMoeda = pega_cota('JPY'); // Pega o array com os valores da moeda 	 					 */
/* desejada. EUR -> Euro; USD -> Dolar Americano; GBP -> Libra Esterlina;							 */ 
/* JPY -> Yen																 */
/*																		  */
/* echo "Cotação do dia ". $dtMoeda['data']." as ".$dtMoeda['hora']."<br>";							 */
/* // Escreve os valores achados													 */
/* echo "Cotacao: ".$dtMoeda['cotacao']."<br>";										 */
/* echo "Bid: ".$dtMoeda['bid']."<br>";											*/
/* echo "Ask: ".$dtMoeda['ask']."<br>";											*/
/*																		 */
/* echo '<em>&copy;<a href="http://finance.yahoo.com/currency">Yahoo Finances</a>';			  */
/* // Copyright do Yahoo Finances												*/
/********************************************************************************
*****/
//$moeda = 'USD';
/*
function pega_cota($moeda) {  //Inicia a funcao para pegar a cotacao de determinada moeda ($moeda)
	
	//Link banco central moedas -> http://www4.bcb.gov.br/pec/taxas/batch/cotacaomoedas.asp?id=txtodas
	$link = "http://download.finance.yahoo.com/d/quotes.csv?s=".$moeda."BRL=X&f=sl1d1t1ba&e=.csv"; //link para pegar a cotacao no formato CSv
	
	if (@fopen($link,"r")) { // abre o arquivo CSV
	  $arq = file($link);
	}
   
		if (is_array($arq)) { // Se o arquivo retornar um array continua
	
		   for ($x=0;$x<count($arq);$x++) { // Passa por todas as chaves do array
		   
			  $linha = explode(",",$arq[$x]); // Separa os valores do arquivo CSV
			  
			  $result['cotacao']  = $linha[1]; // Pega o valor que o Yahoo usa para fazer a conversao
			  $result['data'] = ereg_replace('"','',$linha[2]); // Retira as aspas da data
				$date = new DateTime($result['data']); 
				$result['data'] = $date->format('d/m/Y'); //Muda o formato da data para dd/mm/YYYY
			//$result['data'] = date('F d Y',strtotime($result['data']));
			 
			  $result['hora'] = ereg_replace('"','',$linha[3]); // Retira as aspas do horario da cotacao
				$result['hora'] = date('H:i', strtotime($result['hora'])); //COnverte a hora para 14:00
		  //$result['bid']  = $linha[4]; // Pega o valor de compra da moeda
			//$result['ask']  = $linha[5]; // Pega o valor de venda da moeda
			 
		   }
		}
	
		else{ // Se o arquivo nao retornar nenhum array
		
			$result['cotacao'] = "N/A"; // Define not avaiable para os campos
			$result['data'] = "N/A";
			$result['hora'] = "N/A";
		//$result['bid']  = "N/A";
		//$result['ask']  = "N/A";
		}
	
	
return $result; // retorna o array com os valores a serem usados

}

// =====================================================================
//Pegas as datas para fazer comparação no if abaixo
$data_atual = date('Y-m-d H:i'); //Data atual
session_start();
$data_controle_cotacao = $_SESSION['data_controle_cotacao']; //Data que esta na sessão

//echo $data_atual;
//echo $data_controle_cotacao;

//Verifica se data atual tem 30 minutos a mais doque a registrada na sessão
if(strtotime($data_atual) > strtotime($data_controle_cotacao)){

//Adiciona 30 minutos na data
$date = date('Y-m-d H:i');
$timestamp1 = strtotime($date);
$timestamp2 = strtotime('+30 min', $timestamp1);
$_SESSION['data_controle_cotacao'] = date('Y-m-d H:i', $timestamp2);

//Cotação do Dolar
$_SESSION['dolar'] = $dolar = pega_cota('USD'); 

//Cotação do Euro
$_SESSION['euro'] = $euro = pega_cota('EUR'); 

//Cotação do Libra
$_SESSION['libra'] =$libra = pega_cota('GBP'); 
// =====================================================================

} */
?>
 <!-- === Cotação === 
 <div class="sRoundStatsDolar" align="right" style="padding:5px 0 5px 0;">
   	 <ul>
        	<li>Dolar<a href="javascript://void(0);" class="tipN" style="cursor: default;" original-title="<?php //echo $dolar[data]; echo " - "; echo $dolar[hora]; ?>"><span class="roundPos">R$<?php echo substr($dolar[cotacao],0,5); ?></span></a></li>
            <li><a class="tipN" href="javascript://void(0);" style="cursor: default;" original-title="<?php //echo $euro[data]; echo " - "; echo $euro[hora]; ?>"><span class="roundZero">R$<?php echo substr($euro[cotacao],0,5); ?></span></a>Euro</li>
            <li>Libra<a href="javascript://void(0);" class="tipN" style="cursor: default;" original-title="<?php //echo $libra[data]; echo " - "; echo $libra[hora]; ?>"><span class="roundNeg">R$<?php echo substr($libra[cotacao],0,5); ?></span></a></li>
        </ul>
    </div> 
   Fim da cotação -->

