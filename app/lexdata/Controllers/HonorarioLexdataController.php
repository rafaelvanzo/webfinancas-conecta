<?php
define('ROOT',$_SERVER['DOCUMENT_ROOT'].'/');
define('ROOT_MODULOS',$_SERVER['DOCUMENT_ROOT'].'/sistema/modulos/');

require_once(ROOT.'lexdata/Util/Mascara.php');
require_once(ROOT_MODULOS.'favorecido/class/Favorecido.class.php');
require_once(ROOT_MODULOS.'lancamento/class/Lancamento.class.php');
require_once(ROOT_MODULOS.'lancamento/class/Recebimento.class.php');
require_once(ROOT_MODULOS.'lancamento/class/Pagamento.class.php');
require_once(ROOT_MODULOS.'lancamento/class/Transferencia.class.php');
require_once(ROOT.'lexdata/Controllers/RemessaLexdataController.php');
//require_once(ROOT.'lexdata/Database/DbMSSql.php'); //O driver para o mssql do php não está instalado no servidor. Caso seja necessário operar o mssql server, utilizar o driver sqlsrv.

/**
 * HonorarioLexdataController short summary.
 *
 * HonorarioLexdataController description.
 *
 * @version 1.0
 * @author Fabio
 */
class HonorarioLexdataController
{
    private $db;
    const LEXDATA_DB = 'webfinancas22';

    /**
     * Construtor
     * @param Database $dbConnection 
     */
    function __construct(Database $dbConnection = null){
        $this->db = $dbConnection;
    }

    /**
     * Registrar log da geração de honorários
     * @param mixed $arquivo_log 
     * @param mixed $msg 
     * @param mixed $enviar_email 
     * @param mixed $assunto 
     */
    function LogGerarHonorario($arquivo,$msg,$enviar_email=false,$assunto=''){
		$fp = fopen($arquivo,"a+");
		fwrite($fp,$msg."\r\n");
		fclose($fp);
		//if($enviar_email){
			//$conteudo = $msg;
			//self::emailEnviar($assunto,$conteudo);
		//}
	}

    /**
     * Gera honorário para o cliente da contabildade
     */
     
