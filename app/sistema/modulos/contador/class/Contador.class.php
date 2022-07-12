<?php
define('ROOT',$_SERVER['DOCUMENT_ROOT'].'/');

require_once ROOT.'/sistema/servicos/mensagem/MensagemHelper.php';

class Contador{
	
	var $contador_dados = array(
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

        $mensagemHelper = new MensagemHelper();
        $mensagemHelper->EnviarEmail($email_destinatario,$assunto,$conteudo);

        /*
		$email_remetente = "contato@webfinancas.com";
		$nome_remetente = "Web Finanças";
		
		//=========== INICIALIZA O OBJETO QUE ENVIA O EMAIL =======================================
		$transport = Swift_SmtpTransport::newInstance('smtp.webfinancas.com', 587); //$transport = Swift_SmtpTransport::newInstance('smtp.web2business.com.br', 25);
		$transport->setUsername('contato@webfinancas.com');
		$transport->setPassword('W2BSISTEMAS');
		
		$message = Swift_Message::newInstance();
		$message->setSubject($assunto);
		$message->setFrom(array($email_remetente => $nome_remetente));
		//$message->setReturnPath('fabio@web2business.com.br');
	
		$mailer = Swift_Mailer::newInstance($transport);
		//==============================================================================================
		
		$message->setBody($conteudo, 'text/html');
		$message->setTo(array($email_destinatario)); //não precisa limpar o destinatario a cada envio, esta função sobre-escreve o destinatario anterior
		
		$mailer->send($message); 
        */
	}

	/*
	================================================================================================
	LISTA CONEXÕES
	================================================================================================
	*/

    function listaConexoes($db){

		//Conexão no banco da Web 2 Business
		$db_w2b = new Database('mysql.web2business.com.br','web2business','W2BSISTEMAS','web2business');
	
		//Retorna a lista de destinatários aguardando conexão atualizada para o sistema
		$lista_destinatarios = $db->fetch_all_array('select id, email, cliente_id, conectado, date_format(dt_convite, "%d/%m/%Y") as dt_convite, date_format(dt_inicio, "%d/%m/%Y") as dt_inicio, date_format(dt_final, "%d/%m/%Y") as dt_final, remetente from conexao where conectado = 0 OR conectado = 3 order by conectado ASC');
		
		if($lista_destinatarios == true){
		
		$remetente_id = $_SESSION['cliente_id'];
		
		foreach($lista_destinatarios as $l){
										$m = $l['mes'];
										if($m == 01){ $mes = 'Jan';}
										elseif($m == 02){ $mes = 'Fev';}
										elseif($m == 03){ $mes = 'Mar';}
										elseif($m == 04){ $mes = 'Abr';}
										elseif($m == 05){ $mes = 'Mai';}
										elseif($m == 06){ $mes = 'Jun';}
										elseif($m == 07){ $mes = 'Jul';}
										elseif($m == 08){ $mes = 'Ago';}
										elseif($m == 09){ $mes = 'Set';}
										elseif($m == 10){ $mes = 'Out';}
										elseif($m == 11){ $mes = 'Nov';}
										else{ $mes = 'Dez';}

		if($l['conectado'] == 0 || $l['conectado'] == 3){ 
		
		$icone = 'icon_conectar02.png';		
		$nome = $l['email']; 
		
					$menu = ' <a href="'.$remetente_id.'-'.$l['cliente_id'].'-'.$l['id'].'" original-title="Cancelar" class="smallButton btTBwf redB tipS excluirConvite"><img src="images/icons/light/close.png" width="10"></a>';
		
			if($l['remetente'] == 1){
					$menu .= ' <a href="'.$remetente_id.'-'.$l['id'].'" original-title="Reenviar convite" class="smallButton btTBwf greenB tipS reenviarConvites" ><img src="images/icons/light/mail.png" width="10"></a>';
					$title_convite = 'Convite enviado: '.$l['dt_convite'];
					$cor_conexao = "green";
					$conexao='Aguardando convidado'; 
			}else{
					$menu .= ' <a href="'.$remetente_id.'-'.$l['cliente_id'].'-'.$l['id'].'" original-title="Aceitar convite" class="smallButton btTBwf blueB tipS aceitarConvite"><img src="images/icons/light/check.png" width="10"></a>';
					$title_convite = 'Convite recebido: '.$l['dt_convite'];
					$cor_conexao = "blue";
					$conexao='Aguardando você'; 
			}	
	
		}else{ 	
		
		$icone = 'icon_sem_conexao02.png';
		$title_convite = 'Conexão cancelada desde: '.$l['dt_final'];
		$conexao='Conexão cancelada';
			
		//Acessa o nome do remetente
		$nomeDestinatario = $db_w2b->fetch_assoc('select nome from clientes where id = '.$l['cliente_id']);		
		$nome = $nomeDestinatario['nome'];	
		
		$menu = '<a href="'.$l['id'].'" original-title="Convidar novamente" class="smallButton btTBwf greenB tipS reenviarConvites" ><img src="images/icons/light/mail.png" width="10"></a>';
		$cor = 'red';	
		$cor_conexao = "red"; 
			
		}	
		$lista_conexoes .='
			<div class="newUpdate tipN" original-title="'.$title_convite.'" >															
					<span class="lDespesa" style="margin-left:-22px;">
						<a href="javascript://" class="" style="cursor: default; color:#333"><b>'.$nome.'<b/></a>
							<span class="'.$cor_conexao.'">'.$conexao.'</span>
					</span>				 	
					'.$menu.'					 
				</div>	
				
		';
		}
		//===================================================
		
		}/*else{
			
			$lista_conexoes = '<div align="center"> <p>Não existem convites.</p> </div>';
				
			}*/
	
		return $lista_conexoes;

    }


/*
	================================================================================================
	CONVITE EMAIL
	================================================================================================
	*/
	
