<?php
class Boleto{

	public $dadosboleto;

	function __construct($db,$lancamento_id,$boleto_id,$cliente_id_c){

		$dados = $db->fetch_assoc("select valor, dt_vencimento, dt_emissao, favorecido_id, conta_id, observacao from lancamentos where id = ".$lancamento_id);
		$conta = $db->fetch_assoc("select banco_id, agencia, agencia_dv, numero, carteira, convenio, variacao, modalidade, custo_emissao, custo_compensacao, multa, juros, msg1, msg2, msg3, inst1, inst2, inst3, nomeTitular, cpf_cnpj from contas where id = ".$dados['conta_id']);
		$banco = $db->fetch_assoc("select codigo, logo_boleto from bancos where id = ".$conta['banco_id']);
		$lancamento = $db->fetch_assoc("select parcela_numero from lancamentos where id = ".$lancamento_id);
		$boleto = $db->fetch_assoc("select sequencial, nosso_numero from boletos where id = ".$boleto_id);
		$favorecido = $db->fetch_assoc("select * from favorecidos where id = ".$dados['favorecido_id']);

		// ------------------------- DADOS DIN�MICOS DO SEU CLIENTE PARA A GERA��O DO BOLETO (FIXO OU VIA GET) -------------------- //
		// Os valores abaixo podem ser colocados manualmente ou ajustados p/ formul�rio c/ POST, GET ou de BD (MySql,Postgre,etc)	//

		// DADOS DO BOLETO PARA O SEU CLIENTE
		$this->dadosboleto['boleto_id'] = $boleto_id;
		$this->dadosboleto['codigo_banco'] = $banco['codigo'];
		$this->dadosboleto['logo_boleto'] = $banco['logo_boleto'];
		$this->dadosboleto['sequencial'] = $boleto['sequencial'];
        $this->dadosboleto['nosso_numero'] = $boleto['nosso_numero'];
		$this->dadosboleto['numero_documento'] = $boleto['sequencial'];
		$this->dadosboleto['data_vencimento'] = $db->sql_to_data($dados["dt_vencimento"]);
		$this->dadosboleto['data_documento'] = $db->sql_to_data($dados["dt_emissao"]);
		$this->dadosboleto['data_processamento'] = date("d/m/Y");
		//$this->custo_boleto = $conta["custo"];
        //$this->dadosboleto['valor_boleto'] = number_format($dados["valor"]+$conta["custo"], 2, ',', '');
		$this->dadosboleto['valor_lancamento'] = $dados["valor"];
        $this->dadosboleto['valor_boleto'] = number_format($dados["valor"], 2, ',', '');

        //INFORMAÇÕES DE MULTA E JUROS
        $this->dadosboleto['multa'] = number_format($conta["multa"], 2, ',', ''); 
        $this->dadosboleto['juros'] = number_format($conta["juros"], 2, ',', '');
        
		// DADOS DO SEU CLIENTE
		$this->dadosboleto["sacado"] = $favorecido["nome"].', '.$favorecido['cpf_cnpj'];
		$this->dadosboleto["endereco1"] = $favorecido["logradouro"].', '.$favorecido["numero"].', '.$favorecido["complemento"].', '.$favorecido["bairro"];
		$this->dadosboleto["endereco2"] = $favorecido["cidade"].' - '.$favorecido["uf"].' - '.$favorecido["cep"];

		// INFORMACOES PARA O CLIENTE
		$this->dadosboleto["demonstrativo1"] = $conta['msg1'];
		$this->dadosboleto["demonstrativo2"] = $conta['msg2'];
		$this->dadosboleto["demonstrativo3"] = $conta['msg3'];

		// INSTRU��ES PARA O CAIXA
		$this->dadosboleto["instrucoes1"] = $conta['inst1'];
		$this->dadosboleto["instrucoes2"] = $conta['inst2'];
		$this->dadosboleto["instrucoes3"] = $conta['inst3'];
		
		// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
		$this->dadosboleto["quantidade"] = "";//$dadosboleto["quantidade"] = "10";
		$this->dadosboleto["valor_unitario"] = "";//$dadosboleto["valor_unitario"] = "10";
		$this->dadosboleto["aceite"] = "N";//$dadosboleto["aceite"] = "N"; //A - Cobrança aceita pelo pagador via assinatra no título, N - Cobrança sem assinatura no título
		$this->dadosboleto["especie"] = "R$";
		$this->dadosboleto["especie_doc"] = "DM";//$dadosboleto["especie_doc"] = "DM"; //DM - Duplicata mercantil, DS - Duplicata de serviço, NP - Nota promissória...

        //DADOS DO CONTRATO PARA EMISSÃO DE BOLETO JUNTO AO BANCO
        $this->dadosboleto["conta_cedente"] = $conta["convenio"];
        $this->dadosboleto["convenio"] = $conta["convenio"];
        $this->dadosboleto["carteira"] = $conta["carteira"];

		//OBSERVACAO
		$this->dadosboleto['observacao'] = $dados['observacao'];

		// ---------------------- DADOS FIXOS DE CONFIGURA��O DO SEU BOLETO --------------- //

		// SEUS DADOS
		$db_w2b = mysqli_connect("mysql.web2business.com.br","web2business","W2BSISTEMAS","web2business");
		$cedente = mysqli_fetch_assoc(mysqli_query($db_w2b,"select * from clientes where id = ".$cliente_id_c));
		$this->dadosboleto["cedente"] = $conta['nomeTitular'];//$cedente["nome"];//$dadosboleto["identificacao"] = "BoletoPhp - C�digo Aberto de Sistema de Boletos";
		$this->dadosboleto["cpf_cnpj"] = $conta['cpf_cnpj'];//$cedente["cpf_cnpj"];//$dadosboleto["cpf_cnpj"] = "";
		$this->dadosboleto["endereco"] = $cedente["logradouro"].", ".$cedente["numero"].", ".$cedente["complemento"]." - ".$cedente["bairro"]." - ".$cedente["cidade"]."/".$cedente["uf"]." - ".$cedente["cep"]; //$dadosboleto["endereco"] = "Coloque o endere�o da sua empresa aqui";
        $this->dadosboleto["logo_cliente"] = $cedente['logo_recibo'];       
        
		// DADOS DA SUA CONTA
		$this->dadosboleto["agencia"] = $conta["agencia"];//$dadosboleto["agencia"] = "9999"; // Num da agencia, sem digito
        $this->dadosboleto["agencia_dv"] = $conta["agencia_dv"];
		$this->dadosboleto["conta"] = $conta["numero"];//$dadosboleto["conta"] = "99999"; 	// Num da conta, sem digito

		// DADOS PERSONALIZADOS - BANCO DO BRASIL
		if($banco['codigo']=='001'){
			$this->dadosboleto["contrato"] = "";//$dadosboleto["contrato"] = "999999"; // Num do seu contrato
			if($conta["variacao"]!=""){
				$this->dadosboleto["variacao_carteira"] = "-0".$conta["variacao"];//$dadosboleto["variacao_carteira"] = "-019";  // Varia��o da Carteira, com tra�o (opcional)
			}else{
				$this->dadosboleto["variacao_carteira"] = "";
			}
		}
		
		// DADOS PERSONALIZADOS - CEF
		if($banco['codigo']=='104'){
			$this->dadosboleto["inicio_nosso_numero"] = $conta["carteira"];            
            $this->dadosboleto["BancoMsg"] = 'PREFERENCIALMENTE NAS CASAS LOTÉRICAS ATÉ O VALOR LIMITE.<br><br>
                                              SAC CAIXA: 0800 726 0101 (informações, reclamações, sugestões e elogios). 
                                              Para pessoas com deficiência auditiva ou de fala: 0800 726 2492. 
                                              Ouvidoria: 0800 725 7474 ou pelo site caixa.gov.br.';
		}

		// DADOS PERSONALIZADOS - SICOOB
		if($banco['codigo']=='756'){
			$this->dadosboleto["modalidade_cobranca"] = $conta["modalidade"]; //dois dígitos: 02 (01 com registro e 02 sem registro)
			$this->dadosboleto["numero_parcela"] = $lancamento['parcela_numero'];
		}

		// DADOS PERSONALIZADOS - BANESTES
		if($banco['codigo']=='021'){
			$this->dadosboleto["tipo_cobranca"] = "2";  // 2- Sem registro; // 3- Caucionada; // 4,5,6 e 7-Cobrança com registro
		}

        if($cliente_id_c == '1' || $cliente_id_c == '134' || $cliente_id_c == '244')
            self::CalcularJurosMulta($db,$dados["dt_vencimento"],$dados["valor"],$conta["juros"],$conta["multa"]);
	}