    function Create(){

        //Baixar arquivos de remessa via FTP
        //RemessaLexdataController::GetRemessaFtp();

        //Leitura do arquivo de remessa
        //$boletos = RemessaLexdataController::LerRemessa(); 

        $boletos = RemessaLexdataController::NovosBoletosSUFOL();
                
        //Conexão com o banco de dados principal do WF
        $dbWf = new Database("mysql.webfinancas.com","webfinancas","W2BSISTEMAS","webfinancas");
        
        //Arquivo de log
        $arquivoLog = ROOT.'lexdata/Log/log_'.date('Ymd').'.txt';
        $boletosGerados = 0;
        $boletosErro = 0;
        $msgLog = '';

        //Processa boletos da remessa do sufol
        if(count($boletos)>0){ 

        $DadosSMS = array();

            foreach($boletos as $key=>$prop){

                //Conexão com o banco de dados da Lexdata
                $dbLexdata = new Database("mysql.webfinancas.com",self::LEXDATA_DB,"W2BSISTEMAS",self::LEXDATA_DB);

                $boleto = $boletos[$key];

                echo 'arquivo: '.$boleto['arquivo'].' <br><br>';

                $dbLexdata->query('start transaction');

                try{

                    //Busca id do cliente na tabela conexão da Lexdata
                    $clienteId = $dbLexdata->fetch_assoc('select cliente_id from conexao where cpf_cnpj = "'.$boleto['favorecido']['cpf_cnpj'].'" and conectado = 1');

                    if($clienteId){

                        echo 'cliente_id: '.$clienteId['cliente_id'].' <br>';

                        //Verifica se já existe boleto com o nosso número
                        $boletoExiste = mysql_fetch_assoc(mysql_query('select a.id from boletos a join lancamentos b on a.lancamento_id = b.id where nosso_numero = "'.$boleto['nosso_numero'].'" and conta_id = 2', $dbLexdata->link_id));

                        echo 'consultou boleto <br>';

                        if(!$boletoExiste){

                            echo 'boleto não existe <br>';

                            //Inclusão do favorecido
                            //-------------------------------------------------------------------------------
                            $favorecido = mysql_fetch_assoc(mysql_query('select id, nome from favorecidos where cpf_cnpj = "'.$boleto['favorecido']['cpf_cnpj'].'"', $dbLexdata->link_id));
                            if(!$favorecido){
                                $favorecido = new Favorecido($boleto['favorecido']);
                                $favorecido = $favorecido->favorecidosIncluir($dbLexdata);
                                $favorecido['nome'] = $boleto['favorecido']['nome'];
                                echo 'Novo favorecido cadastrado <br>';
                            }

                            echo 'favorecido_id: '.$favorecido['id'].' <br>';
                            echo 'nome: '.$favorecido['nome'].' <br>';

                            //Inclusão do lançamento
                            //-------------------------------------------------------------------------------
                            
                            $boleto['lancamento']['favorecido_id'] = $favorecido['id'];
                            $recebimento = new Recebimento($dbLexdata,$boleto['lancamento']);

                            echo '<br>';
                            var_dump($boleto['lancamento']).' <br>';
                            echo '<br>';

                            $lancamentoId = $recebimento->lancamentoIncluir($dbLexdata,$boleto['lancamento']);

                            echo 'lancamento_id: '.$lancamentoId.' <br>';

                            //Inclusão do boleto
                            //-------------------------------------------------------------------------------
                            
                            $sequecial = substr($boleto['nosso_numero'],0,-1); //nosso número sem dv

                            //$boletoId = $dbLexdata->query_insert('boletos',array('sequencial'=>$sequecial,'lancamento_id'=>$lancamentoId,'nosso_numero'=>$boleto['nosso_numero']));
                            mysql_query('insert into boletos (sequencial,lancamento_id,nosso_numero) values("'.$sequecial.'","'.$lancamentoId.'","'.$boleto['nosso_numero'].'")', $dbLexdata->link_id);
                            $boletoId = mysql_insert_id($dbLexdata->link_id);

                            echo 'boleto_id: '.$boletoId.' <br>';

                            //Inclusão do link referente ao boleto de honorários no banco de dados do cliente
                            //-------------------------------------------------------------------------------

                            //Busca banco de dados do cliente e faz a conexão
                            $dadosClienteDb = $dbWf->fetch_assoc('select db, db_senha from clientes_db where cliente_id = '.$clienteId['cliente_id']);
                            
                            if($dbCliente = new Database("mysql.webfinancas.com",$dadosClienteDb['db'],$dadosClienteDb['db_senha'],$dadosClienteDb['db'])){
                            
                                //Inclui honorário no banco de dados do cliente
                                $honorario = array(
                                        'contador_id'=>244, //134 - Web Finanças Teste; 244 - Lexdata
                                        'lancamento_id'=>$lancamentoId,
                                        'nome_contabilidade'=>'Lexdata',
                                        'valor'=>$dbCliente->valorToDouble($boleto['lancamento']['valor']),
                                        'compensado'=>0,
                                        'link'=>"244-147720-$lancamentoId-$boletoId-$sequecial", //chave=cliente_id(id do cedente)-convenio-lancamento_id-boleto_id-sequencial
                                        'dt_vencimento'=>$dbCliente->data_to_sql($boleto['lancamento']['dt_vencimento']),
                                        'dt_cadastro'=>date('Y-m-d H:i:s')
                                    );

                                $honorarioId = $dbCliente->query_insert('honorarios',$honorario, $dbCliente->link_id);
                                
                                echo 'honorario_id: '.$honorarioId.' <br>';

                                //Encerra conexão com banco de dados do cliente
                                $dbCliente->close();
                            }

                            $msgLog .= date('d/m/Y - H:i:s')."\n";
                            $msgLog .= 'Arquivo: '.$boleto['arquivo']."\n";
                            $msgLog .= 'Nosso número: '.$boleto['nosso_numero']."\n";
                            $msgLog .= 'Status: Honorário gerado com sucesso.'."\n";
                            $msgLog .= '------------------------------------------------------------------'."\n\n";

                        }else{

                            echo 'boleto existe <br>';

                            $msgLog .= date('d/m/Y - H:i:s')."\n";
                            $msgLog .= 'Arquivo: '.$boleto['arquivo']."\n";
                            $msgLog .= 'Nosso número: '.$boleto['nosso_numero']."\n";
                            $msgLog .= 'Status: Boleto já existe.'."\n";
                            $msgLog .= '------------------------------------------------------------------'."\n\n";
                        }
                        
                    }else{
                        echo 'cliente não está conectado <br>';

                        $msgLog .= date('d/m/Y - H:i:s')."\n";
                        $msgLog .= 'Arquivo: '.$boleto['arquivo']."\n";
                        $msgLog .= 'Nosso número: '.$boleto['nosso_numero']."\n";
                        $msgLog .= 'Status: Cliente não está conectado.'."\n";
                        $msgLog .= '------------------------------------------------------------------'."\n\n";
                    }


                    $dbLexdata->query('commit');

                    $boletosGerados++;
                }
                catch(Exception $e){

                    $dbLexdata->query('rollback');
                    
                    $boletosErro++;

                    $msgLog .= date('d/m/Y - H:i:s')."\n";
                    $msgLog .= 'Arquivo: '.$boleto['arquivo']."\n";
                    $msgLog .= 'Nosso número: '.$boleto['nosso_numero']."\n";
                    $msgLog .= 'Status: Erro ao gerar honorário.'."\n";
                    $msgLog .= '------------------------------------------------------------------'."\n\n";
                }

                //Encerra conexão com banco de dados da lexdata
                $dbLexdata->close();

                echo '----------------------------------------- <br><br>';
            }
        
            self::LogGerarHonorario($arquivoLog,$msgLog);
        }
        
        //Encerra conexão com banco de dados webfinancas
        $dbWf->close();
        
    }
    
