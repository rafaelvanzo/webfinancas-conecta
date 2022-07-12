<?php

class BoletoSantander extends Boleto{

	function __construct($db,$lancamento_id,$boleto_id,$cliente_id_c){
		parent::__construct($db,$lancamento_id,$boleto_id,$cliente_id_c);
	}

    static function GerarNossoNumero($sequencial,$anoEmissao){
        
        //nosso número (sem dv) é 12 digitos
		$nossoNumeroSemDv = $anoEmissao.parent::formata_numero($sequencial,10,0);
		//dv do nosso número
		$dv_nosso_numero = parent::modulo_11($nossoNumeroSemDv,9);
		// nosso número (com dv) são 13 digitos
		$nossoNumero = parent::formata_numero($nossoNumeroSemDv.$dv_nosso_numero,13,0);

        return $nossoNumero;
    }

	function boletoMontar(){

		$dadosboleto = $this->dadosboleto;

		$codigobanco = $dadosboleto['codigo_banco']; //033; Antigamente era 353
		$codigo_banco_com_dv = parent::geraCodigoBanco($codigobanco);
		$nummoeda = "9";
		$fixo     = "9";   // Numero fixo para a posição 05-05
		$ios	  = "0";   // IOS - somente para Seguradoras (Se 7% informar 7, limitado 9%); Demais clientes usar 0 (zero)
		$fator_vencimento = parent::fator_vencimento($dadosboleto["data_vencimento"]);
		
		//valor tem 10 digitos, sem virgula
		$valor = parent::formata_numero($dadosboleto["valor_boleto"],10,0,"valor");
		//Modalidade Carteira
		$carteira = $dadosboleto["carteira"];
		//conta cedente deve possuir 7 caracteres
		$conta_cedente = parent::formata_numero($dadosboleto["conta_cedente"],7,0);
        
        //Pega o ano de emissão do boleto
        $anoEmissao = substr($dadosboleto["nosso_numero"],0,2);

        //nosso número
        $nossoNumero = self::GerarNossoNumero($dadosboleto["sequencial"],$anoEmissao);

		// 43 numeros para o calculo do digito verificador do codigo de barras
		$barra = "$codigobanco$nummoeda$fator_vencimento$valor$fixo$conta_cedente$nossoNumero$ios$carteira";
		
		//$barra = "$codigobanco$nummoeda$fixo$codigocliente$nossonumero$ios$carteira";
		$dv = self::digitoVerificador_barra($barra);
		// Numero para o codigo de barras com 44 digitos
		$linha = substr($barra,0,4) . $dv . substr($barra,4);
		
		$agencia = parent::formata_numero($dadosboleto["agencia"],4,0);
		$agencia_codigo = $agencia."-".parent::modulo_11($agencia)." / ". $conta_cedente;
		
		$dadosboleto["codigo_barras"] = $linha;
		$dadosboleto["linha_digitavel"] = self::monta_linha_digitavel($linha);
		$dadosboleto["agencia_codigo"] = $agencia_codigo;
		//$dadosboleto["nosso_numero"] = $nossoNumero;
		$dadosboleto["codigo_banco_com_dv"] = $codigo_banco_com_dv;

		$this->dadosboleto = $dadosboleto;
		
	}

	function digitoVerificador_barra($numero) {
		$resto2 = parent::modulo_11($numero, 9, 1);
			 if ($resto2 == 0 || $resto2 == 1 || $resto2 == 10) {
					$dv = 1;
			 } else {
					$dv = 11 - $resto2;
			 }
		 return $dv;
	}

	function modulo_10($num) { 
			$numtotal10 = 0;
					$fator = 2;
	
					// Separacao dos numeros
					for ($i = strlen($num); $i > 0; $i--) {
							// pega cada numero isoladamente
							$numeros[$i] = substr($num,$i-1,1);
							// Efetua multiplicacao do numero pelo (falor 10)
							// 2002-07-07 01:33:34 Macete para adequar ao Mod10 do Itaú
							$temp = $numeros[$i] * $fator; 
							$temp0=0;
							foreach (preg_split('//',$temp,-1,PREG_SPLIT_NO_EMPTY) as $k=>$v){ $temp0+=$v; }
							$parcial10[$i] = $temp0; //$numeros[$i] * $fator;
							// monta sequencia para soma dos digitos no (modulo 10)
							$numtotal10 += $parcial10[$i];
							if ($fator == 2) {
									$fator = 1;
							} else {
									$fator = 2; // intercala fator de multiplicacao (modulo 10)
							}
					}
			
					// várias linhas removidas, vide função original
					// Calculo do modulo 10
					$resto = $numtotal10 % 10;
					$digito = 10 - $resto;
					if ($resto == 0) {
							$digito = 0;
					}
			
					return $digito;
			
	}
	
	function monta_linha_digitavel($codigo) 
	{ 
		// Posição 	Conteúdo
		// 1 a 3    Número do banco
		// 4        Código da Moeda - 9 para Real ou 8 - outras moedas
		// 5        Fixo "9'
		// 6 a 9    PSK - codigo cliente (4 primeiros digitos)
		// 10 a 12  Restante do PSK (3 digitos)
		// 13 a 19  7 primeiros digitos do Nosso Numero
		// 20 a 25  Restante do Nosso numero (8 digitos) - total 13 (incluindo digito verificador)
		// 26 a 26  IOS
		// 27 a 29  Tipo Modalidade Carteira
		// 30 a 30  Dígito verificador do código de barras
		// 31 a 34  Fator de vencimento (qtdade de dias desde 07/10/1997 até a data de vencimento)
		// 35 a 44  Valor do título
		
		// 1. Primeiro Grupo - composto pelo código do banco, código da moéda, Valor Fixo "9"
		// e 4 primeiros digitos do PSK (codigo do cliente) e DV (modulo10) deste campo
		$campo1 = substr($codigo,0,3) . substr($codigo,3,1) . substr($codigo,19,1) . substr($codigo,20,4);
		$campo1 = $campo1 . self::modulo_10($campo1);
		$campo1 = substr($campo1, 0, 5).'.'.substr($campo1, 5);
	
	
		
		// 2. Segundo Grupo - composto pelas 3 últimas posiçoes do PSK e 7 primeiros dígitos do Nosso Número
		// e DV (modulo10) deste campo
		$campo2 = substr($codigo,24,10);
		$campo2 = $campo2 . self::modulo_10($campo2);
		$campo2 = substr($campo2, 0, 5).'.'.substr($campo2, 5);
	
	
		// 3. Terceiro Grupo - Composto por : Restante do Nosso Numero (6 digitos), IOS, Modalidade da Carteira
		// e DV (modulo10) deste campo
		$campo3 = substr($codigo,34,10);
		$campo3 = $campo3 . self::modulo_10($campo3);
		$campo3 = substr($campo3, 0, 5).'.'.substr($campo3, 5);
	
	
	
		// 4. Campo - digito verificador do codigo de barras
		$campo4 = substr($codigo, 4, 1);
	
	
		
		// 5. Campo composto pelo fator vencimento e valor nominal do documento, sem
		// indicacao de zeros a esquerda e sem edicao (sem ponto e virgula). Quando se
		// tratar de valor zerado, a representacao deve ser 0000000000 (dez zeros).
		$campo5 = substr($codigo, 5, 4) . substr($codigo, 9, 10);
		
		return "$campo1 $campo2 $campo3 $campo4 $campo5"; 
	}
	
}

?>