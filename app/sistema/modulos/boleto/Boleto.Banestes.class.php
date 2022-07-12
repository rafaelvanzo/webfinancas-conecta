<?php

class BoletoBanestes extends Boleto{

	function __construct($db,$lancamento_id,$boleto_id,$cliente_id_c){
		parent::__construct($db,$lancamento_id,$boleto_id,$cliente_id_c);
	}

    static function GerarNossoNumero($sequencial,$anoEmissao){

		//nosso número (sem dv) são 8 digitos
		$nossoNumeroSemDv = $anoEmissao.parent::formata_numero($sequencial,6,0);
		
		//dvs do nosso número
		$nossonumeroDv1 = parent::modulo_11($nossoNumeroSemDv);
		$nossonumeroDv2 = parent::modulo_11($nossoNumeroSemDv.$nossonumeroDv1,10);
		$nossoNumero = $nossoNumeroSemDv."-".$nossonumeroDv1.$nossonumeroDv2;

        return $nossoNumero;

    }

	function boletoMontar(){

		$dadosboleto = $this->dadosboleto;

		$codigobanco = "021";
		$codigo_banco_com_dv = parent::geraCodigoBanco($codigobanco);
		$nummoeda = "9";
		$fator_vencimento = parent::fator_vencimento($dadosboleto["data_vencimento"]);
		$cvt = "5";
		$zero = "00";
		
		//valor tem 10 digitos, sem virgula
		$valor = parent::formata_numero($dadosboleto["valor_boleto"],10,0,"valor");
		
		//$carteira = $dadosboleto["carteira"];
        
        //Pega o ano de emissão do boleto
        $anoEmissao = substr($dadosboleto["nosso_numero"],0,2);
		
		//nosso número (sem dv) são 8 digitos
		$nossonumero_sem_dv = $anoEmissao . parent::formata_numero($dadosboleto["sequencial"],6,0); //parent::formata_numero($dadosboleto["nosso_numero"],8,0); substr($dadosboleto["nosso_numero"],0,8);
		
        //nosso número
        //$nossonumero_com_dv = self::GerarNossoNumero($dadosboleto["sequencial"],$anoEmissao);

        //conta corrente (sem dv) são 11 digitos
		$conta = parent::formata_numero($dadosboleto["conta"],11,0);

		// Chave ASBACE 25 dígitos
		$Wtemp=parent::formata_numero($nossonumero_sem_dv,8,0).$conta.$dadosboleto["tipo_cobranca"].$codigobanco;
		$chaveasbace_dv1=self::modulo_10($Wtemp);
		$chaveasbace_dv2=parent::modulo_11($Wtemp.$chaveasbace_dv1,7);
		$dadosboleto["chave_asbace"] = $Wtemp.$chaveasbace_dv1.$chaveasbace_dv2;
		
		// 43 numeros para o calculo do digito verificador
		$dv = self::digitoVerificador("$codigobanco$nummoeda$fator_vencimento$valor".$dadosboleto['chave_asbace']);
		$linha = "$codigobanco$nummoeda$dv$fator_vencimento$valor".$dadosboleto['chave_asbace'];
		
		$agencia = parent::formata_numero($dadosboleto["agencia"],4,0);
		$agencia_codigo = $agencia." / ".$conta;
		
		$dadosboleto["codigo_barras"] = $linha;
		$dadosboleto["linha_digitavel"] = self::monta_linha_digitavel($linha);
		$dadosboleto["agencia_codigo"] = $agencia_codigo;
		$dadosboleto["codigo_banco_com_dv"] = $codigo_banco_com_dv;
		//$dadosboleto["nosso_numero"] = $nossonumero_com_dv;

		$this->dadosboleto = $dadosboleto;

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

	function digitoVerificador($numero) {
		$digito = parent::modulo_11($numero);
		if (in_array((int)$digito,array(0,1,10,11))) {
			$digito = 1;
		}
		return $digito;
	}

	function monta_linha_digitavel($codigo) {

		$banco    = substr($codigo,0,3);
		$moeda    = substr($codigo,3,1);
		$k        = substr($codigo,4,1);
		$fator    = substr($codigo,5,4);
		$valor    = substr($codigo,9,10);
		$ch       = substr($codigo,-25);
	
		$p1 = $banco.$moeda.substr($ch,0,5);
		$dv_1 = self::modulo_10($p1);
		$campo1 = substr($p1,0,5).'.'.substr($p1,-4).$dv_1;
	
		$p12 = substr($ch,5,10);
		$dv_2 = self::modulo_10($p12);
		$campo2 = substr($p12,0,5).'.'.substr($p12,-5).$dv_2;
	
		$p13 = substr($ch,15,10);
		$dv_3 = self::modulo_10($p13);
		$campo3 = substr($p13,0,5).'.'.substr($p13,-5).$dv_3;
	
		$campo4 = $k;
	
		// 5. Campo composto pelo valor nominal pelo valor nominal do documento, sem
		// indicacao de zeros a esquerda e sem edicao (sem ponto e virgula). Quando se
		// tratar de valor zerado, a representacao deve ser 000 (tres zeros).
		$campo5 = $fator.$valor;
	
		return "$campo1 $campo2 $campo3 $campo4 $campo5"; 
		/*
		// Composição da Linha Digitável
		// Primeiro Campo
		$Wtemp=$codigobanco.$nummoeda.substr($chaveasbace,0,5);
		$campo1=$Wtemp.modulo_10($Wtemp);
		$campo1=substr($campo1,0,5).".".substr($campo1,5,5);
		// Segundo Campo
		$Wtemp=substr($chaveasbace,5,10);
		$campo2=$Wtemp.modulo_10($Wtemp);
		$campo2=substr($campo2,0,5).".".substr($campo2,5,6);
		// Terceiro Campo
		$Wtemp=substr($chaveasbace,15,10);
		$campo3=$Wtemp.modulo_10($Wtemp);
		$campo3=substr($campo3,0,5).".".substr($campo3,5,6);
		// Quarto Campo
		//$Wtemp=substr($chaveasbace,15,10);
		//$campo4=$Wtemp.modulo_10($Wtemp);
		//$campo4=substr($campo4,0,5).".".substr($campo4,5,6);
		// Quinto Campo
		$campo5=$fator_vencimento.$valor;
		echo "$campo1 $campo2 $campo3 $dv $campo5"; 
		
		//$nossonumero = substr($nossonumero_dv,0,14).'-'.substr($nossonumero_dv,14,1);
		//$agencia_codigo = $agencia." / ". $conta ."-". $conta_dv;
		*/

	}
	
}

?>