	function conteudoConvite($texto){
	
	$enderecoArquivos = 'http://www.webfinancas.com/site/img/email_paginas/';
	/*
	$nome = $array_dados['nome'];
	$id =  $array_dados['remetente_id'];
	$id_list = $array_dados['id_list'];
	$email= $array_dados['destinatario_email'];
	$tp = $array_dados['tp'];
	*/
	$conteudo = '
	<style>
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
	
	.btn{
	font-family:Arial, Helvetica, sans-serif;
	font-size:14px;
	padding: 10px;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
	border-radius: 5px;	
	}
	.btn-primary,
	.active > a {
	  text-decoration:none; 
	  border-color: #0088cc;
	  color: #ffffff;
	  text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
	  background-color: #0088cc;
	  border-color: #0044cc #0044cc #002a80;
	  border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
	}
	.btn-primary:hover,
	.active > a:hover {
	  border-color: #0099e6;
	  background-color: #0099e6;
	}
	.btn-primary:active,
	.active > a:active,
	.btn-primary:focus,
	.active > a:focus {
	  border-color: #0077b3;
	  background-color: #0077b3;
	}
	
	</style>
		
	
	
		<div id="palco">
		
	<table width="700" height="100%" border="0" align="center">
			<tr>
			
				<td width="50%" align="center"><img src="http://www.webfinancas.com/site/img/logo_webfinancas_fundo_branco.png" align="middle" class="logo" /> </td>      
				<td width="50%" align="center">
	
									<a href="http://www.facebook.com/" target="_blank" title="Facebook"><img src="'.$enderecoArquivos.'iconFacebook.png"></a></li>
									<a href="http://www.twitter.com/" target="_blank" title="Twitter"><img src="'.$enderecoArquivos.'iconGooglePlus.png"></a></li>
									<a href="http://www.linkedin.com/" target="_blank" title="Linkedin"><img src="'.$enderecoArquivos.'iconLinkedin.png"></a></li>

				</td>
				
			</tr>
			<tr>
			
			<td width="50%" align="justify" valign="top">
			
				<br />
				
			 '.$texto.'		
				<br />
			</td>    
			<td width="50%" align="center" valign="bottom"> 
				 <img src="'.$enderecoArquivos.'mail.png" align="center" class="img" width="215" />	
			 </td>
			 
		</tr>
	</table>	
		
		';
		
		return $conteudo;

	}

	//CONVIDAR CONTADOR/CLIENTES
	//================================================================================================

