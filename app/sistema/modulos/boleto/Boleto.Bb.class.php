<?php

class BoletoBb extends Boleto{

	function __construct($db,$lancamento_id,$boleto_id,$cliente_id_c){
		parent::__construct($db,$lancamento_id,$boleto_id,$cliente_id_c);
	}

    static function GerarNossoNumero($sequencial,$anoEmissao,$convenio){
        
        $convenioDigitos = strlen($convenio);

        $formatacaoNossoNumero = 2;

        // Carteira 18 com Conv�nio de 8 d�gitos
		if ($convenioDigitos == "8") {
			$convenio = parent::formata_numero($convenio,8,0,"convenio");
			// Nosso n�mero de at� 9 d�gitos
			$nossoNumero = $anoEmissao . parent::formata_numero($sequencial,7,0);  //parent::formata_numero($dadosboleto["nosso_numero"],9,0);
			//montando o nosso numero que aparecer� no boleto
			$nossoNumero = $convenio . $nossoNumero ."-". parent::modulo_11($convenio.$nossoNumero);
		}
		
		// Carteira 18 com Conv�nio de 7 d�gitos
		if ($convenioDigitos == "7") {
			$convenio = parent::formata_numero($convenio,7,0,"convenio");
			// Nosso n�mero de at� 10 d�gitos
			$nossoNumero = $anoEmissao . parent::formata_numero($sequencial,8,0); //parent::formata_numero($dadosboleto["nosso_numero"],10,0)
			$nossoNumero = $convenio.$nossoNumero; //N�o existe DV na composi��o do nosso-n�mero para conv�nios de sete posi��es
		}
		
		// Carteira 18 com Conv�nio de 6 d�gitos
		if ($convenioDigitos == "6") {

			$convenio = parent::formata_numero($convenio,6,0,"convenio");
			
			//Campo nosso número 11 dígitos mais o dígito verificador
            if ($formatacaoNossoNumero == 1) {
				
				$nossoNumero = parent::formata_numero($sequencial,5,0);
				//montando o nosso numero que aparecer� no boleto
				$nossoNumero = $convenio . $nossoNumero ."-". parent::modulo_11($convenio.$nossoNumero);
			}
			
			//Campo nosso número com 17 dígitos; Não tem dígito verificador; Somente para boleto sem registro
            if ($formatacaoNossoNumero == 2) {
				
                $nossoNumero = $convenio.$anoEmissao.parent::formata_numero($sequencial,9,0);
			}
		}

        return $nossoNumero;

    }