    /**
     * Edição de honorários
     */
    function Edit($honorariosWf,$honorariosLexdata){
        
        //Conexão com o banco de dados da Lexdata no Web Finanças
        $dbLexdataWf = new Database("mysql.webfinancas.com",self::LEXDATA_DB,"W2BSISTEMAS",self::LEXDATA_DB);

        //Conexão com o banco de dados da Lexdata no SUFOL
        //$dbLexdata = new DbMSSql();

        //$dbLexdata->Open();

        $honorariosEditados = array();

        //Atualiza lançamento e honorário no Web Finanças
        foreach($honorariosWf as $honorario){

            //Instanciar a classe Lançamento
            $lancamento = new Lancamento();

            //Consultar lançamento
            $lancamento = $lancamento->lancamentosExibir($dbLexdataWf,array('lancamento_id'=>$honorario['lancamento_id']));
            $lancamento = $lancamento['lancamento'];

            $nossoNumero = explode('-',$honorario['link']);
            $nossoNumero = $nossoNumero[4];
            
            //Atualiza lançamento e honorário somente se encontrar o nosso número nos boletos do sufol ($honorariosLexdata) 
            //e o lançamento existir no banco de dados da Lexdata no Web Finanças
            if(array_key_exists($nossoNumero,$honorariosLexdata) && array_key_exists('id',$lancamento)){
           
                //Completa array com a id do lançamento para editá-lo
                $lancamento['lancamento_id'] = $honorario['lancamento_id'];

                if($honorariosLexdata[$nossoNumero]['valorpago'] == null){ $compensado = 0; }else{ $compensado = 1; };

                //Identifica alterações feitas pela Lexdata nossonumero, datavencimento, valorapagar, datapagamento, valorpago
                $lancamento['dt_vencimento'] = date('d/m/Y',strtotime($honorariosLexdata[$nossoNumero]['datavencimento']));
                $lancamento['valor'] = $dbLexdataWf->valorFormat($honorariosLexdata[$nossoNumero]['valorapagar']);
                $lancamento['valor_pago'] = $dbLexdataWf->valorFormat($honorariosLexdata[$nossoNumero]['valorpago']);
                $lancamento['compensado'] = $compensado; //$honorariosLexdata[$nossoNumero]['Pago'];
                $lancamento['dt_compensacao'] = date('d/m/Y',strtotime($honorariosLexdata[$nossoNumero]['datapagamento']));
                
                //Atualizar lançamento no banco de dados da Lexdata no Web Finanças
                $recebimento = new Recebimento($dbLexdataWf,$lancamento);
                $editado = $recebimento->lancamentoEditar($dbLexdataWf,$lancamento);
                
                //Atualizar honorário no banco de dados do cliente no Web Finanças...
                if($editado){
                    $this->db->query('update honorarios set valor = '.$honorariosLexdata[$nossoNumero]['Valor'].', compensado = '.$lancamento['compensado'].', dt_vencimento = "'.date('Y-m-d',strtotime($honorariosLexdata[$nossoNumero]['Vencimento'])).'" where id = '.$honorario['id']);
                }

                if($honorariosLexdata[$nossoNumero]['valorpago'] == null){ $compensado = 0; }else{ $compensado = 1; };

                //Atualiza honorários que serão exibidos para o cliente
                $honorario['dt_vencimento'] = date('Y-m-d',strtotime($honorariosLexdata[$nossoNumero]['datavencimento']));
                $honorario['valor'] = $dbLexdataWf->valorFormat($honorariosLexdata[$nossoNumero]['valorapagar']);
                $honorario['compensado'] = $compensado; //$honorariosLexdata[$nossoNumero]['Pago'];
            
                array_push($honorariosEditados,$honorario);
                
            }else{
            
                //Instanciar recebimento
                $recebimento = new Recebimento($dbLexdataWf,$lancamento);
               
                //Excluir lançamento e honorário
                if($recebimento->lancamentoExcluir($dbLexdataWf,$lancamento) || !array_key_exists('id',$lancamento))
                    $this->db->query('delete from honorarios where id = '.$honorario['id']);    
            } 
        }

        //$dbLexdata->Close();
        
        $dbLexdataWf->Close();

        //$this->db->close();
        
        return $honorariosEditados;
    }