    function GetAllFeriados($ano=null)
    {
        if ($ano === null)
        {
            $ano = intval(date('Y'));
        }
        
        $pascoa     = easter_date($ano); // Limite de 1970 ou após 2037 da easter_date PHP consulta http://www.php.net/manual/pt_BR/function.easter-date.php
        $dia_pascoa = date('j', $pascoa);
        $mes_pascoa = date('n', $pascoa);
        $ano_pascoa = date('Y', $pascoa);
        
        $feriados = array(
          // Datas Fixas dos feriados Nacionail Basileiras
          mktime(0, 0, 0, 1,  1,   $ano), // Confraternização Universal - Lei nº 662, de 06/04/49
          mktime(0, 0, 0, 4,  21,  $ano), // Tiradentes - Lei nº 662, de 06/04/49
          mktime(0, 0, 0, 5,  1,   $ano), // Dia do Trabalhador - Lei nº 662, de 06/04/49
          mktime(0, 0, 0, 9,  7,   $ano), // Dia da Independência - Lei nº 662, de 06/04/49
          mktime(0, 0, 0, 10,  12, $ano), // N. S. Aparecida - Lei nº 6802, de 30/06/80
          mktime(0, 0, 0, 11,  2,  $ano), // Todos os santos - Lei nº 662, de 06/04/49
          mktime(0, 0, 0, 11, 15,  $ano), // Proclamação da republica - Lei nº 662, de 06/04/49
          mktime(0, 0, 0, 12, 25,  $ano), // Natal - Lei nº 662, de 06/04/49
   
          // These days have a date depending on easter
          mktime(0, 0, 0, $mes_pascoa, $dia_pascoa - 48,  $ano_pascoa),//2ºferia Carnaval
          mktime(0, 0, 0, $mes_pascoa, $dia_pascoa - 47,  $ano_pascoa),//3ºferia Carnaval	
          mktime(0, 0, 0, $mes_pascoa, $dia_pascoa - 2 ,  $ano_pascoa),//6ºfeira Santa  
          mktime(0, 0, 0, $mes_pascoa, $dia_pascoa     ,  $ano_pascoa),//Pascoa
          mktime(0, 0, 0, $mes_pascoa, $dia_pascoa + 60,  $ano_pascoa),//Corpus Cirist
        );
        
        sort($feriados);
        
        return $feriados;
    }

