<?php
class Usuario{
	
	var $usuario_dados = array(
		"email"=>"",
		"senha"=>""
	);
	
	/*
	================================================================================================
	CONSTRUTOR
	================================================================================================
	*/

	function __construct(){
	}

	/*
	================================================================================================
	LOGIN
	================================================================================================
	*/
	
/*************************************************************************************/
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
/*************************************************************************************/

//$moeda = 'USD';
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
			  $result['bid']  = $linha[4]; // Pega o valor de compra da moeda
			  $result['ask']  = $linha[5]; // Pega o valor de venda da moeda
			 
		   }
		}
	
		else{ // Se o arquivo nao retornar nenhum array
		
			$result['cotacao'] = "N/A"; // Define not avaiable para os campos
			$result['data'] = "N/A";
			$result['hora'] = "N/A";
			$result['bid']  = "N/A";
			$result['ask']  = "N/A";
		}
	
	
return $result; // retorna o array com os valores a serem usados
	
	}

}
?>