    /**
     * Retorna honorários do banco de dados da Lexdata 
     * para comparar com os honorários no Web Finanças
     * @param mixed $params
     */
    function GetHonorariosLexdata($params){
    
        //Conexão no Sufol
        $conexao = "host=177.53.174.212 port=15780 dbname=dbsufol user=wf password=webfinancas";
        $dbSufol = pg_connect($conexao);
        
        //Campos: nossonumero, datavencimento, valorapagar, datapagamento, valorpago
        $query = pg_query($dbSufol, "select nomesacado, cnpjcpf, nossonumero, datavencimento, valorapagar, datapagamento, valorpago from view_boletos WHERE datavencimento >= '".$params['dtIni']."' and datavencimento <= '".$params['dtFim']."'");
        
        if($honorario = pg_fetch_all($query)){
          
            $honorarios = array();

                foreach($honorario as $honorario){

                    $honorario['nossonumero'] = ltrim($honorario['nossonumero'],'0');

                    $honorarios[substr(ltrim($honorario['nossonumero'],'0'),0,-1)] = $honorario;
                    //$honorarios['nossonumero'] = ltrim($honorario['nossonumero'],'0');
                    
               }
                //print_r($honorario);
        }else{

          $honorarios = false;
        }

        pg_close($dbSufol);

        return $honorarios;
    }

