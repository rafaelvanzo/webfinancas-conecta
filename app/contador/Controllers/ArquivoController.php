<?php
require_once(ROOT_SISTEMA.'Models/Arquivo.php');

/**
 * ArquivoController short summary.
 *
 * ArquivoController description.
 *
 * @version 1.0
 * @author Fabio
 */
class ArquivoController
{

    private $db;
    private $dbCliente;

    /**
     * Construtor
     * @param Database $dbConnection 
     */
    function __construct(Database $dbConnection = null){
        $this->db = $dbConnection;
    }

    /**
     * Conexão com banco de dados do cliente
     * @param mixed $clienteId 
     */
    function DbClienteConn($clienteId){
        $db_wf = new Database('mysql.webfinancas.com','webfinancas','W2BSISTEMAS','webfinancas');
        $clienteDb = $db_wf->fetch_assoc('select db, db_senha from clientes_db where cliente_id = '.$clienteId);
        $db_wf->close();
        $this->dbCliente = new Database('mysql.webfinancas.com',$clienteDb['db'],$clienteDb['db_senha'],$clienteDb['db']);
    }

    /**
     * Summary of Delete
     * @param mixed $params
     */
    function Delete($params){
        self::DbClienteConn($params['cliente_id']);
	    $anexo = $this->dbCliente->fetch_assoc('select nome_arquivo from lnct_anexos where id = '.$params['id']);
	    $arquivo = ROOT.'sistema/uploads/cliente_'.$params["cliente_id"].'/'.$anexo["nome_arquivo"];
	    if(file_exists($arquivo)){
		    unlink($arquivo);
	    }
	    $this->dbCliente->query("delete from lnct_anexos where id = ".$params['id']);
        echo UtilController::array_to_json(array('status' => 1, 'msg' => 'Arquivo excluído com sucesso'));
	}

    /**
     * Summary of DataTable
     * @param mixed $db
     * @param mixed $params
     * @return string
     */
    function DataTable($params){
        
        $filtro = $params['filtro'];
        $filtro = str_replace('\"','"',$filtro);
        $filtro = str_replace("\'","'",$filtro);
        $filtro = json_decode($filtro, true);

        if($filtro['cliente_id']!=0){

            self::DbClienteConn($filtro['cliente_id']);

            //filtro do data table
            $sSearch = $params["sSearch"];
            $sEcho = $params["sEcho"];
            $iDisplayStart = $params["iDisplayStart"];
            $iDisplayLength = $params["iDisplayLength"];
            //$iTotalRecords = $db->numRows('select id from lancamentos');
            $iTotalDisplayRecords = 0;

            //Busca lançamentos que serão exibidos
            $aaData = array();
            
            if($sSearch==""){

                $queryArquivo = "select l.*, c.nome as classificacao, t.nome as tp_documento
                            from lnct_anexos l 
                            join arq_classificacao c on l.classificacao_id = c.id 
                            join arq_tp_documento t on l.tp_documento_id = t.id";

            }else{
                
                $queryArquivo = "select l.*, c.nome as classificacao, t.nome as tp_documento
                            from lnct_anexos l 
                            join arq_classificacao c on l.classificacao_id = c.id 
                            join arq_tp_documento t on l.tp_documento_id = t.id
                            where (l.nome_arquivo_org like '%".$sSearch."%' || c.nome like '%".$sSearch."%' || t.nome like '%".$sSearch."%')";
            }
            
            $iTotalDisplayRecords = mysql_num_rows(mysql_query($queryArquivo, $this->dbCliente->link_id));

            $queryArquivo = mysql_query($queryArquivo.' order by l.id desc limit '.$iDisplayStart.",".$iDisplayLength, $this->dbCliente->link_id);

            while($arquivo = mysql_fetch_assoc($queryArquivo)){
                $dtCadastro = $this->dbCliente->sql_to_data($arquivo['dt_cadastro']);
                $dtCompetencia = substr($this->dbCliente->sql_to_data($arquivo['dt_competencia']),3);
                $visualizado = ($arquivo['visualizado']==1)? 'Sim' : 'Não';
                $opcoes = '
                <a href="'.$arquivo['id'].'" title="Excluír" class="smallButton redB btTBwf excluir-arquivo" data-arquivo-id="'.$arquivo['id'].'" id="link-exc-'.$arquivo['id'].'"><img src="../../sistema/images/icons/light/close.png" width="10"></a>
                <a href="" title="Download" class="smallButton greyishB btTBwf download" data-nome="'.$arquivo['nome_arquivo'].'" data-nome-org="'.$arquivo['nome_arquivo_org'].'"><img src="../../sistema/images/icons/light/download.png" width="10"></a>
            ';  
                $nomeArquivo = '<b>'.$arquivo['nome_arquivo_org'].'</b>';
                array_push($aaData,array('arquivo'=>$nomeArquivo,'tp_documento'=>$arquivo['tp_documento'],'classificacao'=>$arquivo['classificacao'],'visualizado'=>$visualizado,'dt_cadastro'=>$dtCadastro, 'dt_competencia'=>$dtCompetencia,'opcoes'=>$opcoes));
            }

            $retorno = array('sEcho'=>$sEcho,'iTotalRecords'=>$iTotalDisplayRecords,'iTotalDisplayRecords'=>$iTotalDisplayRecords,'aaData'=>$aaData);
            
        }else{
            $retorno = array('sEcho'=>$params["sSearch"],'iTotalRecords'=>0,'iTotalDisplayRecords'=>0,'aaData'=>array());
        }
        

        echo json_encode($retorno);//UtilController::array_to_json($retorno);
    }
    
