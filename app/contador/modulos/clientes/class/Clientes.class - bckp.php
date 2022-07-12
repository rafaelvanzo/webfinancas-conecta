<?php
class Clientes{
	
	
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
		
		$mailer->send($message); 

	}
     
    
    
/*
===========================================================================================
DATA TABLE AJAX
===========================================================================================
*/

function DataTableAjax($db_w2b,$params){
    
    session_start(); 
    $cliente_id = $_SESSION['cliente_id'];
    
    //filtro do data table
    $sSearch = $params["sSearch"];
    $sEcho = $params["sEcho"];
    $iDisplayStart = $params["iDisplayStart"];
    $iDisplayLength = $params["iDisplayLength"];
    //$iTotalRecords = $db->numRows('select id from lancamentos');
    $iTotalDisplayRecords = 0;
    
    $aaData = array();
    
    if($sSearch==""){

        $query_dataTable = "SELECT * FROM clientes WHERE parceiro_id = ".$cliente_id;

    }else{
        //Query princial
        $query_dataTable = "SELECT * FROM clientes WHERE parceiro_id = ".$cliente_id." AND nome like '%".$sSearch."%'";
    }   
    
    //total de registros
    $iTotalDisplayRecords = $db_w2b->numRows('select id from ('.$query_dataTable.') as lancamentos WHERE parceiro_id = '.$cliente_id);
    
    //Limita a consulta no db de acordo com a configuração da tabela
    $query_order .= ' order by nome, dt_cadastro ASC limit '.$iDisplayStart.','.$iDisplayLength;
    
    
    $array_dados = $db_w2b->fetch_all_array($query_dataTable.$search.$query_order);

    foreach($array_dados as $dados){	
  
    // ============ data ============
    $dt_inicio = explode("-", $dados['dt_cadastro']);
    $dia = substr($dt_inicio[2], 0, 2);
    $m = $dt_inicio[1];
    if($m == '01'){ $mes = 'Jan';}
    elseif($m == '02'){ $mes = 'Fev';}
    elseif($m == '03'){ $mes = 'Mar';}
    elseif($m == '04'){ $mes = 'Abr';}
    elseif($m == '05'){ $mes = 'Mai';}
    elseif($m == '06'){ $mes = 'Jun';}
    elseif($m == '07'){ $mes = 'Jul';}
    elseif($m == '08'){ $mes = 'Ago';}
    elseif($m == '09'){ $mes = 'Set';}
    elseif($m == '10'){ $mes = 'Out';}
    elseif($m == '11'){ $mes = 'Nov';}
    else{ $mes = 'Dez';}
    $ano = substr($dt_inicio[0], -2);
   
    if($dados['inscricao'] == "CPF"){
        $cpf_cnpj = "CPF: ";
    }else{
        $cpf_cnpj = "CNPJ: ";
    }
    
    if($dados['situacao'] == 1){
        $situacao = '<div class="tbWFvalue blue">ATIVO</div>';
    }else{
        $situacao = '<div class="tbWFvalue red">INATIVO</div>';
    }
    
    $registros_tabela = '<span>
                                    <div class="uDate tbWF tipS" align="center" original-title="Data do cadastro" > <span class="uDay ">'.$dia.'</span>'.$mes.'/'.$ano.'<br></div>
                                    
                                    <span class="lDespesa tbWF">
					                   <a href="javascript://void(0);" style="cursor: default;" original-title="Descrição" class="tipS"> <strong class="blue">'.$dados['nome'].' </strong></a>                                     
						                <span class="tipN">'.$cpf_cnpj.'<b>'.$dados['cpf_cnpj'].'</b></span>
				                    </span>                                    
                                    <div class="tbWFoption" style="z-index:999;">
			    	                    <a href="javascript://void(0);" original-title="Excluír" class="smallButton btTBwf redB tipS" onClick="excluirDialog('.$dados['id'].');"><img src="../../sistema/images/icons/light/close.png" width="10"></a>
				                        <a href="javascript://void(0);" original-title="Editar" class="smallButton btTBwf greyishB tipS" onClick="visualizarInfo('.$dados['id'].');"><img src="../../sistema/images/icons/light/pencil.png" width="10"></a>
                                        <a href="javascript://void(0);" original-title="Alterar Senha" class="smallButton btTBwf tipS" onClick="alterarSenha('.$dados['id'].');"><img src="../../sistema/images/icons/dark/key.png" width="10"></a>
				                    </div>   
                                    
                                    '.$situacao.'
                             
                             </span>'  ;
    
         //insere resultado dentro do arra $aaData
         array_push($aaData,array('msg'=>$registros_tabela));
         
    }
    
    $retorno = array('sEcho'=>$sEcho,'iTotalRecords'=>$iTotalDisplayRecords,'iTotalDisplayRecords'=>$iTotalDisplayRecords,'aaData'=>$aaData);
    
    return json_encode($retorno);
}

