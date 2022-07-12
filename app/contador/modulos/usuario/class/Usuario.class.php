<?php
class Usuario{
	
	var $usuario_dados = array(
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
	LOGIN
	================================================================================================
	*/
	
	function login($db,$array_dados){

		$email = $array_dados['email'];
		$senha = md5($array_dados['senha']);
	
		$query= "
						select u.id, u.cliente_id, u.cliente_db_id, u.situacao, u.primeiro_acesso, cas.situacao cli_acesso_situacao
						from usuarios u, cli_acesso_situacao cas
						where u.email = '".$email."'
							and u.senha = '".$senha."'
							and u.cliente_id = cas.cliente_id
		";

		$usuario = $db->fetch_assoc($query);

		if(!empty($usuario)){
			if($usuario['situacao']==1){
				$cliente_db = $db->fetch_assoc('select db, db_senha from clientes_db where id = '.$usuario['cliente_db_id']);
				$retorno['notificacao'] = "";
				$retorno['situacao'] = 1;
				$retorno['cli_acesso_situacao'] = $usuario['cli_acesso_situacao'];
				$retorno['permissao_contador'] = "1";
				$retorno['cliente_id'] = $usuario['cliente_id'];
				$retorno['usuario_id'] = $usuario['id'];
				$retorno['email'] = $email;
				$retorno['cliente_db'] = $cliente_db['db'];
				$retorno['cliente_db_senha'] = $cliente_db['db_senha'];
				$retorno['primeiro_acesso'] = $usuario['primeiro_acesso'];
				
				$db->query('update usuarios set primeiro_acesso = 1 where id = '.$usuario['id']);
			}else{
				$retorno['notificacao'] = "Usuário inativo.";
				$retorno['situacao'] = 2;
			}
		}else{
			$retorno['notificacao'] = "E-mail ou senha inválida.";
			$retorno['situacao'] = 2;
		}
	
		return $retorno;

	}
	
	
	/*
	================================================================================================
	ALTERAR SENHA
	================================================================================================
	*/

	function senhaAlterar($db,$array_dados){

		$usuario_id = $array_dados['usuario_id'];
		$senha = md5($array_dados['senha']);
		
		$alterar_senha = $db->query('update usuarios set senha = "'.$senha.'" where id = '.$usuario_id);
		if(!empty($alterar_senha)){
			$retorno = array("notificacao" => "Senha alterada com sucesso.", "situacao" => 1);
			return $retorno;
		}else{
			$retorno = array("notificacao" => "Não foi possível alterar a senha. Por favor tente novamente mais tarde.", "situacao" => 2);
			return $retorno;
		}
	}
	
	/*
	================================================================================================
	RECUPERAR SENHA
	================================================================================================
	*/
	
	function senhaRecuperar($db,$array_dados){
		$email = $array_dados['email'];
	
		$query= "
						select id
						from usuarios
						where email = '$email'
		";
		
		$usuario = $db->fetch_array($db->query($query));

		if(!$usuario){
			$retorno = array("situacao"=>2,"notificacao"=>"Preencha um email cadastrado.");
		}else{

			$senha = self::geradorSenha();
			$senha_criptografada = md5($senha);

			//envia mensagem para o estabelecimento com os dados de acesso ao sistema
			$assunto = "Nova senha de acesso";
			
				// ========================== Conteúdo ============================
	

	$enderecoArquivos = 'http://www.webfinancas.com/site/img/email_paginas/';
	
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
			
			<td width="50%" align="center" valign="top">
			
				<br />
				
			 <h2> Recuperação de senha! </h2>
				<p>Nova Senha: <b>'.$senha.'</b> <br><br>
				<a href="https://www.webfinancas.com/#login" target="_blank"><img src="http://www.webfinancas.com/site/img/email_paginas/bt_entrar.png"></a>
					
			
			</td>    
			<td width="50%" align="center" valign="bottom"> 
				 <img src="'.$enderecoArquivos.'cadeado.png" align="center" class="img" width="215" />	
			 </td>
			 
		</tr>
	</table>	
		';
		
		// ================================================================================
						
			$email_destinatario = $email;

			self::emailEnviar($email_destinatario,$assunto,$conteudo);

			$db->query('update usuarios set senha = "'.$senha_criptografada.'" where id = '.$usuario['id']);

			$retorno = array("situacao"=>1,"notificacao"=>"<br> Uma nova senha foi enviada para o email informado.");
		}
	
		return $retorno;
	}

	/*
	================================================================================================
	GERADOR DE SENHA
	================================================================================================
	*/
	
	function geradorSenha($tipo="L N L N L N") {
		//o explode retira os espaços presentes entre as letras (L) e números (N)        
			$tipo = explode(" ", $tipo);
		
		//Criação de um padrão de letras e números (no meu caso, usei letras maiúsculas
		//mas você pode intercalar maiusculas e minusculas, ou adaptar ao seu modo.)
			$padrao_letras = "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|X|W|Y|Z";
			$padrao_numeros = "0|1|2|3|4|5|6|7|8|9";
		
		//criando os arrays, que armazenarão letras e números
		//o explode retire os separadores | para utilizar as letras e números
			$array_letras = explode("|", $padrao_letras);
			$array_numeros = explode("|", $padrao_numeros);
		
		//cria a senha baseado nas informações da função (L para letras e N para números)
			$senha = "";
			for ($i=0; $i<sizeOf($tipo); $i++) {
					if ($tipo[$i] == "L") {
							$senha.= $array_letras[array_rand($array_letras,1)];
					} else {
							if ($tipo[$i] == "N") {
									$senha.= $array_numeros[array_rand($array_numeros,1)];
							}
					}
			}
			
		//informa qual foi a senha gerada para o usuário naquele momento
		return $senha;
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
		
		$mailer->send($message); 

	}
	
	/*
	================================================================================================
	USUARIO EDITAR
	================================================================================================
	*/

	function usuariosEditar($db,$array_dados){
		//Conexão no banco da Web 2 Business
		$db_w2b = new Database('mysql.web2business.com.br','web2business','W2BSISTEMAS','web2business');
		
		$cliente_id = $array_dados['cliente_id'];
		unset($array_dados['cliente_id']);
		unset($array_dados['funcao']);
		unset($array_dados['PHPSESSID']);
		
		
		$usuario_editar = $db_w2b->query_update('clientes', $array_dados, 'id = '.$cliente_id);
		if(!empty($usuario_editar)){
			$retorno = array("notificacao" => "Registros atualizados com sucesso.", "situacao" => 1);
			return $retorno;
		}else{
			$retorno = array("notificacao" => "Não foi possível atualizar. Por favor tente novamente mais tarde.", "situacao" => 2);
			return $retorno;
		}
	}
	
	/*
	================================================================================================
	PLANO EDITAR
	================================================================================================
	*/

	function planoEditar($db,$array_dados){
		//Conexão no banco geral WF
		$db_wf = new Database('mysql.webfinancas.com','webfinancas','W2BSISTEMAS','webfinancas');
		
		/* ==================== */

		$plano_id = $array_dados['plano_id'];
		$tp_plano = $array_dados['tp_plano'];
				
		if($tp_plano == '1'){ $valorPlano = 'vl_mensal'; }elseif($tp_plano == '2'){ $valorPlano = 'vl_trimestral'; }elseif($tp_plano == '3'){ $valorPlano = 'vl_semestral'; }else{ $valorPlano = 'vl_anual'; }
		
		//buscar valor do plano
		$buscar_valores = $db_wf->fetch_assoc('select '.$valorPlano.' from planos where id ='.$plano_id);
		if($tp_plano == '1'){ $dadosP['vl_plano'] = $buscar_valores['vl_mensal'];  }elseif($tp_plano == '2'){ $dadosP['vl_plano'] = $buscar_valores['vl_trimestral'];  }elseif($tp_plano == '3'){ $dadosP['vl_plano'] = $buscar_valores['vl_semestral'];  }else{ $dadosP['vl_plano'] = $buscar_valores['vl_anual'];  }
				
		$cliente_id = $array_dados['cliente_id'];
		$dadosP['dia_vencimento'] = $array_dados['vencimento'];
		$dadosP['periodo'] = $tp_plano;
		//Atualiza o plano
		$planoEditar = $db_wf->query_update('clientes_planos', $dadosP, 'cliente_id = '.$cliente_id);
		
		/* ==================== */ 
		
		$jsonTxt = str_replace('\"','"',$array_dados['modulos']);
		$jsonObj = json_decode($jsonTxt, true);
		$array_modulos = $jsonObj;
		
		$cli_fatura_vl_total = 0;
		
		foreach($array_modulos as $modulo){
			$cli_modulo = $db_wf->fetch_assoc('select id, modulo_id, valor from clientes_modulos where cliente_id = '.$cliente_id.' and modulo_id = '.$modulo['modulo_id']);
			if(!empty($cli_modulo)){
				if($modulo['operacao']==0){
					$db_wf->query('delete from clientes_modulos where id = '.$cli_modulo['id']);
				}else{
						$vl_modulos = $db_wf->fetch_assoc('select ano, grupo_id, '.$valorPlano.' from planos_modulos where id ='.$cli_modulo['modulo_id']);
						
						if($tp_plano == '1'){ 
							$dados_modulos['valor'] = $vl_modulos['vl_mensal']; 
						}elseif($tp_plano == '2'){ 
							$dados_modulos['valor'] = $vl_modulos['vl_trimestral']; 
						}elseif($tp_plano == '3'){ 
							$dados_modulos['valor'] = $vl_modulos['vl_semestral']; 
						}else{ 
							$dados_modulos['valor'] = $vl_modulos['vl_anual']; }
							
					~		$dados_modulos['ano'] = $vl_modulos['ano'];
							$dados_modulos['grupo_id'] = $vl_modulos['grupo_id'];
						
						$db_wf->query_update('clientes_modulos',$dados_modulos,'cliente_id = '.$cliente_id.' and modulo_id = '.$modulo['modulo_id']);
//					$cli_fatura_vl_total += $cli_modulo['valor'];
				}
			}else{
				if($modulo['operacao']==1){
					
					$ano_grupo_id = $db_wf->fetch_assoc('select ano, grupo_id from planos_modulos where id ='.$modulo['modulo_id']);
					
					$array_insert['cliente_id'] = $cliente_id;
					$array_insert['modulo_id'] = $modulo['modulo_id'];
					$array_insert['ano'] = $ano_grupo_id['ano'];
					$array_insert['grupo_id'] = $ano_grupo_id['grupo_id'];
					$array_insert['valor'] = $modulo['vl_modulo'];
					$array_insert['situacao'] = 1;
					$array_insert['dt_cadastro'] = date('Y-m-d H:i:s');
					$db_wf->query_insert('clientes_modulos',$array_insert);
					$cli_fatura_vl_total += $modulo['vl_modulo'];
				}
			}
		}
		
	
		
		/* ======== Retorno no Perfil do Usuário na tela do Planos Contratados ========== */
			
					$db_wf = new mysqli('mysql.webfinancas.com', 'webfinancas', 'W2BSISTEMAS', 'webfinancas');
			
					$cliente_id = $_SESSION['cliente_id'];					
					$dados_plano = mysqli_fetch_assoc(mysqli_query($db_wf,'select plano_id, vl_plano, periodo, dia_vencimento from clientes_planos where cliente_id = '.$cliente_id));
					


                                  	if($dados_plano['periodo'] == '1'){ 
																			$plContratado['tpPlano'] = "Mensal"; 
																		}elseif($dados_plano['periodo'] == '2'){ 
																			$plContratado['tpPlano'] = "Trimestral"; 
																		}elseif($dados_plano['periodo'] == '3'){ 
																			$plContratado['tpPlano'] = "Semestral"; 
																		}elseif($dados_plano['periodo'] == '4'){ 
																			$plContratado['tpPlano'] = "Anual";  
																		}


																	 $dia_vencimento = $dados_plano['dia_vencimento']; if($dia_vencimento < '10'){ $plContratado['diaVencimento'] = '0'.$dia_vencimento; }else{ $plContratado['diaVencimento'] = $dia_vencimento; }  

   $produtos .= '<div class="updates">
                  
                  	<!-- Modulo -->
									<div class="newUpdate">
									                  
										<span id="plNome" class="lReceita" style="padding-top: 10px;  padding-left: 0; font-size: 12px; ">';
											$plano_id = $dados_plano['plano_id'];
											$dados_plano_nome = mysqli_fetch_assoc(mysqli_query($db_wf,'select nome from planos where id = '.$plano_id)); 
   $produtos .= '<img src="images/zero.png" align="middle" class="tipS" original-title="Sistema contratado."> &nbsp; '.$dados_plano_nome['nome'];

   $produtos .= '</span>
									
										<div id="vlPlanoB" style="float:right; padding:10px 5px; font-size:12px;">';

														$valor_plano = number_format($dados_plano['vl_plano'],2,',','.');
                            if($dados_plano['periodo'] == '1'){ 
                               $produtos .= "R$ ".$valor_plano." / mês"; 
                            }elseif($dados_plano['periodo'] == '2'){ 
                               $produtos .= "R$ ".$valor_plano." / trimestre"; 
                            }elseif($dados_plano['periodo'] == '3'){ 
                               $produtos .= "R$ ".$valor_plano." / semestre"; 
                            }elseif($dados_plano['periodo'] == '4'){ 
                             	 $produtos .=  "R$ ".$valor_plano." / ano";  
                            }

   $produtos .= '</div>
										 
									</div>
									<!-- Fim modulo -->';

										$m_plano_modulo = mysqli_query($db_wf,'select modulo_id, valor from clientes_modulos where cliente_id = '.$cliente_id);		
									
									if($num_plano_modulo = mysqli_num_rows($m_plano_modulo) > 0){
										
										while($dados_plano_modulo = mysqli_fetch_assoc($m_plano_modulo)){	
											$modulo_id = $dados_plano_modulo['modulo_id'];
											$dados_plano_modulo_nome = mysqli_fetch_assoc(mysqli_query($db_wf,'select nome, situacao from planos_modulos where id = '.$modulo_id)); 
                  
   $produtos .= '<!-- Modulo -->
									<div class="newUpdate">
       
                  <span id="plModulo" class="lReceita" style="padding-top: 10px; padding-left: 0; font-size: 12px; ">';

	 if($dados_plano_modulo_nome['situacao'] == 1){  $icon = '<img src="images/subIcon3.png" align="middle" class="tipS" original-title="O módulo que seguem valores e reajustes atuais."> &nbsp; ';  }else{ $icon = '<img src="images/subIcon2.png" align="middle" class="tipS" original-title="O módulo que seguem valores e reajustes antigos."> &nbsp; '; } 

   $produtos .= $icon.$dados_plano_modulo_nome['nome'];
   $produtos .= '</span>
									
										<div id="vlPlanoM" style="float:right; padding:10px 5px; font-size:12px;">';

														$valor_modulos = number_format($dados_plano_modulo['valor'],2,',','.');
                            if($dados_plano['periodo'] == '1'){ 
                               $produtos .= "+ R$ ".$valor_modulos." / mês"; 
                            }elseif($dados_plano['periodo'] == '2'){ 
                               $produtos .= "+ R$ ".$valor_modulos." / trimestre"; 
                            }elseif($dados_plano['periodo'] == '3'){ 
                               $produtos .= "+ R$ ".$valor_modulos." / semestre"; 
                            }elseif($dados_plano['periodo'] == '4'){ 
                               $produtos .= "+ R$ ".$valor_modulos." / ano";  
                            }

   $produtos .= '</div>
										 
									</div>
									<!-- Fim modulo -->';

								 		} 
									}

  $produtos .= '</div>';
          

						$vl_total_modulo = mysqli_fetch_assoc(mysqli_query($db_wf,'select sum(valor) as valor from clientes_modulos where cliente_id = '.$cliente_id)); 

          
																	$valor_total = number_format($vl_total_modulo['valor'] + $dados_plano['vl_plano'],2,',','.');
																	
																		if($dados_plano['periodo'] == '1'){ 
																			$plContratado['vlTotal'] = "R$ ".$valor_total." / mês"; 
																		}elseif($dados_plano['periodo'] == '2'){ 
																			$plContratado['vlTotal'] = "R$ ".$valor_total." / trimestre"; 
																		}elseif($dados_plano['periodo'] == '3'){ 
																			$plContratado['vlTotal'] = "R$ ".$valor_total." / semestre"; 
																		}elseif($dados_plano['periodo'] == '4'){ 
																			$plContratado['vlTotal'] = "R$ ".$valor_total." / ano";  
																		}
        
		
		/* =========================================================================================== */
				
		if(!empty($planoEditar)){
			$retorno = array("notificacao" => "Registros atualizados com sucesso.", "situacao" => 1, "tpPlano" => $plContratado['tpPlano'], "diaVencimento" => $plContratado['diaVencimento'], "produtos" => $produtos, "vlTotal" => $plContratado['vlTotal']);
		}else{
			$retorno = array("notificacao" => "Não foi possível atualizar. Por favor tente novamente mais tarde.", "situacao" => 2);
		}
		return $retorno;
		
	} 

	/*
	================================================================================================
	REGISTRO CLIENTES HISTÓRICOS
	================================================================================================
	*/

	function clientesHistoricos($array_dados){
		/* Inclui -> tipo = 1; Alterar -> tipo = 2; Cancelar -> tipo = 3; */		
		//Conexão no banco da Web 2 Business
		$db_wf = new Database('mysql.webfinancas.com','webfinancas','W2BSISTEMAS','webfinancas');		
		$array_dados['dt_cadastro'] = date('Y-m-d H:i:s');
		$array_dados['ip'] = $_SERVER["REMOTE_ADDR"];		
		$historico = $db_w2b->query_isert('clientes_historicos', $array_dados);		
			return 1;
	}	
	
	
	/*
	================================================================================================
	CONVIDAR CONTADOR/CLIENTES
	================================================================================================
	*/

	function conviteContador($db,$array_dados){
		//Conexão no banco da Web 2 Business
		$db_w2b = new Database('mysql.web2business.com.br','web2business','W2BSISTEMAS','web2business');
		
		$contador_id = $array_dados['cliente_id']; //contador id
		$email_cliente = $array_dados['email_cliente'];
		$dt_convite = date('Y-m-d H:m:s');
		
		$convite_cliente = $db_w2b->fetch_assoc('select nome from clientes where id = '.$contador_id);
		
		$assunto = $convite_cliente['nome'].' enviou um convite';

		$enderecoArquivos = 'http://www.webfinancas.com/site/img/email_paginas/';

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
				
			 <h2> Olá, </h2>
				<p style="line-height:20px;">Estamos utilizando o Web Finanças uma ferramenta simples e intuitiva facilitará muito a integração e a comunicação entre a nosso escritório e a sua empresa.<br><br>
					 Aceite esse convite e, em minutos, estaremos conectados.
	
				<br><br>	
				Atenciosamente,
				<br>	
				
					<b>'.$convite_cliente['nome'].'</b>	
			</p>
			<br>
			<div align="center"><a href="http://www.web2business.com.br/webfinancas/convite.php?id='.$contador_id.'&email='.$email_cliente.'&tp=2" target="_blank"><img src="http://www.webfinancas.com/site/img/email_paginas/bt_aceitar_convite.png"></a></div>
			
			</td>    
			<td width="50%" align="center" valign="bottom"> 
				 <img src="'.$enderecoArquivos.'mail.png" align="center" class="img" width="215" />	
			 </td>
			 
		</tr>
	</table>	
		
		';
	
	
		
		if(!empty($convite_cliente)){			
		
		$db_contador = new Database('mysql.web2business.com.br','web2business04','W2BSISTEMAS','web2business04'); //db dinâmico
		$db_contador->query("insert into clientes (email, dt_convite, visualizado) values ('".$email_cliente."','".$dt_convite."', 0)"); 
	
			//Verificação se o cliente já existe e inserção do convite dentro do sistema financeiro
		
			//$db_wf = new Database('mysql.webfinancas.com','webfinancas','W2BSISTEMAS','webfinancas'); descomentar após migração **
			//$verificacao = $db_wf->fetch_assoc('select cliente_db_id, financeiro from usuarios where email = '.$email_cliente); descomentar após migração **
			
			//if($verificacao['financeiro'] == 1){ //remover após migração***
				// $cliente_db_id = $verificacao['cliente_db_id'];  descomentar após migração **
				//$cliente_db = $db_wf->fetch_assoc('select db, db_senha from clientes_db where id = '.$cliente_db_id); descomentar após migração **
				
				//$db_clientes_db = new Database('mysql.webfinancas.com',$cliente_db['db'],$cliente_db['db_senha'],$cliente_db['db']);
				$db_clientes_db = new Database('mysql.web2business.com.br','web2business09','W2BSISTEMAS','web2business09'); // REMOVER APÓS MIGRAÇÃO
				
				//$verif_email = $db_clientes_db->fetch_assoc('select email from usuarios where email = '.$email_cliente.'and financeiro = 1');

				$db_clientes_db->query("insert into contador (email, contador_id, dt_convite, visualizado) values ('".$email_cliente."','".$contador_id."','".$dt_convite."', 0)"); 
				
			//}
			//======================================
	
		$lista = $db_contador->fetch_all_array('select email, visualizado, date_format(dt_convite, "%d") as dia, date_format(dt_convite, "%m"), date_format(dt_convite, "%y") as ano from clientes');
		
		foreach($lista as $l){
										$m = $l['mes'];
										if($m == 1){ $mes = 'Jan';}
										elseif($m == 2){ $mes = 'Fev';}
										elseif($m == 3){ $mes = 'Mar';}
										elseif($m == 4){ $mes = 'Abr';}
										elseif($m == 5){ $mes = 'Mai';}
										elseif($m == 6){ $mes = 'Jun';}
										elseif($m == 7){ $mes = 'Jul';}
										elseif($m == 8){ $mes = 'Ago';}
										elseif($m == 9){ $mes = 'Set';}
										elseif($m == 10){ $mes = 'Out';}
										elseif($m == 11){ $mes = 'Nov';}
										else{ $mes = 'Dez';}
		if($l['visualizado'] == 1){ $status = 'uDone'; }else{ $status = 'uAlert'; }								
										
		$clientes_conexoes .='<div class="newUpdate" >
                            <div class="'.$status.'">
                                <a href="javascript://" title=""><strong>'.$l['email'].'</strong></a>
                                <span>Aguardando</span>
                            </div>
                            <div class="uDate" style="float:right; padding-right:15px;" ><span class="uDay">'.$l['dia'].'</span>'.$mes.'/'.$l['ano'].'</div>
                        </div>';
		}
		
		self::emailEnviar($email_cliente,$assunto,$conteudo);
		
		$db_contador->close();
											
			$retorno = array("notificacao" => "Convite enviado com sucesso.", "situacao" => 1, "clientes_conexoes" => $clientes_conexoes);
			return $retorno;
		}else{
			$retorno = array("notificacao" => "Não foi possível enviar o convite. Por favor tente novamente mais tarde.", "situacao" => 2);
			return $retorno;
		}
	}

}
?>