    /**
     * Summary of DownloadArquivo
     * @param mixed $params
     */
    function DownloadArquivo($params){
        //$arquivo = $this->db->fetch_assoc('select nome_arquivo, nome_arquivo_org where id = '.$params['id']);
        header("Content-Disposition:attachment;filename=".$params['nome_org']."");
        readfile('../../sistema/uploads/cliente_'.$_SESSION['contador_cliente']['id'].'/'.$params['nome']);
        //header("Content-Disposition:attachment;filename='teste.pdf'");
        //readfile("../php/uploads/cliente_134/134_comprovanteCludia04032015_1.pdf");
    }

    /**
     * Summary of AutoCompleteCliente
     * @param mixed $params 
     */
    function AutoCompleteCliente($params){

        //Busca clientes conectados
        $conexoes = $this->db->fetch_all_array('select cliente_id from conexao where contador_id = 0 and conectado = 1 order by cliente_id'); //0 = convite enviado, aguardando confirmação, 1 = conectado
        $array_cliente_id = array();
        foreach($conexoes as $conexao){
            array_push($array_cliente_id,$conexao['cliente_id']);
        }
        $clientes_id = join(',',$array_cliente_id);

        //Conexão no banco da Web 2 Business
		$db_w2b = new Database('mysql.web2business.com.br','web2business','W2BSISTEMAS','web2business');
        
        $q = $params["term"];
        
        if (!$q || $q==""){
            $query = mysql_query("select id, nome from clientes where id in (".$clientes_id.") order by nome", $db_w2b->link_id);
        }else{
            $query = mysql_query("select id, nome from clientes where id in (".$clientes_id.") and nome LIKE '%".$q."%' order by nome", $db_w2b->link_id);
        }

        $items = array();
        while($consulta = mysql_fetch_assoc($query)){
            array_push($items, $consulta);
        }

        if(count($items)>0){
            $result = array();
            foreach ($items as $item) {
                array_push($result, array("id"=>$item['id'], "label"=>$item['nome']));
            }
            echo UtilController::array_to_json($result);
        }else{
            echo '[]';
        }
    }

	/*
	================================================================================================
	ENVIAR SMS/EMAIL
	================================================================================================
	*/

    function EnvioSMSEmail($params){
/*
    $db_w2b = new Database('mysql.web2business.com.br','web2business','W2BSISTEMAS','web2business');
    $DadosCliente = $db_w2b->fetch_assoc('SELECT email, telefone, celular, parceiro_id FROM clientes WHERE id ='.$params['ClienteId']);
   
    //Verifica se é a LexData para fazer o envio do SMS (A única que possui este serviço). LexData: 244, WebFinançasTeste: 134
    if($DadosCliente['parceiro_id'] == 244){

        //Nome do remetente.
        $remetente = 'WebFinanças';

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
                           
                        $SmsMsg = 'Lexdata Contabilidade Informa: O documento '.$params['NomeArq'].' esta disponivel no sistema para pagamento.';
                
                        $url = 'https://sms.comtele.com.br/api/fe0d9d82-5fec-4949-b95a-22abfd082b42/sendmessage';
                        $data = array(
                                'content' => $SmsMsg,
                                'sender' => $remetente,
                                'receivers' => $numero
                        );  

                        //Envia o SMS
                        self::EnvioSMS($url, $data);    

            }    

        }

        $db_w2b->close();    
        */
    }


	/*
	================================================================================================
	ENVIAR SMS
	================================================================================================
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

    function ListaEnvio($params){
        
         $db_w2b = new Database('mysql.web2business.com.br','web2business','W2BSISTEMAS','web2business');  

         $contador_id = $_SESSION['cliente_id'];
         $contador = $db_w2b->fetch_assoc('SELECT nome, email, logo_recibo FROM clientes WHERE id ='.$contador_id);
         
         $cliente = $db_w2b->fetch_assoc('SELECT nome, email_fin FROM clientes WHERE id ='.$params['ClienteId']);         
         /*=======================================================================================*/
        
            $listaArquivos = $params['NomeArquivos'];

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
			
				    <td width="60%" align="center"><img src="'.$logo.'" align="middle" class="logo" /> </td>      
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

        $dadosEmail['Sistema'] = 'WebFinanças - Documentos';
        $dadosEmail['ClienteId'] = $contador_id;

        $dadosEmail['nomeRemetente'] = $contador['nome'];
        $dadosEmail['emailRemetente'] = $contador['email'];
        $dadosEmail['nomeDestinatario'] = $cliente['nome'];
        $dadosEmail['emailDestinatario'] = $cliente['email_fin'];
        $dadosEmail['assunto'] = 'Documentos disponíveis';
        $dadosEmail['mensagem'] = $mensagem;        
        $dadosEmail['situacao'] = 0;

        if(!empty($dadosEmail['emailDestinatario'])){
            $db_w2b->query_insert('servico_envio_email', $dadosEmail, $db_w2b->link_id);
        }

        //$db_w2b->close();

     }

}


?>