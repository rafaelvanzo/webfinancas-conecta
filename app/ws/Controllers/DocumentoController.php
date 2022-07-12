<?php

require_once('Models/Documento.php');

/**
 * DocumentoController short summary.
 *
 * DocumentoController description.
 *
 * @version 1.0
 * @author Fabio
 */
class DocumentoController extends Controller
{
    private $db;

    /**
     * Construtor
     * @param Database $dbConnection 
     */
    function __construct(Database $dbConnection = null){
        $this->db = $dbConnection;
    }

    /**
     * Incluir documentos no banco de dados
     * @param mixed $params 
     */
    function Create($params){
        
        $arq = array();
        $arqEmail = array();

        //Registra na tabela lnct_anexos
        foreach($params['documentos'] as $documento){
            
            //Nome do arquivo (A acentuação é removida para encontrar o arquivo na pasta do cliente porque o arquivo é enviado via FTP pelo Doc Monitor sem acentuação)
            $nomeArquivo = utf8_decode(self::RemoverAcento($documento['nome_arquivo']));

            //Extensão do arquivo
            $ext = substr($nomeArquivo, strrpos($nomeArquivo,'.'));

            //Gera novo nome aleatório
            $novoNome = bin2hex(openssl_random_pseudo_bytes(16)).$ext;

            //Caminho dos arquivos
            $filePath = $_SERVER['DOCUMENT_ROOT'].'/sistema/uploads/cliente_'.$params['cliente_id'].'/';

            //Renomeia arquivo
            rename($filePath.$nomeArquivo, $filePath.$novoNome);

            $documento['nome_arquivo'] = $novoNome;
            
            $novoDocumento = new Documento($documento);

            $this->db->query_insert('lnct_anexos',$novoDocumento->fields);

            //Prepara os nomes dos arquivos para envio por SMS.
            $nomeArquivo = substr($documento['nome_arquivo_org'],0,strrpos($documento['nome_arquivo_org'],"."));
            array_push($arq, $nomeArquivo);

            //Prepara os nomes dos arquivos para envio por email.
            $nomeArquivoEmail = '<li>'.$params['documentos'].'</li>';
            array_push($arqEmail, $nomeArquivoEmail); 
        }

        //Envio de SMS e Email
        foreach($arq as $arq){  
                
            self::PrepararEnvioSMS($params['cliente_id'], $arq); 
        }

        //Envio Email
        self::emailEnviar($params['parceiro_id'], $params['cliente_id'], $arqEmail);
    }

    
    /**
     * Summary of GetAll
     * @param mixed $params 
     */
    function GetAll($params){
        
        $items = array();

        $q = $params["query"];

        if (!$q || $q==""){
            $query = mysql_query("select id, nome from documentos order by nome", $this->db->link_id);
        }else{
            $query = mysql_query("select id, nome from documentos where nome LIKE '%".$q."%' order by nome", $this->db->link_id)or die(mysql_error());
        }

        while($consulta = mysql_fetch_assoc($query)){
            $key = $consulta['nome'];
            $value = $consulta['id'];
            $items[$key] = $value;
        }

        if(count($items)>0){
            $result = array();
            foreach ($items as $key=>$value) {
                //if (strpos(strtolower($key), $q) !== false) {
                array_push($result, array("id"=>$value, "descricao"=>$key));
                //}
                //if (count($result) > 11)
                    //break;
            }
            echo parent::array_to_json($result);
        }else{
            echo '[]';
        }
    }

    /**
     * Agendar envio de documentos via ftp aos clientes da contabilidade
     * @param mixed $params 
     */
    function AgendarEnvio($params){
        try{
            $novoAgendamento = new AgendaEnvio($params['agendamento']);

            $id = $this->db->query_insert('arq_agenda_envio',$novoAgendamento->fields);

            echo $id;
        }
        catch(Exception $e){
            echo -1;
        }
    }

    /**
     * Retornar agendamentos por status
     * @param mixed $params 
     */
    function GetAllAgendamentosParaEnvio($params){
        
        $query = '
            select 
                id as Id, 
                tipo_documento_id as TipoDocumentoId, 
                departamento_id as DepartamentoId, 
                dt_referencia as DtReferencia, 
                dt_liberacao as DtLiberacao, 
                enviar_para_todos as EnviarParaTodos, 
                cliente_id as ClienteId, 
                nome_cliente as NomeCliente,
                dominio as Dominio, 
                proprietario as Proprietario, 
                status as Status
            from arq_agenda_envio
            where status = '.$params['status'].'
            order by id';

        $agendamentos = $this->db->fetch_all_array($query);

        foreach($agendamentos as $agendamento)
            $this->db->query('delete from arq_agenda_envio where id = '.$agendamento['Id']);

        echo json_encode($agendamentos, JSON_NUMERIC_CHECK);

    }

