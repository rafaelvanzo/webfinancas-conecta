<?php

class BoletoSicoob extends Boleto{

	function __construct($db,$lancamento_id,$boleto_id,$cliente_id_c){
		parent::__construct($db,$lancamento_id,$boleto_id,$cliente_id_c);
	}

    static function GerarNossoNumero($sequencial,$anoEmissao,$convenio,$agencia){
    
		$nossoNumero = parent::formata_numero($sequencial,5,0);
		$nossoNumero = $anoEmissao.$nossoNumero; 
		$nossoNumero = $nossoNumero.self::dv_nosso_numero($agencia,$convenio,$nossoNumero);

        return $nossoNumero;
    }

	function boletoMontar(){

		$dadosboleto = $this->dadosboleto;

		$codigobanco = $dadosboleto['codigo_banco'];//"756";
		$codigo_banco_com_dv = parent::geraCodigoBanco($codigobanco);
		$nummoeda = "9";
		$fator_vencimento = parent::fator_vencimento($dadosboleto["data_vencimento"]);
		
		//valor tem 10 digitos, sem virgula
		$valor = parent::formata_numero($dadosboleto["valor_boleto"],10,0,"valor");
		//agencia é sempre 4 digitos
		$agencia = parent::formata_numero($dadosboleto["agencia"],4,0);
		//conta é sempre 8 digitos
		$conta = parent::formata_numero($dadosboleto["conta"],8,0);

		//Carteira
		$carteira = $dadosboleto["carteira"];

		//Modalidade da cobrança
		$modalidadecobranca = $dadosboleto["modalidade_cobranca"];
		
		//Número da parcela
		$numeroparcela = parent::formata_numero($dadosboleto["numero_parcela"],3,0);

		//Número do convênio, código do cliente, código do beneficiário...
		$convenio = parent::formata_numero($dadosboleto["convenio"],7,0);

		//agencia e conta
		$agencia_codigo = $agencia ." / ". $convenio;

		// Nosso número de até 8 dígitos - 2 digitos para o ano, 1 dígito verificado e outros 5 numeros sequencias por ano
		//o nosso número do sicoob é composto por 8 dígitos
		//os dois primeiros dígitos da esquerda representam o ano da data de emissão
		//o último dígito da direita representa o dígito verificador do nosso número(construir uma função específica para gerar esse dígito)
		//os 5 dígitos restantes são preenchidos pelo Web Finanças

        $anoEmissao = substr($dadosboleto["nosso_numero"],0,2);
		
        $nossoNumero = self::GerarNossoNumero($dadosboleto["sequencial"],$anoEmissao,$convenio,$agencia);

        $campolivre  = "$modalidadecobranca$convenio$nossoNumero$numeroparcela";
		
		$dv=self::modulo_11("$codigobanco$nummoeda$fator_vencimento$valor$carteira$agencia$campolivre");
		//echo $codigobanco,$nummoeda,$fator_vencimento,$valor,$carteira,$agencia,$campolivre,'<br>';
		//echo $dv,' <br>';
		$linha="$codigobanco$nummoeda$dv$fator_vencimento$valor$carteira$agencia$campolivre";
		
		$dadosboleto["codigo_barras"] = $linha;
		$dadosboleto["linha_digitavel"] = parent::monta_linha_digitavel($linha);
		$dadosboleto["agencia_codigo"] = $agencia_codigo;
		//$dadosboleto["nosso_numero"] = $nossoNumero;
		$dadosboleto["codigo_banco_com_dv"] = $codigo_banco_com_dv;

		$this->dadosboleto = $dadosboleto;

	}

	//Função criada pela Web 2 Business
	static function dv_nosso_numero($agencia,$convenio,$nossonumero){

		//nosso número para teste//$nossonumero = '15001155';
		//formatar o numero da agencia/coperativa em 4 digitios
		//formatar o numero do cliente/convenio em 10 dígitos
		//formatar o nosso número em 7 dígitos

		//Para o cálculo do dv, o número do convênio deve conter 10 numeros
		$convenio = parent::formata_numero($convenio,10,0);
		
		$sequencia = $agencia.$convenio.$nossonumero;
		$constante = '319731973197319731973'; 

		$soma = 0;
		$i=0;
		while($i<21){
			$soma += $sequencia[$i]*$constante[$i];
			$i++;
		}

		$resto = $soma % 11;		
		if($resto==0 || $resto==1){ $dv = 0; }else{ $dv = 11 - $resto; }
       
		return $dv;

	}

	//Função retirada do projeto boleto.php referente ao bancoob(sicoob)
	static function modulo_11($num, $base=9, $r=0) {
		$soma = 0;
		$fator = 2; 
		for ($i = strlen($num); $i > 0; $i--) {
			$numeros[$i] = substr($num,$i-1,1);
			$parcial[$i] = $numeros[$i] * $fator;
			$soma += $parcial[$i];
			if ($fator == $base) {
				$fator = 1;
			}
			$fator++;
		}
		if ($r == 0) {
			$soma *= 10;
			$digito = $soma % 11;
			
			//corrigido
			if ($digito == 10) {
				$digito = "X";
			}
	
			/*
			alterado por mim, Daniel Schultz
	
			Vamos explicar:
	
			O módulo 11 só gera os digitos verificadores do nossonumero,
			agencia, conta e digito verificador com codigo de barras (aquele que fica sozinho e triste na linha digitável)
			só que é foi um rolo...pq ele nao podia resultar em 0, e o pessoal do phpboleto se esqueceu disso...
			
			No BB, os dígitos verificadores podem ser X ou 0 (zero) para agencia, conta e nosso numero,
			mas nunca pode ser X ou 0 (zero) para a linha digitável, justamente por ser totalmente numérica.
	
			Quando passamos os dados para a função, fica assim:
	
			Agencia = sempre 4 digitos
			Conta = até 8 dígitos
			Nosso número = de 1 a 17 digitos
	
			A unica variável que passa 17 digitos é a da linha digitada, justamente por ter 43 caracteres
	
			Entao vamos definir ai embaixo o seguinte...
	
			se (strlen($num) == 43) { não deixar dar digito X ou 0 }
			*/
			
			if (strlen($num) == "43") {
				//então estamos checando a linha digitável
				if ($digito == "0" or $digito == "X" or $digito > 9) {
						$digito = 1;
				}
			}
			return $digito;
		} 
		elseif ($r == 1){
			$resto = $soma % 11;
			return $resto;
		}
	}

}

?>