    function VerificarDiaUtil($dataTimeStamp)
    {
        $diaUtil = false;

        $feriados = self::GetAllFeriados();

        while(!$diaUtil)
        {
            if (in_array($dataTimeStamp,$feriados) || date('w',$dataTimeStamp) == 0 || date('w',$dataTimeStamp) == 6)
                $dataTimeStamp = strtotime('+ 1 day', $dataTimeStamp);
            else
                $diaUtil = true;
        }

        return $dataTimeStamp;
    }

    function CalcularJurosMulta($db,$dtVencimento,$valor,$taxaJurosMensal,$taxaMulta)
    {

        $hoje = strtotime(date('Y-m-d'));
        $dtVencimento = self::VerificarDiaUtil(strtotime($dtVencimento)); //$dtVencimento = strtotime($dtVencimento);
        $dateDiff = ($dtVencimento - $hoje)/86400; //86400 é o total de segundos em um dia ou 24h

        if($dateDiff<0){

            $diasAtraso = $dateDiff*(-1);

            $this->dadosboleto['valor_boleto'] = number_format($valor + $taxaMulta/100 * $valor + $taxaJurosMensal/30/100 * $valor * $diasAtraso, 2, ',', '');
            $this->dadosboleto['data_vencimento'] = date('d/m/Y');
        }
    }

	// FUN��ES
	// Algumas foram retiradas do Projeto PhpBoleto e modificadas para atender as particularidades de cada banco
	
	static function formata_numero($numero,$loop,$insert,$tipo = "geral") {
		if ($tipo == "geral") {
			$numero = str_replace(",","",$numero);
			while(strlen($numero)<$loop){
				$numero = $insert . $numero;
			}
		}
		if ($tipo == "valor") {
			/*
			retira as virgulas
			formata o numero
			preenche com zeros
			*/
			$numero = str_replace(",","",$numero);
			while(strlen($numero)<$loop){
				$numero = $insert . $numero;
			}
		}
		if ($tipo == "convenio") {
			while(strlen($numero)<$loop){
				$numero = $numero . $insert;
			}
		}
		return $numero;
	}
	
	
	function fbarcode($valor){
	
	$fino = 1 ;
	$largo = 3 ;
	$altura = 50 ;
	
		$barcodes[0] = "00110" ;
		$barcodes[1] = "10001" ;
		$barcodes[2] = "01001" ;
		$barcodes[3] = "11000" ;
		$barcodes[4] = "00101" ;
		$barcodes[5] = "10100" ;
		$barcodes[6] = "01100" ;
		$barcodes[7] = "00011" ;
		$barcodes[8] = "10010" ;
		$barcodes[9] = "01010" ;
		for($f1=9;$f1>=0;$f1--){ 
			for($f2=9;$f2>=0;$f2--){  
				$f = ($f1 * 10) + $f2 ;
				$texto = "" ;
				for($i=1;$i<6;$i++){ 
					$texto .=  substr($barcodes[$f1],($i-1),1) . substr($barcodes[$f2],($i-1),1);
				}
				$barcodes[$f] = $texto;
			}
		}
	
	
	//Desenho da barra
	
	
	//Guarda inicial
	$barcode = '
	<img src=imagens/p.png width='.$fino.' height='.$altura.' border=0><img 
	src=imagens/b.png width='.$fino.' height='.$altura.' border=0><img 
	src=imagens/p.png width='.$fino.' height='.$altura.' border=0><img 
	src=imagens/b.png width='.$fino.' height='.$altura.' border=0><img';

	$texto = $valor ;
	if((strlen($texto) % 2) <> 0){
		$texto = "0" . $texto;
	}

	// Draw dos dados
	while (strlen($texto) > 0) {
		$i = round(self::esquerda($texto,2));
		$texto = self::direita($texto,strlen($texto)-2);
		$f = $barcodes[$i];
		for($i=1;$i<11;$i+=2){
			if (substr($f,($i-1),1) == "0") {
				$f1 = $fino ;
			}else{
				$f1 = $largo ;
			}

			$barcode .= '
			src=imagens/p.png width='.$f1.' height='.$altura.' border=0><img';

			if (substr($f,$i,1) == "0") {
				$f2 = $fino ;
			}else{
				$f2 = $largo ;
			}

			$barcode .= '
			src=imagens/b.png width='.$f2.' height='.$altura.' border=0><img
			';
		}
	}
	
	// Draw guarda final

	$barcode .= '
	src=imagens/p.png width='.$largo.' height='.$altura.' border=0><img 
	src=imagens/b.png width='.$fino.' height='.$altura.' border=0><img 
	src=imagens/p.png width=1 height='.$altura.' border=0>
	';
	
	return $barcode;

	} //Fim da fun��o
	
	function esquerda($entra,$comp){
		return substr($entra,0,$comp);
	}
	
	function direita($entra,$comp){
		return substr($entra,strlen($entra)-$comp,$comp);
	}
	