    function GetHonorariosLexdataTest($params=''){

        $params['dtIni'] = '2017-07-01';
        $params['dtFim'] = '2017-07-05';


        /*
        $dbContext = new DbMSSql();

        $dbContext->Open();

        if($query = mssql_query('select * from Boletos where Vencimento >= "'.$params['dtIni'].'" and Vencimento <= "'.$params['dtFim'].'"',$dbContext->GetLinkId())){
        
            $honorarios = array();
    
            while($honorario = mssql_fetch_assoc($query)){
                $honorarios[substr(ltrim($honorario['Numero'],'0'),0,-1)] = $honorario;
            }
        */
        
        //Conexão no Sufol
        $conexao = "host=177.53.174.212 port=15780 dbname=dbsufol user=wf password=webfinancas";
        $dbSufol = pg_connect($conexao);
        
        //Campos: nossonumero, datavencimento, valorapagar, datapagamento, valorpago
        $query = pg_query($dbSufol, "select nossonumero, datavencimento, valorapagar, datapagamento, valorpago from view_boletos WHERE datavencimento >= '".$params['dtIni']."' and datavencimento <= '".$params['dtFim']."'");
        
        if($honorario = pg_fetch_all($query)){
          
            $honorarios = array();
                
                 foreach($honorario as $honorario){
                    
                    $honorario['nossonumero'] = ltrim($honorario['nossonumero'],'0');
                    $honorarios[substr(ltrim($honorario['nossonumero'],'0'),0,-1)] = $honorario;
                    //$honorarios[substr(ltrim($honorario['nossonumero'],'0'),0,-1)] = ltrim($honorario['nossonumero'],'0');
                    
               }
                print_r($honorario['datapagamento']); echo 'entrou!';

        }else{

            $honorarios = false;
        }

        //print_r($fatura);

        //$dbContext->Close();
        pg_close($dbSufol);

        return $honorarios['Ativo'];
    }

    /**
     * Retorna honorários consultando boletos no banco de dados da Lexdata
     * @param mixed $params
     */
    function DataTable($params){
        
        //filtro do data table
        $sSearch = $params["sSearch"];
        $sEcho = $params["sEcho"];
        $iDisplayStart = $params["iDisplayStart"];
        $iDisplayLength = $params["iDisplayLength"];
        $iTotalDisplayRecords = 0;
        
        //start: período
        $mes = $params['mes'];
        $ano = $params['ano'];
        $dtIni = $ano.'-'.$mes.'-01';
        $dtFim = date('Y-m-d', strtotime('+1 month', strtotime($dtIni)));
        //end: período

        //Busca lançamentos que serão exibidos
        $aaData = array();
        
        $queryHonorario = "select * from honorarios where dt_vencimento >= '".$dtIni."' and dt_vencimento < '".$dtFim."'";

        $iTotalDisplayRecords = mysql_num_rows(mysql_query($queryHonorario, $this->db->link_id));

        $queryHonorario = mysql_query($queryHonorario.' order by dt_vencimento desc limit '.$iDisplayStart.",".$iDisplayLength, $this->db->link_id);
        
        $honorariosParaEditar = array();

        while($honorario = mysql_fetch_assoc($queryHonorario))
            array_push($honorariosParaEditar,$honorario);

        $honorariosLexdata = self::GetHonorariosLexdata(array('dtIni'=>$dtIni,'dtFim'=>$dtFim));
        
        //Atualiza honorários somente se a conexão com a Lexdata ocorrer com sucesso
        //porque, caso contrário, os honorários serão excluídos no banco de dados do cliente da Lexdata no Web Finanças
        if($honorariosLexdata)
            $honorariosEditados = self::Edit($honorariosParaEditar,$honorariosLexdata);
        else
            $honorariosEditados = $honorariosParaEditar;

        foreach($honorariosEditados as $honorario){

            $dtVencimento = $this->db->sql_to_data($honorario['dt_vencimento']);
            $visualizado = ($honorario['visualizado']==1)? 'Sim' : 'Não';
            $compensado = ($honorario['compensado']==1)? 'Sim' : 'Não';
            $valor = $this->db->valorFormat($honorario['valor']);
            $opcoes = ($honorario['compensado']==0)? '<a href="" title="Download" class="smallButton greyishB download" data-link="'.$honorario['link'].'" data-id="'.$honorario['id'].'"><img src="images/icons/light/download.png" width="10"></a>' : '-';
            array_push($aaData,array('dt_vencimento'=>$dtVencimento,'valor'=>'R$ '.$valor,'compensado'=>$compensado,'visualizado'=>$visualizado,'opcoes'=>$opcoes));
        }

        $retorno = array('sEcho'=>$sEcho,'iTotalRecords'=>$iTotalDisplayRecords,'iTotalDisplayRecords'=>$iTotalDisplayRecords,'aaData'=>$aaData);

        echo Util::array_to_json($retorno);
    }