	function conviteContador($db,$array_dados){
		
		$tp = '2'; //Se o remetente é o contador = 1 ou o cliente = 2		
		$remetente_id = $array_dados['remetente_id']; 
		$destinatario_email = strtolower($array_dados['destinatario_email']);
		$dt_convite = date('Y-m-d H:m:s');
		
		//Conexão no banco do Web Finanças
		$db_wfp = new Database('mysql.webfinancas.com','webfinancas','W2BSISTEMAS','webfinancas');
		
		//Pega a id de cliente do contador
		$verifica_contador = $db_wfp->fetch_assoc('select DISTINCT cliente_id FROM usuarios WHERE email = "'.$destinatario_email.'"  ORDER BY id DESC');

		//Verifica se o convite já foi enviado por você e está ativo
		$verifica_email_contador = $db->fetch_assoc('select DISTINCT id, email, conectado FROM conexao WHERE contador_id = '.$verifica_contador['cliente_id'].' ORDER BY id DESC');
		
	    if($verifica_email_contador['email'] == false || $verifica_email_contador['id'] == false || $verifica_email_contador['conectado'] == 0){
		
		    //Insere no db do remetente o convite e retorna a id da lista de convites
		    $remetente_dados = array('dt_convite' => $dt_convite, 'email' => $destinatario_email, 'conectado' => '0', 'remetente' => '1');
		    $db->query_insert('conexao', $remetente_dados);
		
		    //Pega a ultima id inserida
		    $id_list_id = $db->fetch_assoc('select max(id) as id FROM conexao WHERE email = "'.$destinatario_email.'" AND dt_convite = "'.$dt_convite.'" AND conectado = 0');
		    $id_list = $id_list_id['id'];
		
		    //Conexão no banco da Web 2 Business
		    $db_w2b = new Database('mysql.web2business.com.br','web2business','W2BSISTEMAS','web2business');
		
		    //Acessa o nome do remetente
		    $remetente = $db_w2b->fetch_assoc('select nome, email, cpf_cnpj from clientes where id = '.$remetente_id);
	
		    $assunto = $remetente['nome'].' enviou um convite';		
		
		    //==========================================================

		    $destinatario_dados = $db_wfp->fetch_assoc('select cliente_db_id from usuarios where email = "'.$destinatario_email.'"');
	
		    //Verifica se o destinatario já esta cadastrado
		    if($destinatario_dados == true){
			
			    //Acesso à tabela do banco de dados do contador no Web Finanças
			    $destinatario_db = $db_wfp->fetch_assoc('select cliente_id, db, db_senha from clientes_db where id = '.$destinatario_dados['cliente_db_id'].'');
			    $destinatario_id = $destinatario_db['cliente_id'];
			
			    //Conexão no banco do Destinatario
			    $usuario = $destinatario_db['db']; 
                $senha_db = $destinatario_db['db_senha']; 
                $db_usuario = $destinatario_db['db'];

			    $db_destinatario = new Database('mysql.webfinancas.com',$usuario,$senha_db,$db_usuario);
			
			    //Insere convite no banco de dados do contador
                $contador_dados = array(
                        'dt_convite' => $dt_convite,
                        'email' => $remetente['email'],
                        'cpf_cnpj' => $remetente['cpf_cnpj'],
                        'cliente_id' => $remetente_id,
                        'conectado' => 0,
                    );
                $db_destinatario->query_insert('conexao', $contador_dados);
			
			    //Atualiza a id do contador no banco de dados do cliente
			    $db->query('update conexao set contador_id = '.$destinatario_id.' where id = '.$id_list);
			
			    //Finaliza conexão com DB do destinatario
			    $db_destinatario->close();
		     
            }
		
            //start: Monta dados do convite enviado por e-mail
            $dadosConvite = array(
                "view" => "_ConviteClienteContador.php",
                "nomeRemetente" => $remetente["nome"],
                "remetenteId" => $remetente_id,
                "idList" => $id_list,
                "emailDestinatario" => $destinatario_email,
                "tipoRemetente" => $tp
                );
            //end: Monta dados do convite enviado por e-mail

		    //Envia o convite por email para o Destinatario
		    self::emailEnviar($destinatario_email,$assunto,$dadosConvite);
		
		    //Retorna a lista de destinatários aguardando conexão atualizada para o sistema
		    $lista_conexoes = self::listaConexoes($db);
		
		    //Finaliza conexão com DB da Web 2 Business
		     $db_w2b->close();
		 
		     //Finaliza conexão com DB do Web Finanças Prinicipal
		     $db_wfp->close();
		 
		     //Fecha banco de dados db
		     $db->close();
											
		    $retorno = array("notificacao" => "Convite enviado com sucesso.", "situacao" => 1, "lista_conexoes" => $lista_conexoes);
		    return $retorno;
			
		
		}elseif($verifica_email_contador['conectado'] == 1){
			
			$retorno = array("notificacao" => "Você já está conectado a esse usuário.", "situacao" => 2);
			return $retorno;
			
		}else{	

			//Cria um array com a id do remetente e a id da lista
			$dados = array("id_list" => $remetente_id."-".$verifica_email_contador['id']);
			
			$retorno = self::reenviarConvite($db,$dados);				
			return $retorno;

		}
	}



/*
===========================================================================================
REENVIO CONVITES
===========================================================================================
*/

function reenviarConvite($db,$array_dados){
		
		//Conexão no banco da Web 2 Business
		$db_w2b = new Database('mysql.web2business.com.br','web2business','W2BSISTEMAS','web2business');
		
		$dados_envio = explode("-",$array_dados['id_list']);
		
		$remetente_id = $dados_envio['0'];
		$id_list = $dados_envio['1'];		
		
		$dados_contador = $db->fetch_assoc('select email from conexao where id = '.$id_list);
		$email = $dados_contador['email'];	
		
		//Acessa o nome do remetente
		$remetente = $db_w2b->fetch_assoc('select nome from clientes where id = '.$remetente_id);
				
		$assunto = $remetente['nome'].' enviou um convite';

        //start: Monta dados do convite enviado por e-mail
        $dadosConvite = array(
            "view" => "_ConviteClienteContador.php",
            "nomeRemetente" => $remetente["nome"],
            "remetenteId" => $remetente_id,
            "idList" => $id_list,
            "emailDestinatario" => $email,
            "tipoRemetente" => 2
            );
        //end: Monta dados do convite enviado por e-mail
		
		//Envia o convite por email para o Destinatario
		self::emailEnviar($email,$assunto,$dadosConvite);
		
		$lista_conexoes = self::listaConexoes($db);

		//Finaliza conexão com DB da Web 2 Business
		$db_w2b->close();
		
		$retorno = array("situacao" => 1,"notificacao"=>"Convite reenviado com sucesso.", "lista_conexoes" => $lista_conexoes);
		return $retorno;

}

/*
===========================================================================================
CANCELAR CONEXÕES - EXCLUI O CONVITE, UMA CONEXÃO QUE AINDA NÃO ESTÁ ATIVA
===========================================================================================
*/	