//===========================================================================================
//ADD
//===========================================================================================

function add($db, $db_wf, $db_w2b, $params){
    
    $contador_id = $_SESSION['cliente_id'];

    $cliente_id = $db_wf->fetch_assoc('select cliente_id, dt_cadastro from usuarios where email = "'.$params['email'].'"');

    if(!$cliente_id){
        
        //insere cliente no banco de dados da w2b
        $array_cadastro['nome'] = $params['nome'];
        $array_cadastro['inscricao'] = $params['inscricao'];
        $array_cadastro['cpf_cnpj'] = $params['cpf_cnpj'];
        $array_cadastro['logradouro'] = $params['logradouro'];
        $array_cadastro['numero'] = $params['numero'];
        $array_cadastro['complemento'] = $params['complemento'];
        $array_cadastro['bairro'] = $params['bairro'];
        $array_cadastro['cidade'] = $params['cidade'];
        $array_cadastro['uf'] = $params['uf'];
        $array_cadastro['cep'] = $params['cep']; 
        $array_cadastro['telefone'] = $params['tel'];
        $array_cadastro['celular'] = $params['cel'];
        $array_cadastro['email'] = $params['email'];
        $array_cadastro['email_fin'] = $params['email'];
        $array_cadastro['observacao'] = $params['observacao'];
        $array_cadastro['tp_cadastro'] = 1;
        $array_cadastro['dt_cadastro'] = date('Y-m-d H:i:s');
        $array_cadastro['parceiro_id'] = $contador_id;
        $array_cadastro['situacao'] = 1;
        $db_w2b->query_insert('clientes',$array_cadastro);
        $cliente_id = mysql_insert_id($db_w2b->link_id);

        //busca um banco de dados no web finanças para alocar os registros do cliente
        $cliente_db = $db_wf->fetch_assoc('select min(id) id from clientes_db where situacao = 0 for update');
        $db_wf->query('update clientes_db set cliente_id = '.$cliente_id.', situacao = 1 where id = '.$cliente_db['id']);

        //insere usuário no banco de dados do web finanças
        $array_usuario['cliente_id'] = $cliente_id;
        $array_usuario['cliente_db_id'] = $cliente_db['id'];
        $array_usuario['nome'] = $params['nome'];
        $array_usuario['email'] = $params['email'];
        $array_usuario['senha'] = md5($params['senha']);
        $array_usuario['situacao'] = 1;
        $array_usuario['grupo_id'] = 1;
        $array_usuario['dt_cadastro'] = date('Y-m-d H:i:s');
        $array_usuario['financeiro'] = 1; //registro que o usuário se cadastrou para o sistema financeiro
        $usuario_id = $db_wf->query_insert('usuarios',$array_usuario);
        
        //registra situação de acesso do cliente no web finanças
        $array_situacao['cliente_id'] = $cliente_id;
        $array_situacao['situacao'] = 1;
        $db_wf->query_insert('cli_acesso_situacao',$array_situacao);
        
        //Registra no contador de clientes + 1 cliente após o término do cadastro
        $qtd = $db_wf->fetch_assoc('select qtd from contador_clientes where id = 1');
        $qtd = $qtd['qtd'] + 1;
        $db_wf->query('update contador_clientes set qtd = "'.$qtd.'" where id = 1');

        //registra sistema contratado pelo cliente
        $array_sistema = array("sistema_id"=>1,"cliente_id"=>$cliente_id);
        $db_w2b->query_insert('sistemas_clientes',$array_sistema);

        //Consulta o plano do parceiro
        $plano_parceiro = $db_wf->fetch_assoc('SELECT id, vl_mensal, vencimento FROM planos WHERE parceiro_id ='.$contador_id.' AND situacao = 1');
        
        //Adiciona o cliente no plano do parceiro
        $inserir_plano['ano'] = date('Y');
        $inserir_plano['cliente_id'] = $cliente_id;
        $inserir_plano['plano_id'] = $plano_parceiro['id'];
        $inserir_plano['vl_plano'] = $plano_parceiro['vl_mensal'];
        $inserir_plano['periodo'] = 1;
        $inserir_plano['dia_vencimento'] = $plano_parceiro['vencimento'];
        $inserir_plano['dt_cadastro'] = date('Y-m-d H:i:s');
        $db_wf->query_insert('clientes_planos', $inserir_plano);

        $conexaoClienteContabilidade = 0;

        $clienteExiste = false;

    }else{
        
        $cliente_id = $cliente_id['cliente_id'];

        //Conecta no db do cliente
        $dadosDbCliente = $db_wf->fetch_assoc('SELECT db, db_senha FROM clientes_db WHERE cliente_id = '.$cliente_id);
        $db_cliente = new Database("mysql.webfinancas.com", $dadosDbCliente['db'], $dadosDbCliente['db_senha'], $dadosDbCliente['db']);
        
        
        //Verifica se cliente já está conectado a alguma contabilidade
        $conexaoClienteContabilidade = $db_cliente->numRows('select id from conexao where conectado = 1');
        
       //Verifica se o cliente tem menos de 24h de cadastro para readicionar a contabilidade 
       $dt_cliente = $db_wf->fetch_assoc('select dt_cadastro from usuarios where email = "'.$params['email'].'"');
        
       $dt_limite = strtotime(date('Y-m-d', strtotime('+1 day', strtotime($dt_cliente['dt_cadastro']))));
       $dt_hoje = strtotime(date('Y-m-d'));
       
       if($dt_hoje <= $dt_limite && $conexaoClienteContabilidade == 0){
            
           //insere cliente no banco de dados da w2b
           $array_cadastro['nome'] = $params['nome'];
           $array_cadastro['inscricao'] = $params['inscricao'];
           $array_cadastro['cpf_cnpj'] = $params['cpf_cnpj'];
           $array_cadastro['logradouro'] = $params['logradouro'];
           $array_cadastro['numero'] = $params['numero'];
           $array_cadastro['complemento'] = $params['complemento'];
           $array_cadastro['bairro'] = $params['bairro'];
           $array_cadastro['cidade'] = $params['cidade'];
           $array_cadastro['uf'] = $params['uf'];
           $array_cadastro['cep'] = $params['cep']; 
           $array_cadastro['telefone'] = $params['tel'];
           $array_cadastro['celular'] = $params['cel'];
           $array_cadastro['email'] = $params['email'];
           $array_cadastro['email_fin'] = $params['email'];
           $array_cadastro['observacao'] = $params['observacao'];
           $array_cadastro['tp_cadastro'] = 1;
           $array_cadastro['dt_cadastro'] = date('Y-m-d H:i:s');
           $array_cadastro['parceiro_id'] = $contador_id;
           $array_cadastro['situacao'] = 1;
           $db_w2b->query_update('clientes', $array_cadastro ,' id ='.$cliente_id);    
            
        }else{

        $clienteExiste = true;
        $conexaoClienteContabilidade = 1;
    
        }
    }

    if($conexaoClienteContabilidade == 0){
        
        //Conectar contador ao cliente
        $conexao_contador_cliente['email'] = $params['email'];
        $conexao_contador_cliente['cpf_cnpj'] = $params['cpf_cnpj'];
        $conexao_contador_cliente['cliente_id'] = $cliente_id;
        $conexao_contador_cliente['dt_inicio'] = date('Y-m-d H:i:s');
        $conexao_contador_cliente['conectado'] = 1;
        $conexao_contador_cliente['remetente'] = 1;
        
        $db->query_insert('conexao', $conexao_contador_cliente);

        //Conectar cliente ao contador
        $contador_email = $db_w2b->fetch_assoc('SELECT email FROM clientes WHERE id = '.$contador_id);

        $conexao_cliente_contador['email'] = $contador_email['email'];
        $conexao_cliente_contador['contador_id'] = $contador_id;        
        $conexao_cliente_contador['dt_inicio'] = date('Y-m-d H:i:s');
        $conexao_cliente_contador['conectado'] = 1;
        $conexao_cliente_contador['remetente'] = 0;

        if(!$clienteExiste){
            $dadosDbCliente = $db_wf->fetch_assoc('SELECT db, db_senha FROM clientes_db WHERE cliente_id = '.$cliente_id);
            $db_cliente = new Database("mysql.webfinancas.com", $dadosDbCliente['db'], $dadosDbCliente['db_senha'], $dadosDbCliente['db']);
        }

        $db_cliente->query_insert('conexao', $conexao_cliente_contador);

        $db_cliente->close();

        return array("situacao"=>1 ,"notificacao"=>"Cliente cadastro com sucesso.");

    }else{
        
        return array("situacao"=>2 ,"notificacao"=>"Cliente já está conectado a outra contabilidade.");
    }
}