    /**
     * Summary of DownloadHonorario
     * @param mixed $params
     */
    function DownloadHonorario($params){
        
        $this->db->query('update honorarios set visualizado = 1 where id = '.$params['id']);
        
        header('location:https://www.webfinancas.com/boleto/'.$params['link']);
    }

    /**
    *@SMS Envio de SMS e Emails para o cliente  
    */
    
    function EnvioSMSEmail($DadosSMS){   
                  
    //Conexão com o banco de dados da w2b
    $db_w2b = new Database("mysql.web2business.com.br","web2business","W2BSISTEMAS","web2business");
        
        foreach($DadosSMS as $dados){
        
        $DadosCliente = $db_w2b->fetch_assoc('SELECT nome, telefone, celular, email_fin FROM clientes WHERE id ='.$dados['ClienteId'], $db_w2b->link_id);
    
           //Nome do remetente e o tipo da mensagem.
            $remetente = 'Lex Finanças';
            $Tpmsg = $dados['Tpmsg'];
            $emailCliente = $DadosCliente['email_fin'];            
            $nomeCliente = $DadosCliente['nome'];

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
                         
                //Boleto que acabou de ser cadastrado.
                if($Tpmsg == 1){ 
                    $SmsMsg = 'Lexdata Contabilidade Informa: Seu boleto para pagamento de honorário já está disponível no sistema. Acesse agora.';                    
                }elseif($Tpmsg == 0){ 
                    $SmsMsg = 'Lexdata Contabilidade informa: Seu boleto para pagamento está disponível no sistema. Acesse agora e regularize seu debito. Caso ja tenha pago, desconside a mensagem.';
                }

                    //Verifica se o número realmente é um celular e faz o envio do SMS.
                    if(strlen($numero) == 11){
                                    
                        $url = 'https://sms.comtele.com.br/api/fe0d9d82-5fec-4949-b95a-AAA-22abfd082b42/sendmessage';
                        $data = array(
                                'content' => $SmsMsg,
                                'sender' => $remetente,
                                'receivers' => $numero
                        );  

                        //Envia o SMS
                        self::EnvioSMS($url, $data);  
                    
                    }    
   
                    // Enviar email
                    //self::emailEnviar('Lexdata Contabilidade', 'financeiro@lexdata.com.br', $nomeCliente, $emailCliente, 'Honorário disponível', $SmsMsg);


        }

    //$db_w2b->close(); 

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
    


    /**
    *@FATURAS Verificar faturas em vencidas ou faltando 5 dias para vencer
    */
    function VerificarPgto(){

        //Conexão com o banco de dados da Lexdata
        $dbLexdata = new Database("mysql.webfinancas.com",self::LEXDATA_DB,"W2BSISTEMAS",self::LEXDATA_DB);
        
      
        $hoje = date('Y-m-d');
        $vecimento_5_dias = date('Y-m-d', strtotime("+5 days",strtotime($hoje))); 

        $Honorarios = $dbLexdata->fetch_all_array('SELECT DISTINCT(favorecido_id) FROM lancamentos WHERE compensado = 0 AND dt_vencimento = "'.$vecimento_5_dias.'"', $dbLexdata->link-id); //Para enviar 5 dias antes e no dia do vencimento. (dt_vencimento = "'.$hoje.'" OR dt_vencimento = "'.$vecimento_5_dias.'")
        
        // Conexao com o banco de dados da W2B
        $db_w2b = new Database("mysql.web2business.com.br","web2business","W2BSISTEMAS","web2business");
    
            $DadosSMS = array();
            $ok = 0;
        //=======================================================================
        
        if(!empty($Honorarios)){

           foreach($Honorarios as $Honorarios){
            
                // Pega o CPF/CNPJ na tabela de favorecido
                $cpf_cnpj = $dbLexdata->fetch_assoc('SELECT cpf_cnpj FROM favorecidos WHERE id = '.$Honorarios['favorecido_id'], $dbLexdata->link-id);

                // Pega a id do cliente na web 2 business
                $Cliente = $db_w2b->fetch_assoc('SELECT id FROM clientes WHERE cpf_cnpj = "'.$cpf_cnpj['cpf_cnpj'].'" AND parceiro_id = 244', $db_w2b->link-id);
                
                //Array para enviar SMS
                array_push($DadosSMS, array('ClienteId' => $Cliente['id'], 'Tpmsg' => 1));     
                $ok +=1;    
            }
            
        } // Fim verificação se o existe honorários 5 dias a frente para vencer. 
   
        //=======================================================================
        //=======================================================================
       
        //Verifica se o dia de hoje é multiplo de 5
        $diaHoje = date('d');
        if(($diaHoje % 5) == 0){

            $Honorarios_atrasados = $dbLexdata->fetch_all_array('SELECT DISTINCT(favorecido_id) FROM lancamentos WHERE compensado = 0 AND dt_vencimento < "'.$hoje.'"', $dbLexdata->link-id);
        
             if(!empty($Honorarios_atrasados)){

                foreach($Honorarios_atrasados as $honorario){

                    // Pega o CPF/CNPJ na tabela de favorecido
                    $cpf_cnpj = $dbLexdata->fetch_assoc('SELECT cpf_cnpj FROM favorecidos WHERE id = '.$honorario['favorecido_id'], $dbLexdata->link-id);

                    // Pega a id do cliente na web 2 business
                    $Cliente = $db_w2b->fetch_assoc('SELECT id FROM clientes WHERE cpf_cnpj = "'.$cpf_cnpj['cpf_cnpj'].'" AND parceiro_id = 244', $db_w2b->link-id);

                    //Array para enviar SMS
                    array_push($DadosSMS, array('ClienteId' => $Cliente['id'], 'Tpmsg' => 0)); 
                    $ok +=1;       
                }

            } // Fim verificação se o existe honorários 5 dias a frente para vencer.
        
        } // Fim da verificação se é multiplo de 5

        //Chama a função de enviar SMS.
        if($ok > 1){
            self::EnvioSMSEmail($DadosSMS);
            //print_r($DadosSMS);
        }
        $dbLexdata->close(); 

        $db_w2b->close(); 
    }


    /**
    *@Rotina para atualizar boletos dos clientes para envio de lembretes de pgto
    */
    function RotinaAtualizarBoletos(){
        
        $hoje = date('Y-m-d'); //'2017-01-01';
        $vecimento_5_dias = date('Y-m-d', strtotime("+5 days",strtotime($hoje))); //'2017-07-19';

        //Conexão no Sufol
        $conexao = "host=177.53.174.212 port=15780 dbname=dbsufol user=wf password=webfinancas";
        $dbSufol = pg_connect($conexao);
        
        //Campos: cnpjcpf, nossonumero, datavencimento, datapagamento, valorpago
        $query = pg_query($dbSufol, "SELECT cnpjcpf, nossonumero, datavencimento, datapagamento, valorapagar, valorpago FROM view_boletos WHERE datavencimento >= '".$hoje."' and datavencimento <= '".$vecimento_5_dias."' AND valorpago is not null order by datavencimento");
        
        //Busca os lançamentos no SUFOL
        $honorarios = pg_fetch_all($query);      
        
        //Conexão com o banco de dados da Lexdata
        $dbLexdata = new Database("mysql.webfinancas.com",self::LEXDATA_DB,"W2BSISTEMAS",self::LEXDATA_DB);
        
        //Instanciar a classe Lançamento
        //$lan = new Lancamento();

        //$lancamento = array();
        
        foreach($honorarios as $honorario)
        { 
            //Formatando dados do SUFOL
            $nossoNumero = ltrim($honorario['nossonumero'],'0');            
            
            //Pega a id do lancamento dentro da tabela boletos
            $lanc_id = $dbLexdata->fetch_assoc("SELECT lancamento_id, nosso_numero FROM boletos WHERE nosso_numero = '".$nossoNumero."'");

            if($lanc_id['lancamento_id'] != null){
                
                //Verifica se o lançamento já está compensado
                $verificacao = $dbLexdata->fetch_assoc('SELECT compensado, conta_id FROM lancamentos WHERE id ='.$lanc_id['lancamento_id'].' AND compensado = 0');

                    if($verificacao['compensado'] != null){
                                              
                        //Identifica alterações feitas pela Lexdata nossonumero, datavencimento, valorapagar, datapagamento, valorpago
                        $dt_vencimento = date('Y-m-d',strtotime($honorario['datavencimento']));
                        $valor = number_format($honorario['valorapagar'], 2, '.', ''); 
                        $dt_compensacao = date('Y-m-d',strtotime($honorario['datapagamento']));
                        $valorPago = number_format($honorario['valorpago'], 2, '.', ''); 
               
                        //Atualizar lançamento no banco de dados da Lexdata no Web Finanças
                        $dbLexdata->query('UPDATE lancamentos SET dt_vencimento = "'.$dt_vencimento.'", valor = '.$valor.', valor_pago = '.$valorPago.', dt_compensacao = "'.$dt_compensacao.'", compensado = 1 WHERE id = '.$lanc_id['lancamento_id']);
                        
                        //Pega o saldo
                        $saldo_conta = $dbLexdata->fetch_assoc('SELECT vl_saldo FROM contas WHERE id = '.$verificacao['conta_id']);

                        $saldo = $saldo_conta['vl_saldo'];
                        $saldo = $saldo + $valorPago;

                        //Atualiza o saldo
                        $dbLexdata->query('UPDATE contas SET vl_saldo = '.$saldo.' WHERE id = '.$verificacao['conta_id']);

                    }
             }
        }
        
        //Chama a função de verificar quem esta em aberto ou a vencer para enviar SMS
        self::VerificarPgto();
    }


    /**
    *@Conexão Teste Lexdata PostGres
    *@Link:https://www.webfinancas.com/sistema/php/Route.php?Controller=HonorarioLexdata&Action=LexdataSufol
    */
    /* 
    function LexdataSufol(){

        //Conexão db do Sufol (Tipo db PostGree)
        $conexao = "host=177.53.174.212 port=15780 dbname=dbsufol user=wf password=webfinancas";
        $dbSufol = pg_connect($conexao);
        
        $NossoNumero = '000003034820';  
        $query = pg_query($dbSufol, "select * from view_boletos WHERE nossonumero = '".$NossoNumero."' LIMIT 1");
        //$query = pg_query($dbSufol, "select * from view_boletos where datavencimento >= '2017-07-01' and valorpago is null limit 1");
        
        $resultado = pg_fetch_all($query);
        var_dump($resultado);
        //var_dump(number_format($boletos['$resultado'], 2, '.', ''));
    }
   */


   /*
	================================================================================================
	ENVIAR EMAIL
	================================================================================================
	*/

    function emailEnviar($nome_remetente, $email_remetente, $nome_destinatario, $email_destinatario, $assunto, $conteudo){

    $enderecoArquivos = 'http://www.webfinancas.com/site/img/email_paginas/';

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
				<p style="line-height:20px;">'.$conteudo.'

				<br><br>	
				Atenciosamente,
				<br>	
				
					<b>Contabilidade</b>	
			</p>
						
			</td>    
			<td width="40%" align="center" valign="bottom"> 
				 <img src="'.$enderecoArquivos.'boleto.png" align="center" class="img" width="215" />	
			 </td>
			 
		</tr>
	</table>';

        
        $db_w2b = new Database('mysql.web2business.com.br','web2business','W2BSISTEMAS','web2business');  

        $dadosEmail['Sistema'] = 'WebFinanças - Honorários LexData';
        $dadosEmail['ClienteId'] = '244';

        $dadosEmail['nomeRemetente'] = $nome_remetente;
        $dadosEmail['emailRemetente'] = $email_remetente;
        $dadosEmail['nomeDestinatario'] = $nome_destinatario;
        $dadosEmail['emailDestinatario'] = $email_destinatario;
        $dadosEmail['assunto'] = $assunto;
        $dadosEmail['mensagem'] = $mensagem;        
        $dadosEmail['situacao'] = 0;
        
        if(!empty($dadosEmail['emailDestinatario'])){
            $db_w2b->query_insert('servico_envio_email', $dadosEmail, $db_w2b->link_id);
        }

        //$db_w2b->close();


	}



}
?>