	function cancelarConexoes($db,$array_dados){

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
		
		    $dados_contador = $db_wfp->fetch_assoc('select db, db_senha from clientes_db where cliente_id = '.$contador_id);
		
		    $db_contador =  new Database('mysql.webfinancas.com',$dados_contador['db'],$dados_contador['db_senha'],$dados_contador['db']);

		    //Exclui o registro dentro da tabela do contador
		    $db_contador->query('DELETE FROM conexao WHERE cliente_id = '.$cliente_id.' AND conectado = 0');
		
            $db_wfp->close();
            $db_contador->close();
		}
		
		//Pega o email do contador para informar que o convite foi cancelado
		$email = $db->fetch_assoc('select email from conexao where id = '.$conexao_id);
		
		//Exclui o registro dentro da tabela do cliente
		$db->query('DELETE FROM conexao WHERE id = '.$conexao_id);
		
        //Pega o nome do cliente
		$nome_cliente = $db_w2b->fetch_assoc('select nome from clientes where id = '.$cliente_id);
        
        $db_w2b->close();

		$assunto = $nome_cliente['nome'].' cancelou o convite';
		
        //start: Monta dados do convite enviado por e-mail
        $dadosCancelamento = array(
            "view" => "_CancelarConviteContador.php",
            "nomeRemetente" => $nome_cliente["nome"]
            );
        //end: Monta dados do convite enviado por e-mail

		//Envia o convite por email para o Destinatario
		self::emailEnviar($email['email'],$assunto,$dadosCancelamento);

		$retorno = array("situacao" => 1,"notificacao" => "Convite excluído com sucesso.");

        return $retorno;
	}


/*
===========================================================================================
CANCELAR CONEXÕES ATIVAS
===========================================================================================
*/	

	function cancelarConexoesAtivas($db,$array_dados){
		//Conexão no banco do Web Finanças
		$db_w2b = new Database('mysql.web2business.com.br','web2business','W2BSISTEMAS','web2business');		
		
		//Pega as informações no href explode e preenvhe o remetente_id e cliente_id
		$dados_cancelar = explode('-',$array_dados['cliente_id']);		
		$cliente_id = $dados_cancelar['0'];
		$contador_id = $dados_cancelar['1'];
        $conexao_id = $dados_cancelar['2'];
				
		//Conexão no banco do Web Finanças
		$db_wfp = new Database('mysql.webfinancas.com','webfinancas','W2BSISTEMAS','webfinancas');
		
		$contador_dados = $db_wfp->fetch_assoc('select db, db_senha from clientes_db where cliente_id = '.$contador_id);
		
		//Exclui conexão do banco de dados do contador
        $contador_db =  new Database('mysql.webfinancas.com',$contador_dados['db'],$contador_dados['db_senha'],$contador_dados['db']);
		$contador_db->query('delete from conexao where cliente_id = '.$cliente_id.' and conectado = 1');

		//Pega o email do destinatario para informar que o convite foi cancelado
		$email = $db->fetch_assoc('select email from conexao where id = '.$conexao_id.' AND conectado = 1');

        //Exclui conexão do banco de dados do cliente
        $db->query('delete from conexao where id = '.$conexao_id);

		//Conexão no banco do Web Finanças
		$db_w2b = new Database('mysql.web2business.com.br','web2business','W2BSISTEMAS','web2business');
		
		$nome_remetente = $db_w2b->fetch_assoc('select nome from clientes where id = '.$cliente_id);
		
		$assunto = $nome_remetente['nome'].' finalizou a conexão com você';	
		
        //start: Monta dados do convite enviado por e-mail
        $dadosCancelamento = array(
            "view" => "_CancelarConexaoContador.php",
            "nomeRemetente" => $nome_remetente["nome"]
            );
        //end: Monta dados do convite enviado por e-mail

		//Envia o convite por email para o Destinatario
		self::emailEnviar($email['email'],$assunto,$dadosCancelamento);
		
		$retorno = array("situacao" => 1,"notificacao" => "Conexão cancelada com sucesso.");

		return $retorno;
	}

	