    /**
     * Retornar envios agendados
     * @param mixed $params 
     */
    function GetEnviosAgendados($params){
        
        $query = '
            select 
                a.nome_cliente as Cliente,
                b.nome as Departamento,
                c.nome as Documento,
                dt_referencia as Referencia, 
                "Aguardando" as Status
            from arq_agenda_envio a
            join arq_classificacao b on a.tipo_documento_id = b.id
            join arq_tp_documento c on a.departamento_id = c.id
            where status = 1
            order by a.id';

        $agendamentos = $this->db->fetch_all_array($query);

        echo parent::array_to_json($agendamentos, JSON_NUMERIC_CHECK);

    }

    /**
     * Retornar configurações do doc monitor
     */
    function GetConfigDocMonitor(){
        $configuracao = $this->db->fetch_assoc('select pasta_raiz as PastaRaiz from arq_doc_monitor_config');
        $configuracao['PastaRaiz'] = str_replace('/', '\\', $configuracao['PastaRaiz']);
        echo parent::array_to_json($configuracao);
    }

    /**
     * Salvar configurações do doc monitor
     * @param mixed $params 
     */
    function SalvarConfigDocMonitor($params){
        
        $pastaRaiz = str_replace('\\', '/', $params['pasta_raiz']);
        
        $configuracao = $this->db->fetch_assoc('select * from arq_doc_monitor_config');
        
        if($configuracao)
            $this->db->query('update arq_doc_monitor_config set pasta_raiz = "'.$pastaRaiz.'"');
        else
            $this->db->query_insert('arq_doc_monitor_config',array('pasta_raiz'=>$pastaRaiz));
    }

    /**
    * Prepara e envia Email e SMS
    * @param mixed $params 
    */
    function PrepararEnvioSMS($ClienteId, $Arquivo){

    $db_w2b = new Database('mysql.web2business.com.br','web2business','W2BSISTEMAS','web2business');
    $DadosCliente = $db_w2b->fetch_assoc('SELECT email, telefone, celular, parceiro_id FROM clientes WHERE id ='.$ClienteId);
   
    //Verifica se é a LexData para fazer o envio do SMS (A única que possui este serviço). LexData: 244, WebFinançasTeste: 134
    if($DadosCliente['parceiro_id'] == 244){

        //Nome do remetente.
        $remetente = 'Lex Finanças';

        //remove os caracteres invalidos e espaços.
        $caracteres = array('(', ')', '-','_', ' ');
        $telefone =  str_replace($caracteres, "", $DadosCliente['telefone']);
        $celular =  str_replace($caracteres, "", $DadosCliente['celular']);

        //Verifica se os números são celulares.
        if(strlen($telefone) == 11){
            $numero = $telefone; 
        }elseif(strlen($celular) == 11){
            $numero = $celular;  
        }        

            //Verifica se o número realmente é um celular e faz o envio do SMS.
            if(strlen($numero) == 11){ 
                                
                        $SmsMsg = 'Lexdata Contabilidade Informa: O documento '.$Arquivo.' esta disponivel no sistema para pagamento.';
                
                        $url = 'https://sms.comtele.com.br/api/fe0d9d82-5fec-4949-b95a-22abfd082b42/sendmessage';
                        $data = array(
                                'content' => $SmsMsg,
                                'sender' => $remetente,
                                'receivers' => $numero
                        );                        

                        //Chama a função de envio de SMS.
                        self::EnvioSMS($url, $data);
                         
                                /* === PARA TESTES ===
                                $fp = fopen("Controllers/SMS.txt", "a"); 
                                // Escreve "exemplo de escrita" no bloco1.txt
                                $escreve = fwrite($fp, curl_error($post)); 
                                // Fecha o arquivo
                                fclose($fp);*/
                        
            }    
                  

        }
       
        $db_w2b->close();    
    }

     /**
     * Efetua o envio do SMS
     * @param mixed $url 
     */
    function EnvioSMS($url, $data){

           $fields = http_build_query($data);
           $post = curl_init();

           $url = $url.'?'.$fields;

           curl_setopt($post, CURLOPT_URL, $url);
           curl_setopt($post, CURLOPT_POST, 1);
           curl_setopt($post, CURLOPT_POSTFIELDS, $fields);

           $result = curl_exec($post);

           if($result == false){
               //die('Curl error: ' . curl_error($post));

               //Log Envio SMS
                $fp = fopen("Controllers/LogSMS/Log.txt", "a"); 
                // Escreve "exemplo de escrita" no bloco1.txt
                $escreve = fwrite($fp, curl_error($post)); 
                // Fecha o arquivo
                fclose($fp);
           }

           curl_close($post);

    }


    /*
	================================================================================================
	INCLUSÃO NO SERVIÇO DE ENVIO DE EMAIL
	================================================================================================
	*/