/*
===========================================================================================
VISUALIZAR INFORMATIVO
===========================================================================================
*/

function visualizar($db_w2b, $params){

    $registro = $db_w2b->fetch_assoc('SELECT * FROM clientes WHERE id ='.$params['id']);
   
    $retorno = array("nome"=>$registro['nome'], "inscricao"=>$registro['inscricao'], "cpf_cnpj"=>$registro['cpf_cnpj'], "logradouro"=>$registro['logradouro'], "numero"=>$registro['numero'], "complemento"=>$registro['complemento'], "bairro"=>$registro['bairro'], "cidade"=>$registro['cidade'], "uf"=>$registro['uf'], "cep"=>$registro['cep'], "telefone"=>$registro['telefone'], "celular"=>$registro['celular'], "email"=>$registro['email'], "email_fin"=>$registro['email_fin'], "observacao"=>$registro['observacao']);
   
   return $retorno;
    
    }


/*
===========================================================================================
EDITAR INFORMATIVO
===========================================================================================
 */

function editar($db_w2b, $db_wf, $params){
    
    $cliente_id = $db_w2b->fetch_assoc('select id from clientes where email = "'.$params['email'].'"');
    
    $usuario_id = $db_wf->fetch_assoc('select cliente_id from usuarios where email = "'.$params['email'].'"');
    	
	if($cliente_id['id'] == $params['id'] && $usuario_id['cliente_id'] == $params['id'] || $cliente_id['id'] == false && $usuario_id['cliente_id'] == false){
        
        
        $registro['nome'] = $params['nome'];
        $registro['inscricao'] = $params['inscricao'];
        $registro['cpf_cnpj'] = $params['cpf_cnpj'];
        $registro['logradouro'] = $params['logradouro'];
        $registro['numero'] = $params['numero'];
        $registro['complemento'] = $params['complemento'];
        $registro['bairro'] = $params['bairro'];
        $registro['cidade'] = $params['cidade'];
        $registro['uf'] = $params['uf'];
        $registro['cep'] = $params['cep']; 
        $registro['telefone'] = $params['tel'];
        $registro['celular'] = $params['cel'];
        $registro['email'] = $params['email'];
        $registro['email_fin'] = $params['email'];
        $registro['observacao'] = $params['observacao'];    

        $registro = $db_w2b->query_update('clientes', $registro ,' id ='.$params['id']);    
        
        $registro_usuario_adm['nome'] = $params['nome'];
        $registro_usuario_adm['email'] = $params['email'];
        
        $db_wf->query_update('usuarios', $registro_usuario_adm,' cliente_id ='.$params['id']);
     
        $retorno = array("situacao"=>1, "notificacao"=>"Cliente atualizado com sucesso.");
        
    }else{
        
        $retorno = array("situacao"=>2, "notificacao"=>"O e-mail está sendo útilizado em outra conta.");
    
    }
    
    return $retorno;
}