//===========================================================================================
//ACEITAR CONEXÕES


	function aceitarConexoes($db,$array_dados){

        //Informações do destinatario e remetente
		$info_dados = explode('-',$array_dados['cliente_id']);
		$contador_id = $info_dados[0];
		$cliente_id = $info_dados[1];
		$conexao_id = $info_dados[2];
		
		//Conexão no banco do Web Finanças
		$db_wfp = new Database('mysql.webfinancas.com','webfinancas','W2BSISTEMAS','webfinancas');
		
		//Quem enviou o convite
		$contador_dados = $db_wfp->fetch_assoc('select db, db_senha from clientes_db where cliente_id = '.$contador_id);
		$db_contador =  new Database('mysql.webfinancas.com',$contador_dados['db'],$contador_dados['db_senha'],$contador_dados['db']);
		
		//Verifica se o convite ainda esta ativo no momento do ACEITE
		$verifcacao = $db_contador->fetch_assoc('SELECT id FROM conexao WHERE cliente_id = '.$cliente_id.' AND conectado = 0');

	    if($verifcacao){
		
		    $array_dados_contador = array('dt_inicio' => date('Y-m-d H:m:s'),'conectado' => 1);
		    $db_contador->query_update('conexao', $array_dados_contador, 'cliente_id = '.$cliente_id.' AND conectado = 0');

		    //Quem está aceitando o convite
		    $array_dados_cliente = array('dt_inicio' => date('Y-m-d H:m:s'), 'conectado' => 1);
		    $db->query_update('conexao', $array_dados_cliente, 'id = '.$conexao_id);
			
			//Informações do contador conectado
            $contador_info = self::ContadorInfo($contador_id, $conexao_id);

		    $retorno = array("situacao" => 1,"notificacao"=>"Convite aceito com sucesso.", "contador_info" => $contador_info);
		    
	    }else{
		    
            //Remover convite do banco de dados do cliente e remover convite da lista no retorno...
            $db->query('delete from conexao where id = '.$conexao_id);
            $retorno = array("situacao" => 2,"notificacao"=>"<b>Conexão não estabelecida</b>. <br>O contador cancelou o convite.");

        }

        $db_wfp->close();
        $db_contador->close();
        return $retorno;

    }

    //===========================================================================================
    //DADOS DO CONTADOR CONECTADO

    function ContadorInfo($contador_id, $conexao_id){
        
        //Conexão no banco da Web 2 Business
        $db_w2b = new Database('mysql.web2business.com.br','web2business','W2BSISTEMAS','web2business');
        
        //Acessa os dados do contador
        $contador_info = $db_w2b->fetch_assoc('select nome, email, telefone, celular from clientes where id = '.$contador_id);
        $db_w2b->close();

        //Telefone
        $tel = '';
        $cel = '';
        if(!empty($contador_info['telefone'])){ $tel = '<img src="images/icons/dark/phone.png" style="margin-bottom:-3px;">&nbsp;&nbsp;'.$contador_info['telefone']; }
        if(!empty($contador_info['celular'])){ $cel = '<img src="images/icons/dark/phone.png" style="margin-bottom:-3px;">&nbsp;&nbsp;'.$contador_info['celular']; }
        $telefones = $tel.' '.$cel;

        $contador_info = '  
            <div id="div-contador-conectado" style="margin:10px;text-align:left;">
                <span class="span11">
                    <b>'.$contador_info['nome'].'</b> - '.$contador_info['email'].'
                    <br>'.$telefones.'
                </span>
                <span class="span1">
                    <a href="'.$_SESSION['cliente_id'].'-'.$contador_id.'-'.$conexao_id.'" class="smallButton redwB tipS excluirConexao" original-title="Cancelar conexão"><img src="images/icons/light/close.png" width="10"></a>
                </span>
            </div> 
        ';

        return $contador_info;

    }

//===========================================================================================
//ADD CONVERSAS

function addConversa($db,$array_dados){
	
	//Conexão no banco do Web Finanças
	$db_wfp = new Database('mysql.webfinancas.com','webfinancas','W2BSISTEMAS','webfinancas');
	
	$conversas = array("assunto"=>$array_dados['assunto'], "cliente_id"=>$array_dados['cliente_id'], "prestador_id"=>$array_dados['prestador_id'],"dt_cadastro"=>date("Y-m-d H:m:s"));
	$chat_id = $db_wfp->query_insert('conversas', $conversas);

    //Subistituir \r e \rn para <br> 
    $mensagem = preg_replace("/(\\r)?\\n/i", "<br/>", $array_dados['msg']);	

    //Informações do TXT
    $data = date("d/m/Y - H:m:s");
    $dados_arquivoTXT = $array_dados['cliente_id'].'|'.$data.'|'.$mensagem.'|';
    //Eliminar espaço em branco
    $dados_arquivoTXT = rtrim($dados_arquivoTXT,"");	

	//Cria e preenche o arquivo TXT
	$nomeArquivoTXT	= 'chat_'.$chat_id.'.txt'; //nome do arquivo TXT
	$arquivoTXT = '../../../../conversas/'.$nomeArquivoTXT; //Diretório do arquivo TXT
	
	$fp = fopen($arquivoTXT,"a+"); // Cria o arquivo TXT
	fwrite($fp,$dados_arquivoTXT); //."\r\n" - Adiciona as linhas dentro do arquivo TXT
	fclose($fp); //Finaliza o arquivo TXT
	 
	 //Atualiza o nome do arquivo	
	 $nome_arquivo = array("conversa_txt"=>$nomeArquivoTXT);
	 $db_wfp->query_update('conversas', $nome_arquivo," id = ".$chat_id);
	
	$atualizarConversa = '<img src="../sistema/images/user.png" class="floatR bordaRedonda" />
							<div class="messageArea" align="left">
								<span class="aro"></span>
								<div class="infoRow">
									<span class="name"><strong>Rafael Vanzo</strong>:</span>
									<span class="time">'.$data.'</span>
								</div>
							   '.$mensagem.'
							</div>';
	
	
	$retorno = array("atualizarConversa" => $atualizarConversa, "chat_id"=>$chat_id);
		return $retorno;

	}

/*
===========================================================================================
ADD MENSAGEM
===========================================================================================
*/

