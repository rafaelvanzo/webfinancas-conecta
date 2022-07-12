<?php

/**
 * RemessaBb short summary.
 *
 * RemessaBb description.
 *
 * @version 1.0
 * @author Fabio
 */
class RemessaBb extends Remessa
{
    function __construct(){
    }

    function ArquivoRemessa($conta_financeira,$remessa_id,$totali,$dados_boleto){

        $banco_carteira = $conta_financeira['carteira'];

        if(!empty($conta_financeira['msg1'])){
            $msg_banco = $conta_financeira['msg1']; 
        }else if(!empty($conta_financeira['msg2'])){
            $msg_banco = $conta_financeira['msg2']; 
        }else{ $msg_banco = $conta_financeira['msg3']; }
        
        $dados_banco = array(
            'codigo'=>$conta_financeira['codigo_banco'],
            'nome'=>$conta_financeira['nome_banco']
            );

        $tp_inscricao = $conta_financeira['inscricao'] == "CPF" ? '1' : '2';

        //========== HEADER DO ARQUIVO ===========//
        $banco = substr($dados_banco['codigo'], 0, 3);                                                      // 3
        $loteHA = "0000";                                                                                   // 4
        $tp_inscricao;                                                                                      // 1
        $numero_inscricao = substr(self::limpaCPF_CNPJ($conta_financeira['cpf_cnpj']), 0, 14);              // 14
        $agencia_sem_dv = substr($conta_financeira['agencia'], 0, 5);                                       // 5
        $agencia_dv = $conta_financeira['agencia_dv'];                                                      // 1 * colocar campo dv no modulo conta financeira
        $convenio = $conta_financeira['convenio'];                                                          // Convênio
        $conta_sem_dv = $conta_financeira['numero'];                                                        // número da conta financeira sem dv
        $conta_dv = $conta_financeira['numero_dv'];                                                         // dv da conta financeira
        $nome_beneficiario = substr($conta_financeira['nomeTitular'], 0, 30);                               // 30
        $nome_banco = substr($dados_banco['nome'], 0, 30);                                                  // 30
        $dt_geracao_arq = substr(date('dmY'), 0, 8);                                                        // 8
        $hr_geracao_arq = substr(date('Hms'), 0, 6);                                                        // 6
        $numero_seq_arq = str_pad($remessa_id, 6 , "0", STR_PAD_LEFT );                                     // 6 *

        //========== HEADER DO LOTE ===========//
        $loteHL = "0001";			                                                                        // 4 add + 1 a cada lote       
        $mensagem01 = substr($msg_banco, 0, 40);                                                            // 40
        
        $n_remessa = str_pad( $remessa_id, 6 , "0", STR_PAD_LEFT );                                         // Constroi o número da remessa
        $numero_remessa = substr(date('y').$n_remessa, 0, 8);         		                                // 8
        
        $dt_gravacao_remessa = substr(date('dmY'), 0, 8);                                                   // 8                
        
        //=========================================
        $nomeArquivoTXT	= 'arquivo_remessa_'.$numero_remessa.'.txt'; //nome do arquivo TXT
        $arquivoTXT = '../../boleto/txt/'.$nomeArquivoTXT; //Diretório do arquivo TXT -> 'txt/'.
        $fp = fopen($arquivoTXT,"a+"); // Cria o arquivo TXT
        
        //========== HEADER DO ARQUIVO ===========//

        
        //Espaçamento dentro do arquivo TXT
        
        $h1 = str_pad( $banco, 3 , "0", STR_PAD_LEFT );          //Código do BB: "001"
        
        $h2 = str_pad( $loteHA, 4 );         //Lote de Serviço: "0000"
        
        $h3 = str_pad( '0', 1 );            //Tipo de Registro: "0"
        
        $h4 = str_pad( '', 9, ' ' );    //Uso Exclusivo FEBRABAN / CNAB: Brancos 
        
        $h5 = str_pad( $tp_inscricao, 1 );            //Tipo de Inscrição da Empresa: '1'  =  CPF | '2'  =  CGC / CNPJ" 
        
        $h6 = str_pad( $numero_inscricao, 14, "0", STR_PAD_LEFT );    //Número de Inscrição da Empresa CPF ou CNPJ
        
        $h7 = str_pad( $convenio, 9, '0', STR_PAD_LEFT );    //Código do Convênio com o banco - 33 ao 41 9

        $h7 .= '0014'; //Cobrança cedente BB - 42 ao 45

        $h7 .= str_pad( $banco_carteira, 2, "0", STR_PAD_LEFT ); //Carteira - 46 ao 47

        $h7 .= str_pad( $conta_financeira['variacao'], 3, "0", STR_PAD_LEFT ); //Variação - 48 ao 50 3

        $h7 .= str_pad( ' ', 2 );   //Uso Exclusivo BB - 51 ao 52

        $h8 = str_pad( $agencia_sem_dv, 5 , "0", STR_PAD_LEFT );   // número da agência sem dv - 53 ao 57 5
        
        $h9 = str_pad( strtoupper($agencia_dv), 1 );   // dv da agência - 58 58 1 Obs.: Em caso de dígito X informar maiúsculo.
        
        $h10 = str_pad( $conta_sem_dv, 12 , "0", STR_PAD_LEFT );  // Número da conta sem dv 59 70 12
        
        $h11 = str_pad( strtoupper($conta_dv), 1 );   // dv do número da conta 71 71 1 Obs.: Em caso de dígito X informar maiúsculo.
        
        $h12 = str_pad( '0', 1 );   // 72 72 1 Obs.: Campo não tratado pelo Banco do Brasil. Informar 'branco' (espaço) OU zero.
        
        $h13 = str_pad( utf8_decode ( $nome_beneficiario ), 30 );  //* Nome do beneficiário 73 102 30
        
        $h14 = str_pad( $nome_banco, 30 );  //Nome do Banco: 103 132 30
        
        $h15 = str_pad( '          ', 10 );   //Uso Exclusivo FEBRABAN / CNAB: Brancos 133 142 10
        
        $h16 = str_pad( '1', 1 );   //Código Remessa / Retorno: "1" 143 143 1
        
        $h17 = str_pad( $dt_geracao_arq, 8 ); //* Data de Geração do Arquivo 144 151 8
        
        $h18 = str_pad( $hr_geracao_arq, 6 );   //* Hora de Geração do Arquivo 152 157 6
        
        $h19 = str_pad( $numero_seq_arq, 6 );   //* 158 163 6 Número Seqüencial do Arquivo: Número seqüencial adotado e controlado pelo responsável pela geração do arquivo para ordenar a disposição dos arquivos encaminhados. Evoluir um número seqüencial a cada header de arquivo.
        
        $h20 = str_pad( '083', 3 );   //No da Versão do Layout do Arquivo: "083" 164 166 3
        
        $h21 = str_pad( '', 5, '0' );    //Densidade de Gravação do Arquivo: "0" 167 171 5
        
        $h22 = str_pad( '', 20, ' ' );   //Para Uso Reservado do Banco: Brancos 172 191 20
        
        $h23 = str_pad( '', 20, ' ' );   //Para Uso Reservado da Empresa: 192 211 20
        
        $h24 = str_pad( '', 29, ' ' );   //Uso exclusivo do banco. 212 240 29

        $dados_arquivoTXT01 = $h1.$h2.$h3.$h4.$h5.$h6.$h7.$h8.$h9.$h10.$h11.$h12.$h13.$h14.$h15.$h16.$h17.$h18.$h19.$h20.$h21.$h22.$h23.$h24;

        fwrite($fp,$dados_arquivoTXT01."\r\n"); //."\r\n" - Adiciona as linhas dentro do arquivo TXT
        //========== Fim HEADER DO ARQUIVO ===========//  
        
        //========== HEADER DO LOTE ===========//          
        
        //Espaçamento dentro do arquivo TXT
        
        $hl1 = str_pad( $banco, 3 , "0", STR_PAD_LEFT );   //Código do CEF na Compensação: "104"
        
        $hl2 = str_pad( $loteHL, 4, "0", STR_PAD_LEFT  );   //* Lote de Serviço: Número seqüencial para identificar univocamente um lote de serviço. Criado e controlado pelo responsável pela geração magnética dos dados contidos no arquivo. Preencher com '0001' para o primeiro lote do arquivo. Para os demais: número do lote anterior acrescido de 1. O número não poderá ser repetido dentro do arquivo."

        $hl3 = str_pad( '1', 1 );   //Tipo de Registro: "1"
        
        $hl4 = str_pad( 'R', 1 );    //Tipo de Operação: "R"
        
        $hl5 = str_pad( '01', 2 );   //Tipo de Serviço: "01"
        
        $hl6 = str_pad( ' ', 2 );    //Uso Exclusivo FEBRABAN/CNAB: Brancos
        
        $hl7 = str_pad( '000', 3 );   //Nº da Versão do Layout do Lote - 14 16 3 Obs.: Campo não criticado pelo sistema. Informar Zeros OU se preferir, informar número da versão do leiaute do Lote que foi utilizado como base para formatação dos campos.

        $hl8 = str_pad( ' ', 1 );   //Uso Exclusivo FEBRABAN/CNAB: Brancos
        
        $hl9 = str_pad( $tp_inscricao, 1 );   //* Tipo de Inscrição da Empresa: '1'  =  CPF | '2'  =  CGC / CNPJ"
        
        $hl10 = str_pad( $numero_inscricao, 15, "0", STR_PAD_LEFT );  //* Nº de Inscrição da Empresa
        
        $hl11 = str_pad( $convenio, 9, '0', STR_PAD_LEFT ); //Código do Convênio com o banco - 34 ao 42 9

        $hl11 .= '0014'; //Cobrança cedente BB - 43 ao 46 4

        $hl11 .= str_pad( $banco_carteira, 2, "0", STR_PAD_LEFT ); //Carteira - 47 ao 48 2

        $hl11 .= str_pad( $conta_financeira['variacao'], 3, "0", STR_PAD_LEFT ); //Variação - 49 ao 51 3

        $hl11 .= str_pad( '', 2 ); //Uso Exclusivo BB - 52 ao 53 2 Obs.: Informar TS para testes ou brancos para produção.

        $hl12 = str_pad( $agencia_sem_dv, 5 , "0", STR_PAD_LEFT );  //Número da agência sem dv - 54 58 5
        
        $hl13 = str_pad( strtoupper($agencia_dv), 1 ); //Digito verificador da agência - 59 59 1 Obs.: Em caso de dígito X informar maiúsculo.
        
        $hl14 = str_pad( $conta_sem_dv, 12 , "0", STR_PAD_LEFT ); //Número da conta sem dv - 60 71 12
        
        $hl15 = str_pad( strtoupper($conta_dv), 1 );  //dv do número da conta 72 72 1 Obs.: Em caso de dígito X informar maiúsculo.
        
        $hl16 = str_pad( '', 1, '0' ); //Brancos ou zeros - 73 73 1
        
        $hl17 = str_pad( utf8_decode ( $nome_beneficiario ), 30 );   //Nome da Empresa - 74 103 30
        
        $hl18 = str_pad( utf8_decode ( $mensagem01 ), 40 );   // 104 143 40 Mensagem 1: Texto referente a mensagens que serão impressas em todos os boletos referentes ao mesmo lote. Estes campos não serão utilizados no arquivo retorno.

        $hl19 = str_pad( '', 40 ); // 144 183 40 Mensagem 2: Texto referente a mensagens que serão impressas em todos os boletos referentes ao mesmo lote. Estes campos não serão utilizados no arquivo retorno.
        
        $hl20 = $numero_remessa;   // Número Remessa/Retorno - 184 191 8
        
        $hl21 = str_pad( date('dmY'), 8 );   // Data de Gravação Remessa/Retorno - 192 199 8
        
        $hl22 = str_pad( '', 8 );   // Data do Crédito - 200 207 8 Obs.: Campo não tratado pelo Banco do Brasil. Informar 'brancos' (espaços) OU Zeros.
        
        $hl23 = str_pad( '', 33 );   // Informar 'brancos' (espaços). - 208 240 33
        
        $dados_arquivoTXT02 = $hl1.$hl2.$hl3.$hl4.$hl5.$hl6.$hl7.$hl8.$hl9.$hl10.$hl11.$hl12.$hl13.$hl14.$hl15.$hl16.$hl17.$hl18.$hl19.$hl20.$hl21.$hl22.$hl23;
        
        fwrite($fp,$dados_arquivoTXT02."\r\n"); //."\r\n" - Adiciona as linhas dentro do arquivo TXT

        //========== Fim HEADER DO ARQUIVO ===========//  

        $i = 1;
		$vl_total_boletos = 0;	


        while($i <= $totali){
            
            $c+=1;                                                                               //Escreve a sequencia das linhas P, Q, R
            //========== REGISTRO DETALHE SEGMENTO P ===========//
            
            $loteDP = substr($c, 0, 5); 			                                        // 5 add + 1 a cada lote  

            $nosso_numero = $dados_boleto['nosso_numero'.$i];  	                            // Convênio de 4 dígitos: nº convênio + número sequencial do boleto com 7 posições + DV do nosso número; Convênio de 6 dígitos: nº convênio + número sequencial do boleto com 5 posições + DV do nosso número; Convênio de 7 dígitos: nº convênio + número sequencial do boleto com 10 posições, sem o DV do nosso número;
            $identificacao_titulo_banco = str_pad( $nosso_numero, 20 );                     // Todos os "nosso número" devem ser alinhados à esquerda com brancos à direita.

            $codigo_carteira = $banco_carteira;			                                    // 1
            
            $dt_venc = explode('-', $dados_boleto['dt_vencimento'.$i]);
            $dt_vencimento = substr($dt_venc[2].$dt_venc[1].$dt_venc[0], 0, 8);		        // 8
            
            $vl_b = str_replace(".", "", $dados_boleto['valor'.$i]);
            $vl_boleto = substr($vl_b, 0, 15);		                                        // 15
            
            $aceito = "A";				                                                    // 1 A = aceita e N = não aceita *
            
            $dt_emis = explode('-', $dados_boleto['dt_emissao'.$i]);
            $dt_emissao =  substr($dt_emis[2].$dt_emis[1].$dt_emis[0], 0, 8);		        // 8
            
            $cd_juros = "2";			                                                    // 1 - '0'  =  Isento, '1'  =  Valor por Dia, '2'  =  Taxa Mensal 
            
            $dt_j_somar = date('dmY', strtotime("+1 days",strtotime($dados_boleto['dt_vencimento'.$i])));
            //$dt_j = explode('-', $dt_j_somar);
            $dt_juros = substr($dt_j_somar, 0, 8);			                // 8  *
            
            $tx_j = str_replace(".", "", $conta_financeira['juros']);	
            $tx_juros = substr( $tx_j, 0, 15);		                                        // 15 *
            
            $protesto = "3";			                                                    // 1 - Protestar; 3 - Não protestar; 9 - Cancelamento protesto automático
            $numero_dias_protesto = "00";		                                            // 2 
            
            
            //Espaçamento dentro do arquivo TXT
            
            $hsP1 = str_pad( $banco, 3 );          //Código do BB: "001"
            
            $hsP2 = str_pad( $loteHL, 4 );         //* Lote de Serviço: Número seqüencial para identificar univocamente um lote de serviço. Criado e controlado pelo responsável pela geração magnética dos dados contidos no arquivo. Preencher com '0001' para o primeiro lote do arquivo. Para os demais: número do lote anterior acrescido de 1. O número não poderá ser repetido dentro do arquivo."

            $hsP3 = str_pad( '3', 1 );            //Tipo de Registro: "3"
            
            $hsP4 = str_pad( $loteDP, 5 , "0", STR_PAD_LEFT );            //* Nº Sequencial do Registro no Lote: Número adotado e controlado pelo responsável pela geração magnética dos dados contidos no arquivo, para identificar a seqüência de registros encaminhados no lote. Deve ser inicializado sempre em '1', em cada novo lote.
            
            $hsP5 = str_pad( 'P', 1 );           //Cód. Segmento do Registro Detalhe: "P"
            
            $hsP6 = str_pad( '', 1);             //Uso Exclusivo FEBRABAN/CNAB: Brancos
            
            $hsP7 = str_pad( '01', 2 );          //Código de Movimento Remessa:'01'  =  Entrada de Títulos        

            $hsP8 = str_pad( $agencia_sem_dv, 5, "0", STR_PAD_LEFT );         //* Prefixo da Cooperativa: vide e-mail enviado com os dados do processo de homologação AGÊNCIA
            
            $hsP9 = str_pad( strtoupper($agencia_dv), 1 );        // Dígito Verificador da Agência - 23 23 1 Obs.: Em caso de dígito X informar maiúsculo.
            
            $hsP10 = str_pad( $conta_sem_dv, 12 , "0", STR_PAD_LEFT );  //* Convênio 24 35 12
            
            $hsP11 = str_pad( strtoupper($conta_dv), 1 ); //Digito verificador da conta - 36 36 1 Obs.: Em caso de dígito X informar maiúsculo.
            
            $hsP12 = ' ';  // Dígito Verificador da Ag/Conta 37 37 1 Obs.: Campo não tratado pelo Banco do Brasil. Informar 'branco' (espaço) OU zero.
            
            $hsP13 = $identificacao_titulo_banco;         //43 57 15 ( 1500415 0 01 01 6 = Nosso número (1500415) + digito verificador (0) + parcela (01) + Modalidade (01) + Tipo Formulário (6) 5 digitos em branco)Nosso Número + Dv: 10 posições. Preencher conforme boleto, completando com "zeros" à esquerda; * Parcela: 02 posições; * Modalidade: 02 posições; * Tipo Formulário - 01 posição:          "1" -auto-copiativo            "3"-auto-envelopável          "4"-A4 sem envelopamento          "6"-A4 sem envelopamento 3 vias * Em branco: 05.
            
            $hsP14 = str_pad( '1', 1 );     //* Código da Carteira: vide e-mail enviado com os dados do processo de homologação 58 58 1
            
            $hsP15 = str_pad( '1', 1 );     //Forma de Cadastr. do Título no Banco: "1" 59 59 1
            
            $hsP16 = str_pad( '2', 1 );     //Tipo de Documento: "2" 60 60 1

            $hsP17 = str_pad( '2', 1 );     //61 61 1 Identificação da Emissão do Boleto: (videe-mail enviado com os dados do processo de homologação) '1'  =  CEF Emite | '2'  =  Beneficiário Emite"

            $hsP18 = str_pad( '0', 1 );     //62 62 1 Identificação da Distribuição do Boleto: (vide e-mail enviado com os dados do processo de homologação) '1'  =  CEF Distribui | '0'  =  Beneficiário Distribui"
            
            $hsP19 = str_pad( '', 15 ); //63 73 11 Número do Documento de Cobrança: Número adotado e controlado pelo Cliente, para identificar o título de cobrança. Informação utilizada pelo CEF para referenciar a identificação do documento objeto de cobrança. Poderá conter número de duplicata, no caso de cobrança de duplicatas; número da apólice, no caso de cobrança de seguros, etc
            
            $hsP20 = str_pad( $dt_vencimento, 8 );       //* Data de Vencimento do Título 78 85 8

            $hsP21 = str_pad( $vl_boleto, 15 , "0", STR_PAD_LEFT  );   //* Valor Nominal do Título 86 100 15 (13 + 2 decimais)
            
            $hsP22 = str_pad( '', 5, '0' );       // Agência Encarregada da Cobrança 101 105 5 Obs.: Informar Zeros. A agência encarregada da Cobrança é definida de acordo com o CEP do sacado.
            
            $hsP23 = str_pad( '', 1 ); // Dígito Verificador da Agência - 106 106 1 Obs.: Informar brancos (espaço)
            
            $hsP24 = str_pad( '02', 2 );       //Espécie do Título: "02" Duplicata Mercantil - DM 107 108 2

            $hsP25 = str_pad( $aceito, 1 );       //* Identific. de Título Aceito/Não Aceito: Código adotado pela FEBRABAN para identificar se o título de cobrança foi aceito (reconhecimento da dívida pelo Pagador). 'A'  =  Aceite | 'N'  =  Não Aceite"

            $hsP26 = str_pad( $dt_emissao, 8 );       //* Data da Emissão do Título - 110 117 8

            $hsP27 = str_pad( $cd_juros, 1 );       //* Código do Juros de Mora: '0'  =  Isento, '1'  =  Valor por Dia, '2'  =  Taxa Mensal
            
            $hsP28 = str_pad( $dt_juros, 8 );       //* Data do Juros de Mora
            
            $hsP29 = str_pad( $tx_juros, 15, "0", STR_PAD_LEFT );      //* Juros de Mora por Dia 127 141 15 (13 + 2 decimais)

            $hsP30 = str_pad( '0', 1 );       //Código do Desconto 1 '1'  =  Valor Fixo Até a Data Informada, '2'  =  Percentual Até a Data Informada"
            
            $hsP31 = str_pad( '00000000', 8 );       //Data do Desconto 1 - 143 150 8 Obs.: Zeros, quando não houver desconto a ser concedido 
            
            $hsP32 = str_pad( '', 15, '0' );       //Valor/Percentual a ser Concedido 151 165 15 Obs.: Zeros, quando não houver desconto a ser concedido (13 + 2 decimais)
            
            $hsP33 = str_pad( '', 15, '0' );       //Valor do IOF a ser Recolhido - 166 180 15 Obs: Zeros, quando não houver IOF a ser cobrando. (13 + 2 decimais)
            
            $hsP34 = str_pad( '', 15, '0' );        //Valor do Abatimento - 181 195 15 Obs.: O valor do abatimento, sempre que informado, é deduzido do valor original do título, não importa quando o sacado efetue o pagamento. (13 + 2 decimais)
            
            $hsP35 = str_pad( ' ', 25 );       //Identificação do Título na Empresa: Campo destinado para uso do Beneficiário para identificação do Título. 196 220 25
            
            $hsP36 = str_pad( $protesto, 1 );       //* Código para Protesto: "1"
            
            $hsP37 = str_pad( '00', 2 );       //Número de Dias Corridos para Protesto
            
            $hsP38 = 0;       //Código para Baixa/Devolução - 224 224 1 Obs.: Campo não tratado pelo sistema. Informar 'zeros'. O sistema considera a informação que foi cadastrada na sua carteira junto ao Banco do Brasil.
            
            $hsP39 = '000';   //Número de Dias para Baixa/Devolução - 225 227 3 Obs.:Campo não tratado pelo sistema. Informar 'zeros'. O sistema acata a informação que foi cadastrada na sua carteira junto ao Banco do Brasil.
            
            $hsP40 = str_pad( '09', 2 );       //Código da Moeda: '02'  =  Dólar Americano Comercial (Venda), '09'  = Real"
            
            $hsP41 = str_pad( '', 10, '0' ); //Nº do Contrato da Operação de Créd. - 230 239 10 Obs.: Campo não tratado pelo sistema. Pode ser informado 'zeros' ou o número do contrato de cobrança.
            
            $hsP42 = 2;       // Uso livre Banco/Empresa ou Autorização de Pagamento Parcial 1 – Não autoriza pagamento parcial; 2 – Autoriza pagamentos parciais
            
            $dados_arquivoTXT03 = $hsP1.$hsP2.$hsP3.$hsP4.$hsP5.$hsP6.$hsP7.$hsP8.$hsP9.$hsP10.$hsP11.$hsP12.$hsP13.$hsP14.$hsP15.$hsP16.$hsP17.$hsP18.$hsP19.$hsP20.$hsP21.$hsP22.$hsP23.$hsP24.$hsP25.$hsP26.$hsP27.$hsP28.$hsP29.$hsP30.$hsP31.$hsP32.$hsP33.$hsP34.$hsP35.$hsP36.$hsP37.$hsP38.$hsP39.$hsP40.$hsP41.$hsP42;
            
            fwrite($fp,$dados_arquivoTXT03."\r\n"); //."\r\n" - Adiciona as linhas dentro do arquivo TXT
            
            
            //========== Fim REGISTRO DETALHE SEGMENTO P ===========//

            //========== REGISTRO DETALHE SEGMENTO Q ===========// 
            $nQ = $c+=1; 
            $numero_reg_lote_q = substr($nQ, 0, 5);                                                                             // 5
            
            if($dados_boleto['inscricao'.$i] == "cpf" ){ $tp_inscricao_cliente = '1'; }else{ $tp_inscricao_cliente = '2'; }     // 1 - 1 = CPF/ 2 = CNPJ/CGC
            
            $numero_inscricao_cliente = substr(self::limpaCPF_CNPJ($dados_boleto['cpf_cnpj'.$i]), 0, 15); 	                    // 15
            $cliente_nome = $dados_boleto['nome'.$i];//substr(, 40);			                                                // 40
            $cliente_end = $dados_boleto['logradouro'.$i]." ".$dados_boleto['numero'.$i]." ".$dados_boleto['complemento'.$i];   // 40
            $cliente_bairro = $dados_boleto['bairro'.$i];	                                                                    // 15
            
            $cliente_cep = explode("-",$dados_boleto['cep'.$i]);
            $cliente_cep_1 = str_replace('.', '', $cliente_cep[0]);                                                             // 5
            $cliente_cep_2 = $cliente_cep[1];			                                                                        // 3
            
            $cliente_cidade = $dados_boleto['cidade'.$i];		                                                                // 15
            $cliente_uf = $dados_boleto['uf'.$i];	                                                                            // 2
            $cliente_sacado = $cliente_nome;	                                                                                // 40 - Avalista/sacador
            $cliente_sacado_inc = $numero_inscricao_cliente;                                                                    // 15 - 
            
            //Espaçamento dentro do arquivo TXT
            
            $hsQ1 = str_pad( $banco, 3 , "0", STR_PAD_LEFT );          //Código do CEF na Compensação: "104"
            
            $hsQ2 = str_pad( $loteHL, 4 );         //* Lote de Serviço: Número seqüencial para identificar univocamente um lote de serviço. Criado e controlado pelo responsável pela geração magnética dos dados contidos no arquivo. Preencher com '0001' para o primeiro lote do arquivo. Para os demais: número do lote anterior acrescido de 1. O número não poderá ser repetido dentro do arquivo."

            $hsQ3 = str_pad( '3', 1 );            //Tipo de Registro: "3"
            
            $hsQ4 = str_pad( $numero_reg_lote_q, 5, "0", STR_PAD_LEFT );            //* Nº Sequencial do Registro no Lote: Número adotado e controlado pelo responsável pela geração magnética dos dados contidos no arquivo, para identificar a seqüência de registros encaminhados no lote. Deve ser inicializado sempre em '1', em cada novo lote.
            
            $hsQ5 = str_pad( 'Q', 1 );           //Cód. Segmento do Registro Detalhe: "Q"
            
            $hsQ6 = str_pad( ' ', 1);             //Uso Exclusivo FEBRABAN/CNAB: Brancos
            
            $hsQ7 = str_pad( '01', 2 );          //Código de Movimento Remessa:'01'  =  Entrada de Títulos        

            $hsQ8 = str_pad( $tp_inscricao_cliente, 1 );         //* Tipo de Inscrição Pagador:'1'  =  CPF, '2'  =  CGC / CNPJ"
            
            $hsQ9 = str_pad( $numero_inscricao_cliente, 15, "0", STR_PAD_LEFT );        //* Número de Inscrição
            
            $hsQ10 = str_pad( substr( utf8_decode ( $cliente_nome ), 0, 40), 40 );       //* Nome
            
            $hsQ11 = str_pad( substr( utf8_decode ( $cliente_end ), 0, 40), 40 );    //* Endereço        
            
            $hsQ12 = str_pad( substr( utf8_decode ( $cliente_bairro ), 0, 15), 15 );         //* Bairro
            
            $hsQ13 = str_pad( $cliente_cep_1, 5 );         //* CEP
            
            $hsQ14 = str_pad( $cliente_cep_2, 3 );           //* Sufixo do CEP
            
            $hsQ15 = str_pad( substr( utf8_decode ( $cliente_cidade ), 0, 15), 15 );       //* Cidade
            
            $hsQ16 = str_pad( $cliente_uf, 2 );            //* UF  - Unidade da Federação

            $hsQ17 = str_pad( $tp_inscricao_cliente, 1 );           //* Tipo de Inscrição Sacador Avalista: '1'  =  CPF, '2'  =  CGC / CNPJ"

            $hsQ18 = str_pad( $cliente_sacado_inc, 15, "0", STR_PAD_LEFT );   //* Número de Inscrição
            
            $hsQ19 = str_pad( substr( utf8_decode ( $cliente_sacado ), 0, 40), 40 );       //* Nome do Sacador/Avalista
            
            $hsQ20 = str_pad( '   ', 3 );      //Cód. Bco. Corresp. na Compensação: Caso o Beneficiário não tenha contratado a opção de Banco Correspondente com o CEF, preencher com ""000""; Caso o Beneficiário tenha contratado a opção de Banco Correspondente com o CEF e a emissão seja a cargo do CEF (SEQ 17.3.P do Segmento P do Detalhe), preencher com ""001"" (Banco do Brasil)"
            
            $hsQ21 = str_pad( '                    ', 20 );       //Nosso Nº no Banco Correspondente: ""1323739"" O campo NN deve ser preenchido, somente nos casos em que o campo anterior tenha indicado o uso do Banco Correspondente."
            
            $hsQ22 = str_pad( '        ', 8 );       //Uso Exclusivo FEBRABAN/CNAB


            
            $dados_arquivoTXT04 = $hsQ1.$hsQ2.$hsQ3.$hsQ4.$hsQ5.$hsQ6.$hsQ7.$hsQ8.$hsQ9.$hsQ10.$hsQ11.$hsQ12.$hsQ13.$hsQ14.$hsQ15.$hsQ16.$hsQ17.$hsQ18.$hsQ19.$hsQ20.$hsQ21.$hsQ22;      
            
            fwrite($fp,$dados_arquivoTXT04."\r\n"); //."\r\n" - Adiciona as linhas dentro do arquivo TXT
            
            //========== Fim REGISTRO DETALHE SEGMENTO Q ===========//
            
            //========== REGISTRO DETALHE SEGMENTO R ===========// 
            $nR = $c+=1;
            $numero_reg_lote_q = $nR;		            // 4
            $codigo_multa = "2";			            // 1 - Código da Multa: '0'  =  Isento, '1'  =  Valor Fixo, '2'  =  Percentual
            
            $dt_m_somar = date('dmY', strtotime("+1 days",strtotime($dados_boleto['dt_vencimento'.$i])));
            // $dt_m = explode('-', $dt_m_somar);
            $dt_multa = substr($dt_m_somar, 0, 8);			                // 8  *
            
            $tx_m = str_replace(".", "", $conta_financeira['multa']);	
            $tx_multa = substr( $tx_m, 0, 15);		                                        // 15 - (13 + 2) 000000000010000 = 100,00*

            
            //Espaçamento dentro do arquivo TXT
            
            $hsR1 = str_pad( $banco, 3 , "0", STR_PAD_LEFT );          //Código do CEF na Compensação: "104"
            
            $hsR2 = str_pad( $loteHL, 4 );         //* Lote de Serviço: Número seqüencial para identificar univocamente um lote de serviço. Criado e controlado pelo responsável pela geração magnética dos dados contidos no arquivo. Preencher com '0001' para o primeiro lote do arquivo. Para os demais: número do lote anterior acrescido de 1. O número não poderá ser repetido dentro do arquivo."

            $hsR3 = str_pad( '3', 1 );            //Tipo de Registro: "3"
            
            $hsR4 = str_pad( $numero_reg_lote_q , 5, "0", STR_PAD_LEFT );            //* Nº Sequencial do Registro no Lote: Número adotado e controlado pelo responsável pela geração magnética dos dados contidos no arquivo, para identificar a seqüência de registros encaminhados no lote. Deve ser inicializado sempre em '1', em cada novo lote.
            
            $hsR5 = str_pad( 'R', 1 );           //Cód. Segmento do Registro Detalhe: "R"
            
            $hsR6 = str_pad( ' ', 1);             //Uso Exclusivo FEBRABAN/CNAB: Brancos
            
            $hsR7 = str_pad( '01', 2 );          //Código de Movimento Remessa:'01'  =  Entrada de Títulos        

            $hsR8 = str_pad( '0', 1 );         //Código do Desconto 2 '0' = Nenhum '1'  =  Valor Fixo Até a Data Informada, '2'  =  Percentual Até a Data Informada
            
            $hsR9 = str_pad( '00000000', 8 );        //Data do Desconto 2
            
            $hsR10 = str_pad( '000000000000000', 15 );       //Valor/Percentual a ser Concedido ( 13 + 2 ), os dois ultimos digitos são casas decimais.
            
            $hsR11 = str_pad( '0', 1 );    //Código do Desconto 3 '1'  =  Valor Fixo Até a Data Informada '2'  =  Percentual Até a Data Informada    
            
            $hsR12 = str_pad( '00000000', 8 );        //Data do Desconto 3
            
            $hsR13 = str_pad( '000000000000000', 15 );         //Valor/Percentual a ser Concedido: "000000000000000" ( 13 + 2 ), os dois ultimos digitos são casas decimais.
            
            $hsR14 = str_pad( $codigo_multa, 1 );         //* Código da Multa: '0'  =  Isento, '1'  =  Valor Fixo, '2'  =  Percentual"
            
            $hsR15 = str_pad( $dt_multa, 8 );           //* Data da Multa
            
            $hsR16 = str_pad( $tx_multa, 15, "0", STR_PAD_LEFT );       //* Valor/Percentual a Ser Aplicado ( 13 + 2 ), os dois ultimos digitos são casas decimais.
            
            $hsR17 = str_pad( '          ', 10 );            //Informação ao Pagador: Brancos

            $hsR18 = str_pad( '                                        ', 40 );   //* Mensagem 3: Texto de observações destinado ao envio de mensagens livres, a serem impressas no campo de instruções da ficha de compensação do bloqueto. As Mensagens 3 e 4 prevalecem sobre as mensagens 1 e 2."

            $hsR19 = str_pad( '                                        ', 40 );   //* Mensagem 4: Texto de observações destinado ao envio de mensagens livres, a serem impressas no campo de instruções da ficha de compensação do bloqueto. As Mensagens 3 e 4 prevalecem sobre as mensagens 1 e 2."

            $hsR20 = str_pad( '                                                  ', 50 );  //E-mail sacado p/ envio de informações           

            $hsR21 = str_pad( '           ', 11 );             //Uso Exclusivo FEBRABAN/CNAB: Brancos
            
            

            
            $dados_arquivoTXT05 = $hsR1.$hsR2.$hsR3.$hsR4.$hsR5.$hsR6.$hsR7.$hsR8.$hsR9.$hsR10.$hsR11.$hsR12.$hsR13.$hsR14.$hsR15.$hsR16.$hsR17.$hsR18.$hsR19.$hsR20.$hsR21;      
            
            fwrite($fp,$dados_arquivoTXT05."\r\n"); //."\r\n" - Adiciona as linhas dentro do arquivo TXT
            
            //========== Fim REGISTRO DETALHE SEGMENTO R ===========//s
            
            $i++;
            $vl_total_boletos = $vl_total_boletos + $vl_boleto; 
        }

        //remove a ultima somatória do $i
        $i = $i - 1;
        
        //========== REGISTRO TRAILLER DO LOTE ===========// 
        $qtd_reg_lote = substr($i, 0, 6);		        // 6 - qtd registros no lote
        $total_cob_simples = substr($i, 0, 6);		    // 6 - qtd titulos no lote
        $vl_total_cob_simples = substr($vl_total_boletos, 0, 17);    // 17 - valor titulos no lote
        //$qtd_titulos_em_cobranca = substr($i, 0, 6);	// 6 qtd de titulos em cobrança vinculada
        //$vl_total_em_cobranca = substr($vl_total_boletos, 0, 17);    // 17 valor total de títulos em cobrança vinculada
        
        //Espaçamento dentro do arquivo TXT
        
        $hTL1 = str_pad( $banco, 3 , "0", STR_PAD_LEFT );          //Código do CEF na Compensação: "104"
        
        $hTL2 = str_pad( $loteHL, 4 );         //* Lote de Serviço: Número seqüencial para identificar univocamente um lote de serviço. Criado e controlado pelo responsável pela geração magnética dos dados contidos no arquivo. Preencher com '0001' para o primeiro lote do arquivo. Para os demais: número do lote anterior acrescido de 1. O número não poderá ser repetido dentro do arquivo."

        $hTL3 = str_pad( '5', 1 );            //Tipo de Registro: "5"
        
        $hTL4 = str_pad( '         ', 9 );            //"Uso Exclusivo FEBRABAN/CNAB: Brancos
        
        $hTL5 = str_pad( ($qtd_reg_lote * 3 + 2), 6, "0", STR_PAD_LEFT );           //* 18 23 6 Quantidade de Registros no Lote. É a somatória dos registros de tipo 1, 3 e 5. 1 - Header do lote; 3 - P, Q e R; 5 - Trailler do lote
        
        $hTL6 = str_pad( $qtd_reg_lote, 6, "0", STR_PAD_LEFT  );             //* 24 29 6 Totalização da Cobrança Simples - Quantidade de Títulos em Cobrança
        
        $hTL7 = str_pad( $vl_total_cob_simples, 17, "0", STR_PAD_LEFT  );          //*30 46 17 Totalização da Cobrança Simples - Valor Total dos Títulos em Carteiras ( 15 + 2 ), os dois ultimos digitos são casas decimais.
        
        $hTL8 = str_pad( '000000', 6 ); //47 52 6 //* Totalização da Cobrança Vinculada	- Quantidade de Títulos em Cobrança Vinculada "zeros"
        
        $hTL9 = str_pad( '00000000000000000', 17 ); //53 69 17 //* Totalização da Cobrança Vinculada - Valor Total dos Títulos em Carteiras ( 15 + 2 ), os dois ultimos digitos são casas decimais. "zeros"
        
        $hTL10 = str_pad( '000000', 6 );             //70 75 6 Totalização da Cobrança Caucionada - Quantidade de Títulos em Cobrança: "zeros"
        
        $hTL11 = str_pad( '00000000000000000', 17 ); //76 92 17 Totalização da Cobrança Caucionada - Quantidade de Títulos em Carteiras ( 15 + 2 ), os dois ultimos digitos são casas decimais. "zeros"
        
        $hTL12 = str_pad( '', 6, ' ' );             //Totalização da Cobrança Descontada - Quantidade de Títulos em Cobrança
        
        $hTL13 = str_pad( '', 17, ' ' );          //Totalização da Cobrança Descontada - Valor Total dos Títulos em Carteiras ( 15 + 2 ), os dois ultimos digitos são casas decimais.    

        $hTL14 = str_pad( '', 8, ' ' );         //Número do Aviso de Lançamento: Brancos
        
        $hTL15 = str_pad( '', 117, ' ' );        //Uso Exclusivo FEBRABAN/CNAB: Brancos
        
        $dados_arquivoTXT06 = $hTL1.$hTL2.$hTL3.$hTL4.$hTL5.$hTL6.$hTL7.$hTL8.$hTL9.$hTL10.$hTL11.$hTL12.$hTL13.$hTL14.$hTL15;      
        
        fwrite($fp,$dados_arquivoTXT06."\r\n"); //."\r\n" - Adiciona as linhas dentro do arquivo TXT
        
        //========== Fim REGISTRO TRAILLER DO LOTE ===========//
        
        
        
        //========== REGISTRO TRAILLER DO ARQUIVO ===========//  
        $qtd_reg_arq_cal = $i * 3;
        $qtd_reg_arq_cal = $qtd_reg_arq_cal + 2;
        $qtd_reg_arq =  substr($qtd_reg_arq_cal, 0, 6);		            // 6 Quantidade de registros do Arquivo
        
        //Espaçamento dentro do arquivo TXT
        
        $hTA1 = str_pad( $banco, 3 , "0", STR_PAD_LEFT );          //Código do CEF na Compensação: "104"
        
        $hTA2 = str_pad( '9999', 4 );         //Preencher com '9999'.

        $hTA3 = str_pad( '9', 1 );            //Tipo de Registro: "9"
        
        $hTA4 = str_pad( '         ', 9 );    //"Uso Exclusivo FEBRABAN/CNAB: Brancos
        
        $hTA5 = str_pad( '000001', 6 );       //Quantidade de Lotes do Arquivo
        
        $hTA6 = str_pad( ($qtd_reg_lote * 3 + 4), 6, "0", STR_PAD_LEFT );   //* Quantidade de registros do Arquivo. É a somatória dos registros de tipo 0, 1, 3, 5 e 9. 0 - Header do arquivo; 1 - Header do lote; 3 - P, Q e R; 5 - Trailler do lote; 9 - Trailler do arquivo
        
        $hTA7 = str_pad( '      ', 6 );       //Qtde de Contas p/ Conc. (Lotes): "000000"
        
        $hTA8 = str_pad( '                                                                                                                                                                                                             ', 205 );        //Uso Exclusivo FEBRABAN/CNAB: Brancos
        
        
        $dados_arquivoTXT07 = $hTA1.$hTA2.$hTA3.$hTA4.$hTA5.$hTA6.$hTA7.$hTA8;      
        
        fwrite($fp,$dados_arquivoTXT07); //."\r\n" - Adiciona as linhas dentro do arquivo TXT
        
        //========== Fim REGISTRO TRAILLER DO ARQUIVO ===========//
        
        //==========	
        
        fclose($fp); //Finaliza o arquivo TXT //$nomeArquivoTXT
        
        $retorno = array("nome_arquivo" => $nomeArquivoTXT, "numero_remessa" => $numero_remessa, "valor" => $vl_total_boletos);       //$vl_total_boletos
        
        return $retorno;
    }
}

?>