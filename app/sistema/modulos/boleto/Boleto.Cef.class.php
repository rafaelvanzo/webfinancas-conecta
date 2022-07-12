<?php

class BoletoCef extends Boleto{

	function __construct($db,$lancamento_id,$boleto_id,$cliente_id_c){
		parent::__construct($db,$lancamento_id,$boleto_id,$cliente_id_c);
	}

	function boletoMontar(){

		$dadosboleto = $this->dadosboleto;

		if($dadosboleto['carteira']=='14' || $dadosboleto['carteira']=='24'){
			$dadosboleto = self::sigcb($dadosboleto);
		}elseif( in_array($dadosboleto['carteira'],array('80','81','82')) ){
			$dadosboleto = self::sicob($dadosboleto);
		}else{
			$dadosboleto = self::sicon($dadosboleto);
		}
		
		$this->dadosboleto = $dadosboleto;

	}

    static function GerarNossoNumero($carteira,$sequencial,$anoEmissao){
    
        if($carteira=='14' || $carteira=='24'){

            $anoSequencial = $anoEmissao.parent::formata_numero($sequencial,13,0);
            $nosso_numero1 = substr($anoSequencial,0,3);//"000"; // tamanho 3
            $nosso_numero_const1 = substr($carteira,0,1); //constante 1 , 1=registrada , 2=sem registro
            $nosso_numero2 = substr($anoSequencial,3,3);//"000"; // tamanho 3
            $nosso_numero_const2 = "4"; //constante 2 , 4=emitido pelo proprio cliente
            $nosso_numero3 = substr($anoSequencial,6,9);//"000000019"; // tamanho 9

            //nosso número (sem dv) são 17 digitos
            $nossoNumeroSemDv = parent::formata_numero($nosso_numero_const1,1,0).parent::formata_numero($nosso_numero_const2,1,0).parent::formata_numero($nosso_numero1,3,0).parent::formata_numero($nosso_numero2,3,0).parent::formata_numero($nosso_numero3,9,0);
            //nosso número completo (com dv) com 18 digitos
            $nossoNumero = $nossoNumeroSemDv.self::digitoVerificador_nossonumero($nossoNumeroSemDv);

        }elseif(in_array($carteira,array('80','81','82'))){
        
            //nosso número (sem dv) são 10 digitos
            $nossoNumeroSemDv = $carteira.$anoEmissao.parent::formata_numero($sequencial,6,0);
            //nosso número completo (com dv) com 11 digitos
            $nossoNumero = $nossoNumeroSemDv .'-'. self::digitoVerificador_nossonumero($nossoNumeroSemDv);

        }else{
        
            //nosso número (sem dv) é 17 digitos
            $nossoNumeroSemDv = $carteira . $anoEmissao . parent::formata_numero($sequencial,13,0);
            //nosso número completo (com dv)
            $nossoNumero = $nossoNumeroSemDv.self::digitoVerificador_nossonumero($nossoNumeroSemDv);

            $nossoNumero = substr($nossoNumero,0,18).'-'.substr($nossoNumero,18,1);
        
        }

        return $nossoNumero;
    
    }

