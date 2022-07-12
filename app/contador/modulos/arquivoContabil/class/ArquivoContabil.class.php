<?php
class ArquivoContabil{
	
	var $geral_dados = array(
		"email"=>"",
		"senha"=>""
	);
	
	/*
	================================================================================================
	CONSTRUTOR
	================================================================================================
	*/

	function __construct(){
	}

	/*
	================================================================================================
	ENVIAR EMAIL
	================================================================================================
	*/

	function emailEnviar($email_destinatario,$assunto,$conteudo){

		/*	
        $conteudo = "conteudo de teste";
        $email_destinatario	= "fabio@web2business.com.br";
        $assunto = "Testando Radar Gourmet";
         */
		$email_remetente = "contato@webfinancas.com";
		$nome_remetente = "Web Finanças";
		
		/*=========== INICIALIZA O OBJETO QUE ENVIA O EMAIL =======================================*/
		$transport = Swift_SmtpTransport::newInstance('smtp.webfinancas.com', 587); //$transport = Swift_SmtpTransport::newInstance('smtp.web2business.com.br', 25);
		$transport->setUsername('contato@webfinancas.com');
		$transport->setPassword('W2BSISTEMAS');
		
		$message = Swift_Message::newInstance();
		$message->setSubject($assunto);
		$message->setFrom(array($email_remetente => $nome_remetente));
		//$message->setReturnPath('fabio@web2business.com.br');
        
		$mailer = Swift_Mailer::newInstance($transport);
		/*==============================================================================================*/
		
		$message->setBody($conteudo, 'text/html');
		$message->setTo(array($email_destinatario)); //não precisa limpar o destinatario a cada envio, esta função sobre-escreve o destinatario anterior
		//$message->setTo(array('fabio@web2business.com.br'));

		$mailer->send($message); 

	}

    

    /*
    ================================================================================================
    LISTAR CLIENTES
    ================================================================================================
    */

    function listarClientes($db){

		session_start(); 
    	$cliente_id = $_SESSION['cliente_id'];

		$raiz = "http://www.web2business.com.br/webfinancas/";
        $retorno = '';

		//Conexão no banco da Web 2 Business
		$db_w2b = new Database('mysql.web2business.com.br','web2business','W2BSISTEMAS','web2business');
	
		//Retorna a lista de clientes conectados
        $conexoes = $db->fetch_all_array('select id, cliente_id, email, remetente from conexao where contador_id = 0 and conectado = 1'); //0 = convite enviado, aguardando confirmação, 1 = conectado

        foreach($conexoes as $conexao){

            $cliente_id = $conexao['cliente_id'];
            $cliente_dados = $db_w2b->fetch_assoc('select nome from clientes where id ='.$cliente_id);

            $remetente_id = $_SESSION['cliente_id'];
                
			$cor = 'blue';
            
            //<a href="'.$remetente_id.'-'.$cliente_id.'" data-cliente-row-id="'.$cliente_id.'" original-title="Cancelar conexão" class="smallButton btTBwf redB tipS excluirConexao"><img src="'.$raiz.'images/icons/light/close.png" width="10"></a>
            $opcoes = '			        
					<a href="clientesDetalhes/'.$cliente_id.'" original-title="Visualizar" class="smallButton btTBwf greyishB tipS "><img src="'.$raiz.'images/icons/light/magnify.png" width="10"></a>
					<a href="javascript://void(0);" original-title="Bloquear Lançamentos" class="smallButton btTBwf greyishB tipS" onclick="abrirJanelaBloqueio('.$cliente_id.');"><img src="../../sistema/images/icons/light/locked2.png" width="10"></a>
			    ';
            
            $retorno .= '			
					<tr class="gradeA" id="cliente-row-'.$cliente_id.'">
						<td class="updates newUpdate">
							<a href="clientesDetalhes/'.$cliente_id.'" class="'.$cor.'" >
                                <div class="uDate tbWF" align="center">
                                    <img src="'.$raiz.'images/user.png" alt="">
                                </div>
								<span class="lDespesa tbWF">
									<strong>'.$cliente_dados['nome'].'</strong>
									<span>'.$conexao['email'].'</span>
								</span>
                            </a>
							<div class="tbWFoption">
        						'.$opcoes.'
							</div>
						</td> 
				</tr>		
            ';

        }

        $db_w2b->close();
		
		return $retorno;

    }




/*
    ===========================================================================================
    SALVAR CONFIGURAÇÃO PLANO DE CONTAS
    ===========================================================================================
    */	
	
	function salvarPlConfigContas($db,$array_dados){ 
		
		$dt_cadastro = date('Y-m-d H:i:s');
		
		/*========== Contas Financeiras ===========*/
		$countC = $array_dados['cfTotal'];
		
		$c = 1;
		while($c <= $countC){
			
			//Verifica se a conta já esta cadastrada na tabela do contador
			$contador_cf_cod = $db->fetch_assoc('select id from clientes_cf where cliente_id ='.$array_dados['cliente_id'].' and cliente_cf_id ='.$array_dados['cliente_cf_id'.$c]);
			
			if($contador_cf_cod == true){
				
			    //Atualiza o registro
			    $dados = array('contador_cf_cod' => $array_dados['contador_cf_cod'.$c], 'dt_cadastro' => $dt_cadastro);
			    $db->query_update('clientes_cf',$dados," cliente_id = ".$array_dados['cliente_id']." and cliente_cf_id =".$array_dados['cliente_cf_id'.$c]);
				
			}else{		
			
			    $dados = array('cliente_id' => $array_dados['cliente_id'], 'cliente_cf_id' => $array_dados['cliente_cf_id'.$c], 'contador_cf_cod' => $array_dados['contador_cf_cod'.$c], 'dt_cadastro' => $dt_cadastro);
			    $db->query_insert('clientes_cf', $dados);

			}

			$c+=1;			
			
		}
		

		$retorno = array("situacao" => 1,"notificacao"=>"Configuração salva com sucesso.");
		return $retorno;
		
	}	



		
	function salvarPlConfigPlano($db,$array_dados){ 
		
		$dt_cadastro = date('Y-m-d H:i:s');
		
		/*========== Plano de Contas ===========*/
		
		$countP = $array_dados['plTotal'];
		
		$c = 1;
		while($c <= $countP){
			
			//Verifica se a conta já esta cadastrada na tabela do contador
			$contador_pl_cod = $db->fetch_assoc('select id from clientes_pl_config where cliente_id ='.$array_dados['cliente_id'].' and cliente_pl_id ='.$array_dados['cliente_pl_id'.$c]);	
			
			if($contador_pl_cod == true){
				
			    //Atualiza o registro
			    $dados = array('contador_pl_cod' => $array_dados['contador_pl_cod'.$c], 'dt_cadastro' => $dt_cadastro);
			    $db->query_update('clientes_pl_config', $dados, "cliente_id = ".$array_dados['cliente_id']." and cliente_pl_id = ".$array_dados['cliente_pl_id'.$c]);
				
			}else{	
			
			    $dados = array('cliente_id' => $array_dados['cliente_id'], 'cliente_pl_id' => $array_dados['cliente_pl_id'.$c], 'contador_pl_cod' => $array_dados['contador_pl_cod'.$c], 'dt_cadastro' => $dt_cadastro);
			    $db->query_insert('clientes_pl_config', $dados);

			}
			
		    $c+=1;
		}
		
		

		$retorno = array("situacao" => 1,"notificacao"=>"Configuração salva com sucesso.");
		return $retorno;
		
	}	




	function salvarPlConfigFavorecido($db,$array_dados){ 
		
		$dt_cadastro = date('Y-m-d H:i:s');
		
		/*========== Favorecidos ===========*/
		
		$countF = $array_dados['favTotal']; 

		$c = 1;
		while($c <= $countF){
			
			
			//Verifica se a conta já esta cadastrada na tabela do contador
			$contador_fav_cod = $db->fetch_assoc('select id from clientes_favorecidos where cliente_id ='.$array_dados['cliente_id'].' and cliente_favorecido_id = '.$array_dados['cliente_favorecido_id'.$c]);


			if($contador_fav_cod == true){
				
			    //Atualiza o registro
			    $dados = array('contador_cliente_cod' => $array_dados['contador_cliente_cod'.$c], 'contador_fornecedor_cod' => $array_dados['contador_fornecedor_cod'.$c], 'dt_cadastro' => $dt_cadastro);
			    $db->query_update('clientes_favorecidos', $dados, "cliente_id = ".$array_dados['cliente_id']." and cliente_favorecido_id = ".$array_dados['cliente_favorecido_id'.$c]);
				
			}else{	
                
			    $dados = array('cliente_id' => $array_dados['cliente_id'], 'cliente_favorecido_id' => $array_dados['cliente_favorecido_id'.$c], 'contador_cliente_cod' => $array_dados['contador_cliente_cod'.$c],'contador_fornecedor_cod' => $array_dados['contador_fornecedor_cod'.$c], 'dt_cadastro' => $dt_cadastro);
				$db->query_insert('clientes_favorecidos', $dados);
				
			}
			
		    $c+=1;	
		}
		
		
		

		$retorno = array("situacao" => 1,"notificacao"=>"Configuração salva com sucesso.");
		return $retorno;
		
	}	







  
    /*
    ===========================================================================================
    SALVAR CONFIGURAÇÃO PLANO DE CONTAS
    ===========================================================================================
    */	
	
	function salvarPlConfig($db,$array_dados){ 
		
		$dt_cadastro = date('Y-m-d H:i:s');
		
		/*========== Contas Financeiras ===========*/
		$countC = $array_dados['cfTotal'];
		
		$c = 1;
		while($c <= $countC){
			
			//Verifica se a conta já esta cadastrada na tabela do contador
			$contador_cf_cod = $db->fetch_assoc('select id from clientes_cf where cliente_id ='.$array_dados['cliente_id'].' and cliente_cf_id ='.$array_dados['cliente_cf_id'.$c]);
			
			if($contador_cf_cod == true){
				
			    //Atualiza o registro
			    $dados = array('contador_cf_cod' => $array_dados['contador_cf_cod'.$c], 'dt_cadastro' => $dt_cadastro);
			    $db->query_update('clientes_cf',$dados," cliente_id = ".$array_dados['cliente_id']." and cliente_cf_id =".$array_dados['cliente_cf_id'.$c]);
				
			}else{		
			
			    $dados = array('cliente_id' => $array_dados['cliente_id'], 'cliente_cf_id' => $array_dados['cliente_cf_id'.$c], 'contador_cf_cod' => $array_dados['contador_cf_cod'.$c], 'dt_cadastro' => $dt_cadastro);
			    $db->query_insert('clientes_cf', $dados);

			}

			$c+=1;			
			
		}
		
		/*========== Plano de Contas ===========*/
		
		$countP = $array_dados['plTotal'];
		
		$c = 1;
		while($c <= $countP){
			
			//Verifica se a conta já esta cadastrada na tabela do contador
			$contador_pl_cod = $db->fetch_assoc('select id from clientes_pl_config where cliente_id ='.$array_dados['cliente_id'].' and cliente_pl_id ='.$array_dados['cliente_pl_id'.$c]);	
			
			if($contador_pl_cod == true){
				
			    //Atualiza o registro
			    $dados = array('contador_pl_cod' => $array_dados['contador_pl_cod'.$c], 'dt_cadastro' => $dt_cadastro);
			    $db->query_update('clientes_pl_config', $dados, "cliente_id = ".$array_dados['cliente_id']." and cliente_pl_id = ".$array_dados['cliente_pl_id'.$c]);
				
			}else{	
			
			    $dados = array('cliente_id' => $array_dados['cliente_id'], 'cliente_pl_id' => $array_dados['cliente_pl_id'.$c], 'contador_pl_cod' => $array_dados['contador_pl_cod'.$c], 'dt_cadastro' => $dt_cadastro);
			    $db->query_insert('clientes_pl_config', $dados);

			}
			
		    $c+=1;
		}
		
	

        /*========== Favorecidos ===========*/
		
		$countF = $array_dados['favTotal']; 

		$c = 1;
		while($c <= $countF){
			
			
			//Verifica se a conta já esta cadastrada na tabela do contador
			$contador_fav_cod = $db->fetch_assoc('select id from clientes_favorecidos where cliente_id ='.$array_dados['cliente_id'].' and cliente_favorecido_id = '.$array_dados['cliente_favorecido_id'.$c]);


			if($contador_fav_cod == true){
				
			    //Atualiza o registro
			    $dados = array('contador_cliente_cod' => $array_dados['contador_cliente_cod'.$c], 'contador_fornecedor_cod' => $array_dados['contador_fornecedor_cod'.$c], 'dt_cadastro' => $dt_cadastro);
			    $db->query_update('clientes_favorecidos', $dados, "cliente_id = ".$array_dados['cliente_id']." and cliente_favorecido_id = ".$array_dados['cliente_favorecido_id'.$c]);
				
			}else{	
                
			    $dados = array('cliente_id' => $array_dados['cliente_id'], 'cliente_favorecido_id' => $array_dados['cliente_favorecido_id'.$c], 'contador_cliente_cod' => $array_dados['contador_cliente_cod'.$c],'contador_fornecedor_cod' => $array_dados['contador_fornecedor_cod'.$c], 'dt_cadastro' => $dt_cadastro);
				$db->query_insert('clientes_favorecidos', $dados);
				
			}
			
		    $c+=1;	
		}
		

		$retorno = array("situacao" => 1,"notificacao"=>"Configuração salva com sucesso.");
		return $retorno;
		
	}	
    