function addMensagem($db,$array_dados){
	
	//Conexão no banco do Web Finanças
	$db_wfp = new Database('mysql.webfinancas.com','webfinancas','W2BSISTEMAS','webfinancas');
	//Pega o nome do arquivo
	$chat_id = $db_wfp->fetch_assoc('SELECT conversa_txt FROM conversas WHERE id = '.$array_dados['chat_id']);
		
//Subistituir \r e \rn para <br> 
$mensagem = preg_replace("/(\\r)?\\n/i", "<br/>", $array_dados['msg']);	

//Informações do TXT
$data = date("d/m/Y - H:m:s");
$dados_arquivoTXT = $array_dados['cliente_id'].'|'.$data.'|'.$mensagem.'|2-3-4|';
//Eliminar espaço em branco
$dados_arquivoTXT = rtrim($dados_arquivoTXT,"");	

	//Cria e preenche o arquivo TXT
	$nomeArquivoTXT	= $chat_id['conversa_txt']; //nome do arquivo TXT
	$arquivoTXT = '../../../../conversas/'.$nomeArquivoTXT; //Diretório do arquivo TXT
	
	$fp = fopen($arquivoTXT,"a+"); // Edita o arquivo TXT
	fwrite($fp,"\r\n".$dados_arquivoTXT); //."\r\n" - Adiciona as linhas dentro do arquivo TXT
	fclose($fp); //Finaliza o arquivo TXT
	
	$atualizarConversa = '<img src="../sistema/images/user.png" class="floatR bordaRedonda" />
							<div class="messageArea" align="left">
								<span class="aro"></span>
								<div class="infoRow">
									<span class="name"><strong>Rafael Vanzo</strong>:</span>
									<span class="time">'.$data.'</span>
								</div>
							   '.$mensagem.'
							</div>';
	
	
	$retorno = array("atualizarConversa" => $atualizarConversa, "chat_id"=>$array_dados['chat_id']);
		return $retorno;

	}

/*
===========================================================================================
VISUALIZAR MENSAGEMS
===========================================================================================
*/
function visualizarMensagens($db,$array_dados){
	
	//Explode variavel para pegar o chat_id e o cliente_id
	$id = explode('-',$array_dados['chat_id']);
	
	//Conexão no banco do Web Finanças
	$db_wfp = new Database('mysql.webfinancas.com','webfinancas','W2BSISTEMAS','webfinancas');
	//Pega o nome do arquivo
	$chat_id = $db_wfp->fetch_assoc('SELECT conversa_txt FROM conversas WHERE id = "'.$id['0'].'"');

	//Preenche o caminnho e nome do arquivo TXT
	$nomeArquivoTXT	= $chat_id['conversa_txt']; //nome do arquivo TXT
	$arquivoTXT = '../../../../conversas/'.$nomeArquivoTXT; //Diretório do arquivo TXT


	//Conexão no banco do Web 2 Business
	$db_w2b = new Database('mysql.web2business.com.br','web2business','W2BSISTEMAS','web2business');	
	
	$fp = fopen($arquivoTXT,"a+"); //Lê o arquivo TXT
$c = 1;	
while((!feof($fp)) and ($c <= 5)){
$c++;
	
$linha = fgets($fp);
$info = explode("|",$linha);

$nome = $db_w2b->fetch_assoc('SELECT nome FROM clientes WHERE id = "'.$info['0'].'"');

if($info['0'] == $id['1']){ $by = 'by_me'; $float = 'floatR'; }else{ $by = 'by_user'; $float = 'floatL'; }

	 $atualizarConversa .= '<li class="'.$by.'">
						<img src="../sistema/images/user.png" class="'.$float.' bordaRedonda" />
						<div class="messageArea" align="left">
							<span class="aro"></span>
							<div class="infoRow">
								<span class="name"><strong>'.$nome['nome'].'</strong>:</span>
								<span class="time">'.$info[1].'</span>
							</div>
							'.$info[2].'
						</div>
					</li>';
								
  }     
            

	fclose($fp); //Finaliza o arquivo TXT
		
	
	$retorno = array("atualizarConversa" => $atualizarConversa, "chat_id"=>$id['0']);
		return $retorno;

	}	