	function boletoMontar(){

		$dadosboleto = $this->dadosboleto;

		// TIPO DO BOLETO
		$conveniodigitos = strlen($dadosboleto["convenio"]);
		$dadosboleto["formatacao_convenio"] = $conveniodigitos;//$dadosboleto["formatacao_convenio"] = "7"; // REGRA: 8 p/ Conv�nio c/ 8 d�gitos, 7 p/ Conv�nio c/ 7 d�gitos, ou 6 se Conv�nio c/ 6 d�gitos
		$dadosboleto["formatacao_nosso_numero"] = "2"; // REGRA: Usado apenas p/ Conv�nio c/ 6 d�gitos: informe 1 se for NossoN�mero de at� 5 d�gitos ou 2 para op��o de at� 17 d�gitos

		$codigobanco = $dadosboleto['codigo_banco'];//"001";
		$codigo_banco_com_dv = parent::geraCodigoBanco($codigobanco);
		$nummoeda = "9";
		$fator_vencimento = parent::fator_vencimento($dadosboleto["data_vencimento"]);
		
		//valor tem 10 digitos, sem virgula
		$valor = parent::formata_numero($dadosboleto["valor_boleto"],10,0,"valor");
		//agencia � sempre 4 digitos
		$agencia = parent::formata_numero($dadosboleto["agencia"],4,0);
		//conta � sempre 8 digitos
		$conta = parent::formata_numero($dadosboleto["conta"],8,0);
		//carteira 18
		$carteira = $dadosboleto["carteira"];
		//agencia e conta
		$agencia_codigo = $agencia."-". parent::modulo_11($agencia) ." / ". $conta ."-". parent::modulo_11($conta);
		//Zeros: usado quando convenio de 7 digitos
		$livre_zeros='000000';
		
		// Carteira 18 com Conv�nio de 8 d�gitos
		if ($dadosboleto["formatacao_convenio"] == "8") {

            //Pega o ano de emissão do boleto
            $anoEmissao = substr($dadosboleto["nosso_numero"],strlen($dadosboleto["convenio"]),2);

			$convenio = parent::formata_numero($dadosboleto["convenio"],8,0,"convenio");
			// Nosso n�mero de at� 9 d�gitos
			$nossonumero = $anoEmissao . parent::formata_numero($dadosboleto["sequencial"],7,0);  //parent::formata_numero($dadosboleto["nosso_numero"],9,0);
			$dv=parent::modulo_11("$codigobanco$nummoeda$fator_vencimento$valor$livre_zeros$convenio$nossonumero$carteira");
			$linha="$codigobanco$nummoeda$dv$fator_vencimento$valor$livre_zeros$convenio$nossonumero$carteira";
			//montando o nosso numero que aparecer� no boleto
            //$nossonumero = self::GerarNossoNumero($dadosboleto["sequencial"],$anoEmissao,$dadosboleto["convenio"]);
		}
		
		// Carteira 18 com Conv�nio de 7 d�gitos
		if ($dadosboleto["formatacao_convenio"] == "7") {

            //Pega o ano de emissão do boleto
            $anoEmissao = substr($dadosboleto["nosso_numero"],strlen($dadosboleto["convenio"]),2);

			$convenio = parent::formata_numero($dadosboleto["convenio"],7,0,"convenio");
			// Nosso n�mero de at� 10 d�gitos
			$nossonumero = $anoEmissao . parent::formata_numero($dadosboleto["sequencial"],8,0); //parent::formata_numero($dadosboleto["nosso_numero"],10,0)
			$dv=parent::modulo_11("$codigobanco$nummoeda$fator_vencimento$valor$livre_zeros$convenio$nossonumero$carteira", 9, 2);
			$linha="$codigobanco$nummoeda$dv$fator_vencimento$valor$livre_zeros$convenio$nossonumero$carteira";
			//N�o existe DV na composi��o do nosso-n�mero para conv�nios de sete posi��es
            //$nossonumero = self::GerarNossoNumero($dadosboleto["sequencial"],$anoEmissao,$dadosboleto["convenio"]);
		}
		
		// Carteira 18 com Conv�nio de 6 d�gitos
		if ($dadosboleto["formatacao_convenio"] == "6") {

			$convenio = parent::formata_numero($dadosboleto["convenio"],6,0,"convenio");
			
			//Campo nosso número 11 dígitos mais o dígito verificador
            if ($dadosboleto["formatacao_nosso_numero"] == "1") {
				
				$nossonumero = parent::formata_numero($dadosboleto["sequencial"],5,0);
				$dv = parent::modulo_11("$codigobanco$nummoeda$fator_vencimento$valor$convenio$nossonumero$agencia$conta$carteira");
				$linha = "$codigobanco$nummoeda$dv$fator_vencimento$valor$convenio$nossonumero$agencia$conta$carteira";
				//montando o nosso numero que aparecer� no boleto
                //$nossonumero = self::GerarNossoNumero($dadosboleto["sequencial"],'',$dadosboleto["convenio"]);
			}
			
			//Campo nosso número com 17 dígitos; Não tem dígito verificador; Somente para boleto sem registro
            if ($dadosboleto["formatacao_nosso_numero"] == "2") {
				
                //Pega o ano de emissão do boleto
                $anoEmissao = substr($dadosboleto["nosso_numero"],strlen($dadosboleto["convenio"]),2);

				$nservico = "18"; //no manual diz 21, mas no boleto da Vidromol gerado pelo próprio banco está 18
                $nossonumero = $anoEmissao.parent::formata_numero($dadosboleto["sequencial"],9,0); //$nossonumero = parent::formata_numero($convenio,12,0) . $ano_emissao . parent::formata_numero($dadosboleto["nosso_numero"],9,0);
				$dv = parent::modulo_11("$codigobanco$nummoeda$fator_vencimento$valor$nossonumero$nservico", 9, 2);
				$linha = "$codigobanco$nummoeda$dv$fator_vencimento$valor$nossonumero$nservico";
                //$nossonumero = self::GerarNossoNumero($dadosboleto["sequencial"],$anoEmissao,$dadosboleto["convenio"]); //$nossonumero = $convenio.parent::formata_numero($dadosboleto["nosso_numero"],11,0);

			}
		}
		
		$dadosboleto["codigo_barras"] = $linha;
		$dadosboleto["linha_digitavel"] = parent::monta_linha_digitavel($linha);
		$dadosboleto["agencia_codigo"] = $agencia_codigo;
		//$dadosboleto["nosso_numero"] = $nossonumero;
		$dadosboleto["codigo_banco_com_dv"] = $codigo_banco_com_dv;

		$this->dadosboleto = $dadosboleto;
		
	}

}

?>