    //VISUALIZAR LANCAMENTOS CONTABILIDADE
    //===========================================================================================

    function visualizarLancamentos($db,$array_dados){

		//Instancia com a provisão false
		$is_provisao = false;

        //Conexão no banco do Web Finanças
        $db_wfp = new Database('mysql.webfinancas.com','webfinancas','W2BSISTEMAS','webfinancas');
        
        //Formata a data de inicio
		$ini = explode('/',$array_dados["mes_ini"]);
		$dia_ini = $ini[0];
        $mes_ini = $ini[1];
		$ano_ini = $ini[2];

		$competencia_ini = $ano_ini.'-'.$mes_ini.'-01';
		
		$dt_ini = $ano_ini.'-'.$mes_ini.'-'.$dia_ini;
        
        //Formata a data final
		$fim = explode('/',$array_dados["mes_fim"]);
		$dia_fim = $fim[0];
        $mes_fim = $fim[1];
		$ano_fim = $fim[2];

		$competencia_fim = $ano_fim.'-'.$mes_fim.'-01';
		
		$dt_fim = $ano_fim.'-'.$mes_fim.'-'.$dia_fim;
        
        $cliente_id = $array_dados['cliente_id'];
        
        $db_dados_cliente = $db_wfp->fetch_assoc('select db, db_senha from clientes_db where cliente_id = '.$cliente_id);

        //Verifica qual é o tratamento contábil e altera a busca dos lançamentos
        $rtc = $array_dados['rtc'];
        $ptc = $array_dados['ptc'];
        
        //Conexão com o db do cliente
        $db_cliente =  new Database('mysql.webfinancas.com',$db_dados_cliente['db'],$db_dados_cliente['db_senha'],$db_dados_cliente['db']);

        //Seleciona contas financeiras e meses que foram liberadas pelo cliente  
		$query_remessa_contabil = '
		    select conta_id, mes, ano
		    from remessa_contabil
		    where operacao = 1
			    and ( (mes >= "'.$mes_ini.'" and ano >= "'.$ano_ini.'") and (mes <= "'.$mes_fim.'" and ano <= "'.$ano_fim.'") )
			    and conta_id in ('.$array_dados['cf_id'].')
	    ';

        $remessa_contabil = $db_cliente->fetch_all_array($query_remessa_contabil);

        //busca lançamentos

        //obs: Para simplificar a lógica do select, a verificação se o lançamento está compensado, para diferenciar entre provisão e baixa, é feita ao percorrer o array de lançamento retornados pela query,
        //pois como a busca não é feita utilizando índices, todos os registros da tabela serão verificados, desta forma, o ganho de performance colocando restrições na cláusula where não compensa.
        //Estudar depois a inclusão de um índice baseado na data para evitar a verificação de todos os registros da tabela.

        //tabela temporaria para armazenar lançamentos
        $db->query("
		    CREATE TEMPORARY TABLE lancamentos_rc_temp (
			    id int PRIMARY KEY AUTO_INCREMENT,
			    tipo char(1),
                descricao varchar(255),
                lancamento_pai_id int(11),
                qtd_parcelas int(3),
                valor decimal(10,2) NOT NULL,
                conta_id int(11),
                conta_id_origem int(11),
                conta_id_destino int(11),
                favorecido_id int(11),
                compensado tinyint(1),
				dt_compensacao date NOT NULL,
				dt_competencia date NOT NULL,
				dt_emissao date NOT NULL,
				dt_vencimento date NOT NULL,
				dt_ordem date NOT NULL,
				provisao int(11) NOT NULL
		    ) ENGINE=MEMORY
	    ");
        
        $lancamentos = false;
        $array_cf_id_fechado = array();

        foreach($remessa_contabil as $rc){
            
            $cf_id = $rc['conta_id'];
            $mes = $rc['mes'];
			$ano = $rc['ano'];
			
            //$dt_ini = $ano.'-'.$mes.'-01';
            //$dt_fim_ts = mktime(0,0,0,$mes+1,'00',$ano);
            //$dt_fim = date('Y-m-d',$dt_fim_ts);

			$provisao_baixa = '( dt_compensacao BETWEEN "'.$dt_ini.'" and "'.$dt_fim.'" and compensado = 1 ) or ( dt_vencimento BETWEEN "'.$dt_ini.'" and  "'.$dt_fim.'" and compensado = 0 )';
            $cf = 'and ( conta_id in ('.$cf_id.') or conta_id_origem in ('.$cf_id.') or conta_id_destino in ('.$cf_id.') )';

            $query_lancamentos = '
			    select id, tipo, descricao, lancamento_pai_id, qtd_parcelas, valor, conta_id, conta_id_origem, conta_id_destino, favorecido_id, dt_competencia, compensado, dt_compensacao, dt_emissao, dt_vencimento, if(compensado=0, dt_vencimento, dt_compensacao) dt_ordem
			    from lancamentos
			    where '.$provisao_baixa.' '.$cf.' ORDER BY dt_ordem';

            $lancamentos = $db_cliente->fetch_all_array($query_lancamentos);
			
            foreach($lancamentos as $lancamento){
                $db->query_insert('lancamentos_rc_temp', $lancamento);
            }

            array_push($array_cf_id_fechado, $cf_id);

        }

        $cf_id_fechado = join(',', $array_cf_id_fechado);
		
        if($lancamentos){

            $query_lancamentos = 'select *, date_format(dt_compensacao, "%d/%m/%Y") as dt_compensacao, date_format(dt_vencimento, "%d/%m/%Y") as dt_vencimento, if(compensado=0, dt_vencimento, dt_compensacao) dt_ordem from lancamentos_rc_temp order by dt_ordem ASC';
            
			$lancamentos = $db->fetch_all_array($query_lancamentos);

            $db->query('drop table lancamentos_rc_temp');

            $palco = '
			    <form id="form_gerar_arquivo">
				    <input type="hidden" name="funcao" value="gerarArquivoContabil" />
				    <input type="hidden" name="tratamentoContabil" value="'.$array_dados['tc'].'" />
				    <input type="hidden" name="cf_id" value="'.$cf_id_fechado.'" />
				    <input type="hidden" name="mes_ini" value="'.$array_dados['mes_ini'].'" />
				    <input type="hidden" name="mes_fim" value="'.$array_dados['mes_fim'].'" />
                    <input type="hidden" name="cliente_id" value="'.$array_dados['cliente_id'].'" />
		    ';

            $loteAtual = 1;
            $nLote = 0;
            $controleLote = '';

            //fazer lógica para busca na tabela temporária de forma ordenada
			

            foreach($lancamentos as $lanc){
                
                $l_tipo = $lanc['tipo']; //Tipo do lançamento

                //Verifica se o lançamento preenche as condições do tratamento contábil selecionado

                if( $lanc['compensado']==1 //baixas
                    || ($l_tipo=='R' &&  $lanc['compensado']==0 && $rtc==1) //recebimentos provisionados
					|| ($l_tipo=='P' &&  $lanc['compensado']==0 && $ptc==1) ) //pagamentos provisionados

                {	
					/** Comentado porque estava gerando inconsistencia na baixa contra cliente e baixa contra fornecedor */
                   // if($lanc['lancamento_pai_id'] == $lanc['id'] || $lanc['lancamento_pai_id'] == 0) //Verifica se o lançamento não é parcelado ou se é a primeira parcela
                   // {


                        if( ($lanc['compensado'] == 0 && ($l_tipo == 'R' || $l_tipo == 'P')) || $lanc['compensado'] == 1 ) //Ignora o registro se for uma transferência programada
                        {
                            //Disponibiliza as datas referente ao tipo de tratamento contábil

                            if($lanc['compensado'] == 1){ 
                                $data = $lanc['dt_compensacao'];
                            }else{ 
                                $data = $lanc['dt_vencimento'];
                            }
                            
                            $cLote = explode("/", $data); 
                            $mes = $cLote['1']; //Mês para controle 
                            
                            if($mes !== $controleLote){

                                //pega a quantidade de lotes e registros de cada lote
                                
                                $nLote += 1;
                                
                                $controleLote = $mes; //Mês para controle 
                                
                                $palco .= '</tbody> </table> </div>
			
			                <div class="widget" align="center">
			                <div class="title" ><img src="https://app.webfinancas.com/sistema/images/icons/dark/frames.png" alt="" class="titleIcon"><h6>Lote '.$nLote.'</h6></div>
	
			                <table cellpadding="0" cellspacing="0" width="100%" class="sTable withCheck tbl-lote" data-lote="'.$nLote.'" id="tbl-lote-'.$nLote.'">
			                    <thead>
					                    <tr>
							                    <td><img src="https://app.webfinancas.com/sistema/images/icons/tableArrows.png" alt=""></td>
							                    <td width="20">Tipo</td>
                                                <td width="80">Data</td>
							                    <td width="100">Débito</td>
							                    <td width="100">Crédito</td>
							                    <td width="180">Valor</td>
							                    <td>Histórico</td>
					                    </tr>
			                    </thead>
			                    <tbody>';		
                                
                            }
                            
                            if($nLote > $loteAtual){ //inclui a quantidade de id quando só existe mais de 1 lote
                                
                                $palco .= '<input type="hidden" name="lote'.$loteAtual.'" value="'.$id.'" />'; 
                                $id = 0; 
                                $loteAtual += 1;
                                
                            } 
                            
                            $id +=1; //Controle para colocar o número do indice

                            //======= Verificação para Tratamento Contábil ==========================================

                            //Pega o código do plano de contas do lancamento no db do cliente

                            $pl_id_cliente = $db_cliente->fetch_assoc('select plano_contas_id from ctr_plc_lancamentos where lancamento_id = '.$lanc['id']);
                            $de_para_contador = $db->fetch_assoc('select contador_pl_cod from clientes_pl_config where cliente_id = '.$cliente_id.' AND cliente_pl_id = '.$pl_id_cliente['plano_contas_id']);

                            //Pega o código da conta financeira
                            //--------------------------------------------------------------------------------------------------------------

                            if($l_tipo == 'R' || $l_tipo == 'P' ){ 
                                
                                $conta_financeira_contador = $db->fetch_assoc('select contador_cf_cod from clientes_cf where cliente_id = '.$cliente_id.' AND cliente_cf_id = '.$lanc['conta_id']); 
                                
                            }else{			
                                
                                //Pega os códigos das contas e origem de débito de uma transferencia no pl configurações  .$lanc['conta_id_origem']"'.$cliente_id.'"
                                $conta_financeira_contador = $db->fetch_assoc('select contador_cf_cod from clientes_cf where cliente_id = '.$cliente_id.' AND cliente_cf_id = '.$lanc['conta_id_origem']); 
                                //Pega os códigos das contas e destino de débito de uma transferencia no pl configurações  .$lanc['conta_id_destino']
                                $conta_financeira_contador2 = $db->fetch_assoc('select contador_cf_cod from clientes_cf where cliente_id = '.$cliente_id.' AND cliente_cf_id = '.$lanc['conta_id_destino']);		
                                
                            }

                            //Pega o código do favorecido
                            //--------------------------------------------------------------------------------------------------------------
                            $contador_fav_cod = $db->fetch_assoc('select contador_cliente_cod, contador_fornecedor_cod from clientes_favorecidos where cliente_id = '.$cliente_id.' and cliente_favorecido_id = '.$lanc['favorecido_id']);

                            // Relaciona código contábil com o plano de contas financeiro
                            //--------------------------------------------------------------------------------------------------------------


                            if($l_tipo == 'R')
                            {
                                if($rtc==1) //Provisão e baixa
                                { 
                                    if($lanc['compensado']==1){

                                        $debito = $conta_financeira_contador['contador_cf_cod'];
                                        $credito =  $contador_fav_cod['contador_cliente_cod'];

                                        $dt_emissao = explode('/', $lanc['dt_vencimento']);
                                        $dt_compensacao = explode('/', $lanc['dt_compensacao']);
                                        if($dt_emissao['1']==$dt_compensacao['1'] && $dt_emissao['2']==$dt_compensacao['2']){
                                            $debito_provisao = $contador_fav_cod['contador_cliente_cod'];
                                            $credito_provisao =  $de_para_contador['contador_pl_cod'];
                                            $is_provisao = true;
                                        }

                                    }else{
                                        
                                        $debito = $contador_fav_cod['contador_cliente_cod'];
                                        $credito =  $de_para_contador['contador_pl_cod'];

                                    }
                                }
                                elseif($rtc==2 && $lanc['compensado']==1) //Baixa contra cliente
                                { 
                                    $debito = $conta_financeira_contador['contador_cf_cod']; //buscar código contábil da conta financeira
                                    $credito = $contador_fav_cod['contador_cliente_cod'];
                                }
                                elseif($rtc==3 && $lanc['compensado']==1) //Baixa contra receita
                                { 
                                    $debito = $conta_financeira_contador['contador_cf_cod']; //buscar código contábil da conta financeira
                                    $credito =  $de_para_contador['contador_pl_cod']; //buscar código contábil do plano de contas
                                }

                            }elseif($l_tipo == 'P'){
                                
                                if($ptc==1) //Provisão e baixa
                                { 
                                    if($lanc['compensado']==1)
                                    {
                                        $debito =  $contador_fav_cod['contador_fornecedor_cod']; //buscar código contábil do plano de contas
                                        $credito = $conta_financeira_contador['contador_cf_cod'];

                                        $dt_emissao = explode('/', $lanc['dt_vencimento']);
                                        $dt_compensacao = explode('/', $lanc['dt_compensacao']);
                                        if($dt_emissao['1']==$dt_compensacao['1'] && $dt_emissao['2']==$dt_compensacao['2']){
                                            $debito_provisao = $de_para_contador['contador_pl_cod'];
                                            $credito_provisao =  $contador_fav_cod['contador_fornecedor_cod'];
                                            $is_provisao = true;
                                        }

                                    }else{

                                        $debito = $de_para_contador['contador_pl_cod'];
                                        $credito =  $contador_fav_cod['contador_fornecedor_cod'];

                                    }
                                }
                                elseif($ptc==2 && $lanc['compensado']==1) //Baixa contra fornecedor
                                { 
                                    $debito = $contador_fav_cod['contador_fornecedor_cod'];
                                    $credito = $conta_financeira_contador['contador_cf_cod']; //buscar código contábil da conta financeira

                                }
                                elseif($ptc==3 && $lanc['compensado']==1) //Baixa contra despesa
                                { 
                                    $debito =  $de_para_contador['contador_pl_cod']; //buscar código contábil do plano de contas
                                    $credito = $conta_financeira_contador['contador_cf_cod']; //buscar código contábil da conta financeira
                                }

                            }
                            elseif($lanc['compensado']==1) //Transferência compensada - sempre débito na conta de destino e crédito na conta de origem
                            { 
                                $debito = $conta_financeira_contador2['contador_cf_cod'];
                                $credito = $conta_financeira_contador['contador_cf_cod'];

                            }

							//start: Calcula o valor total de um lançamento parcelado		XX **	//&& ( $lanc['dt_competencia'] == $competencia_ini || $lanc['dt_competencia'] == $competencia_fim ) 			   
							if($is_provisao == true && $lanc['id'] == $lanc['lancamento_pai_id'] ){
								$valorProvisao = $lanc['valor'] * $lanc['qtd_parcelas'];
								$valor = $lanc['valor'];

							}else{						
								$valorProvisao = $lanc['valor'];		
								$valor = $lanc['valor']; 
							}							
							//end: Calcula o valor total de um lançamento parcelado			**

                            // Nome do Favorecido
                            $favorecido = $db_cliente->fetch_assoc('Select nome FROM favorecidos where id ='.$lanc['favorecido_id']);


                            if($is_provisao && ( $lanc['id'] == $lanc['lancamento_pai_id'] || $lanc['lancamento_pai_id'] == 0 ) ){

                                $palco .= '
				                <tr data-lancamento-id="'.$id.'" class="tr-lote-'.$nLote.'">
					                <td align="center">'.$id.' <input type="hidden" name="id'.$nLote.'-'.$id.'" value="'.$id.'" /></td>
					                <td align="center">'.$lanc['tipo'].'</td>
                                    <td align="center"><input type="text" name="data'.$nLote.'-'.$id.'" value="'.$data.'"  style="text-align:center;" id="data-'.$id.'"/></td>
					                <td align="center"><input type="text" name="debito'.$nLote.'-'.$id.'" value="'.$debito_provisao.'"  style="text-align:center;" id="debito-'.$id.'"/></td>
					                <td align="center"><input type="text" name="credito'.$nLote.'-'.$id.'" value="'.$credito_provisao.'"  style="text-align:center;" id="credito-'.$id.'"/></td>
					                <td align="center"><input type="text" name="valor'.$nLote.'-'.$id.'" value="'.number_format($valorProvisao,2,',','.').'"  style="text-align:right;padding-right:4px;" id="valor-'.$id.'"/></td>
					                <td align="left">
                                        <input type="text" name="descricao'.$nLote.'-'.$id.'" value="'.$lanc['descricao'].' - '.$favorecido['nome'].'"  style="padding-left:4px;" id="descricao-'.$id.'"/>
							            <input type="hidden" name="d_c'.$nLote.'-'.$id.'" value="'.$lanc['tipo'].'" id="tipo-'.$id.'"/>
						            </td>
				                </tr>
								';
/*								
								$palco .= '
				            <tr data-lancamento-id="'.$id.'" class="tr-lote-'.$nLote.'">
					            <td align="center">'.$id.' <input type="hidden" name="id'.$nLote.'-'.$id.'" value="'.$id.'"/></td>                                
					            <td align="center">'.$lanc['tipo'].'</td>
					            <td align="center"><input type="text" name="data'.$nLote.'-'.$id.'" value="'.$data.'"  style="text-align:center;" id="data-'.$id.'" style="text-align:center;"/></td>
					            <td align="center"><input type="text" name="debito'.$nLote.'-'.$id.'" value="'.$debito.'"  style="text-align:center;" id="debito-'.$id.'"/></td>
					            <td align="center"><input type="text" name="credito'.$nLote.'-'.$id.'" value="'.$credito.'"  style="text-align:center;" id="credito-'.$id.'"/></td>
					            <td align="center"><input type="text" name="valor'.$nLote.'-'.$id.'" value="'.number_format($valorProvisao,2,',','.').'"  style="text-align:right;padding-right:4px;" id="valor-'.$id.'"/></td>
					            <td align="left">
                                    <input type="text" name="descricao'.$nLote.'-'.$id.'" value="'.$lanc['descricao'].' - '.$favorecido['nome'].'"  style="padding-left:4px;" id="descricao-'.$id.'"/>
							        <input type="hidden" name="d_c'.$nLote.'-'.$id.'" value="'.$lanc['tipo'].'" id="tipo-'.$id.'"/>
						        </td>
				            </tr>	
							';
*/
                                $id++;
								$is_provisao = false;
								
							
							}
							

								$palco .= '
								<tr data-lancamento-id="'.$id.'" class="tr-lote-'.$nLote.'">
									<td align="center">'.$id.' <input type="hidden" name="id'.$nLote.'-'.$id.'" value="'.$id.'"/></td>                                
									<td align="center">'.$lanc['tipo'].'</td>
									<td align="center"><input type="text" name="data'.$nLote.'-'.$id.'" value="'.$data.'"  style="text-align:center;" id="data-'.$id.'" style="text-align:center;"/></td>
									<td align="center"><input type="text" name="debito'.$nLote.'-'.$id.'" value="'.$debito.'"  style="text-align:center;" id="debito-'.$id.'"/></td>
									<td align="center"><input type="text" name="credito'.$nLote.'-'.$id.'" value="'.$credito.'"  style="text-align:center;" id="credito-'.$id.'"/></td>
									<td align="center"><input type="text" name="valor'.$nLote.'-'.$id.'" value="'.number_format($valor,2,',','.').'"  style="text-align:right;padding-right:4px;" id="valor-'.$id.'"/></td>
									<td align="left">
										<input type="text" name="descricao'.$nLote.'-'.$id.'" value="'.$lanc['descricao'].' - '.$favorecido['nome'].'"  style="padding-left:4px;" id="descricao-'.$id.'"/>
										<input type="hidden" name="d_c'.$nLote.'-'.$id.'" value="'.$lanc['tipo'].'" id="tipo-'.$id.'"/>
									</td>
								</tr>	
								';

							

						} 
						
                        
                   // }//Fim da verificação se o lançamento parcelado em questão é o principal ou se não é parcelado 
                    
                }	
                
            }

            $palco .= '<input type="hidden" name="lote'.$loteAtual.'" value="'.$id.'" />'; //inclui a quantidade de id quando só existe 1 lote
            
            $palco .= '</tbody> 
				    </table> 
			    </div>
			    <input type="hidden" name="totalLote" value="'.$nLote.'" id="totalLote"/>
		        </form>
	
		        <a href="javascript://" class="wContentButton greenwB" onClick="gerar_arquivo_contabil();">Gerar arquivo contábil</a> <br><br>';

        }else{ 
            
            $palco = "<br><br> <div width='100%' align='center'> <h5>Não existem lançamentos para o período informado.</h5> </div> <br><br>"; 
            
        }
        
        $retorno = array("situacao" => 1, "palco" => $teste.$palco);
        
        return $retorno;
    }

    /*
    ===========================================================================================
    GERAR ARQUIVO
    ===========================================================================================
    */	
    
    function gerarArquivoContabil($db,$array_dados){

        //return array('situacao'=>'', 'notificacao'=>'cliente_id: '.$array_dados['cliente_id'], 'download' =>'');

	    //Pega o nome do cliente
	    $db_w2b = new Database('mysql.web2business.com.br','web2business','W2BSISTEMAS','web2business');
	    $nomeCliente = $db_w2b->fetch_assoc('select nome from clientes where id = '.$array_dados['cliente_id']);
	    $db_w2b->close();

	    //Cria pasta para colocar os TXT
	    $nomePasta = 'cod_'.$array_dados['cliente_id'].'_'.date('d-m-y');
        
	    mkdir('../txt/'.$nomePasta, 0777); //Cria a pasta
	    chmod('../txt/'.$nomePasta, 0777); //Habilita a permissão de leitura, modificação para todo mundo

	    //Gera o arquivo ZIP
	    $zip = new ZipArchive();
	    $diretorioZip = '../zip/';
	    $nomeArquivoZIP = $nomeCliente['nome'].'_'.date('d-m-y').'.zip';
	    $zip->open($diretorioZip.$nomeArquivoZIP, ZIPARCHIVE::CREATE);
        
        $i = 1;
        while($i <= $array_dados['totalLote']){
            
		    $nomeArquivoTXT	= 'Lote_'.$i.'.txt'; //nome do arquivo TXT
		    $arquivoTXT = '../txt/'.$nomePasta.'/'.$nomeArquivoTXT; //Diretório do arquivo TXT
		    $fp = fopen($arquivoTXT,"a+"); // Cria o arquivo TXT
            
            $jsonTxt = str_replace('\"','"',$array_dados['lote'.$i]);
            $jsonObj = json_decode($jsonTxt, true);
            $arrayLancamento = $jsonObj;

            foreach($arrayLancamento as $lancamento){

                //Espaçamento dentro do arquivo TXT
			    $id = str_pad( $lancamento['id'], 7 ); // 5
                
			    $data = str_pad( $lancamento['data'], 11 ); //11
                
                $credito = str_pad( $lancamento['credito'], 8 ); //
			    
                $debito = str_pad( $lancamento['debito'], 8 );
                
			    $descricao = str_pad( utf8_decode($lancamento['descricao']), 101 ); // 60
                
			    $valor = str_pad( $lancamento['valor'], 73 ); // 73

			    if($lancamento['tipo'] = 'R'){ 
                    
                    $r_c1 = 'C';  
                    $r_c2 = 'D'; 
                    
				    $d_c1 = str_pad( $r_c1, 1 );
				    $d_c2 = str_pad( $r_c2, 1 );
                    
				    $dados_arquivoTXT01 = $id.$data.$descricao.$credito.$valor.$d_c1;
				    $dados_arquivoTXT02 = $id.$data.$descricao.$debito.$valor.$d_c2;
                    
			    }else{ 
                    
                    $r_c1 = 'D'; 
                    $r_c2 = 'C'; 
                    
				    $d_c1 = str_pad( $r_c1, 1 );
				    $d_c2 = str_pad( $r_c2, 1 );
                    
				    $dados_arquivoTXT01 = $id.$data.$descricao.$debito.$valor.$d_c1;
				    $dados_arquivoTXT02 = $id.$data.$descricao.$credito.$valor.$d_c2;

			    }

			    fwrite($fp,$dados_arquivoTXT01."\r\n"); //."\r\n" - Adiciona as linhas dentro do arquivo TXT
			    fwrite($fp,$dados_arquivoTXT02."\r\n");

            }
            
            fclose($fp); //Finaliza o arquivo TXT
            
		    $zip->addFile($arquivoTXT,$nomeArquivoTXT); //Adiciona o arquivo TXT dentro do arquivo ZIP
            
		    $i += 1;

	    }
        
	    $zip->close(); //Fecha o arquivo ZIP

	    //Exclusão de arquivos e pastas TXT
        $array_txt = glob("../txt/".$nomePasta."/*.txt");
        if(count($array_txt)>0){
	        array_map('unlink', $array_txt);//remove todos os TXT da pasta criada temporáriamente
	        rmdir('../txt/'.$nomePasta);//remove a pasta que estavam os TXT após a compactação dos arquivos
        }

	    //-------------------------------------------------------------------------------------------------------------------
	    //Registra no histórico que o arquivo contábil foi gerado pela contabilidade
        
	    //Conexão com o db do Web Finanças
	    $db_wf =  new Database('mysql.webfinancas.com','webfinancas','W2BSISTEMAS','webfinancas');

	    //Conexão com o db do cliente
	    $cliente_id = $array_dados['cliente_id'];
	    $db_dados_cliente = $db_wf->fetch_assoc('select db, db_senha from clientes_db where cliente_id = '.$cliente_id);
	    $db_wf->close();
	    $db_cli =  new Database('mysql.webfinancas.com',$db_dados_cliente['db'],$db_dados_cliente['db_senha'],$db_dados_cliente['db']);

	    //Registrar no histórico a geração do arquivo contábil somente para as contas liberadas pelo cliente

	    //Formata a data de inicio
	    $ini = explode('/',$array_dados["mes_ini"]);
	    $mes_ini = $ini[0];
	    $ano_ini = $ini[1];
        
	    //Formata a data final
	    $fim = explode('/',$array_dados["mes_fim"]);
	    $mes_fim = $fim[0];
	    $ano_fim = $fim[1];

        $array_cf_id = explode(',',$array_dados['cf_id']); //as contas são fixadas no formulário de gerar lote
        
	    foreach($array_cf_id as $cf_id){
            
		    $flag = true;
		    $_mes = $mes_ini;
		    $_ano = $ano_ini;

		    while( $flag ){

			    //data final de cada mês para cálculo do saldo anterior
			    $dt_ini = $_ano.'-'.$_mes.'-01';
			    $dt_fim_ts = mktime(0,0,0,$_mes+1,'00',$_ano);
			    $dt_fim = date('Y-m-d',$dt_fim_ts);
                
			    $array_balanco = self::Balanco($db_cli,array('dt_ini'=>$dt_ini,'dt_fim'=>$dt_fim,'cf_id'=>$cf_id));
                
			    $array_insert = array(
				    'conta_id' => $cf_id,
				    'operacao' => 2,
				    'vl_rcbt' => $array_balanco['vl_rcbt'],
				    'qtd_rcbt' => $array_balanco['qtd_rcbt'],
				    'vl_pgto' => $array_balanco['vl_pgto'],
				    'qtd_pgto' => $array_balanco['qtd_pgto'],
				    'saldo' => $array_balanco['vl_balanco'],
				    'mes' => $_mes,
				    'ano' => $_ano,
				    'dt_cadastro' => date('Y-m-d H:i:s')
			    );

			    //verifica se a remessa já foi enviada
			    $is_exist_remessa = $db_cli->fetch_assoc('select id from remessa_contabil where conta_id = '.$cf_id.' and mes = '.$_mes.' and ano = '.$_ano.' and operacao = 2');

			    if($is_exist_remessa){
				    $db_cli->query_update('remessa_contabil',$array_insert,'id = '.$is_exist_remessa['id']);
			    }else{
				    $db_cli->query_insert('remessa_contabil',$array_insert);
			    }

			    $_mes++;

			    if($_mes > $mes_fim && $_ano == $ano_fim)
				    $flag = false;

			    if($_mes>12){
				    $_mes = 1;
				    $_ano++;
			    }
                
		    }

	    }

	    $db_cli->close();

	    $retorno = array("situacao" => 1, "notificacao" => "Arquivo gerado com sucesso.", "download" => "https://app.webfinancas.com/contador/modulos/arquivoContabil/paginas/download.php?fnamedownload=Arquivo_Contabil_".$nomeCliente['nome']."_".date('d_m_y')."&file=".$nomeArquivoZIP);

	    return $retorno;
        
    }

    /*
    function gerarArquivoContabil($db,$array_dados){ 

        //return array('situacao'=>'', 'notificacao'=>'cliente_id: '.$array_dados['cliente_id'], 'download' =>'');

	    //Pega o nome do cliente
	    $db_w2b = new Database('mysql.web2business.com.br','web2business','W2BSISTEMAS','web2business');
	    $nomeCliente = $db_w2b->fetch_assoc('select nome from clientes where id = '.$array_dados['cliente_id']);
	    $db_w2b->close();

	    //Cria pasta para colocar os TXT
	    $nomePasta = 'cod_'.$array_dados['cliente_id'].'_'.date('d-m-y');
	
	    mkdir('../txt/'.$nomePasta, 0777); //Cria a pasta
	    chmod('../txt/'.$nomePasta, 0777); //Habilita a permissão de leitura, modificação para todo mundo

	    //Gera o arquivo ZIP
	    $zip = new ZipArchive();
	    $diretorioZip = '../zip/';
	    $nomeArquivoZIP = $nomeCliente['nome'].'_'.date('d-m-y').'.zip';
	    $zip->open($diretorioZip.$nomeArquivoZIP, ZIPARCHIVE::CREATE);
	
 	    $i = 1;
         while($i <= $array_dados['totalLote']){ //141
	
		    $nomeArquivoTXT	= 'Lote_'.$i.'.txt'; //nome do arquivo TXT
		    $arquivoTXT = '../txt/'.$nomePasta.'/'.$nomeArquivoTXT; //Diretório do arquivo TXT
		    $fp = fopen($arquivoTXT,"a+"); // Cria o arquivo TXT
		
		    //==========
		    $c = 1;
		    while($c <= $array_dados['lote'.$i]){ 
			
			    //Espaçamento dentro do arquivo TXT
			    $id = str_pad( $array_dados['id'.$i.'-'.$c], 5 );
			
			    $data = str_pad( $array_dados['data'.$i.'-'.$c], 11 );
			
			    $debito = str_pad( $array_dados['debito'.$i.'-'.$c], 8 );
			
			    $descricao = str_pad( $array_dados['descricao'.$i.'-'.$c], 61 );
			
			    $credito = str_pad( $array_dados['credito'.$i.'-'.$c], 8 );
			
			    $valor = str_pad( $array_dados['valor'.$i.'-'.$c], 73 );

			    if($array_dados['d_c'.$i.'-'.$c] = 'R'){ 
                
                    $r_c1 = 'C';  
                    $r_c2 = 'D'; 
			
				    $d_c1 = str_pad( $r_c1, 1 );
				    $d_c2 = str_pad( $r_c2, 1 );
			
				    $dados_arquivoTXT01 = $id.$data.$descricao.$credito.$valor.$d_c1;
				    $dados_arquivoTXT02 = $id.$data.$descricao.$debito.$valor.$d_c2;
				
			    }else{ 
                
                    $r_c1 = 'D'; 
                    $r_c2 = 'C'; 
			
				    $d_c1 = str_pad( $r_c1, 1 );
				    $d_c2 = str_pad( $r_c2, 1 );
			
				    $dados_arquivoTXT01 = $id.$data.$descricao.$debito.$valor.$d_c1;
				    $dados_arquivoTXT02 = $id.$data.$descricao.$credito.$valor.$d_c2;

			    }

			    fwrite($fp,$dados_arquivoTXT01."\r\n"); //."\r\n" - Adiciona as linhas dentro do arquivo TXT
			    fwrite($fp,$dados_arquivoTXT02."\r\n");

			    $c += 1;

		    }
		    //==========	
		
  	        fclose($fp); //Finaliza o arquivo TXT
		
		    $zip->addFile($arquivoTXT,$nomeArquivoTXT); //Adiciona o arquivo TXT dentro do arquivo ZIP
		
		    $i += 1;

	    }
	
	    $zip->close(); //Fecha o arquivo ZIP

	    //Exclusão de arquivos e pastas TXT
        $array_txt = glob("../txt/".$nomePasta."/*.txt");
        if(count($array_txt)>0){
	        array_map('unlink', $array_txt);//remove todos os TXT da pasta criada temporáriamente
	        rmdir('../txt/'.$nomePasta);//remove a pasta que estavam os TXT após a compactação dos arquivos
        }

	    //-------------------------------------------------------------------------------------------------------------------
	    //Registra no histórico que o arquivo contábil foi gerado pela contabilidade
	
	    //Conexão com o db do Web Finanças
	    $db_wf =  new Database('mysql.webfinancas.com','webfinancas','W2BSISTEMAS','webfinancas');

	    //Conexão com o db do cliente
	    $cliente_id = $array_dados['cliente_id'];
	    $db_dados_cliente = $db_wf->fetch_assoc('select db, db_senha from clientes_db where cliente_id = '.$cliente_id);
	    $db_wf->close();
	    $db_cli =  new Database('mysql.webfinancas.com',$db_dados_cliente['db'],$db_dados_cliente['db_senha'],$db_dados_cliente['db']);

	    //Registrar no histórico a geração do arquivo contábil somente para as contas liberadas pelo cliente

	    //Formata a data de inicio
	    $ini = explode('/',$array_dados["mes_ini"]);
	    $mes_ini = $ini[0];
	    $ano_ini = $ini[1];
			
	    //Formata a data final
	    $fim = explode('/',$array_dados["mes_fim"]);
	    $mes_fim = $fim[0];
	    $ano_fim = $fim[1];

 	    $array_cf_id = explode(',',$array_dados['cf_id']); //as contas são fixadas no formulário de gerar lote
  
	    foreach($array_cf_id as $cf_id){
		
		    $flag = true;
		    $_mes = $mes_ini;
		    $_ano = $ano_ini;

		    while( $flag ){

			    //data final de cada mês para cálculo do saldo anterior
			    $dt_ini = $_ano.'-'.$_mes.'-01';
			    $dt_fim_ts = mktime(0,0,0,$_mes+1,'00',$_ano);
			    $dt_fim = date('Y-m-d',$dt_fim_ts);
			
			    $array_balanco = self::Balanco($db_cli,array('dt_ini'=>$dt_ini,'dt_fim'=>$dt_fim,'cf_id'=>$cf_id));
	 
			    $array_insert = array(
				    'conta_id' => $cf_id,
				    'operacao' => 2,
				    'vl_rcbt' => $array_balanco['vl_rcbt'],
				    'qtd_rcbt' => $array_balanco['qtd_rcbt'],
				    'vl_pgto' => $array_balanco['vl_pgto'],
				    'qtd_pgto' => $array_balanco['qtd_pgto'],
				    'saldo' => $array_balanco['vl_balanco'],
				    'mes' => $_mes,
				    'ano' => $_ano,
				    'dt_cadastro' => date('Y-m-d H:i:s')
			    );

			    //verifica se a remessa já foi enviada
			    $is_exist_remessa = $db_cli->fetch_assoc('select id from remessa_contabil where conta_id = '.$cf_id.' and mes = '.$_mes.' and ano = '.$_ano.' and operacao = 2');

			    if($is_exist_remessa){
				    $db_cli->query_update('remessa_contabil',$array_insert,'id = '.$is_exist_remessa['id']);
			    }else{
				    $db_cli->query_insert('remessa_contabil',$array_insert);
			    }

			    $_mes++;

			    if($_mes > $mes_fim && $_ano == $ano_fim)
				    $flag = false;

			    if($_mes>12){
				    $_mes = 1;
				    $_ano++;
			    }
			
		    }

	    }

	    $db_cli->close();

	    $retorno = array("situacao" => 1, "notificacao" => "Arquivo gerado com sucesso.", "download" => "https://www.webfinancas.com/contador/modulos/arquivoContabil/paginas/download.php?fnamedownload=Arquivo_Contabil_".$nomeCliente['nome']."_".date('d_m_y')."&file=".$nomeArquivoZIP);

	    return $retorno;
     
    }
    */

    /*
    ===========================================================================================
    EXCLUSÃO DE ARQUIVO ZIP GERADO PELO CONTADOR
    ===========================================================================================
    */	
    function excluirPastasZip(){
	    $nomePasta = "zip_".date("d-m-y", strtotime("-1 days"));
	    /* ==== Exclusão de arquivos e pastas ZIP ==== */
	    if(file_exists("../zip/".$nomePasta)){
		    array_map("unlink", glob("../zip/".$nomePasta."/*.zip"));//remove todos os ZIP da pasta criada temporáriamente
		    rmdir("../zip/".$nomePasta);//remove a pasta que estavam os ZIP após a compactação dos arquivos	
	    }
    }

    /*
    ===========================================================================================
    DOWNLOAD DE DOCUMENTOS
    ===========================================================================================
    */

    function DocumentosDownload($array_dados){

//var_dump($array_dados);

	    //dados para teste
	    /*
	    $array_dados = array(
		    'cliente_id' => 134,
		    'mes_ini' => '08/2015',
		    'mes_fim' => '08/2015',
		    'cf_id' => '1,2,3'
	    );
	    */
		
	    //Formata a data de inicio
	    $ini = explode('/',$array_dados["mes_ini"]);
	    $mes_ini = $ini[1];
	    $ano_ini = $ini[2];
	    $dt_inicio = $ano_ini.'-'.$mes_ini.'-01';
	    $dt_referencia_ini = '01/'.$mes_ini.'/'.$ano_ini;
			
	    //Formata a data final
	    $fim = explode('/',$array_dados["mes_fim"]);
	    $mes_fim = $fim[1];
	    $ano_fim = $fim[2];
	    $dt_fim_ts = mktime(0,0,0,$mes_fim+1,'00',$ano_fim);
	    $dt_final = date('Y-m-d',$dt_fim_ts);
	    $dt_referencia_fim = date('d/m/Y',$dt_fim_ts);

	    //Conexão com o db do Web Finanças
	    $db =  new Database('mysql.webfinancas.com','webfinancas','W2BSISTEMAS','webfinancas');

	    //Conexão com o db do cliente
	    $cliente_id = $array_dados['cliente_id'];
	    $db_dados_cliente = $db->fetch_assoc('select db, db_senha from clientes_db where cliente_id = '.$cliente_id);
	    $db_cli =  new Database('mysql.webfinancas.com',$db_dados_cliente['db'],$db_dados_cliente['db_senha'],$db_dados_cliente['db']);

	    //Seleciona contas financeiras que foram liberadas pelo cliente
	    $query_remessa_contabil = '
		    select distinct conta_id
		    from remessa_contabil
		    where operacao = 1
			    and ( (mes >= "'.$mes_ini.'" and ano >= "'.$ano_ini.'") and (mes <= "'.$mes_fim.'" and ano <= "'.$ano_fim.'") )
			    and conta_id in ('.$array_dados['cf_id'].')
	    ';

	    $remessa_contabil = $db_cli->fetch_all_array($query_remessa_contabil);

	    //Busca documentos agrupados por conta financeira e ordenado pela data do lançamento (VERIFICAR SE A DATA SERÁ DE ACORDO COM O TRATAMENTO CONTÁBIL)

	    $zip_name = $cliente_id.'_documentos.zip';
	    $zip_folder = '../zip/';
	    $zip_path = $zip_folder.$zip_name;
	    if(file_exists($zip_path))
		    unlink($zip_path);
	    $zip = new ZipArchive();
		$zip->open($zip_path, ZIPARCHIVE::CREATE);
		
	    $cont = 0;
	    foreach($remessa_contabil as $cf_id){
			
		    $query_documentos = '
			    select l.id, la.nome_arquivo, la.nome_arquivo_org, if(l.tipo = "R" || l.conta_id_destino = '.$cf_id['conta_id'].', 1, 2) as ordem, l.dt_compensacao
			    from lnct_anexos la
			    join lancamentos l on la.lancamento_id = l.id
			    where l.dt_compensacao >= "'.$dt_inicio.'" and l.dt_compensacao <= "'.$dt_final.'"
				    and l.conta_id = '.$cf_id['conta_id'].'
				    and l.compensado = 1
			    order by dt_compensacao, ordem, id
			    ';

		    $array_documentos = $db_cli->fetch_all_array($query_documentos);
		    $lnct_id = 0;
			$cont2 = 0;


		    foreach($array_documentos as $documento){

				$doc_path = '../../../../sistema/uploads/cliente_'.$cliente_id.'/'.$documento['nome_arquivo'];
				
				
			    if( $lnct_id == $documento['id'] ){
				    $cont2++;
				    $prefixo = $cont.'.'.$cont2;
			    }else{
				    $cont++;
				    $cont2 = 0;
				    $prefixo = $cont;
			    }

			    $file_name = $prefixo.'_'.$documento['nome_arquivo_org'];

			    $lnct_id = $documento['id'];
			
				$zip->addFile($doc_path, $file_name);
				
				$doc_path = "";
		    }
			
		}
		

	    //gera extrato
	    $params = array(
		    'dt_ini' => $dt_inicio,
		    'dt_fim' => $dt_final,
		    'dt_referencia_ini' => $dt_referencia_ini,
		    'dt_referencia_fim' => $dt_referencia_fim,
		    'contas_financeiras' => $array_dados['cf_id'],
		    'cliente_id' => $cliente_id
	    );
	    $pdf_nome = self::extrato($db_cli,$params);

	    //adiciona extrato ao arquivo zip
	    $doc_path = '../pdf/'.$pdf_nome;
		$zip->addFile($doc_path, 'Movimentacao_Financeira.pdf');
		
	    $zip->close();
	
	    //remove pdf
	    unlink($doc_path);
	
	    //Pega o nome do cliente
	    $db_w2b = new Database('mysql.web2business.com.br','web2business','W2BSISTEMAS','web2business');
	    $nomeCliente = $db_w2b->fetch_assoc('select nome from clientes where id = '.$cliente_id);
	    $db_w2b->close();
	
	    //retorna link para documentos compactados
	    return "https://app.webfinancas.com/contador/modulos/arquivoContabil/paginas/download.php?fnamedownload=Documentos_".$nomeCliente['nome']."_".date('d_m_y')."&file=".$zip_name;

    }

    /*
    ===========================================================================================
    EXTRATO - MOVIMENTAÇÃO FINANCEIRA
    ===========================================================================================
    */

    function extrato($db,$params){
	
	    $array_dados = $params;

	    $dt_ini = $params['dt_ini'];
	    $dt_fim = $params['dt_fim'];
	    $dt_referencia_ini = $params['dt_referencia_ini'];
	    $dt_referencia_fim = $params['dt_referencia_fim'];

	    $dt_hoje = date('Y-m-d');
	    $hora_relatorio = date('H:i:s');
	    $data_relatorio = date('d/m/Y');
	    $relatorio = "";
	
	    //tabela temporaria para armazenar lançamentos
	    $db->query("
		    CREATE TEMPORARY TABLE lancamentos_temp (
			    id int PRIMARY KEY AUTO_INCREMENT,
			    ordem tinyint(1),
			    compensado tinyint(1),
			    dt_vencimento date NOT NULL,
			    dt_compensacao date NOT NULL,
			    descricao varchar(255),
			    valor decimal(10,2) NOT NULL,
				tipo char(1),
				favorecido_id int(10),
			    frequencia int(3),
			    dia_mes int(1)
		    ) ENGINE=MEMORY
	    ");

	    //situação dos lançamentos
	    $lancamento_situacao = 1;//$array_dados['lancamento_situacao'];
	    $compensado = "";
	    if($lancamento_situacao==0){
		    $compensado = "and l.compensado = 0";
	    }elseif($lancamento_situacao==1){
		    $compensado = "and l.compensado = 1";
	    }

	    //contas financeiras do relatório	
	    $array_cf_id = explode(',',$array_dados["contas_financeiras"]);
	
	    //inclui lançamento na tabela temporária
	    foreach($array_cf_id as $cf_id){

		    $saldo_anterior = 0;
		    $saldo_atual = 0;
		    $total_lancamentos = 0;
		    $total_entradas = 0;
		    $total_saidas = 0;
		    $lancamentos = "";
		    $n = 0;

		    $array_conta = $db->fetch_assoc('
			    select c.id, b.nome, c.descricao, c.vl_saldo_inicial
			    from contas c
			    left join bancos b on c.banco_id = b.id
			    where c.id = '.$cf_id.'
		    ');
		
		    $conta_id = $array_conta['id'];

		    //calculo do saldo anterior
		    $query_receita = '
			    select sum(l.valor) valor
			    from contas c, lancamentos l
			    where l.conta_id = '.$conta_id.'
				    and l.tipo = "R"
				    and l.conta_id = c.id
				    and l.dt_compensacao < "'.$dt_ini.'"
				    and l.compensado = 1
			    group by c.id
		    ';
		    $receita = $db->fetch_assoc($query_receita);
		
		    $query_despesa = '
			    select sum(l.valor) valor
			    from contas c, lancamentos l
			    where l.conta_id = '.$conta_id.'
				    and l.tipo = "P"
				    and l.conta_id = c.id
				    and l.dt_compensacao < "'.$dt_ini.'"
				    and l.compensado = 1
			    group by c.id
		    ';
		    $despesa = $db->fetch_assoc($query_despesa);
		
		    $query_trans_entrada = '
			    select sum(l.valor) valor
			    from contas c, lancamentos l
			    where l.tipo = "T"
				    and l.conta_id_destino = '.$conta_id.'
				    and l.conta_id_destino = c.id
				    and l.dt_compensacao < "'.$dt_ini.'"
				    and l.compensado = 1
			    group by c.id
		    ';
		    $trans_entrada = $db->fetch_assoc($query_trans_entrada);
	
		    $query_trans_saida = '
			    select sum(l.valor) valor
			    from contas c, lancamentos l
			    where l.tipo = "T"
				    and l.conta_id_origem = '.$conta_id.'

				    and l.conta_id_origem = c.id
				    and l.dt_compensacao < "'.$dt_ini.'"
				    and l.compensado = 1
			    group by c.id
		    ';
		    $trans_saida = $db->fetch_assoc($query_trans_saida);
	
		    $saldo_anterior = $array_conta['vl_saldo_inicial'] + $receita[valor] - $despesa[valor] + $trans_entrada[valor] - $trans_saida[valor];
		    $saldo_atual += $saldo_anterior;
		    //fim do cáculo do saldo anterior

		    //busca lançamentos existentes
		    $query_lancamentos = " 
			    select l.compensado, l.dt_vencimento, l.dt_compensacao, l.descricao, l.favorecido_id, l.valor, l.tipo
			    from lancamentos l
			    where l.conta_id = ".$conta_id."
			    and l.tipo <> 'T'
			    ".$compensado."
			    and ((l.dt_compensacao >= '".$dt_ini."' and l.dt_compensacao <= '".$dt_fim."') or (l.dt_vencimento >= '".$dt_ini."' and l.dt_vencimento <= '".$dt_fim."'))
	
			    union all
	
			    select l.compensado, l.dt_vencimento, l.dt_compensacao, l.descricao, l.favorecido_id, l.valor, 'R' tipo
			    from lancamentos l
			    where l.tipo = 'T'
			    and l.conta_id_destino = ".$conta_id."
			    ".$compensado."
			    and ((l.dt_compensacao >= '".$dt_ini."' and l.dt_compensacao <= '".$dt_fim."') or (l.dt_vencimento >= '".$dt_ini."' and l.dt_vencimento <= '".$dt_fim."'))		
			
			    union all
			
			    select l.compensado, l.dt_vencimento, l.dt_compensacao, l.descricao, l.favorecido_id, l.valor, 'P' tipo
			    from lancamentos l
			    where l.tipo = 'T'
			    and l.conta_id_origem = ".$conta_id."
			    ".$compensado."
			    and ((l.dt_compensacao >= '".$dt_ini."' and l.dt_compensacao <= '".$dt_fim."') or (l.dt_vencimento >= '".$dt_ini."' and l.dt_vencimento <= '".$dt_fim."'))
		    ";

		    $query_lancamentos = mysql_query($query_lancamentos);

		    while($lancamento = mysql_fetch_assoc($query_lancamentos)){
			    ($lancamento['tipo']=='R')? $lancamento['ordem'] = 1: $lancamento['ordem'] = 2;
			    $db->query_insert('lancamentos_temp',$lancamento);
		    }
		    //fim da busca por lançamentos existentes

		    //busca lançamentos recorrentes
		    if($lancamento_situacao!=1){
			    $query_lancamentos_rcr = mysql_query("
				    select id 
				    from lancamentos_recorrentes
				    where conta_id = ".$conta_id."
					    and dt_vencimento <= '".$dt_fim."'
			    ");
		
			    while($lancamento = mysql_fetch_assoc($query_lancamentos_rcr)){
		 
				    $lancamento_rcr = $db->fetch_assoc("
					    select 0 compensado, dt_vencimento, descricao, valor, frequencia, dia_mes, tipo, favorecido_id
					    from lancamentos_recorrentes 
					    where id = ".$lancamento[id]
				    );
				
				    $dt_vencimento = date($lancamento_rcr[dt_vencimento]);
		
				    while($dt_vencimento <= $dt_fim){
				
					    if($dt_vencimento >= $dt_ini){
						    ($lancamento_rcr['tipo']=='R')? $lancamento_rcr['ordem'] = 1: $lancamento_rcr['ordem'] = 2;
						    $db->query_insert('lancamentos_temp',$lancamento_rcr);
					    }
					
					    if($lancamento_rcr[frequencia]>=30){
					
						    $frequencia = $lancamento_rcr[frequencia]/30;
						    $dia_vencimento = $lancamento_rcr[dia_mes];
						    $dt_vencimento_atual = explode('-',$dt_vencimento);
						    $mes_prox_venc = mktime(0,0,0,$dt_vencimento_atual[1]+$frequencia,'1',$dt_vencimento_atual[0]);
						    $qtd_dias_mes = date('t',$mes_prox_venc);
		
						    if( $qtd_dias_mes < $dia_vencimento ){
							    $dt_vencimento = mktime(0,0,0,$dt_vencimento_atual[1]+$frequencia,$qtd_dias_mes,$dt_vencimento_atual[0]);
							    $dt_vencimento = date('Y-m-d',$dt_vencimento);
						    }else{
							    $dt_vencimento = mktime(0,0,0,$dt_vencimento_atual[1]+$frequencia,$dia_vencimento,$dt_vencimento_atual[0]);
							    $dt_vencimento = date('Y-m-d',$dt_vencimento);
						    }
					
					    }else{
		
						    $dt_vencimento_atual = explode('-',$dt_vencimento);
						    $dt_vencimento = mktime(0,0,0,$dt_vencimento_atual[1],$dt_vencimento_atual[2]+7,$dt_vencimento_atual[0]);
						    $dt_vencimento = date('Y-m-d',$dt_vencimento);
		
					    }
				
					    $lancamento_rcr[dt_vencimento] = $dt_vencimento;
				
				    }
			    }
		    }
		    //fim da busca por lançamentos recorrentes

		    //busca todos os lançamentos na tabela temporária
		    $query_lancamentos_temp = mysql_query("
			    select id, tipo, descricao, favorecido_id, valor, if(compensado=1,dt_compensacao,dt_vencimento) as dt_lnct, date_format(if(compensado=1,dt_compensacao,dt_vencimento), '%d/%m/%Y') as dt_lnct_format, compensado
			    from lancamentos_temp 
			    order by dt_lnct, ordem, id
		    ");

		    $cont = 1;
		    while($lancamento_temp = mysql_fetch_assoc($query_lancamentos_temp)){ 
			    $dt_lnct_temp = explode('-',$lancamento_temp['dt_lnct']);
			    $time_dt_compensacao = mktime(0,0,0,$dt_lnct_temp[1],$dt_lnct_temp[2],$dt_lnct_temp[0]);
			    $time_hoje = mktime(0,0,0,date('m'),date('d'),date('Y'));
			    if( $lancamento_temp['compensado']==1 || ($lancamento_temp['compensado']==0 && ($time_dt_compensacao >= $time_hoje)) ){
				    ++ $total_lancamentos;
				    if($lancamento_temp[tipo]=='R'){
					    $saldo_atual += $lancamento_temp[valor];
					    $total_entradas += $lancamento_temp[valor];
					    $cor = 'verde';
					    $valor = 'R$ '.$db->valorFormat($lancamento_temp[valor]);
				    }else{
					    $saldo_atual -= $lancamento_temp[valor];
					    $total_saidas += $lancamento_temp[valor];
					    $cor = 'vermelho';
					    $valor = '- R$ '.$db->valorFormat($lancamento_temp[valor]);
				    }
	
				    //calcular atraso do lançamento
				    $dt_compensacao = $lancamento_temp['dt_lnct_format'];
				    if($lancamento_temp['compensado']==0){
					    $data = explode('-',$lancamento_temp['dt_lnct']);
					    $dt_limite = mktime(0,0,0,$data[1],$data[2],$data[0]);
					    $hoje = mktime(0,0,0,date('m'),date('d'),date('Y'));
					    $atraso = $hoje - $dt_limite;
					    //$atraso = date('d',$atraso);
					    $atraso = (int)floor( $atraso / (60 * 60 * 24)); //  Calcula a diferença de dias
				 
					    $dt_limite = date('Y-m-d',$dt_limite);
				 
					    if($dt_hoje > $dt_limite){
						    $situacao = "<font class='vermelho'>Atrasado ".$atraso." dia(s) </font>";
					    }else{
						    $situacao = "<font class='azul'> À realizar </font>";
					    }					
				    }else{
						    $situacao = "<font class='azul'> Realizado </font>";
					}
					
					$favorecido = $db->fetch_assoc('SELECT nome FROM favorecidos WHERE id = '.$lancamento_temp['id']);

				    $n +=1;	
				    if($n % 2){ $bg_color = '#F0F0F0'; }else{ $bg_color = '#FFF'; }
				    $lancamentos .='
					    <tr bgcolor="'.$bg_color.'">
						    <td align="center">'.$cont.'</td>
							<td align="center">'.$dt_compensacao.'</td>
							<td align="left">'.$favorecido['nome'].'</td>
						    <td align="left">'.$lancamento_temp[descricao].'</td>
						    <!--<td align="center">'.$situacao.'</td>-->
						    <td align="right"><span class="'.$cor.'">'.$valor.'<span></td>
						    <td align="right"><span class="verde">R$ '.$db->valorFormat($saldo_atual).'<span></td>
					    </tr>
				    ';
				    $cont++;
			    }
		    }
		    //fim da busca dos lançamentos na tabela temporária

		    $relatorio .= '
		
			
			    <div class="bordaArredondadaTitulo6" align="center"> 
			
				    <div class="cabecalhoInterno">  
					    <div class="bordaArredondadaTitulo4">	<span class="spanCinza"> &bull; '.$array_conta['nome'].' - '.$array_conta['descricao'].' </span> </div>
					    <div class="bordaArredondadaTitulo2" align="right">	<span class="spanCinza"> Saldo Anterior: </span> R$ '.$db->valorFormat($saldo_anterior).' </div>
				    </div>
			
				    <table border="0" cellpadding="0" cellspacing="0">
				
					    <thead>
						    <tr>
							    <td width="50" align="center"><span class="spanCinza">CÓDIGO</span></td>
								<td width="100" align="center"><span class="spanCinza">VENC. / COMP.</span></td>								
							    <td width="300" align="center"><span class="spanCinza">FAVORECIDOS</span></td>
							    <td width="300" align="center"><span class="spanCinza">DESCRIÇÃO</span></td>
							    <!--<td width="100" align="center"><span class="spanCinza">SITUAÇÃO</span></td>-->
							    <td width="140" align="right"><span class="spanCinza">VALOR</span></td>
							    <td width="140" align="right"><span class="spanCinza">SALDO</span></td>
						    </tr>
					    </thead>
			
					    <tbody>
						    '.$lancamentos.'
					    </tbody>
	
				    </table>									
			
				    <div class="subTotal" align="right">  <span class="spanCinza"> Total de Entradas: </span> R$ '.$db->valorFormat($total_entradas).' <span class="spanCinza">&nbsp;&nbsp; Total de Saídas: </span> R$ '.$db->valorFormat($total_saidas).' </div>
			
			    </div><br>					
		    ';
	
		    $db->query("truncate table lancamentos_temp");

	    }
	
	    $pdfHeader = '
								    <div align="left" class="cabecalho" width="565">
									    <span class="nomeRelatorio">&nbsp;</span>
										    <br><b>MOVIMENTAÇÃO FINANCEIRA</b><br>
									    <div class="linha">
										    <div class="nomeRelatorioB" align="left"><font class="nomeRelatorio">Período:</font> '.$dt_referencia_ini.' a '.$dt_referencia_fim.'</div>	<br>
									    </div>
								    </div>
								    <div align="right" class="cabecalho" width="150"><img src="../../../../sistema/images/logo_webfinancas_fundo_branco.png" width="150"></div>
	    ';
	
	    $pdfFooter = '
								    <div class="rodape" width="280" align="left">Emitido: '.$data_relatorio.' as '.$hora_relatorio.' </div> 
								    <div class="rodape" width="150" align="center"><b>{PAGENO}/{nbpg}</b></div>
								    <div class="rodape" width="255" align="right">webfinancas.com</div>
	    ';
	

	    $pdf_nome = $params['cliente_id'].'_Extrato';
	
	    self::pdfGerar($relatorio,$pdfHeader,$pdfFooter,$pdf_nome,"A4-L");
	
	    $pdf_nome .= '.pdf';
	
	    return $pdf_nome;
	
    }

    /*
    ===========================================================================================
    HISTÓRICO DA REMESSA CONTÁBIL
    ===========================================================================================
    */

    function RemessaHistorico($dados){

	    //dados para teste
	    /*
	    $dados = array(
		    'mes_ini' => '08/2015',
		    'mes_fim' => '08/2015',
		    'cliente_id' => '134',
		    'cf_id' => '1,2,3'
	    );
	    */

	    //Formata a data de inicio
	    $ini = explode('/',$dados["mes_ini"]);
	    $mes_ini = $ini[0];
	    $ano_ini = $ini[1];
	    $dt_inicio = $ano_ini.'-'.$mes_ini.'-01';
	    $dt_referencia_ini = '01/'.$mes_ini.'/'.$ano_ini;
			
	    //Formata a data final
	    $fim = explode('/',$dados["mes_fim"]);
	    $mes_fim = $fim[0];
	    $ano_fim = $fim[1];
	    $dt_fim_ts = mktime(0,0,0,$mes_fim+1,'00',$ano_fim);
	    $dt_final = date('Y-m-d',$dt_fim_ts);
	    $dt_referencia_fim = date('d/m/Y',$dt_fim_ts);

	    //contas financeiras do relatório	
	    $array_cf_id = explode(',',$dados["cf"]);
		
	    $relatorio = '';

	    //Conexão com o db do Web Finanças
	    $db =  new Database('mysql.webfinancas.com','webfinancas','W2BSISTEMAS','webfinancas');

	    //Conexão com o db do cliente
	    $cliente_id = $dados['cliente_id'];
	    $db_dados_cliente = $db->fetch_assoc('select db, db_senha from clientes_db where cliente_id = '.$cliente_id);
	    $db_cli =  new Database('mysql.webfinancas.com',$db_dados_cliente['db'],$db_dados_cliente['db_senha'],$db_dados_cliente['db']);

	    //Seleciona contas financeiras que foram liberadas pelo cliente
	    $query_remessa_contabil = '
		    select distinct conta_id
		    from remessa_contabil
		    where ( (mes >= "'.$mes_ini.'" and ano >= "'.$ano_ini.'") and (mes <= "'.$mes_fim.'" and ano <= "'.$ano_fim.'") )
			    and conta_id in ('.$dados['cf_id'].')
	    ';

	    $array_cf = $db_cli->fetch_all_array($query_remessa_contabil);

	    foreach($array_cf as $cf){

		    $n = 0;
		    $historico = '';

		    $array_cf_historico = $db_cli->fetch_all_array('
			    select b.nome, c.descricao, rc.operacao, rc.saldo, rc.vl_rcbt, rc.qtd_rcbt, rc.vl_pgto, rc.qtd_pgto, date_format(rc.dt_cadastro, "%d/%m/%Y") dt_ocorrencia, time_format(rc.dt_cadastro, "%T") hora_ocorrencia, rc.mes, rc.ano
			    from remessa_contabil rc
			    join contas c on rc.conta_id = c.id
			    join bancos b on c.banco_id = b.id
			    where rc.conta_id = '.$cf['conta_id'].'
				    and ( (mes >= "'.$mes_ini.'" and ano >= "'.$ano_ini.'") and (mes <= "'.$mes_fim.'" and ano <= "'.$ano_fim.'") )
			    order by ano, mes, dt_cadastro');

		    foreach($array_cf_historico as $cf_historico){

			    $n +=1;
			    if($n % 2){ $bg_color = '#F0F0F0'; }else{ $bg_color = '#FFF'; }

			    if($cf_historico['operacao']==1)
				    $operacao = 'Remessa enviada para contabilidade';
			    else
				    $operacao = 'Arquivo contábil gerado pela contabilidade';

			    if($cf_historico['mes']<10)
				    $cf_historico['mes'] = '0'.$cf_historico['mes'];

			    $historico .= '
				    <tr bgcolor="'.$bg_color.'">
					    <td align="">'.$cf_historico['dt_ocorrencia'].' - '.$cf_historico['hora_ocorrencia'].'</td>
					    <td align="">'.$cf_historico['mes'].'/'.$cf_historico['ano'].'</td>
					    <td align="">R$ '.$db->valorFormat($cf_historico['vl_rcbt']).' ('.$cf_historico['qtd_rcbt'].')</td>
					    <td align="">R$ '.$db->valorFormat($cf_historico['vl_pgto']).' ('.$cf_historico['qtd_pgto'].')</td>
					    <td align="">R$ '.$db->valorFormat($cf_historico['saldo']).'</td>
				    </tr>
			    ';
		
		    }

		    $relatorio .= '

			    <div class="bordaArredondadaTitulo6" align="center">

				    <div class="cabecalhoInterno">
					    <div class="bordaArredondadaTitulo4">	<span class="spanCinza"> &bull; '.$cf_historico['nome'].' - '.$cf_historico['descricao'].' </span> </div>
					    <div class="bordaArredondadaTitulo2" align="right">	<span class="spanCinza"> </div>
				    </div>
			
				    <table border="0" cellpadding="0" cellspacing="0">
				
					    <thead>
						    <tr>
							    <td width="" align="left"><span class="spanCinza">DATA</span></td>
							    <td width="" align="left"><span class="spanCinza">MÊS DE REFERÊNCIA</span></td>
							    <td width="" align="left"><span class="spanCinza">ENTRADAS</span></td>
							    <td width="" align="left"><span class="spanCinza">SAÍDAS</span></td>
							    <td width="" align="left"><span class="spanCinza">RESULTADO</span></td>
						    </tr>
					    </thead>
			
					    <tbody>
						    '.$historico.'
					    </tbody>
	
				    </table>
			
			    </div><br>					
		    ';

	    }

	    $pdfHeader = '
								    <div align="left" class="cabecalho" width="896">
									    <span class="nomeRelatorio">&nbsp;</span>
									    <br><b>Histórico Mensal De Remessa Contábil</b><br>
									    <div class="linha">
										    <div class="nomeRelatorioB" align="left"><font class="nomeRelatorio">Período de referência:</font> '.$dt_referencia_ini.' a '.$dt_referencia_fim.'</div> <br>
									    </div>
								    </div>
								    <div align="right" class="cabecalho" width="150"><img src="../../../../sistema/images/logo_webfinancas_fundo_branco.png" width="150"></div>
	    ';
	
	    $pdfFooter = '
								    <div class="rodape" width="280" align="left">Emitido: '.date('m/d/Y').' as '.date('H:i:s').' </div>
								    <div class="rodape" width="150" align="center"><b>{PAGENO}/{nbpg}</b></div>
								    <div class="rodape" width="255" align="right">webfinancas.com</div>
	    ';
	
	    self::pdfGerar($relatorio,$pdfHeader,$pdfFooter,"Historico-Contabil",'t','A4-L');

    }

    /*
    ===========================================================================================
    GERAR PDF
    ===========================================================================================
    */

    function pdfGerar($relatorio,$pdfHeader,$pdfFooter,$nomeRelatorio,$tp_print='',$orientation="A4"){

	    require("../../../../sistema/php/MPDF/mpdf.php");
	
	    $mpdf=new mPDF('c'); 
	
	    $mpdf->SetDisplayMode('fullpage');
	
	    //$mpdf->ignore_invalid_utf8 = true;
	
	    //$mpdf->allow_charset_conversion = true;
	
	    //$mpdf->charset_in='UTF-8';
	
	    // LOAD a stylesheet
	    //$stylesheet = file_get_contents('mpdfstyleA4.css');
	    $stylesheet = file_get_contents('../../../../sistema/css/css_relatorios.css');
	
	    //$mpdf=new mPDF('pt_BR','A4-L','','',10,10,29,18,5,8); //Pagina estilo Paisagem (Horizontal)
	    $mpdf=new mPDF('pt_BR',$orientation,'','',10,10,29,18,5,8); //cria um novo container PDF no formato A4 com orientação customizada ex.:class mPDF ([ string $mode [, mixed $format [, float $default_font_size [, string $default_font [, float $margin_left , float $margin_right , float $margin_top , float $margin_bottom , float $margin_header , float $margin_footer [, string $orientation ]]]]]])
	    $mpdf->useSubstitutions=false;
	    $mpdf->simpleTables = true;
	    $mpdf->SetHTMLHeader($pdfHeader);
	    $mpdf->SetHTMLFooter($pdfFooter);
	    $mpdf->WriteHTML($stylesheet,1);	// The parameter 1 tells that this is css/style only and no body/html/text
	    $mpdf->WriteHTML($relatorio);
	
	    if($tp_print=="t"){
		    //Visualização na tela
		    $mpdf->Output($nomeRelatorio.'.pdf','I');
	    }else{
		    //Download
		    //$nomeRelatorio = 'Relatório_Movimentação_Financeira';
		    //$mpdf->Output($nomeRelatorio.'.pdf','D');
		    $mpdf->Output('../pdf/'.$nomeRelatorio.'.pdf');
	    }
	    //exit;

    }

    /*
    ===========================================================================================
    CÁLCULO DO BALANÇO
    ===========================================================================================
    */

    function Balanco($db,$params){

	    $query = '
		    select sum(if(tipo="R" || conta_id_destino = '.$params['cf_id'].',valor,0)) vl_rcbt, sum(if(tipo="R" || conta_id_destino = '.$params['cf_id'].',1,0)) qtd_rcbt, sum(if(tipo="P" || conta_id_origem = '.$params['cf_id'].',valor,0)) vl_pgto, sum(if(tipo="P" || conta_id_origem = '.$params['cf_id'].',1,0)) qtd_pgto
		    from lancamentos
		    where conta_id = '.$params['cf_id'].'
			    and dt_compensacao >= "'.$params['dt_ini'].'" and dt_compensacao <= "'.$params['dt_fim'].'"
			    and compensado = 1';
	
	    $array_valores  = $db->fetch_assoc($query);
	
	    $array_valores['vl_balanco'] = $array_valores['vl_rcbt'] - $array_valores['vl_pgto'];
	
	    return $array_valores;
	
    }

    /*
    ===========================================================================================
    ACEITAR CONEXÕES
    ===========================================================================================
    */	

    function AceitarConexoes($db,$array_dados){

        //Informações do destinatario e remetente
        $info_dados = explode('-',$array_dados['cliente_id']);
        $cliente_id = $info_dados[0];
        $convite_id = $info_dados[1];
        $contador_id = $_SESSION['cliente_id'];
    
        //Conexão no banco do Web Finanças
        $db_wfp = new Database('mysql.webfinancas.com','webfinancas','W2BSISTEMAS','webfinancas');
    
        //Quem enviou o convite
        $cliente_dados = $db_wfp->fetch_assoc('select db, db_senha from clientes_db where cliente_id = '.$cliente_id);
        $db_cliente =  new Database('mysql.webfinancas.com',$cliente_dados['db'],$cliente_dados['db_senha'],$cliente_dados['db']);
    
        //Verifica se o convite ainda esta ativo no momento do aceite
        $verifcacao = $db_cliente->fetch_assoc('SELECT id FROM conexao WHERE contador_id = '.$contador_id.' AND conectado = 0');
    
	    if($verifcacao == true){
		 
		    $array_dados_cliente = array('dt_inicio' => date('Y-m-d H:m:s'),'conectado' => 1);
		    $db_cliente->query_update('conexao', $array_dados_cliente, 'contador_id = '.$contador_id.' AND conectado = 0');
		
		    //Quem está aceitando o convite
		    $array_dados_contador = array('dt_inicio' => date('Y-m-d H:m:s'),'conectado' => 1);
		    $db->query_update('conexao', $array_dados_contador, 'id = '.$convite_id); 
		
		    //Retorna a lista de destinatários aguardando conexão atualizada para o sistema
		    $lista_conexoes = self::listarClientes($db);
        
		    $retorno = array("situacao" => 1,"notificacao"=>"Convite aceito com sucesso.", "lista_conexoes" => $lista_conexoes);

		    return $retorno;
		
	    }else{
		
            $db->query('delete from conexao where cliente_id = '.$cliente_id);
		    $retorno = array("situacao" => 2,"notificacao"=>"<b>Conexão não estabelecida</b>. <br>O cliente cancelou o convite.");
		    return $retorno;
        
        }	
    
    }

    /*
    ===========================================================================================
    EXCLUÍR CONVITE
    ===========================================================================================
    */	

	function ConviteExcluir($db,$array_dados){

		//Pega as informações no href explode e preenvhe o remetente_id e cliente_id
		$dados_cancelar = explode('-',$array_dados['cliente_id']);		
		$contador_id = $dados_cancelar['0'];
		$cliente_id = $dados_cancelar['1'];
		$conexao_id = $dados_cancelar['2'];
		
		//Conexão no banco da Web 2 Business
		$db_w2b = new Database('mysql.web2business.com.br','web2business','W2BSISTEMAS','web2business');		

        //Verfica se o cliente já tem cadastro
		$verif_clientes = $db_w2b->fetch_assoc('select id from clientes where id = '.$cliente_id);
		
		if($verif_clientes == true){
            
		    //Conexão no banco do Web Finanças
		    $db_wfp = new Database('mysql.webfinancas.com','webfinancas','W2BSISTEMAS','webfinancas');
            
		    $dados_cliente = $db_wfp->fetch_assoc('select db, db_senha from clientes_db where cliente_id = '.$cliente_id);
            
		    $db_cliente =  new Database('mysql.webfinancas.com',$dados_cliente['db'],$dados_cliente['db_senha'],$dados_cliente['db']);

		    //Exclui o registro do banco de dados do cliente
		    $db_cliente->query('DELETE FROM conexao WHERE contador_id = '.$contador_id.' AND conectado = 0');
            
            $db_wfp->close();
            $db_cliente->close();
		}
		
		//Pega o email do contador para informar que o convite foi cancelado
		//$email = $db->fetch_assoc('select email from conexao where id = '.$conexao_id);
		
		//Exclui o registro dentro no banco de dados do contador
		$db->query('DELETE FROM conexao WHERE id = '.$conexao_id);
		
        //Pega o nome do cliente
		//$nome_cliente = $db_w2b->fetch_assoc('select nome from clientes where id = '.$cliente_id);
        
        $db_w2b->close();

        //Conetúdo do convite
        /*
		$texto = '<h2> Olá, </h2>
				<p style="line-height:20px;">O convite foi cancelado por <b>'.$nome_cliente['nome'].'</b>.
					
				<br><br>	
				Atenciosamente,
				<br>	
				
					<b>Web Finanças</b>	
					
					<br><br>
					<b>Atenção:</b><i>O convite será removido da sua lista automaticamente.</i>
			</p>
			<br>';	
		
		$conteudo = self::conteudoConvite($texto);
		
		$assunto = $nome_cliente['nome'].' cancelou o convite';
		
		//Envia o convite por email para o Destinatario
		self::emailEnviar($email['email'],$assunto,$conteudo);
        */

		$retorno = array("situacao" => 1,"notificacao" => "Convite excluído com sucesso.");

        return $retorno;
	}

    //LISTAR PLANO DE CONTAS
    //======================================================================================================================================================================================

    function ListarPlanoContas($params,$db,$dadosDbWf){
        
        //Conexão no banco do Web Finanças
	    $dbWf = new Database($dadosDbWf['host'],$dadosDbWf['usuario'],$dadosDbWf['senha'],$dadosDbWf['db']);

        //Conexão com o db do cliente
        $dadosDbCliente = $dbWf->fetch_assoc('select db, db_senha from clientes_db where cliente_id = '.$params['clienteId']);
        $dbCliente =  new Database('mysql.webfinancas.com',$dadosDbCliente['db'],$dadosDbCliente['db_senha'],$dadosDbCliente['db']);
        $dbWf->close();

        //busca plano de contas de forma ordenada
        //$array_plano_contas = $dbCliente->fetch_all_array('select id, cod_conta, nome, tp_conta, nivel, cast(substring_index(cod_conta,",",1) as unsigned) as ordem from plano_contas where cod_conta > 0 order by ordem, cod_conta');

        //start: busca plano de contas ordenado
        $maiorNivel = $dbCliente->fetch_assoc('select max(nivel) nivel from plano_contas');
        $maiorNivel = $maiorNivel['nivel'];

        $ordem = '';
        $orderBy = '';

        if($maiorNivel>1){
            
            $arrayOrderBy = array();
            $arrayOrdem = array();

            for($i=2;$i<$maiorNivel;$i++){
                array_push($arrayOrderBy,'ordem'.$i);
                array_push($arrayOrdem,'if(nivel>='.$i.',cast(substring_index(substring_index(cod_conta,".",'.$i.'),".",-1) as decimal),0) as ordem'.$i);
            }

            array_push($arrayOrdem,'if(nivel>='.$i.',cast(substring_index(cod_conta,".",-1) as decimal),0) as ordem'.$i);
            array_push($arrayOrderBy,'ordem'.$i);

            $ordem = ','.join(',',$arrayOrdem);
            $orderBy = ','.join(',',$arrayOrderBy);
        }

        $array_plano_contas = $dbCliente->fetch_all_array('
                    select id, cod_conta, nome, tp_conta, nivel, cast(substring_index(cod_conta,".",1) as decimal) as ordem1'.$ordem.'
                    from plano_contas 
                    where cod_conta > 0 
                    order by ordem1'.$orderBy);
        //end: busca plano de contas ordenado

        //Monta retorno do plano de contas
        $categorias = '';
        $cont = 0;
        foreach($array_plano_contas as $planoContas){
            
            $cont++;					

            //resgata os valores dentro do banco do contador
            
            $contador_pl_cod = $db->fetch_assoc('select contador_pl_cod from clientes_pl_config where cliente_id ='.$params['clienteId'].' and cliente_pl_id ='.$planoContas['id']);
            
            $espc = $planoContas['nivel'] * 10;  $espc = $espc.'px';
            if($planoContas['nivel'] == 1){ $strong = 'bold;'; $fontN = '16px;';  }else{ $strong = 'normal;'; $fontN = '12px;'; }

            //$valor = $array_valores[$planoContas['id']];
            if($planoContas['tp_conta'] == '1'){ 
                $debito_credito = $contador_pl_cod['contador_pl_cod'];
                $disabled = '';
            }else{ 
                $debito_credito = '';
                $disabled = 'disabled';
            }
            
            $categorias .= '<tr class="gradeA">
				                <td class="hidden">'.$cont.'</td>
				                <td class="updates newUpdate" >
												
						                <div class="uDate tbWF tipS" original-title="Nível" align="center" style="width:auto; padding-left:'.$espc.'"> <span class="uDay" style="font-size:'.$fontN.'">'.$planoContas['cod_conta'].' - </span></div>
							                <span class="lDespesa tbWF" style="margin-top:2px; font-size:14px;">
								                <a href="javascript://void(0);" style="cursor: default; font-size:'.$fontN.'font-weight:'.$strong.'" original-title="Descrição" class="tipS " >'.$planoContas['nome'].'</a>
							                </span>											
																									
				                </td>
				                <td class="updates newUpdate" >
					                <input type="hidden" name="cliente_pl_id'.$cont.'" value="'.$planoContas['id'].'">
					                <input type="text" name="contador_pl_cod'.$cont.'" value="'.$debito_credito.'" style="width:100px; text-align:center;" maxlength="30" class="form" '.$disabled.'>
				                </td>
                                <td class="updates newUpdate" >
                                    <div class="tbWFoption">
        						        <a href="'.$planoContas['id'].'" original-title="Excluír" class="smallButton btTBwf redB tipS planoContasExcluir" data-cliente-id="'.$params['clienteId'].'"><img src="../../sistema/images/icons/light/close.png" width="10"></a>
				                        <a href="javascript://void(0);" original-title="Editar" class="smallButton btTBwf greyishB tipS" onClick="planoContasExibir('.$planoContas['id'].','.$planoContas['tp_conta'].','.$params['clienteId'].');"><img src="../../sistema/images/icons/light/pencil.png" width="10"></a>
                                    </div>
							    </td>
			                </tr>
                        ';
        }

        $tabelaPlanoContas = '<table cellpadding="0" cellspacing="0" width="100%" class="sTable" id="tbl-plc">
                <thead>
                    <tr style="font-size:14px;">
          	            <td width="100%" align="center"> 
                            Plano de Contas
          	            </td>
                        <td width="auto" align="center"> Débito / Crédito </td>
                        <td width="auto" align="center"> Opções </td>
                    </tr>
                </thead>
                <tbody>
                    '.$categorias.'
                    <tr>
                        <td colspan="3" align="right">
                            <a href="javascript://" class="button greenB" onClick="salvar_config_pl();"><img src="https://app.webfinancas.com/sistema/images/icons/light/check.png" alt="" class="icon"><span>Salvar</span></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        ';

        return array('planoContas'=>$tabelaPlanoContas,'totalCategorias'=>$cont);
    }

    //CARREGAR PLANO DE CONTAS
    //======================================================================================================================================================================================

    function CarregarPlanoContas($params,$dadosDbWf){

        //As abas do excel iniciam no índice zero
        //As colunas e linhas iniciam no índice um

        require_once("../../../../sistema/php/reader.php");
        
        //Conexão no banco do Web Finanças
	    $dbWf = new Database($dadosDbWf['host'],$dadosDbWf['usuario'],$dadosDbWf['senha'],$dadosDbWf['db']);

        //Conexão com o db do cliente
        $dadosDbCliente = $dbWf->fetch_assoc('select db, db_senha from clientes_db where cliente_id = '.$params['clienteId']);
        $dbCliente =  new Database('mysql.webfinancas.com',$dadosDbCliente['db'],$dadosDbCliente['db_senha'],$dadosDbCliente['db']);
        $dbWf->close();

        //start: Lê excel com o plano de contas selecionado
        $arquivo = "../modelos_plc/".$params['modelo'].'.xls';
            
        $xls = new Spreadsheet_Excel_Reader($arquivo);
        $xls->read($arquivo);
        $linhas = $xls->sheets[0]['numRows'];
        //end: Lê excel com o plano de contas selecionado

        //start: Inclui categorias no plano de contas
        //VERIFICAR SE O PRIMEIRO NÍVEL DE TODOS OS MODELOS SEMPRE TERÃO A MAIOR PROFUNDIDADE IGUAL
        if($params['modelo']=='engenharia')
            $maiorNivel = 4;
        elseif($params['modelo']=='odontologico')
			$maiorNivel = 2;
		elseif($params['modelo']=='ebitdah')
            $maiorNivel = 4;

        $dtCadastro = date('Y-m-d');
        
        for($i = 2; $i <= $linhas; $i++){

            $codigo = $xls->sheets[0]['cells'][$i][1];

            $arrayCodigo = explode('.',$codigo);
            $nivel = count($arrayCodigo);

            $arrayInsert = array();

            $arrayInsert['cod_conta'] = $codigo;
            $arrayInsert['nome'] = utf8_encode($xls->sheets[0]['cells'][$i][2]);
            $arrayInsert['clfc_fc'] = $xls->sheets[0]['cells'][$i][3];
            $arrayInsert['clfc_dre'] = $xls->sheets[0]['cells'][$i][4];
            $arrayInsert['nivel'] = $nivel;
            $arrayInsert['tp_conta'] = ($nivel >= $maiorNivel)? 1 : 2; //1: Analítico; 2: Sintético
            $arrayInsert['posicao'] = $arrayCodigo[$nivel-1];
            $arrayInsert['situacao'] = 0; //0: Ativo; 1:Inativo
            $arrayInsert['dt_cadastro'] = $dtCadastro;
            $arrayInsert['dedutivel'] = $xls->sheets[0]['cells'][$i][5];

            $dbCliente->query_insert('plano_contas',$arrayInsert);
        }
        
        //Inclui categoria Geral
        $arrayInsert = array();
        $arrayInsert['cod_conta'] = '0';
        $arrayInsert['hierarquia'] = '0';
        $arrayInsert['nome'] = 'Não alocado';
        $arrayInsert['situacao'] = 0; //0: Ativo; 1:Inativo
        $arrayInsert['dt_cadastro'] = $dtCadastro;
        $dbCliente->query_insert('plano_contas',$arrayInsert);
        $dbCliente->query('update plano_contas set id = 0 where cod_conta = "0"');
        //end: Inclui categorias no plano de contas
        
        //start: Atualiza conta_pai_id e hierarquia das categorias incluídas
        $query = mysql_query('select * from plano_contas where id > 0 order by id',$dbCliente->link_id);
        while($categoria = mysql_fetch_assoc($query)){

            $arrayUpdate = array();
            
            if($categoria['nivel']==1){
                $arrayUpdate['conta_pai_id'] = 0;
                $arrayUpdate['hierarquia'] = $categoria['id'];
            }else{
                
                $qtdCaracteres = strrpos($categoria['cod_conta'],'.');
                $codigoPai = substr($categoria['cod_conta'],0,$qtdCaracteres);

                $categoriaPai = $dbCliente->fetch_assoc('select id, hierarquia from plano_contas where cod_conta = "'.$codigoPai.'"');
                $arrayUpdate['hierarquia'] = $categoriaPai['hierarquia'].','.$categoria['id'];

                $arrayUpdate['conta_pai_id'] = $categoriaPai['id'];
            }

            $dbCliente->query_update('plano_contas',$arrayUpdate,'id = '.$categoria['id']);
        }
        //end: Atualiza conta_pai_id e hierarquia das categorias incluídas

        $dbCliente->close();
    }



/*
===================================
BLOQUEAR LANÇAMENTO DE CADA CLIENTE
===================================
*/

function BloquearLiberarLancamentos($dbCliente, $params)
	{

		//Coluna db "bloqueado" : 1 = bloqueado e 0 = liberado

		$ano = $params['ano'];
		$mes = $params['mes'];


		$status = $dbCliente->numRows('SELECT id FROM remessa_contabil WHERE ano = '.$ano.' AND mes = '.$mes);
	
		if($status > 0){

			//Liberado
			
			$dbCliente->query('DELETE FROM remessa_contabil WHERE ano = '.$ano.' AND mes = '.$mes);

			$data['bloqueado'] = 0;
			
		}else{
			
			//Bloqueado

			$contas = $dbCliente->fetch_all_array('SELECT id FROM contas');

			$cf = '';
			$c = 1;

			foreach($contas as $contas)
			{
				if($c > 1){ $cf .= $cf.','; }

					$cf .= $contas['id'];

				$c += 1;
			}
			
				$dados = array('ano' => $ano, 'mes'=> $mes, 'cf'=> $cf);

				$remessa_contabil = new Contador();
				$remessa_contabil->RemessaContabil($dbCliente, $dados);

				$data['bloqueado'] = 1;
			
		}
		
		return array('bloqueado'=>$data['bloqueado']);

	}

/*
===================================
BLOQUEAR LANÇAMENTO DE CADA CLIENTE
===================================
*/
/*
==========================================================
EXIBIR CALENDARIO PARA BLOQUEAR LANÇAMENTO DE CADA CLIENTE
==========================================================
*/

	function BlqLiberarLancExibir($dbCliente, $params)
	{
		
		//Coluna db "bloqueado" : 1 = bloqueado e 0 = liberado

		$meses = array(1 => "jan", 2 => "fev", 3 => "mar", 4 => "abr", 5 => "mai", 6 => "jun", 7 => "jul", 8 => "ago", 9 => "set", 10 => "out", 11 => "nov", 12 => "dez");

		$dados = array();
		$count = 1;	

		while($count <= 12)
		{
			
			$calendario = $dbCliente->fetch_assoc("SELECT mes FROM remessa_contabil WHERE ano = ".$params['ano'].' AND mes = '.$count);
			
				if($calendario['mes'] == $count)
				{
				
					$dados[$meses[$count]] = 1;
				
				}else{
				
					$dados[$meses[$count]] = 0;
				
				}

			$count += 1;

		}

		return $dados; 

	}

/*
==========================================================
EXIBIR CALENDARIO PARA BLOQUEAR LANÇAMENTO DE CADA CLIENTE
==========================================================
*/

}