	function fator_vencimento($data) {
		if ($data != "") {
			$data = explode("/",$data);
			$ano = $data[2];
			$mes = $data[1];
			$dia = $data[0];
			return(abs((self::_dateToDays("1997","10","07")) - (self::_dateToDays($ano, $mes, $dia))));
		} else {
			return "0000";
		}
	}
	
	function _dateToDays($year,$month,$day) {
			$century = substr($year, 0, 2);
			$year = substr($year, 2, 2);
			if ($month > 2) {
					$month -= 3;
			} else {
					$month += 9;
					if ($year) {
							$year--;
					} else {
							$year = 99;
							$century --;
					}
			}
	
			return ( floor((  146097 * $century)    /  4 ) +
							floor(( 1461 * $year)        /  4 ) +
							floor(( 153 * $month +  2) /  5 ) +
									$day +  1721119);
	}
	
	/*
	#################################################
	FUN��O DO M�DULO 10 RETIRADA DO PHPBOLETO
	
	ESTA FUN��O PEGA O D�GITO VERIFICADOR DO PRIMEIRO, SEGUNDO
	E TERCEIRO CAMPOS DA LINHA DIGIT�VEL
	#################################################
	*/
	function modulo_10($num) { 
		$numtotal10 = 0;
		$fator = 2;
	 
		for ($i = strlen($num); $i > 0; $i--) {
			$numeros[$i] = substr($num,$i-1,1);
			$parcial10[$i] = $numeros[$i] * $fator;
			$numtotal10 .= $parcial10[$i];
			if ($fator == 2) {
				$fator = 1;
			}
			else {
				$fator = 2; 
			}
		}
		
		$soma = 0;
		for ($i = strlen($numtotal10); $i > 0; $i--) {
			$numeros[$i] = substr($numtotal10,$i-1,1);
			$soma += $numeros[$i]; 
		}
		$resto = $soma % 10;
		$digito = 10 - $resto;
		if ($resto == 0) {
			$digito = 0;
		}
	
		return $digito;
	}
	
	/*
	#################################################
	FUN��O DO M�DULO 11 RETIRADA DO PHPBOLETO
	
	MODIFIQUEI ALGUMAS COISAS...
	
	ESTA FUN��O PEGA O D�GITO VERIFICADOR:
	
	NOSSONUMERO
	AGENCIA
	CONTA
	CAMPO 4 DA LINHA DIGIT�VEL
	#################################################
	*/

    //EU RETIREI ESTA FUNÇÃO DA GERAÇÃO DE BOLETOS PARA CAIXA ECONÔMICA POIS O DV ESTAVA SENDO GERADO COM X QUANDO ALTERAVA O CÓDIGO DA AGÊNCIA

    static function modulo_11($num, $base=9, $r=0)  {
        /**
         *   Autor:
         *           Pablo Costa <pablo@users.sourceforge.net>
         *
         *   Função:
         *    Calculo do Modulo 11 para geracao do digito verificador
         *    de boletos bancarios conforme documentos obtidos
         *    da Febraban - www.febraban.org.br
         *
         *   Entrada:
         *     $num: string numérica para a qual se deseja calcularo digito verificador;
         *     $base: valor maximo de multiplicacao [2-$base]
         *     $r: quando especificado um devolve somente o resto
         *
         *   Saída:
         *     Retorna o Digito verificador.
         *
         *   Observações:
         *     - Script desenvolvido sem nenhum reaproveitamento de código pré existente.
         *     - Assume-se que a verificação do formato das variáveis de entrada é feita antes da execução deste script.
         */

        $soma = 0;
        $fator = 2;

        /* Separacao dos numeros */
        for ($i = strlen($num); $i > 0; $i--) {
            // pega cada numero isoladamente
            $numeros[$i] = substr($num,$i-1,1);
            // Efetua multiplicacao do numero pelo falor
            $parcial[$i] = $numeros[$i] * $fator;
            // Soma dos digitos
            $soma += $parcial[$i];
            if ($fator == $base) {
                // restaura fator de multiplicacao para 2
                $fator = 1;
            }
            $fator++;
        }

        /* Calculo do modulo 11 */
        if ($r == 0) {
            $soma *= 10;
            $digito = $soma % 11;
            if ($digito == 10) {
                $digito = 0;
            }
            return $digito;
        } elseif ($r == 1){
            $resto = $soma % 11;
            return $resto;
        } elseif ($r == 2){ //Adicionei para o BB convênio de 6 dígitos e carteira 18 sem registro
            $resto = $soma % 11;
            $dv = 11 - $resto;
            if($dv == 0 || $dv == 10 || $dv == 11)
                return 1;
            else
                return $dv;
        }
    }
	
