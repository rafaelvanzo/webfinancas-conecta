<?php

/**
 * BoletoBradesco short summary.
 *
 * BoletoBradesco description.
 *
 * @version 1.0
 * @author Fabio
 */
class BoletoBradesco extends Boleto
{
    //Id de cliente do cedente no Web Finanças; Usado para Lexdata
    private $cedenteId;

    function __construct($db,$lancamento_id,$boleto_id,$cliente_id_c){
		
        parent::__construct($db,$lancamento_id,$boleto_id,$cliente_id_c);
        
        //Usado para Lexdata
        $this->cedenteId = $cliente_id_c;
	}

    static function GerarNossoNumero($carteira,$sequencial,$anoEmissao){
        
        //nosso número (sem dv) são 11 digitos
		$nossoNumeroSemDv = $anoEmissao.parent::formata_numero($sequencial,9,0);
		//dv do nosso número
        $dvNossoNumero = self::digitoVerificador_nossonumero($carteira.$nossoNumeroSemDv);
        // nosso número para registrar no banco de dados e exibir no boleto impresso
        //na montagem da linha digitável e do código de barras, o nosso número será destrinchado
		$nossoNumero =  $carteira.'/'.$nossoNumeroSemDv.'-'.$dvNossoNumero;

        return $nossoNumero;
    }

    function boletoMontar(){

		$dadosboleto = $this->dadosboleto;

		$codigobanco = $dadosboleto['codigo_banco']; //237
        $codigo_banco_com_dv = parent::geraCodigoBanco($codigobanco);
        $nummoeda = "9";
        $fator_vencimento = parent::fator_vencimento($dadosboleto["data_vencimento"]);

        //valor tem 10 digitos, sem virgula
        $valor = parent::formata_numero($dadosboleto["valor_boleto"],10,0,"valor");
        //agencia é 4 digitos
        $agencia = parent::formata_numero($dadosboleto["agencia"],4,0);
        //carteira: 06 - sem registro; 09 - com registro
        $carteira = $dadosboleto["carteira"];
        //conta é 6 digitos
        //$conta = parent::formata_numero($dadosboleto["conta"],6,0);
        //dv da conta
        //$conta_dv = parent::formata_numero($dadosboleto["conta_dv"],1,0);

        //Ex. de nosso número: 99/99999999999-D
        //Vem direto do banco de dados porque já foi montado pela função GerarNossoNumero ao gerar o boleto
        if($this->cedenteId == 244) //134 - Web Finanças Teste; 244 - Lexdata
            //Usado para Lexdata
            $nossoNumeroSemDv = str_pad(substr($dadosboleto["nosso_numero"],0,-1),11,'0',STR_PAD_LEFT);
        else
            $nossoNumeroSemDv = substr($dadosboleto["nosso_numero"],3,11);
        
        //conta cedente (sem dv) é 7 digitos
        $conta_cedente = parent::formata_numero($dadosboleto["conta_cedente"],7,0);
        //dv da conta cedente
        $conta_cedente_dv = self::digitoVerificador_cedente($conta_cedente);
        
        //campo livre (cada banco pode criar o seu)
        $campoLivre = "$agencia$carteira$nossoNumeroSemDv$conta_cedente"."0";
        //echo $campoLivre;
        // 43 numeros para o calculo do digito verificador do codigo de barras
        //Identificação do Banco, Código da Moeda (Real = 9, Outras=0), Fator de Vencimento, Valor, Campo Livre
        $dv = self::digitoVerificador_barra("$codigobanco$nummoeda$fator_vencimento$valor$campoLivre", 9, 0);
        //Número com 44 digitos para montar o codigo de barras
        //Identificação do Banco, Código da Moeda (Real = 9, Outras=0), Dígito verificador do Código de Barras, Fator de Vencimento, Valor, Campo Livre
        $codigoDeBarras = "$codigobanco$nummoeda$dv$fator_vencimento$valor$campoLivre";
        //linha digitável
        $linhaDigitavel = parent::monta_linha_digitavel($codigoDeBarras);
        //Agênica e código do cedente para o boleto impresso
        $agencia_codigo = $agencia."-".$dadosboleto["agencia_dv"]." / ". $conta_cedente ."-". $conta_cedente_dv;
        
        $dadosboleto["codigo_barras"] = $codigoDeBarras;
        $dadosboleto["linha_digitavel"] = $linhaDigitavel;
        $dadosboleto["agencia_codigo"] = $agencia_codigo;
        //$dadosboleto["nosso_numero"] = $nossonumero;
        $dadosboleto["codigo_banco_com_dv"] = $codigo_banco_com_dv;

		$this->dadosboleto = $dadosboleto;
	}

    static function digitoVerificador_nossonumero($numero) {
        $resto2 = parent::modulo_11($numero, 7, 1);
        $digito = 11 - $resto2;
        if ($digito == 10) {
            $dv = "P";
        } elseif($digito == 11) {
            $dv = 0;
        } else {
            $dv = $digito;
        }
        return $dv;
    }
    static function digitoVerificador_barra($numero) {
        $resto2 = parent::modulo_11($numero, 9, 1);
        if ($resto2 == 0 || $resto2 == 1 || $resto2 == 10) {
            $dv = 1;
        } else {
            $dv = 11 - $resto2;
        }
        return $dv;
    }

    static function digitoVerificador_cedente($numero) {
		$resto2 = parent::modulo_11($numero, 9, 1);
		$digito = 11 - $resto2;
		if ($digito == 10 || $digito == 11) $digito = 0;
		$dv = $digito;
		return $dv;
	}
}