/*
===========================================================================================
LISTAR CONTAS FINANCEIRAS
===========================================================================================
*/

	function cfListar($db,$mes,$ano){

		$array_contas = $db->fetch_all_array('
			select distinct rc.conta_id, c.id, banco_id, numero, vl_saldo, descricao
			from contas c
			left join remessa_contabil rc on c.id = rc.conta_id and rc.mes = '.$mes.' and rc.ano = '.$ano.' and rc.operacao = 1
		');
		
		$contas = '';
		
		foreach($array_contas as $conta){
			
			if($conta['conta_id']){
				$situacao = 'Enviado';
				$class_situacao = 'E';
			}else{
				$situacao = 'Pendente';
				$class_situacao = 'P';
			}
				
			$id_banco = $conta['banco_id'];
			$banco = $db->fetch_assoc("select * from bancos where id = ".$id_banco);	
			if(!empty($banco['logo'])){ $logo_banco = $banco['logo'];
			}else{ $logo_banco = 'bank.png'; }
			if(empty($banco['nome'])){ $instituicaoFinanceira = 'Livro de Caixa'; }else{ $instituicaoFinanceira = '(<b>'.$banco['codigo'].'</b>) '.$banco['nome']; }	
	
			$contas .= '
				<tr class="gradeA" id="tbl-cf-row-'.$conta['id'].'">

					<td class="updates newUpdate">
	
							<div class="lnctCheckbox" style="float:left; padding-top:12px; padding-bottom:-12px;margin-right:15px">
								<input type="checkbox" value="'.$conta['id'].'" id="check_'.$conta['id'].'" class="'.$class_situacao.'"/>
							</div>
									
							<div class="uDate tbWF" align="center" style="padding-right:8px; padding-bottom: 5px; margin-right:-8px; "> <img src="images/bancos/'.$logo_banco.'" alt="" class="floatL" style="-webkit-border-radius : 2px; -moz-border-radius: 2px;"></div>
								<span class="lDespesa tbWF" >
									<a href="javascript://void(0);" style="cursor: default;" original-title="Descrição" class="tipS" ><strong >'.$conta['descricao'].'</strong></a>
										<span original-title="Instituição Financeira" class="tipN">'.$instituicaoFinanceira.'</span>
								</span>											
												
							<!--
							<div class="tbWFoption">										
								<a href="'.$conta['id'].'" original-title="Excluir" class="smallButton btTBwf redB tipS contasExcluir"><img src="images/icons/light/close.png" width="10"></a>		
								<a href="javascript://void(0);" original-title="Editar" class="smallButton btTBwf greyishB tipS"  onClick="contasVisualizar('.$conta['id'].')"><img src="images/icons/light/pencil.png" width="10"></a>											
							</div>
	
							<div class="tbWFvalue tipS" original-title="Saldo atual">R$ '.number_format($conta['vl_saldo'],2,',','.').' </div>
							-->
	
							<div class="tbWFvalue tipS" original-title="Saldo atual"> '.$situacao.' </div>
	
				  </td>
			';
		}
		
		$contas = '
			<table cellpadding="0" cellspacing="0" border="0" class="display dTableExtratoBanco" style="border-top:0px">
			
			<thead>
			
			<tr style="border-bottom: 0px solid #e7e7e7;" role="row">
			
				<th class="ckbHeaderCell" style="padding:1px 0px 1px 15px" role="columnheader" rowspan="1" colspan="1">
				
					<div class="sItem" style="float:left; width:20px; margin-left:-7px; margin-top:-30px; padding-left:3px; padding-right:9px; padding-top:1px; padding-bottom:2px; border:1px solid #CCC; background:#F9F9F9;">
				
						<input type="checkbox" id="ckbTblHeader" onclick="lnctChecarTodos(\'\');" style="padding-left:20px; padding-bottom:10px;">
						
						<span class="DataTables_sort_icon css_right ui-icon ui-icon-triangle-1-s btnDropDownCk" id="listItens" style="margin-left:7px;margin-top:-8px;position:absolute;top:50%;right:5px;"></span>
				
						<div class="statsDetailed" id="dropDownMenuCk" style="margin-top:11px;">											
								<div class="statsContent" align="left" >
										<div class="statsUpdate statsUpdateCk">
												<input type="checkbox" id="ckbDropDownHeader"> <label for="ckbDropDownHeader">Todos</label> <div class="clear"></div>
										</div>
																												 
										<div id="ckbDropDownList">
											<div class="statsUpdate statsUpdateCk">
													<input type="checkbox" value="P" class="ckbListItem" id="tpLnctCk01"> <label for="tpLnctCk01">Pendentes</label> <div class="clear"></div>
											</div>
										 
											<div class="statsUpdate statsUpdateCk">
													<input type="checkbox" value="E" class="ckbListItem" id="tpLnctCk02"> <label for="tpLnctCk02">Enviadas</label> <div class="clear"></div>
											</div>
										</div>
								</div>
						</div>
					
					</div>
				
				</th>
				
			</tr>
			</thead>
			<tbody id="contas-financeiras">
				'.$contas.'
			</tbody>
			</table>
		';
	
	
		return $contas;
		
	}

/*
===========================================================================================
ENVIAR REMESSA CONTÁBIL
===========================================================================================
*/
	
	function RemessaContabil($db,$dados){
		$dt_ini = $dados['ano'].'-'.$dados['mes'].'-01';
		$dt_fim_ts = mktime(0,0,0,$dados['mes']+1,'00',$dados['ano']);
		$dt_fim = date('Y-m-d',$dt_fim_ts);

		$mes_atual = (int)date('m');
		$ano_atual = (int)date('Y');

		//if($dados['mes'] <= $mes_atual && $dados['ano'] <= $ano_atual){
			$array_cf = explode(',',$dados['cf']);
			if($array_cf[0]!=''){
				foreach($array_cf as $cf_id){

					$array_balanco = self::Balanco($db,array('dt_ini'=>$dt_ini,'dt_fim'=>$dt_fim,'cf_id'=>$cf_id)); 

					$array_insert = array(
						'conta_id' => $cf_id,
						'operacao' => 1,
						'vl_rcbt' => $array_balanco['vl_rcbt'],
						'qtd_rcbt' => $array_balanco['qtd_rcbt'],
						'vl_pgto' => $array_balanco['vl_pgto'],
						'qtd_pgto' => $array_balanco['qtd_pgto'],
						'saldo' => $array_balanco['vl_balanco'],
						'mes' => $dados['mes'],
						'ano' => $dados['ano'],
						'dt_cadastro' => date('Y-m-d H:i:s')
					);

					//verifica se a remessa já foi enviada
					//$is_exist_remessa = $db->fetch_assoc('select id from remessa_contabil where conta_id = '.$cf_id.' and mes = '.$dados['mes'].' and ano = '.$dados['ano'].' and operacao = 1');

					//if($is_exist_remessa){
						//$db->query_update('remessa_contabil',$array_insert,'id = '.$is_exist_remessa['id']);
					//}else{
						$db->query_insert('remessa_contabil',$array_insert);
					//}
					
				}
			}
		//}
	}

/*
===========================================================================================
HISTÓRICO DA REMESSA CONTÁBIL
===========================================================================================
*/

	function RemessaHistorico($db,$dados){

		$mes = $dados['mes'];
		if($mes<10)
			$mes = '0'.$mes;
		
		$ano = $dados['ano'];

		if($mes=='01')
			$nome_mes = 'Janeiro';
		elseif($mes=='02')
			$nome_mes = 'Fevereiro';
		elseif($mes=='03')
			$nome_mes = 'Março';
		elseif($mes=='04')
			$nome_mes = 'Abril';
		elseif($mes=='05')
			$nome_mes = 'Maio';
		elseif($mes=='06')
			$nome_mes = 'Junho';
		elseif($mes=='07')
			$nome_mes = 'Julho';
		elseif($mes=='08')
			$nome_mes = 'Agosto';
		elseif($mes=='09')
			$nome_mes = 'Setembro';
		elseif($mes=='10')
			$nome_mes = 'Outubro';
		elseif($mes=='11')
			$nome_mes = 'Novembro';
		else
			$nome_mes = 'Dezembro';
			
		$relatorio = '';

		$array_cf = $db->fetch_all_array("select distinct conta_id from remessa_contabil where mes = ".$dados['mes']." and ano = ".$ano);

		foreach($array_cf as $cf){

			$n = 0;
			$historico = '';

			$array_cf_historico = $db->fetch_all_array('
				select b.nome, c.descricao, rc.operacao, rc.saldo, rc.vl_rcbt, rc.qtd_rcbt, rc.vl_pgto, rc.qtd_pgto, date_format(rc.dt_cadastro, "%d/%m/%Y") dt_ocorrencia, time_format(rc.dt_cadastro, "%T") hora_ocorrencia
				from remessa_contabil rc
				join contas c on rc.conta_id = c.id
				join bancos b on c.banco_id = b.id
				where rc.conta_id = '.$cf['conta_id'].'
					and mes = '.$dados['mes'].' 
					and ano = '.$ano.'
				order by dt_cadastro');
	
			foreach($array_cf_historico as $cf_historico){

			  $n +=1;
			  if($n % 2){ $bg_color = '#F0F0F0'; }else{ $bg_color = '#FFF'; }

				if($cf_historico['operacao']==1)
					$operacao = 'Remessa enviada para contabilidade';
				else
					$operacao = 'Arquivo contábil gerado pela contabilidade';

				$historico .= '
					<tr bgcolor="'.$bg_color.'">
						<td align="">'.$cf_historico['dt_ocorrencia'].' - '.$cf_historico['hora_ocorrencia'].'</td>
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
											<div class="nomeRelatorioB" align="left"><font class="nomeRelatorio">Período de referência:</font> '.$nome_mes.' de '.$ano.'</div> <br>
										</div>
									</div>
									<div align="right" class="cabecalho" width="150"><img src="../../../images/logo_webfinancas_fundo_branco.png" width="150"></div>
		';
		
		$pdfFooter = '
									<div class="rodape" width="280" align="left">Emitido: '.date('m/d/Y').' as '.date('H:i:s').' </div>
									<div class="rodape" width="150" align="center"><b>{PAGENO}/{nbpg}</b></div>
									<div class="rodape" width="255" align="right">webfinancas.com</div>
		';
		
		self::pdfGerar($relatorio,$pdfHeader,$pdfFooter,"Historico-Contabil",'t');

	}

/*
===========================================================================================
GERAR PDF
===========================================================================================
*/

	function pdfGerar($relatorio,$pdfHeader,$pdfFooter,$nomeRelatorio,$tp_print,$orientation="A4-L"){
	
		require("../../../php/MPDF/mpdf.php");
		
		$mpdf=new mPDF('c'); 
		
		$mpdf->SetDisplayMode('fullpage');
		
		//$mpdf->ignore_invalid_utf8 = true;
		
		//$mpdf->allow_charset_conversion = true;
		
		//$mpdf->charset_in='UTF-8';
		
		// LOAD a stylesheet
		//$stylesheet = file_get_contents('mpdfstyleA4.css');
		$stylesheet = file_get_contents('../../../css/css_relatorios.css');
		
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
			$mpdf->Output($nomeRelatorio.'.pdf','D');
		}
		//exit;
	
	}

/*
===========================================================================================
CÁLCULO DO SALDO ANTERIOR
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
	
}