	/*
	Montagem da linha digit�vel - Fun��o tirada do PHPBoleto
	N�o mudei nada
	*/
	function monta_linha_digitavel($linha) {
			// Posi��o 	Conte�do
			// 1 a 3    N�mero do banco
			// 4        C�digo da Moeda - 9 para Real
			// 5        Digito verificador do C�digo de Barras
			// 6 a 19   Valor (12 inteiros e 2 decimais)
			// 20 a 44  Campo Livre definido por cada banco
	
			// 1. Campo - composto pelo c�digo do banco, c�digo da mo�da, as cinco primeiras posi��es
			// do campo livre e DV (modulo10) deste campo
			$p1 = substr($linha, 0, 4);
			$p2 = substr($linha, 19, 5);
			$p3 = self::modulo_10("$p1$p2");
			$p4 = "$p1$p2$p3";
			$p5 = substr($p4, 0, 5);
			$p6 = substr($p4, 5);
			$campo1 = "$p5.$p6";
	
			// 2. Campo - composto pelas posi�oes 6 a 15 do campo livre
			// e livre e DV (modulo10) deste campo
			$p1 = substr($linha, 24, 10);
			$p2 = self::modulo_10($p1);
			$p3 = "$p1$p2";
			$p4 = substr($p3, 0, 5);
			$p5 = substr($p3, 5);
			$campo2 = "$p4.$p5";
	
			// 3. Campo composto pelas posicoes 16 a 25 do campo livre
			// e livre e DV (modulo10) deste campo
			$p1 = substr($linha, 34, 10);
			$p2 = self::modulo_10($p1);
			$p3 = "$p1$p2";
			$p4 = substr($p3, 0, 5);
			$p5 = substr($p3, 5);
			$campo3 = "$p4.$p5";
	
			// 4. Campo - digito verificador do codigo de barras
			$campo4 = substr($linha, 4, 1);
	
			// 5. Campo composto pelo valor nominal pelo valor nominal do documento, sem
			// indicacao de zeros a esquerda e sem edicao (sem ponto e virgula). Quando se
			// tratar de valor zerado, a representacao deve ser 000 (tres zeros).
			$campo5 = substr($linha, 5, 14);
	
			return "$campo1 $campo2 $campo3 $campo4 $campo5"; 
	}
	
	function geraCodigoBanco($numero) {
			$parte1 = substr($numero, 0, 3);
			$parte2 = self::modulo_11($parte1);
			return $parte1 . "-" . $parte2;
	}

	function nossoNumeroAtualizar($db){
		
        //Essa função foi criada quando o nosso número era incluído no boleto apenas na download/visualização.
        //Não é mais necessária porque o nosso número agora é gerado no momento em que o boleto é criado, mas ainda é mantida para alguns casos em que o nosso número possa estar em branco.
        //O nosso número deve ser buscado no banco quando o boleto for montado para download/visualização.

        $boleto = $db->fetch_assoc('select nosso_numero from boletos where id = '.$this->dadosboleto['boleto_id']);
        
        if($boleto['nosso_numero']==''){
            $db->query_update('boletos',array('nosso_numero'=>$this->dadosboleto['nosso_numero']),'id = '.$this->dadosboleto['boleto_id']);
        }else{
            $this->dadosboleto['nosso_numero'] = $boleto['nosso_numero'];
        }
	}