    function emailEnviar($ParceiroId, $ClienteId, $NomeDosArquivos){
    
     $db_w2b = new Database('mysql.web2business.com.br','web2business','W2BSISTEMAS','web2business');  

         $contador_id = $ParceiroId;
         $contador = $db_w2b->fetch_assoc('SELECT nome, email, logo_recibo FROM clientes WHERE id ='.$contador_id);
         
         $cliente = $db_w2b->fetch_assoc('SELECT nome, email_fin FROM clientes WHERE id ='.$ClienteId);         
         /*=======================================================================================*/
            
            $parceiroId = $contador_id;
            $nome_remetente = $contador['nome'];
            $email_remetente = $contador['email'];
            $nome_destinatario = $cliente['nome'];
            $email_destinatario = $cliente['email_fin'];
            $listaArquivos = $NomeDosArquivos;


         $enderecoArquivos = 'http://www.webfinancas.com/site/img/email_paginas/';
         if(!empty($contador['logo_recibo'])){ $logo = 'http://www.webfinancas.com/sistema/'.$contador['logo_recibo']; }else { $logo = 'http://www.webfinancas.com/site/img/logo_webfinancas_fundo_branco.png'; }


    $mensagem = '<style>
	body { 
	padding:0 !important; 
	margin:0 !important; 
	display:block !important; 
	background:#f8f8f8;  
	font-family: Arial, Helvetica, sans-serif;
	font-size:12px;
	color:#666666;
	}
	a { text-decoration: none; }
	
	#palco {
	border: 0px;
	min-width: 250px;
	max-width: 700px;
	min-height: 200px;
	height: 100%;
	margin-left:auto;
	margin-right:auto;
	border-radius: 20px;
	}
	
	#rodape {
	text-align: left;
	min-width: 250px;
	max-width: 700px;
	height: 50px;
	margin-left:auto;
	margin-right:auto;
	font-size:10px;
	}
	
	.img { width: 300px; }
	
	</style>
		
	
	
		<div id="palco">
		
	<table width="700" height="100%" border="0" align="center">
			<tr>
			
				<td width="60%"  align="center"><img src="http://www.webfinancas.com/lexdata/logo.png" align="middle" class="logo" /> </td>      
			    <td width="40%"></td>	

			</tr>
			<tr>
			
			<td width="60%" align="justify" valign="top">
			
				<br />
				
			 <h2> Olá, </h2>
				<p style="line-height:20px;">Os seguintes documentos estão disponíveis em nosso sistema:

                <ul>
                    '.$listaArquivos.'
                </ul>

				<br><br>	
				Atenciosamente,
				<br>	
				
					<b>Contabilidade</b>	
			</p>
						
			</td>    
			<td width="40%" align="center" valign="bottom"> 
				 <img src="'.$enderecoArquivos.'documento.png" align="center" class="img" width="215" />	
			 </td>
			 
		</tr>
	</table>';

        
        $db_w2b = new Database('mysql.web2business.com.br','web2business','W2BSISTEMAS','web2business');  


        $dadosEmail['Sistema'] = 'WebFinanças - DocMonitor';
        $dadosEmail['ClienteId'] = $parceiroId;

        $dadosEmail['nomeRemetente'] = $nome_remetente;
        $dadosEmail['emailRemetente'] = $email_remetente;
        $dadosEmail['nomeDestinatario'] = $nome_destinatario;
        $dadosEmail['emailDestinatario'] = $email_destinatario;
        $dadosEmail['assunto'] = 'Documentos disponíveis';
        $dadosEmail['mensagem'] = $mensagem;        
        $dadosEmail['situacao'] = 0;

        if(!empty($dadosEmail['emailDestinatario'])){
            $db_w2b->query_insert('servico_envio_email', $dadosEmail, $db_w2b->link_id);
        }

        //$db_w2b->close();
	}

    //Remover acentuação
    function RemoverAcento($str){

        $arrConv = array(
        "ç" => "c",
        "Ç" => "C",
        "ã" => "a",
        "Ã" => "A",
        "á" => "a",
        "Á" => "A",
        "à" => "a",
        "À" => "A",
        "â" => "a",
        "Â" => "A",
        "é" => "e",
        "É" => "E",
        "ê" => "e",
        "Ê" => "E",
        "è" => "e",
        "È" => "E",
        "í" => "i",
        "Í" => "I",
        "î" => "i",
        "Î" => "I",
        "ì" => "i",
        "Ì" => "I",
        "ó" => "o",
        "Ó" => "O",
        "ô" => "o",
        "Ô" => "O",
        "õ" => "o",
        "Õ" => "O",
        "ò" => "o",
        "Ò" => "O",
        "ú" => "u",
        "Ú" => "U",
        "ù" => "u",
        "Ù" => "U",
        "ü" => "u",
        "Ü" => "U",
        "ñ" => "n",
        "Ñ" => "N",
        "ý" => "y",
        "Ý" => "Y",
        "\"" => "",
        "'" => "",
        "," => "",
        //" " => "",
        "<" => "",
        ">" => "",
        );

        return strtr($str, $arrConv);
    }
}