	function sigcb($dadosboleto){

		$codigobanco = $dadosboleto['codigo_banco']; //"104";
		$codigo_banco_com_dv = parent::geraCodigoBanco($codigobanco);
		$nummoeda = "9";
		$fator_vencimento = parent::fator_vencimento($dadosboleto["data_vencimento"]);
        
        //Pega o ano de emissão do boleto
        $anoEmissao = substr($dadosboleto["nosso_numero"],2,2);

		// Composição Nosso Numero - CEF SIGCB

		$anoSequencial = $anoEmissao . parent::formata_numero($dadosboleto["sequencial"],13,0); // parent::formata_numero($dadosboleto["nosso_numero"],15,0);
		$dadosboleto["nosso_numero1"] = substr($anoSequencial,0,3);//"000"; // tamanho 3
		$dadosboleto["nosso_numero_const1"] = substr($dadosboleto['carteira'],0,1); //constante 1 , 1=registrada , 2=sem registro
		$dadosboleto["nosso_numero2"] = substr($anoSequencial,3,3);//"000"; // tamanho 3
		$dadosboleto["nosso_numero_const2"] = "4"; //constante 2 , 4=emitido pelo proprio cliente
		$dadosboleto["nosso_numero3"] = substr($anoSequencial,6,9);//"000000019"; // tamanho 9

		//valor tem 10 digitos, sem virgula
		$valor = parent::formata_numero($dadosboleto["valor_boleto"],10,0,"valor");
		//agencia é 4 digitos
		$agencia = parent::formata_numero($dadosboleto["agencia"],4,0);
		//conta é 5 digitos
		$conta = parent::formata_numero($dadosboleto["conta"],5,0);
		//dv da conta
		//$conta_dv = formata_numero($dadosboleto["conta_dv"],1,0);
		
		//conta cedente (sem dv) com 6 digitos
		$conta_cedente = parent::formata_numero($dadosboleto["conta_cedente"],6,0);
		//dv da conta cedente
		$conta_cedente_dv = self::digitoVerificador_cedente($conta_cedente);
		
		//campo livre (sem dv) é 24 digitos
		$campo_livre = $conta_cedente . $conta_cedente_dv . parent::formata_numero($dadosboleto["nosso_numero1"],3,0) . parent::formata_numero($dadosboleto["nosso_numero_const1"],1,0) . parent::formata_numero($dadosboleto["nosso_numero2"],3,0) . parent::formata_numero($dadosboleto["nosso_numero_const2"],1,0) . parent::formata_numero($dadosboleto["nosso_numero3"],9,0);
		//dv do campo livre
		$dv_campo_livre = self::digitoVerificador_nossonumero($campo_livre); //variar de 0 a 9 para homologar; varia de acordo com o convenio(conta cedente), DV do convenio(conta cedente) e o nosso número
		$campo_livre_com_dv ="$campo_livre$dv_campo_livre";
		
        //nosso número
        //$nossoNumero = self::GerarNossoNumero($dadosboleto["carteira"],$dadosboleto["sequencial"],$anoEmissao);

        //carteira são 2 caracteres - SR para sem registro
        //altera a carteira para "SR" após utilizar o código na geração do nosso número
		($dadosboleto['carteira'] == '14') ? $dadosboleto["carteira"] = "RG" : $dadosboleto["carteira"] = "SR";

		// 43 numeros para o calculo do digito verificador do codigo de barras
		$dv = self::digitoVerificador_barra("$codigobanco$nummoeda$fator_vencimento$valor$campo_livre_com_dv", 9, 0); //variar de 1 a 9 para homologar; campos variáveis: $fator_vencimento, $valor e $campo_livre_com_dv
		// Numero para o codigo de barras com 44 digitos
		$linha = "$codigobanco$nummoeda$dv$fator_vencimento$valor$campo_livre_com_dv";

		$agencia_codigo = $agencia." / ". $conta_cedente ."-". $conta_cedente_dv;
		
		$dadosboleto["codigo_barras"] = $linha;
		$dadosboleto["linha_digitavel"] = parent::monta_linha_digitavel($linha);
		$dadosboleto["agencia_codigo"] = $agencia_codigo;
		//$dadosboleto["nosso_numero"] = $nossoNumero;
		$dadosboleto["codigo_banco_com_dv"] = $codigo_banco_com_dv;
        
		return $dadosboleto;

	}
	
	function sicob($dadosboleto){

		$codigobanco = "104";
		$codigo_banco_com_dv = parent::geraCodigoBanco($codigobanco);
		$nummoeda = "9";
		$fator_vencimento = parent::fator_vencimento($dadosboleto["data_vencimento"]);
		
		//valor tem 10 digitos, sem virgula
		$valor = parent::formata_numero($dadosboleto["valor_boleto"],10,0,"valor");
		//agencia é 4 digitos
		$agencia = parent::formata_numero($dadosboleto["agencia"],4,0);
		//conta é 5 digitos
		//$conta = formata_numero($dadosboleto["conta"],5,0);
		//dv da conta
		//$conta_dv = formata_numero($dadosboleto["conta_dv"],1,0);
				
		//conta cedente (sem dv) com 11 digitos   (Operacao de 3 digitos + Cedente de 8 digitos)
		$conta_cedente = parent::formata_numero($dadosboleto["conta_cedente"],11,0);
		//dv da conta cedente - retirado da carteira SIGCB pela Web 2 Business
		$conta_cedente_dv = self::digitoVerificador_cedente($conta_cedente); //$conta_cedente_dv = parent::formata_numero($dadosboleto["conta_cedente_dv"],1,0);
		
        //Pega o ano de emissão do boleto
        $anoEmissao = substr($dadosboleto["nosso_numero"],2,2);
        
		//nosso número (sem dv) é 10 digitos
		$nossoNumeroSemDv = $dadosboleto["carteira"].$anoEmissao.parent::formata_numero($dadosboleto["sequencial"],6,0); // parent::formata_numero($dadosboleto["nosso_numero"],8,0);
		
        //nosso número
        //$nossoNumero = self::GerarNossoNumero($dadosboleto["carteira"],$dadosboleto["sequencial"],$anoEmissao);

        //carteira são 2 caracteres
        //altera a carteira para "SR" após utilizar o código na geração do nosso número
		$dadosboleto["carteira"] = "SR";

		// 43 numeros para o calculo do digito verificador do codigo de barras
		$dv = self::digitoVerificador_barra("$codigobanco$nummoeda$fator_vencimento$valor$nossoNumeroSemDv$agencia$conta_cedente", 9, 0);
		// Numero para o codigo de barras com 44 digitos
		$linha = "$codigobanco$nummoeda$dv$fator_vencimento$valor$nossoNumeroSemDv$agencia$conta_cedente";
		
		$agencia_codigo = $agencia." / ". $conta_cedente ."-". $conta_cedente_dv;
		
		$dadosboleto["codigo_barras"] = $linha;
		$dadosboleto["linha_digitavel"] = parent::monta_linha_digitavel($linha);
		$dadosboleto["agencia_codigo"] = $agencia_codigo;
		//$dadosboleto["nosso_numero"] = $nossoNumero;
		$dadosboleto["codigo_banco_com_dv"] = $codigo_banco_com_dv;
		
		return $dadosboleto;

	}
	