	function boletoImprimir(){

		$dadosboleto = $this->dadosboleto;
		
		$cpf_cnpj = isset($dadosboleto["cpf_cnpj"]) ? $dadosboleto["cpf_cnpj"] : '';
		$variacao_carteira = isset($dadosboleto["variacao_carteira"]) ? $dadosboleto["variacao_carteira"] : '&nbsp;';
        
         $mensagemMj = '&nbsp; Após o vencimento cobrar: <br>'; 
        
         $vl_multa_result = (str_replace(',', '.', $dadosboleto["multa"]) * $dadosboleto["valor_lancamento"]) / 100;
         $vl_juros_result = (str_replace(',', '.', $dadosboleto["juros"]) * $dadosboleto["valor_lancamento"]) / 100;
         
         $mensagemMj = 'Após o vencimento cobrar '; 
         $mensagemMulta = ' multa de R$ '.number_format($vl_multa_result, 2, ',', '').' ('.$dadosboleto["multa"].'%) e ';
         $mensagemJuros = ' juros de R$ '.number_format($vl_juros_result, 2, ',', '').' ('.$dadosboleto["juros"].'%) ao mês. <br />';

         $dadosboleto["valor_boleto"] = number_format(str_replace(',', '.', $dadosboleto["valor_boleto"]), 2, ',', '.');
         
         //Logo Cliente
         $caminhoLogo = '../../'.$dadosboleto['logo_cliente'];
         if(!empty($dadosboleto['logo_cliente'])){ $logo = '<div align="right"><img src='.$caminhoLogo.' width="150"/></div>'; }
         

         //Nosso número Caixa 
         if($dadosboleto['codigo_banco']=='104'){        
            $nosso_numero_ini = substr($dadosboleto['nosso_numero'], 0, 2);
            $nosso_numero_meio = substr($dadosboleto['nosso_numero'], 2, -1);
            $dv = substr($dadosboleto['nosso_numero'], -1);
            $dadosboleto['nosso_numero'] = $nosso_numero_ini.'/'.$nosso_numero_meio.'-'.$dv;

            $local_pagamento = 'QUALQUER BANCO ATÉ O VENCIMENTO<br>PREFERENCIALMENTE NAS CASAS LOTÉRICAS ATÉ O VALOR LIMITE.';
         }else{
            //Nosso número outros bancos
            $nosso_numero_ini = substr($dadosboleto['nosso_numero'], 0, -1);
            $dv = substr($dadosboleto['nosso_numero'], -1);
            $dadosboleto['nosso_numero'] = $nosso_numero_ini.'-'.$dv;

            $local_pagamento = 'QUALQUER BANCO ATÉ O VENCIMENTO.';
         }

         //Instrução do boleto
        $instrucoes = $mensagemMj.$mensagemMulta.$mensagemJuros.'<br>'.$dadosboleto['observacao'];
		(!empty($dadosboleto["demonstrativo1"])) ? $instrucoes .= $dadosboleto["demonstrativo1"].'<br />' : '';		
		(!empty($dadosboleto["demonstrativo2"])) ? $instrucoes .= $dadosboleto["demonstrativo2"].'<br />' : '';
		(!empty($dadosboleto["demonstrativo3"])) ? $instrucoes .= $dadosboleto["demonstrativo3"].'<br />' : '';
        (!empty($dadosboleto["instrucoes1"])) ? $instrucoes .= $dadosboleto["instrucoes1"].'<br />' : '';
        (!empty($dadosboleto["instrucoes2"])) ? $instrucoes .= $dadosboleto["instrucoes2"].'<br />' : '';
        (!empty($dadosboleto["instrucoes3"])) ? $instrucoes .= $dadosboleto["instrucoes3"].'<br />' : '';
        (!empty($dadosboleto["instrucoes4"])) ? $instrucoes .= $dadosboleto["instrucoes4"] : '';
        (!empty($dadosboleto["BancoMsg"])) ? $BancoMsg ='<div class="titulosInstrucoes">'.$dadosboleto["BancoMsg"].'</div><br />' : '';

		$html = '	
			<div id="boleto">
            
            '.$logo.'
		
				<div class="cut"> <p>Corte na linha pontilhada</p> </div>
				<div class="cp"> Recibo do Pagador </div>
				
				<table class="linhaTitulo" cellpadding="0" cellspacing="0">
					<tr>
						<td align="left"> <img src="logo_boleto/'.$dadosboleto['logo_boleto'].'" width="100"></td> 
						<td width="60" align="center"><div class="field_cod_banco">&nbsp;'.$dadosboleto["codigo_banco_com_dv"].'&nbsp;</div></td>
						<td class="linha_digitavel">'.$dadosboleto["linha_digitavel"].'</td>
						</tr>
					</tr>
				</table>
				
				<table class="linha" cellspacing="0" cellpadding="0">
						<tbody>
							<tr> <td height="10" colspan="5"></td></tr>
						<tr class="titulos">
							<td class="cedente">Beneficiário</td>
							<td class="ag_cod_cedente">Ag. / Cod. do Benef.</td>
							<td class="especie">Moeda</td>
							<td class="qtd">Qtd. moeda</td>
							<td class="nosso_numero">Nosso n&uacute;mero</td>
						</tr>
				
						<tr class="campos">
							<td class="cedente" width="334">
                            '.$dadosboleto["cedente"].' - '.$dadosboleto["cpf_cnpj"].' - 
                            '.$dadosboleto["endereco"].'                       
                            </td>
							<td class="ag_cod_cedente" width="90" align="right">'.$dadosboleto["agencia_codigo"].'</td>
							<td class="especie" width="25" align="center">'.$dadosboleto["especie"].'	</td>
							<td class="qtd" width="47" align="center">'.$dadosboleto["quantidade"].'</td>
							<td class="nosso_numero" width="119" align="right">'.$dadosboleto["nosso_numero"].'</td>
						</tr>
						</tbody>
						</table>
						
						<table class="linha" cellspacing="0" cellpadding="0" >
						<tbody>
							<tr><td colspan="5"></td></tr>
						<tr class="titulos">
							<td class="num_doc">N&uacute;mero do documento</td>
							<td class="contrato">Contrato</TD>
							<td class="cpf_cei_cnpj">CPF/CEI/CNPJ</TD>
							<td class="vencimento">Vencimento</TD>
							<td class="valor_doc">Valor documento</TD>
						</tr>
				
						<tr class="campos">
							<td class="num_doc" width="118">'.$dadosboleto["numero_documento"].'</td>
							<td class="contrato" width="86">'.$dadosboleto["contrato"].'</td>
							<td class="cpf_cei_cnpj" width="132">'.$dadosboleto["cpf_cnpj"].'</td>
							<td class="vencimento" width="134" align="center">'.$dadosboleto["data_vencimento"].'	</td>
							<td class="valor_doc" width="180" align="right">'.$dadosboleto["valor_boleto"].'
							</td>
						</tr>
						</tbody>
						</table>

						
						
                        <table class="linha" cellspacing="0" cellpadding="0">
						<tbody>
							<tr> <td colspan="5"></td></tr>
						<tr class="titulos">
							<td class="desconto">(-) Desconto </td>
							<td class="outras_deducoes">(-) Outras dedu&ccedil;&otilde;es / Abatimentos</td>
							<td class="mora_multa">(+) Mora / Multa / Juros</td>
							<td class="outros_acrescimos">(+) Outros acr&eacute;scimos</td>
							<td class="valor_cobrado">(=) Valor cobrado</td>
						</tr>
				
						<tr class="campos">
							<td class="desconto" width="110"></td>
							<td class="outras_deducoes" width="118"></td>
							<td class="mora_multa" width="113"></td>
							<td class="outros_acrescimos" width="113"></td>
							<td class="valor_cobrado" width="181"></td>
						</tr>
						</tbody>
						</table>
						
						<table class="linha" cellspacing="0" cellpadding="0">
						<tbody>
							<tr> <td ></td>< /tr>
						<tr class="titulos">
							<td class="sacado">Pagador</td>
						</tr>
						
						<tr class="campos">
							<td class="sacado" width="659">
								'.$dadosboleto["sacado"].' - 
								'.$dadosboleto["endereco1"].' - 
								'.$dadosboleto["endereco2"].'
							</td>
						</tr>
						</tbody>
						</table>
						
						<div class="footer">
							<p>Autenticação mecânica</p>
						</div>			
						
                        '.$BancoMsg.'

						<div class="cut">
							<p>Corte na linha pontilhada</p>
						</div>
						
					<br />	
						
					<table class="linhaTitulo" cellpadding="0" cellspacing="0">
					<tr>
						<td align="left"> <img src="logo_boleto/'.$dadosboleto['logo_boleto'].'" width="100"></td>
						<td width="60" align="center"><div class="field_cod_banco">&nbsp;'.$dadosboleto["codigo_banco_com_dv"].'&nbsp;</div></td>
						<td class="linha_digitavel">'.$dadosboleto["linha_digitavel"].'</td>
						</tr>
				</table>
				
				<table class="linha" cellspacing="0" cellpadding="0">
				<tbody>
						<tr> <td height="10" colspan="2"></td>< /tr>
				<tr class="titulos">
					<td class="local_pagto">Local de pagamento</td>
					<td class="vencimento2">Vencimento</td>
				</tr>
				<tr class="campos">
					<td class="local_pagto" width="472">
						'.$local_pagamento.'
					</td>
					<td class="vencimento2" width="186" align="right">'.$dadosboleto["data_vencimento"].'	</td>
				</tr>
				</tbody>
				</table>
				
				<table class="linha" cellspacing="0" cellpadding="0">
				<tbody>
						<tr> <td colspan="2"></td>< /tr>
				<tr class="titulos">
					<td class="cedente2">Beneficiário</td>
					<td class="ag_cod_cedente2">Ag&ecirc;ncia/C&oacute;digo beneficiário</td>

				</tr>
				<tr class="campos">
					<td class="cedente2" width="478">
                        '.$dadosboleto["cedente"].' - '.$dadosboleto["cpf_cnpj"].' - 
                        '.$dadosboleto["endereco"].' 
                    </td>
					<td class="ag_cod_cedente2" width="188" align="right">'.$dadosboleto["agencia_codigo"].'</td>
				</tr>
				</tbody>
				</table>
		
				<table class="linha" cellspacing="0" cellpadding="0">
				<tbody>
						<tr> <td colspan="6"></td>< /tr>	
				<tr class="titulos">
							<td class="data_doc">Data do documento</td>
							<td class="num_doc2">No. documento</td>
							<td class="especie_doc">Esp&eacute;cie doc.</td>
							<td class="aceite">Aceite</td>
							<td class="data_process">Data process.</td>
							<td class="nosso_numero2">Nosso n&uacute;mero</td>
				</tr>
				
				<tr class="campos">
					<td class="data_doc" width="95">'.$dadosboleto["data_documento"].'</td>
					<td class="num_doc2" width="181">'.$dadosboleto["numero_documento"].'</td>
					<td class="especie_doc" width="72" align="center">'.$dadosboleto["especie_doc"].'</td>
					<td class="aceite" width="38" align="center">'.$dadosboleto["aceite"].'</td>
					<td class="data_process" width="74">'.$dadosboleto["data_processamento"].'</td>
					<td class="nosso_numero2" width="180" align="right">'.$dadosboleto["nosso_numero"].'</td>
				</tr>	
				</tbody>
				</table>	
				
				<table class="linha" cellspacing="0" cellpadding="0">
				<tbody>
				<tr> <td colspan="6"></td> </tr>
				<tr class="titulos">
							<td class="reservado">Uso do  banco</td>
							<td class="carteira">Carteira</td>
							<td class="especie2">Esp&eacute;cie moeda</td>
							<td class="qtd2">Qtde moeda</td>
							<td class="xvalor">x Valor</td>
							<td class="valor_doc2">(=) Valor documento</td>
					/tr>
				<tr class="campos">
					<td class="reservado" width="92">	</td>
					<td class="carteira" width="73" align="center">'.$dadosboleto["carteira"].$variacao_carteira.' </td>
					<td class="especie2" width="73" align="center"> '.$dadosboleto["especie"].' </td>
					<td class="qtd2" width="130" align="center">'.$dadosboleto["quantidade"].'	</td>
					<td class="xvalor" width="71"  align="center">'.$dadosboleto["valor_unitario"].'	</td>
					<td class="valor_doc2" width="184" align="right">'.$dadosboleto["valor_boleto"].' </td>
				</tr>
				</tbody>
				</table>		
					
				<table class="linha" cellspacing="0" cellpadding="0">
				<tbody>
				<tr>
					<td colspan="2" height="0"></td>
				</tr>
		
				<tr class="campos">
					<td class="instrucoes" rowspan="4" valign="top" width="475">
						<div class="titulosInstrucoes">&nbsp;Instrucoes (Texto de responsabilidade do beneficiário)</div>
						<div class="instrucoes">
							<br>
                                    '.$instrucoes.'
                                    
						</div>
					</td>
					<td valign="top">
											
								<table class="linha" cellspacing="0" cellpadding="0">
								<tbody>
								<tr class="titulos">
										<td class="desconto2">(-) Desconto</td>
								</tr>
								<tr class="campos">
									<td class="desconto2" width="175" align="right"></td>				
								</tr>
								</tbody>
								</table>
					
								<table class="linha" cellspacing="0" cellpadding="0"  valign="top">
								<tbody>
								<tr>
									<td height="0"></td>
								</tr>
								<tr class="titulos">
									<td class="outras_deducoes2">(-) Outras dedu&ccedil;&otilde;es / Abatimentos</td>
								</tr>
								<tr class="campos">
									<td class="outras_deducoes2" width="175" align="right"></td>				
								</tr>
								</tbody>
								</table>	
								
								
								<table class="linha" cellspacing="0" cellpadding="0"  valign="top">
								<tbody>
								<tr>
									<td height="0"></td>
								</tr>
								<tr class="titulos">
									<td class="mora_multa2">(+) Mora / Multa / Juros</td>
								</tr>
								<tr class="campos">
									<td class="mora_multa2" width="175" align="right"></td>				
								</tr>
								</tbody>
								</table>	
								
								<table class="linha" cellspacing="0" cellpadding="0"  valign="top">
								<tbody>
								<tr>
									<td height="0"></td>
								</tr>
								<tr class="titulos">
									<td class="outros_acrescimos2">(+) Outros Acr&eacute;scimos</td>
								</tr>
								<tr class="campos">
									<td class="outros_acrescimos2" width="175" align="right"></td>				
								</tr>
								</tbody>
								</table>					
								
								
								<table align="right" cellspacing="0" cellpadding="0"  valign="top">
								<tbody>
								<tr>
									<td height="0"></td>
								</tr>
								<tr class="titulos">
									<td class="valor_cobrado2">(=) Valor cobrado</td>
								</tr>
								<tr class="campos">
									<td class="valor_cobrado2" width="187" align="right"></td>				
								</tr>
								</tbody>
								</table>		
					</td>
					
				</tr>
				</tbody>
				</table>	
			
				<table class="linha" cellspacing="0" cellpadding="0"  valign="top">
				<tbody>
				<tr>
					<td height="0"></td>
				</tr>
				<tr class="titulos">
					<td class="sacado2">Pagador</td>
				</tr>
				<tr class="campos">
					<td class="sacado2" width="659">
								'.$dadosboleto["sacado"].' - 
								'.$dadosboleto["endereco1"].' - 
								'.$dadosboleto["endereco2"].'
					</td>				
				</tr>
				</tbody>
				</table>	
			
				<table class="linha" cellspacing="0" cellpadding="0"  valign="top">
				<tbody>
				<tr>
					<td height="0"></td>
				</tr>
				<tr class="titulos">
					<td class="sacador_avalista" colspan="2">Pagador/Avalista</td>
				</tr>
				<tr class="campos">
					<td class="sacador_avalista" width="474"></td>	
					<td class="cod_baixa"  width="188">C&oacute;d. baixa</td>			
				</tr>
				</tbody>
				</table>			
				
				<table cellspacing=0 cellpadding=0 width=666 ><tbody><tr><td width=666 align=right ><font style="font-family: Arial Narrow; font-size: 10px;">Autenticação mecânica - Ficha de Compensação</font></td></tr></tbody></table>
				<div class="barcode">
					<p>'.self::fbarcode($dadosboleto["codigo_barras"]).'</p>
				</div>
				
				<div class="cut"> <p>Corte na linha pontilhada</p> </div>		
				
			</div>				
		'; 

			//Cabeçalho		
/*			$pdfHeader = '
				<table width="100%" class="rodape"><tr>
					<td><img src="../../../images/logo_Fatura_Expressa_fundo_branco.png"></td>
					<td align="right"><img src="../../../images/logo_claro.png"></td>
				</tr></table>'; */
			//Rodapé
			$pdfFooter = '
				<table width="100%" align="center" class="rodape">
					<tr>
						<td align="center">Boleto gerado pelo sistema <a href="https://www.webfinancas.com" target="_blank">webfinancas.com</a></td>
					</tr>
				</table>
			';

		//==============================================================
		//==============================================================
		//==============================================================
		
		$mpdf=new mPDF('pt_BR','A4','','',10,10,10,18,5,8); //cria um novo container PDF no formato A4 com orientação customizada ex.:class mPDF ([ string $mode [, mixed $format [, float $default_font_size [, string $default_font [, float $margin_left , float $margin_right , float $margin_top , float $margin_bottom , float $margin_header , float $margin_footer [, string $orientation ]]]]]]) 
		//$mpdf->SetDisplayMode('fullpage');
		$mpdf->useSubstitutions=false;
		$mpdf->simpleTables = false;
		// LOAD a stylesheet
		//$mpdf->SetHTMLHeader($pdfHeader);
		$mpdf->SetHTMLFooter($pdfFooter);
		$stylesheet = file_get_contents('style_boleto_pdf.css');
		$mpdf->WriteHTML($stylesheet,1);	// The parameter 1 tells that this is css/style only and no body/html/text
		$mpdf->WriteHTML($html,2);
		//$mpdf->Output('pdf/teste.pdf','F');
		$mpdf->Output('Boleto_'.$dadosboleto["data_vencimento"].'.pdf', 'D'); //fazer download
		//$mpdf->Output(); //imprimir na tela
		exit;
		//==============================================================
		//==============================================================
		//==============================================================

	}
}
?>