/*
===========================================================================================
EXCLUIR INFORMATIVO
===========================================================================================
 */

function excluir($db_w2b, $db_wf, $db, $params){   
    
    //remove o parceiro dentro do cadastro de clientes da Web 2 Business
    $remover_parceiro['parceiro_id'] = 1; //quando for removido a id do parceior_id no db_w2b fica = 1, significa que ele saiu e virou da web 2 business. ** Resolver o que iremos fazer depois.
    $db_w2b->query_update('clientes', $remover_parceiro, 'id ='.$params['id']);
    
    $plano= $db_wf->fetch_assoc('SELECT id, vl_mensal FROM planos WHERE parceiro_id = 0 AND situacao = 1');
    
    //Edita o plano
    $editar_plano['plano_id'] = $plano['id'];
    $editar_plano['vl_plano'] = $plano['vl_mensal'];
    $db_wf->query_update('clientes_planos', $editar_plano, 'cliente_id ='.$params['id']);

    session_start();
    $contador_id = $_SESSION['cliente_id'];
    
    /* ===== Desconectar Contador e Cliente ===== */
    
    //Contador conexão
    $conexao_contador_cliente['dt_final'] = date('Y-m-d H:i:s');
    $conexao_contador_cliente['conectado'] = 0;
    
    $db->query_update('conexao', $conexao_contador_cliente, ' cliente_id = '.$params['id']);
    
    //Cliente conexão
    //Localiza os dados para conectar no db do cliente
    $db_cli = $db_wf->fetch_assoc('SELECT db, db_senha FROM clientes_db WHERE cliente_id = '.$params['id']);  
    
    $db_usuario = $db_cli['db'];
    $db_senha = $db_cli['db_senha'];
       
    $conexao_cliente_contador['dt_final'] = date('Y-m-d H:i:s');
    $conexao_cliente_contador['conectado'] = 0;
    
    //Conecta no db do cliente
    $db_cliente = new Database("mysql.webfinancas.com", $db_usuario, $db_senha, $db_usuario);
    
    $db_cliente->query_update('conexao', $conexao_cliente_contador, ' contador_id = '.$contador_id);
    $db_cliente->close();
    /* ===== Desconectar Contador e Cliente ===== */
    
}

    //ALTERAR SENHA DO CLIENTE
    //===========================================================================================

    function alterarSenha($dbW2b, $dbWf, $params){

        $parceiro = $dbW2b->fetch_assoc('select parceiro_id from clientes where id = '.$params['cliente_id']);
        
        if($parceiro['parceiro_id'] == $_SESSION['cliente_id']){

            $dbWf->query('update usuarios set senha = "'.md5($params['senha']).'" where id = '.$params['usuario_id']);

            return array('situacao'=>true,'notificacao'=>'Senha alterada com sucesso.');

        }else{
            
            return array('situacao'=>false,'notificacao'=>'O cliente informado não pertence à sua carteira.');
        }
    }

    //RETORNAR USUÁRIOS DO CLIENTE
    //===========================================================================================

    static function getUsuarios($dbWf,$params){
        
        $usuarios = $dbWf->fetch_all_array('select id, email from usuarios where cliente_id = '.$params['cliente_id'].' order by email');

        return $usuarios;
    }
}