	function sicon($dadosboleto){

		$dadosboleto["campo_fixo_obrigatorio"] = "1";       // campo fixo obrigatorio - valor = 1 
		$dadosboleto["inicio_nosso_numero"] = "9";          // Inicio do Nosso numero - obrigatoriamente deve começar com 9;		
		$codigobanco = "104";
		$codigo_banco_com_dv = parent::geraCodigoBanco($codigobanco);
		$nummoeda = "9";
		$fator_vencimento = parent::fator_vencimento($dadosboleto["data_vencimento"]);
		
		//valor tem 10 digitos, sem virgula
		$valor = parent::formata_numero($dadosboleto["valor_boleto"],10,0,"valor");
		//agencia é 4 digitos
		$agencia = parent::formata_numero($dadosboleto["agencia"],4,0);
		//conta é 5 digitos
		//$conta = parent::formata_numero($dadosboleto["conta"],5,0);
		//dv da conta
		//$conta_dv = parent::formata_numero($dadosboleto["conta_dv"],1,0);
		        
        //Pega o ano de emissão do boleto
        $anoEmissao = substr($dadosboleto["nosso_numero"],2,2);
		
		//nosso número (sem dv) é 17 digitos
		$nossoNumeroSemDv = $dadosboleto["carteira"].$anoEmissao.parent::formata_numero($dadosboleto["sequencial"],13,0); //parent::formata_numero($dadosboleto["nosso_numero"],17,0);
		
		//conta cedente (sem dv) é 6 digitos
		$conta_cedente = parent::formata_numero($dadosboleto["conta_cedente"],6,0);
		//dv da conta cedente - retirado da carteira SIGCB pela Web 2 Business
		$conta_cedente_dv = self::digitoVerificador_cedente($conta_cedente);//$conta_cedente_dv = formata_numero($dadosboleto["conta_cedente_dv"],1,0);
		
		$ag_contacedente  = $agencia . $conta_cedente;
		$fixo             = $dadosboleto["campo_fixo_obrigatorio"];
		$campo_livre      = "$fixo$conta_cedente$nossoNumeroSemDv";
		
		// 43 numeros para o calculo do digito verificador do codigo de barras
		$dv = self::digitoVerificador_barra("$codigobanco$nummoeda$fator_vencimento$valor$campo_livre", 9, 0);
		// Numero para o codigo de barras com 44 digitos
		$linha = "$codigobanco$nummoeda$dv$fator_vencimento$valor$campo_livre";
		
        //nosso número
        //$nossoNumero = self::GerarNossoNumero($dadosboleto["carteira"],$dadosboleto["sequencial"],$anoEmissao);

        //carteira são 2 caracteres
        //altera a carteira para "SR" após utilizar o código na geração do nosso número
		$dadosboleto["carteira"] = "SR";

		$agencia_codigo = $agencia." / ". $conta_cedente ."-". $conta_cedente_dv;
		
		$dadosboleto["codigo_barras"] = $linha;
		$dadosboleto["linha_digitavel"] = parent::monta_linha_digitavel($linha);
		$dadosboleto["agencia_codigo"] = $agencia_codigo;
		//$dadosboleto["nosso_numero"] = $nossoNumero;
		$dadosboleto["codigo_banco_com_dv"] = $codigo_banco_com_dv;

		return $dadosboleto;

	}

	static function digitoVerificador_nossonumero($numero) {
		$resto2 = parent::modulo_11($numero, 9, 1);
			 $digito = 11 - $resto2;
			 if ($digito == 10 || $digito == 11) {
					$dv = 0;
			 } else {
					$dv = $digito;
			 }
		 return $dv;
	}
	
	
	function digitoVerificador_cedente($numero) {
		$resto2 = parent::modulo_11($numero, 9, 1);
		$digito = 11 - $resto2;
		if ($digito == 10 || $digito == 11) $digito = 0;
		$dv = $digito;
		return $dv;
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

}

?>