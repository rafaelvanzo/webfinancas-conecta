<?php
define('ROOT',$_SERVER['DOCUMENT_ROOT'].'/');

require_once ROOT.'/sistema/servicos/mensagem/MensagemHelper.php';

class Usuario{
	 
	public $id;
    public $cliente_id;
    public $cliente_db_id;
    public $grupo_id;
    public $grupo_economico_id;
    public $nome;
    public $email;
    public $senha;
    public $primeiro_acesso;
    public $financeiro;
    public $contador;
    public $carne_leao;
    public $situacao;
	public $fields = array();

    //public $dbWf

	/*
	================================================================================================
	CONSTRUTOR
	================================================================================================
	*/

    function __construct($params=''){
        if($params!=''){
            $vars = get_class_vars(get_class($this));
            foreach($vars as $key => $value){
                if(array_key_exists(strtolower($key),$params) && $params[strtolower($key)] != ''){
                    $this->fields[$key] = $params[strtolower($key)];
                }
            }
        }
    }

    //LOG
    //==============================================================================================

    /**
     * Registrar log de operações do sistema
     * @param mixed $arquivo
     * @param mixed $msg
     * @param mixed $enviar_email 
     * @param mixed $destinatario
     * @param mixed $assunto 
     */
    function Log($arquivo,$msg,$enviar_email=false,$destinatario='',$assunto=''){
		$fp = fopen($arquivo,"a+");
		fwrite($fp,$msg."\r\n");
		fclose($fp);
		if($enviar_email){
            $conteudo = 'Rotina para geração de faturas executada com sucesso - '.date('d/m/Y - H:i:s');
            self::emailEnviar($destinatario,$assunto,$conteudo);
		}
	}

    //RETORNAR GRUPO ECONÔMICO DO CLIENTE
    //==============================================================================================

    /**
     * Retornar id do grupo econômico do cliente
     * @param mixed $db 
     * @param mixed $clienteId 
     */
    function GetGrupoEconomicoId($db, $clienteId){

        $grupoEconomicoId = $db->fetch_assoc('select grupo_id from grupos_economicos_integrantes where cliente_id = '.$clienteId);
        return $grupoEconomicoId['grupo_id'];

    }

    //INCLUÍR USUÁRIO
    //==============================================================================================

    /**
     * Summary of CreateUsuario
     * @param mixed $db 
     * @throws Exception 
     * @return string
     */
    function CreateUsuario($db){
        
        try{
            
            $this->fields['cliente_id'] = $_SESSION['cliente_id'];
            $this->fields['cliente_db_id'] = $_SESSION['db_id'];
            $this->fields['financeiro'] = 1;
            $this->fields['dt_cadastro'] = date('Y-m-d H:i:s');
            $this->fields['senha'] = md5($this->fields['senha']);

            $grupoEconomicoId = self::GetGrupoEconomicoId($db,$_SESSION['cliente_id']);
            $this->fields['grupo_economico_id'] = $grupoEconomicoId;
            
            if(!$db->query_insert('usuarios',$this->fields))
                if($db->errno==1062)
                    throw new Exception('Email já cadastrado',$db->errno);

            return json_encode(array('status'=>1,'msg'=>'Usuário cadastrado com sucesso'));
        
        }catch(Exception $e){
            
            return json_encode(array('status'=>2,'msg'=>$e->getMessage(),'errno'=>$e->getCode(),'full_erro'=>$db->full_error));

        }
        
    }

    //EDITAR USUÁRIO
    //==============================================================================================
    
    /**
     * Summary of EditUsuario
     * @param mixed $db 
     * @throws Exception 
     * @return string
     */
    function EditUsuario($db){
        
        try{
            
            if(!$db->query_update('usuarios',$this->fields,'id = '.$this->fields['id']))
                if($db->errno==1062)
                    throw new Exception('Email já cadastrado',$db->errno);

            return json_encode(array('status'=>1,'msg'=>'Usuário atualizado com sucesso'));
            
        }
        catch(Exception $e){
            
            return json_encode(array('status'=>2,'msg'=>$e->getMessage(),'errno'=>$e->getCode(),'full_erro'=>$db->full_error));

        }
        
    }

    //EXCLUIR USUÁRIO
    //==============================================================================================

    /**
     * Summary of DeleteUsuario
     * @param mixed $db 
     * @param mixed $params 
     * @return string
     */
    function DeleteUsuario($db,$params){
        $db->query('delete from usuarios where id = '.$params['usuarioId']);
        return json_encode(array('status'=>1,'msg'=>'Usuário excluído com sucesso'));
    }

    //EXIBIR USUÁRIO
    //==============================================================================================

    /**
     * Summary of DetailsUsuario
     * @param mixed $db 
     * @param mixed $params 
     * @return string
     */
    function DetailsUsuario($db,$params){
        $usuario = $db->fetch_assoc('select * from usuarios where id = '.$params['usuarioId']);
        return json_encode($usuario);
    }

    //DATA TABLE USUÁRIOS
    //================================================================================================

    function DataTableUsuarios($db,$params){

        //filtro do data table
        $sSearch = $params["sSearch"];
        $sEcho = $params["sEcho"];
        $iDisplayStart = $params["iDisplayStart"];
        $iDisplayLength = $params["iDisplayLength"];
        //$iTotalRecords = $db->numRows('select id from lancamentos');
        $iTotalDisplayRecords = 0;

        $iTotalDisplayRecords = $db->numRows('select id from usuarios');
        
        $aaData = array();
        
        if($sSearch==""){

            $queryUsuarios = "select id, nome, email from usuarios where cliente_id = ".$_SESSION['cliente_id']." AND Excluido = 0 AND Tipo = 0";

        }else{
            
            $queryUsuarios = "select id, nome, email from usuarios where cliente_id = ".$_SESSION['cliente_id']." and nome like '%".$sSearch."%' AND Excluido = 0 AND Tipo = 0";
        }
		
        $iTotalDisplayRecords = mysql_num_rows(mysql_query($queryUsuarios, $db->link_id));

        $queryUsuarios = mysql_query($queryUsuarios.' order by nome limit '.$iDisplayStart.",".$iDisplayLength, $db->link_id);

        while($usuario = mysql_fetch_assoc($queryUsuarios)){

            $dadosUsuario = '
                <span class="lDespesa tbWF" >
	                <a href="javascript://void(0);" style="cursor: default;" original-title="Favorecido" class="tipS" ><strong >'.$usuario['nome'].'</strong></a>
		                <span style="padding-top:5px;">	
			                <div class="tab_tel_cel"><img src="images/icons/dark/mail.png" style="margin-bottom:-3px;"> &nbsp;'.$usuario['email'].' </div>
		                </span>
                </span>											
															
                <div class="tbWFoption">
                    <a href="'.$usuario['id'].'" original-title="Excluír" class="smallButton redB btTBwf tipS excluir-usuario" id="link-exc-'.$usuario['id'].'"><img src="images/icons/light/close.png" width="10"></a>							
                    <a href="javascript://void(0);" original-title="Editar" class="smallButton greyishB btTBwf tipS exibir-usuario" data-usuario-id='.$usuario['id'].'><img src="images/icons/light/pencil.png" width="10"></a>
                </div>
            ';

            array_push($aaData,array('usuario'=>$dadosUsuario));
        }

        $retorno = array('sEcho'=>$sEcho,'iTotalRecords'=>$iTotalDisplayRecords,'iTotalDisplayRecords'=>$iTotalDisplayRecords,'aaData'=>$aaData);
        
        return json_encode($retorno);

    }

    //RETORNA PERMISSÕES DO USUÁRIO
	//================================================================================================
    
    function GetUsuarioPermissoes($db, $grupoId, $clienteDb, $clienteDbSenha){
    
        //start: permissões do usuário
        if($grupoId==1){
            $permissoes = $db->fetch_all_array('select id as permissao_id from sis_permissoes');
        }else{
            $permissoes = $db->fetch_all_array('select permissao_id from usuarios_grupos_permissoes where grupo_id = '.$grupoId);
        }

        $arrayPermissoes = array();

        foreach($permissoes as $p){
            array_push($arrayPermissoes,$p['permissao_id']);
        }
        //end: permissões do usuário

        //start: verifica se cliente está conectado à contabilidade e retira as permissões de criação, edição e exclusão das categorias
        $connClienteDb = new Database("mysql.webfinancas.com", $clienteDb, $clienteDbSenha, $clienteDb);
        
        $conexaoContador = $connClienteDb->numRows('select id from conexao where contador_id <> 0 and conectado = 1');
        
        if($conexaoContador>0){

            $permissoes = $arrayPermissoes;
            $permissaoCategorias = array(31,32,33);

            $exclusoes = 0;
            foreach($permissoes as $key => $value){
                if(in_array($value,$permissaoCategorias)){
                    unset($permissoes[$key]);
                    $exclusoes++;
                }
                if($exclusoes==3)
                    end($permissoes);
            }

            $arrayPermissoes = $permissoes;
        }
        
        $connClienteDb->close();
        //end: verifica se cliente está conectado à contabilidade e retira as permissões de criação, edição e exclusão das categorias
    
        return $arrayPermissoes;
    }

    //RETORNA LOGOMARCA DO CLIENTE
	//================================================================================================

    function GetLogo($clienteId){

        $dbW2b = new Database("mysql.web2business.com.br", "web2business", "W2BSISTEMAS", "web2business");

        $logo = $dbW2b->fetch_assoc("SELECT logo_recibo FROM clientes WHERE id = ".$clienteId);
        
        //$dbW2b->close();
        
        if(!empty($logo['logo_recibo'])){
            return $logo['logo_recibo'];
        }else{
            return "images/logo_recibo.png";
        }
    }

    //VERIFICA SE ALGUMA DAS CONTAS FINANCEIRAS DO CLIENTE É CARNÊ LEÃO
	//================================================================================================

    function VerificaOpcaoCarneLeao($clienteDb, $clienteDbSenha){

        $db_cli = new Database('mysql.webfinancas.com', $clienteDb, $clienteDbSenha, $clienteDb);
        
        $carne_leao = $db_cli->fetch_assoc("SELECT carne_leao FROM contas WHERE carne_leao = 1");
        
        $db_cli->close();

        if($carne_leao == true){ 
            return 1; 
        }else{ 
            return 0;
        }

    }

    //LOGIN
	//================================================================================================
	
	function login($db,$array_dados){

		$email = $array_dados['email'];
		$senha = md5($array_dados['senha']);
	
		$query= "
						select u.id, u.cliente_id, u.cliente_db_id, u.situacao, cas.situacao cli_acesso_situacao, u.financeiro, u.contador, u.grupo_id, u.grupo_economico_id
						from usuarios u, cli_acesso_situacao cas
						where u.email = '".$email."'
							and u.senha = '".$senha."'
							and u.cliente_id = cas.cliente_id
							and u.Tipo = 0
		";
		
		$usuario = $db->fetch_assoc($query);

		if(!empty($usuario)){

			if($usuario['situacao']==1){

				$cliente_db = $db->fetch_assoc('select id, db, db_senha from clientes_db where id = '.$usuario['cliente_db_id']);

				$retorno['notificacao'] = "";
				$retorno['situacao'] = 1;
				$retorno['cli_acesso_situacao'] = $usuario['cli_acesso_situacao'];
				$retorno['permissao'] = "1";
                $retorno['permissao_contador'] = "1";
				$retorno['cliente_id'] = $usuario['cliente_id'];
				$retorno['usuario_id'] = $usuario['id'];
				$retorno['email'] = $email;
                $retorno['financeiro'] = $usuario['financeiro'];
				$retorno['contador'] = $usuario['contador'];
				$retorno['cliente_db_id'] = $cliente_db['id'];
                $retorno['cliente_db'] = $cliente_db['db'];
				$retorno['cliente_db_senha'] = $cliente_db['db_senha'];
                
                //Verifica se em alguma conta financeira esta habilitado o carnê leão
                $retorno['carne_leao'] = self::VerificaOpcaoCarneLeao($cliente_db['db'], $cliente_db['db_senha']);
                //$retorno['carne_leao'] = $usuario['carne_leao'];
                
                //Acessa a W2B
                $db_w2b = new Database("mysql.web2business.com.br", "web2business", "W2BSISTEMAS", "web2business");

                //Consulta dados do cliente
                $cliente = $db_w2b->fetch_assoc("SELECT nome, cpf_cnpj, parceiro, parceiro_id FROM clientes WHERE id = ".$usuario['cliente_id']);

                $retorno['nome'] = $cliente['nome'];
                $retorno['cpf_cnpj'] = $cliente['cpf_cnpj'];

                //Verifica se o cliente é um parceiro e se existe logomarca personalizada para esse parceiro
                $retorno['parceiro'] = $cliente['parceiro'];
				$retorno['parceiro_id'] = $cliente['parceiro_id'];

				
					//Log de acesso
					$infoLog['cliente_id'] = $retorno['cliente_id'];
					$infoLog['usuario_id'] = $retorno['usuario_id'];
					$infoLog['nome'] = $retorno['nome'];
					$infoLog['cpf_cnpj'] = $retorno['cpf_cnpj'];
					$infoLog['email'] = $retorno['email'];
					$infoLog['parceiro'] = $retorno['parceiro'];
                	$infoLog['parceiro_id'] = $retorno['parceiro_id'];
					
					$db_w2b->query_insert("log_acesso", $infoLog);
					//==============
                
                //Verifica se os clientes do parceiro terá a logo personalizada do parceiro,
                //tem que ser > 1 porque parceiro_id = 1 significa que é um cliente que foi desconectado de um parceiro.
                if($cliente['parceiro_id'] > 1){ // if($cliente['parceiro'] > 0 || $cliente['parceiro_id'] > 1){

                    $parceiroCliente = $db->fetch_assoc("SELECT logo_parceiro, logo_imagem FROM parceiros WHERE parceiro_id = ".$cliente['parceiro_id']);
                    $retorno['logo_parceiro'] = $parceiroCliente['logo_parceiro'];
                    $retorno['logo_imagem'] = $parceiroCliente['logo_imagem'];

                }elseif($cliente['parceiro'] == $usuario['cliente_id']){

                    $parceiroCliente = $db->fetch_assoc("SELECT logo_parceiro, logo_imagem FROM parceiros WHERE parceiro_id = ".$cliente['parceiro']);
                    $retorno['logo_parceiro'] = $parceiroCliente['logo_parceiro'];
                    $retorno['logo_imagem'] = $parceiroCliente['logo_imagem'];

                }else{

                    $retorno['logo_parceiro'] = $cliente['logo_parceiro'];
                    $retorno['logo_imagem'] = $cliente['logo_imagem'];
                }                
                
                //start: Logo Recibo
                $retorno['logo_recibo'] = self::GetLogo($usuario['cliente_id']);
                //end: Logo Recibo

                //start: Buscar licenças do grupo econômico
                $arrayLicencas = array();
                $licencas = $db->fetch_all_array('select cliente_id from grupos_economicos_integrantes where grupo_id = '.$usuario['grupo_economico_id']);
                foreach($licencas as $licenca){
                    $nomeCliente = $db_w2b->fetch_assoc('select nome from clientes where id = '.$licenca['cliente_id']);
                    array_push($arrayLicencas, array('cliente_id'=>$licenca['cliente_id'],'nome'=>$nomeCliente['nome']));
                }
                
                if(count($arrayLicencas)>0)
                    $_SESSION['licencas'] = $arrayLicencas;
                //end: Buscar licenças do grupo econômico
                
                $db_w2b->close();

                $retorno['permissoes'] = self::GetUsuarioPermissoes($db, $usuario['grupo_id'], $cliente_db['db'], $cliente_db['db_senha']);

			}else{
				
                $retorno['notificacao'] = "<b>Usuário inativo.</b> <br> Cliente Baker Tilly o endereço de acesso mudou!! <br> O novo acesso ao sistema é através do link:<br> <b><a href='https://webfinancas.bakertillyes.com.br'>webfinancas.bakertillyes.com.br</a></b>.<br> Obrigado!";
				$retorno['situacao'] = 2;
			}

		}else{

			$retorno['notificacao'] = "E-mail ou senha inválida.";
			$retorno['situacao'] = 2;
		}
	
		return $retorno;
	}
	
    //ALTERNAR LICENÇA
    //================================================================================================

    function AlternarLicenca($dbWf, $clienteId){

        $cliAcessoSituacao = $dbWf->fetch_assoc('select situacao from cli_acesso_situacao where cliente_id = '.$clienteId);
        
        $cliente_db = $dbWf->fetch_assoc('select id, db, db_senha from clientes_db where cliente_id = '.$clienteId);

        $grupoId = $dbWf->fetch_assoc('select grupo_id from usuarios where id = '.$_SESSION['usuario_id']);
        
        //definições básicas para a licença
        $_SESSION['cli_acesso_situacao'] = $cliAcessoSituacao['cli_acesso_situacao'];
        $_SESSION['cliente_id'] = $clienteId;
        $_SESSION['db_usuario'] = $cliente_db['db'];
        $_SESSION['db_senha'] = $cliente_db['db_senha'];
        $_SESSION['db_id'] = $cliente_db['id'];

        //retorna permissões e retira permissão para as categorias caso haja conexão com a contabilidade
        //$_SESSION['permissoes'] = self::GetUsuarioPermissoes($dbWf, $grupoId['grupo_id'], $cliente_db['db'], $cliente_db['db_senha']);
		$_SESSION['permissoes'] = self::GetUsuarioPermissoes($dbWf, 1, $cliente_db['db'], $cliente_db['db_senha']);


        //retorna logomarca do cliente para usar no recibo
        $_SESSION['logo_recibo'] = self::GetLogo($clienteId);

        //verifica se cliente tem carnê leão habilitado
        $_SESSION['carne_leao'] = self::VerificaOpcaoCarneLeao($_SESSION['db_usuario'], $_SESSION['db_senha']);

    }

	/*
	================================================================================================
	ALTERAR SENHA
	================================================================================================
	*/

	function senhaAlterar($db,$array_dados){
		
		//$db = new Database('mysql.webfinancas.com','webfinancas','W2BSISTEMAS','webfinancas');

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
				
			 <h3> Recuperação de senha! </h3>
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

	//ENVIAR EMAIL
	//================================================================================================

	function emailEnviar($email_destinatario,$assunto,$conteudo)
    {
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
	EDITAR EMPRESA
	================================================================================================
	*/

	function usuariosEditar($db,$array_dados){
		//Conexão no banco da Web 2 Business
		$db_w2b = new Database('mysql.web2business.com.br','web2business','W2BSISTEMAS','web2business');
		
		$cliente_id = $array_dados['cliente_id'];
		unset($array_dados['cliente_id']);
        unset($array_dados['contador']);
        unset($array_dados['carne_leao']);
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
		$cliente_id = $array_dados['cliente_id'];
				
		if($tp_plano == '1'){ $valorPlano = 'vl_mensal'; $frequencia = 30;}elseif($tp_plano == '2'){ $valorPlano = 'vl_trimestral'; $frequencia = 90;}elseif($tp_plano == '3'){ $valorPlano = 'vl_semestral'; $frequencia = 120;}else{ $valorPlano = 'vl_anual'; $frequencia = 360;}
		
		//buscar valor do plano
		$buscar_valores = $db_wf->fetch_assoc('select '.$valorPlano.' from planos where id ='.$plano_id);
		if($tp_plano == '1'){ $dadosP['vl_plano'] = $buscar_valores['vl_mensal'];  }elseif($tp_plano == '2'){ $dadosP['vl_plano'] = $buscar_valores['vl_trimestral'];  }elseif($tp_plano == '3'){ $dadosP['vl_plano'] = $buscar_valores['vl_semestral'];  }else{ $dadosP['vl_plano'] = $buscar_valores['vl_anual'];  }
				
		
		$dadosP['dia_vencimento'] = $array_dados['diaVencimento'];
		$dadosP['periodo'] = $tp_plano;
		//Atualiza o plano
		$planoEditar = $db_wf->query_update('clientes_planos', $dadosP, 'cliente_id = '.$cliente_id);
		
		/* ==================== */ 
		
		$jsonTxt = str_replace('\"','"',$array_dados['modulos']);
		$jsonObj = json_decode($jsonTxt, true);
		$array_modulos = $jsonObj;

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
							$dados_modulos['valor'] = $vl_modulos['vl_anual'];
						}
							
						$dados_modulos['ano'] = $vl_modulos['ano'];
						$dados_modulos['grupo_id'] = $vl_modulos['grupo_id'];
						
						$db_wf->query_update('clientes_modulos',$dados_modulos,'cliente_id = '.$cliente_id.' and modulo_id = '.$modulo['modulo_id']);

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
        
		//atualiza lançamento recorrente
		$db_wf = new Database('mysql.webfinancas.com','webfinancas02','W2BSISTEMAS','webfinancas02');
		$db_w2b = new Database('mysql.web2business.com.br','web2business','W2BSISTEMAS','web2business');
		$fatura_rcr = $db_w2b->fetch_assoc("select id, lancamento_recorrente_id, dt_vencimento from faturas_recorrentes where cliente_id = ".$cliente_id." and sistema_id = 1");
		$dt_vencimento = explode("-",$fatura_rcr["dt_vencimento"]);

		$dia_vencimento = $array_dados['diaVencimento']; //novo dia escolhiodo pelo cliente
		$qtd_dias_mes = date('t',strtotime($fatura_rcr["dt_vencimento"])); //quantidade de dias do mês de vencimento atual da fatura do cliente
		if( $qtd_dias_mes < $dia_vencimento ){
			$dt_prox_venc = $dt_vencimento[0].'-'.$dt_vencimento[1].'-'.$qtd_dias_mes; //se o dia escolhido for maior que a quantidade de dias do mês, o dia de próximo vencimento será o ultimo dia do mês
		}else{
			$dt_prox_venc = $dt_vencimento[0].'-'.$dt_vencimento[1].'-'.$dia_vencimento; //caso contrário o dia de vencimento será o dia escolhido pelo cliente
		}
		
		$valor = $vl_total_modulo['valor'] + $dados_plano['vl_plano'];
		$db_wf->query("update lancamentos_recorrentes set valor = ".$valor.", frequencia = ".$frequencia.", dia_mes = ".$dia_vencimento.", dt_vencimento = '".$dt_prox_venc."', dt_prox_venc = '".$dt_prox_venc."' where id = ".$fatura_rcr["lancamento_recorrente_id"]);
		$db_w2b->query("update faturas_recorrentes set dt_vencimento = '".$dt_prox_venc."' where id = ".$fatura_rcr["id"]);
		$db_wf->close();
		$db_w2b->close();

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
	
	//CONTRATAR PLANO
	//================================================================================================

	function planoContratar($cliente_id_w2b,$array_dados){
		
        //requisita classe recebimento para gerar nosso número do boleto
        require_once('../../lancamento/class/Lancamento.class.php');
        require_once('../../lancamento/class/Recebimento.class.php');

		//conecta nos bancos da w2b e wf da w2b
		$db_w2b = new Database('mysql.web2business.com.br','web2business','W2BSISTEMAS','web2business');
		$db_wf = new Database('mysql.webfinancas.com','webfinancas02','W2BSISTEMAS','webfinancas02');
		$db_wf_principal = new Database('mysql.webfinancas.com','webfinancas','W2BSISTEMAS','webfinancas');

		$db_w2b->query("start transaction");
		$db_wf->query("start transaction");
		$db_wf_principal->query("start transaction");

		$fatura_existente = $db_w2b->fetch_assoc("select id, lancamento_id, sequencial from faturas where contratacao = 1 and cliente_id = ".$cliente_id_w2b);

		if(!empty($fatura_existente)){

			//$vl_plano = $db_wf_principal->fetch_assoc("select vl_plano from clientes_planos where cliente_id = ".$cliente_id_w2b);
			//$vl_modulo = $db_wf_principal->fetch_assoc("select sum(valor) vl_modulo from clientes_modulos where cliente_id = ".$cliente_id_w2b);
			//$valor = $vl_plano["vl_plano"]+$vl_modulo["vl_modulo"];

			$tp_plano = $array_dados["tp_plano"];
			$valor = $db_wf_principal->fetch_assoc("select vl_mensal, vl_trimestral, vl_semestral, vl_anual from planos where id = 1");
			if($tp_plano==1){
				$valor = $valor["vl_mensal"];
			}elseif($tp_plano==2){
				$valor = $valor["vl_trimestral"];
			}elseif($tp_plano==2){
				$valor = $valor["vl_semestral"];
			}else{
				$valor = $valor["vl_anual"];
			}

			$dt_vencimento = date('Y-m-d', strtotime("+1 days"));
			$lancamento_id = $fatura_existente["lancamento_id"];
			$db_wf->query("update lancamentos set dt_vencimento = '".$dt_vencimento."', valor = ".$valor." where id = ".$lancamento_id);
			$boleto = $db_wf->fetch_assoc("select id from boletos where lancamento_id = ".$lancamento_id);
			$boleto_id = $boleto["id"];
			$sequencial = $fatura_existente["sequencial"];
			$db_w2b->query("update faturas set valor = ".$valor." where id = ".$fatura_existente["id"]);
		
		}else{

			//$conta_id = 2; //banco do brasil
            //$codBanco = '001'; //banco do brasil
			$conta_id = 8; //caixa economica
            $codBanco = '104'; //caixa economica

			$tp_plano = $array_dados["tp_plano"];
			$valor = $db_wf_principal->fetch_assoc("select vl_mensal, vl_trimestral, vl_semestral, vl_anual from planos where id = 1");
			if($tp_plano==1){
				$valor = $valor["vl_mensal"];
			}elseif($tp_plano==2){
				$valor = $valor["vl_trimestral"];
			}elseif($tp_plano==2){
				$valor = $valor["vl_semestral"];
			}else{
				$valor = $valor["vl_anual"];
			}
	
			//busca dados do cliente na w2b
			$favorecido = $db_w2b->fetch_assoc("select * from clientes where id = ".$cliente_id_w2b);
			
			//verifica se favorecido já está cadastrado no web finanças da w2b
			$favorecido_wf = $db_wf->fetch_assoc("select id from favorecidos where cpf_cnpj = '".$favorecido["cpf_cnpj"]."'");
	
			//Cria novo array para inserir ou atualizar os dados do cliente
            $novoFavorecido = array(
                    'nome' => $favorecido['nome'],
	                'inscricao' => $favorecido['inscricao'],
	                'cpf_cnpj' => $favorecido['cpf_cnpj'],
	                'tp_favorecido' => $favorecido['tp_cadastro'],
	                'logradouro' => $favorecido['logradouro'],
	                'numero' => $favorecido['numero'],
	                'bairro' => $favorecido['bairro'],
	                'cidade' => $favorecido['cidade'],
	                'uf' => $favorecido['uf'],
	                'cep' => $favorecido['cep'],
	                'complemento' => $favorecido['complemento'],
	                'email' => $favorecido['email'],
	                'telefone' => $favorecido['telefone']
                );

            if(empty($favorecido_wf)){
				$db_wf->query_insert("favorecidos",$novoFavorecido);
				$cliente_id_wf = mysql_insert_id($db_wf->link_id);
			}else{
				$cliente_id_wf = $favorecido_wf["id"];
				$db_wf->query_update("favorecidos",$novoFavorecido,"id = ".$cliente_id_wf);
			}

			//registra o plano escolhido pelo cliente
			$array_cliente_plano = array(
				"ano" => date('Y'),
				"cliente_id" => $cliente_id_w2b,
				"plano_id" => 1,
				"vl_plano" => $valor, //pegar dinamicamente
				"periodo" => $tp_plano, //mensal, trimestral... pegar dinamicamente
				"dt_cadastro" => date("Y-m-d H:i:s")
			);
			$db_wf_principal->query_insert("clientes_planos",$array_cliente_plano);
			
			//incluír programação nos recebimentos da W2B
			$lancamento = array(
				"tipo" => "R",
				"descricao" => "Web Finanças - Contratação",
				"lancamento_pai_id" => "0",
				"lancamento_recorrente_id" => "0",
				"parcela_numero" => "1",
				"qtd_parcelas" => "1",
				"favorecido_id" => $cliente_id_wf,
				"forma_pgto_id" => "",
				"conta_id" => $conta_id,
				"conta_id_origem" => "",
				"conta_id_destino" => "",
				"documento_id" => "",
				"valor" => $valor,
				"frequencia" => "",
				"auto_lancamento" => "M",
				"observacao" => "",
				"dt_emissao" => date("Y-m-d"),
				"dt_vencimento" => date('Y-m-d', strtotime("+1 days")),
				"sab_dom" => "0",
				"dt_venc_ref" => "",
				"dt_compensacao" => "",
				"compensado" => "0"
			);
			$db_wf->query_insert("lancamentos",$lancamento);
			$lancamento_id = mysql_insert_id($db_wf->link_id);

			//conta financeira
			$contaFinanceira = $db_wf->fetch_assoc("select carteira, convenio, agencia, boleto_ano, sequencial from contas where id = ".$conta_id." for update");
			//verifica se o sequencial e ano do boleto devem ser reiniciados
			//suporta apenas até o ano de 2115 e então começará a repetir
			if($contaFinanceira['boleto_ano']!=date('y')){
				$novo_boleto_ano = $contaFinanceira['boleto_ano'] * 1 + 1;
				if($novo_boleto_ano==100)
					$novo_boleto_ano = '00';
				$db_wf->query("update contas set sequencial = 2, boleto_ano = '".$novo_boleto_ano."' where id = ".$conta_id);
				$sequencial = 1;
			}else{
				$db_wf->query("update contas set sequencial = sequencial + 1 where id = ".$conta_id);
				$sequencial = $contaFinanceira['sequencial'];
			}

			//inclui fatura no adm da w2b
			$fatura = array(
				"cliente_id"=>$cliente_id_w2b,
				"sistema_id"=>1,
				"lancamento_id"=>$lancamento_id,
				"sequencial"=>$sequencial,
				"valor"=>$valor,
				"contratacao"=>1,
				"compensado"=>0,
				"dt_referencia"=>date('Y-m-d'),
				"dt_vencimento"=>date('Y-m-d', strtotime("+1 days"))
			);
			$db_w2b->query_insert("faturas",$fatura);
			
			//inclui boleto no wf da w2b
            $anoEmissao = date('y');
            $nossoNumero = Recebimento::GerarNossoNumeroBoleto($codBanco,$contaFinanceira['carteira'],$sequencial,$anoEmissao,$contaFinanceira['convenio'],$contaFinanceira['agencia']);

			$boleto = array("sequencial"=>$sequencial,"lancamento_id"=>$lancamento_id,'nosso_numero'=>$nossoNumero);
			$db_wf->query_insert("boletos",$boleto);
			$boleto_id = mysql_insert_id($db_wf->link_id);

			//incluir lançamento recorrente no wf da w2b
			/*
				//determina a frequência da fatura recorrente
					$tp_plano = $array_dados["tp_plano"];
					if($tp_plano==1){
						$frequencia=30;
					}elseif($tp_plano==2){
						$frequencia=90;
					}elseif($tp_plano==3){
						$frequencia=120;
					}else{
						$frequencia=360;
					}
					
				//determina a data de vencimento, data de inicio e data de proximo vencimento
					$frequencia_fator = $frequencia/30;
					$dia_vencimento = $array_dados["vencimento"];
					$dt_vencimento_atual = date('Y-m-d', strtotime("+1 days"));
					$dt_vencimento_atual = explode('-',$dt_vencimento_atual);
					$mes_prox_venc = mktime(0,0,0,$dt_vencimento_atual[1]+$frequencia_fator,'1',$dt_vencimento_atual[0]);
					$qtd_dias_mes = date('t',$mes_prox_venc);
					if( $qtd_dias_mes < $dia_vencimento ){
						$dt_vencimento = mktime(0,0,0,$dt_vencimento_atual[1]+$frequencia_fator,$qtd_dias_mes,$dt_vencimento_atual[0]);
						$dt_vencimento = date('Y-m-d',$dt_vencimento);
					}else{
						$dt_vencimento = mktime(0,0,0,$dt_vencimento_atual[1]+$frequencia_fator,$dia_vencimento,$dt_vencimento_atual[0]);
						$dt_vencimento = date('Y-m-d',$dt_vencimento);
					}			
				
				//inclui o lançamento
					$lancamento_rcr = array(
						"tipo" => "R",
						"favorecido_id" => $cliente_id_wf,
						"forma_pgto_id" => "",
						"conta_id" => $conta_id,
						"documento_id" => "",
						"valor" => $valor,
						"frequencia" => $frequencia,
						"auto_lancamento" => "M",
						"observacao" => "",
						"dia_mes" => $array_dados["vencimento"],
						"dt_inicio" => $dt_vencimento,
						"dt_vencimento" => $dt_vencimento,
						"sab_dom" => "0",
						"dt_prox_venc" => $dt_vencimento,
						"descricao" => "Web Finanças - Contratação",
					);
					$db_wf->query_insert("lancamentos_recorrentes",$lancamento_rcr);
					$lancamento_rcr_id = mysql_insert_id($db_wf->link_id);
			
			//inclui a fatura recorrente no adm da w2b
			$fatura_rcr = array(
				"cliente_id"=>$cliente_id_w2b,
				"sistema_id"=>1,
				"lancamento_recorrente_id"=>$lancamento_rcr_id,
				"dt_vencimento"=>date("Y-m-d"),
			);
			$db_w2b->query_insert("faturas_recorrentes",$fatura_rcr);
			*/

		}

		//chave=cliente_id(id do cedente)-convenio-lancamento_id-boleto_id-sequencial
		//$chave = '1-2430891-'.$lancamento_id.'-'.$boleto_id.'-'.$sequencial;
		$chave = '1-591013-'.$lancamento_id.'-'.$boleto_id.'-'.$sequencial;

		$db_w2b->query("commit");
		$db_wf->query("commit");
		$db_wf_principal->query("commit");
				
		$db_w2b->close();
		$db_wf->close();
		$db_wf_principal->close();
		
		return $chave;
		
	}
	
	//GERAR FATURA ATRASADA
	//================================================================================================
	
	/**
	 * Na tela de bloqueio, disponibiliza boleto referente às faturas em aberto, com data de vencimento atualizada
	 * @param mixed $cliente_id_w2b 
	 * @return string
	 */
	function faturaAtrasada($cliente_id_w2b){

		/*
		busca as faturas em aberto
		para cada fatura busca o lançamento correspondente
		soma o valor dos lançamentos
		atualiza o vencimento do lançamento com dois dias de carência
		*/

		//conecta aos bancos da w2b e wf da w2b
		$db_w2b = new Database('mysql.web2business.com.br','web2business','W2BSISTEMAS','web2business');
		$db_wf = new Database('mysql.webfinancas.com','webfinancas02','W2BSISTEMAS','webfinancas02');

        //busca id do lançamento que originou as faturas em aberto
        //a id do lançamento é a mesma para todas as faturas em aberto
        $fatura = $db_w2b->fetch_assoc('select lancamento_id, sequencial from faturas where id = (SELECT max(id) id FROM faturas WHERE cliente_id = '.$cliente_id_w2b.')');

        //busca id do boleto
        $boleto = $db_wf->fetch_assoc("select id from boletos where lancamento_id = ".$fatura['lancamento_id']);

        //atualiza vencimento do lançamento no wf da w2b
        //o lançamento no wf da w2b possui o valor acumulado das faturas em aberto, ainda que exista uma fatura para cada mês no banco de dados w2b
        $dt_vencimento = date("Y-m-d",strtotime("+2 day"));
		$db_wf->query("update lancamentos set dt_vencimento = '".$dt_vencimento."' where id = ".$fatura['lancamento_id']);
        
		$chave = '1-591013-'.$fatura['lancamento_id'].'-'.$boleto['id'].'-'.$fatura['sequencial'];
		
		return $chave;
	}

	//CONCILIAR FATURAS - Essa função deverá ser migrada para o adm da w2b
    //================================================================================================

    /**
     * Faz a conciliação entre as faturas enviadas para os clientes e os lançamentos no Web Finanças da W2B
     * referente às licensas do Web Finanças
     * @param mixed $dbWf 
     * @param mixed $dbW2b 
     */

    static function ConciliarFatura($dbW2b='',$dbWf=''){
    
        $dbW2b->query('start transaction');

        try{
        
            $faturasAbertas = $dbW2b->fetch_all_array('select * from faturas where dt_vencimento < "'.date('Y-m-d').'" and compensado = 0 and contratacao = 0');

            $faturas = array();

            foreach($faturasAbertas as $faturaAberta){
                array_push($faturas,$faturaAberta['lancamento_id']);
            }

            $lancamentos = $dbWf->fetch_all_array('select * from lancamentos where id in ('.join(',',$faturas).') and compensado = 1');

            foreach($lancamentos as $lancamento){

                $dbW2b->query('update faturas set compensado = 1, dt_compensacao = "'.$lancamento['dt_compensacao'].'" where lancamento_id = '.$lancamento['id']);
            }

            $dbW2b->query('commit');
        
        }catch(Exception $e){

            $dbW2b->query('rollback');
        }
    }
    
    //GERAR FATURA DIARIAMENTE - Essa função deverá ser migrada para o adm da w2b
	//================================================================================================
	
	function faturasGerar(){
        
        //Arquivo de log
        $arquivoLog = ROOT.'sistema/modulos/usuario/log_faturas/log_'.date('Ymd').'.txt';
        $msgLog = '';

        require_once('../../lancamento/class/Lancamento.class.php');
        require_once('../../lancamento/class/Recebimento.class.php');

        //DESCRIÇÃO
        //Diariamente é feita a busca das faturas à vencer na tabela faturas_recorrentes
        //O período de referência é de 10 dias contados a partir do dia corrente
        //através da fatura recorrente é feita uma busca no lançamento recorrente correspondente dentro do wf da w2b
        //...continuar descrição depois ;)
        
        //conecta aos bancos da w2b e wf da w2b
        $db_w2b = new Database('mysql.web2business.com.br','web2business','W2BSISTEMAS','web2business');
        $db_wf = new Database('mysql.webfinancas.com','webfinancas02','W2BSISTEMAS','webfinancas02');
        $db_wf_geral = new Database('mysql.webfinancas.com','webfinancas','W2BSISTEMAS','webfinancas');
        
        //Busca dados da conta financeira e do banco para emissão das faturas
        $contaFinanceiraId = 8;
        $contaFinanceira = $db_wf->fetch_assoc('SELECT banco_id, carteira, convenio, agencia from contas where id = '.$contaFinanceiraId);
        $bank_cod = $db_wf->fetch_assoc('SELECT codigo FROM bancos WHERE id = '.$contaFinanceira['banco_id']);
        $codBanco = $bank_cod['codigo'];

        //concilia faturas do Web Finanças com os lançamentos da W2B
        self::ConciliarFatura($db_w2b,$db_wf);

        //data de hoje mais 10 dias
        $hoje = strtotime("now");//strtotime("now"); strtotime("2017-01-26");
        $dt_vencimento_fatura = strtotime("+10 days",$hoje);
        $dt_vencimento_fatura = date("Y-m-d",$dt_vencimento_fatura);
        //$dt_vencimento_fatura = "2014-08-10"; //teste

        //busca dados do lançamento recorrente no wf (***para faturas com vencimento em 10 dias***)
        $faturas_rcr = $db_w2b->fetch_all_array("select id, cliente_id, lancamento_recorrente_id, dt_vencimento from faturas_recorrentes where sistema_id = 1 and dt_vencimento >= '".date('Y-m-d')."' and dt_vencimento <= '".$dt_vencimento_fatura."'");
        
        foreach($faturas_rcr as $fatura_rcr){

            $db_w2b->query("start transaction");
            $db_wf->query("start transaction");

            try{
                
                //Consulta lançamento recorrente no WF da W2B e atribui
                //variável de controle para verificar se lançamento recorrente já foi convertido em lançamento programado
                if($lancamento_rcr = $db_wf->fetch_assoc("select * from lancamentos_recorrentes where id = ".$fatura_rcr["lancamento_recorrente_id"]." and dt_vencimento = '".$fatura_rcr['dt_vencimento']."'")){

                    $lancamentoProgramado = false;

                }else{

                    if(!$lancamento_rcr = $db_wf->fetch_assoc("select * from lancamentos_recorrentes where id = ".$fatura_rcr["lancamento_recorrente_id"]))
                        throw new Exception("Lançamento recorrente não encontrado.");
                    
                    if(!$lancamentoProgramado = $db_wf->fetch_assoc("select * from lancamentos where lancamento_recorrente_id = ".$fatura_rcr["lancamento_recorrente_id"]." and dt_vencimento = '".$fatura_rcr['dt_vencimento']."' and compensado = 0"))
                        throw new Exception("Lançamento programado não encontrado.");

                    $lancamento_prog_id = $lancamentoProgramado['id'];
                    
                    $isExistLancamento = true;
                }

                //start: atualiza data de vencimento do lançamento recorrente
                if($lancamento_rcr['frequencia']>=30){
                    $frequencia = $lancamento_rcr['frequencia']/30;
                    $dia_vencimento = $lancamento_rcr['dia_mes'];
                    $dt_vencimento_atual = explode('-',$fatura_rcr['dt_vencimento']);
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
                    $dt_vencimento_atual = explode('-',$fatura_rcr['dt_vencimento']);
                    $dt_vencimento = mktime(0,0,0,$dt_vencimento_atual[1],$dt_vencimento_atual[2]+7,$dt_vencimento_atual[0]);
                    $dt_vencimento = date('Y-m-d',$dt_vencimento);
                }

                if(!$lancamentoProgramado)
                    $db_wf->query('update lancamentos_recorrentes set dt_vencimento = "'.$dt_vencimento.'", dt_prox_venc = "'.$dt_vencimento.'"  where id = '.$lancamento_rcr['id']);
                //end: Atualiza data de vencimento do lançamento recorrente

                //start: Atualiza data de vencimento da fatura recorrente
                $db_w2b->query("update faturas_recorrentes set dt_vencimento = '".$dt_vencimento."' where id = ".$fatura_rcr["id"]);
                //end: Atualiza data de vencimento da fatura recorrente

                //***** Localizar informações das faturas
                $cl_id = $fatura_rcr["cliente_id"];
                
                $clientePlano = $db_wf_geral->fetch_assoc('select plano_id, vl_plano, periodo from clientes_planos where cliente_id ='.$cl_id);
                
                if($clientePlano['periodo'] == '1')
                { 
                    $periodo = 'Mensal';  
                }
                else if($clientePlano['periodo'] == '2')
                { 
                    $periodo = 'Trimestral'; 
                }
                else if($clientePlano['periodo'] == '3')
                { 
                    $periodo = 'Semestral'; 
                }
                else
                { 
                    $periodo = 'Anual'; 
                } 
                
                $vigencia_inicio = date("d/m/Y",strtotime($fatura_rcr["dt_vencimento"]));
                $dt_vencimento = strtotime("-1 days",strtotime($dt_vencimento));
                $vigencia_fim = date("d/m/Y",$dt_vencimento);

                //start: Descrição e valor do plano Web Finanças
                $clientePlanoDescricao = $db_wf_geral->fetch_assoc('select nome from planos where id ='.$clientePlano['plano_id']);

                $servicosContratados = array();

                array_push($servicosContratados,array(
                    "nome"=>$clientePlanoDescricao["nome"],
                    "valor"=>$clientePlano['vl_plano']
                    ));
                //end: Descrição e valor do plano Web Finanças

                //start: Módulos adicionais contratados pelo cliente
                $clienteModulos = $db_wf_geral->fetch_all_array("select modulo_id, valor from clientes_modulos where cliente_id = $cl_id");
                
                if(count($clienteModulos) > 0)
                {
                    foreach($clienteModulos as $clienteModulo)
                    { 
                        $moduloDescricao = $db_wf_geral->fetch_assoc('select nome from planos_modulos where id ='.$clienteModulo['modulo_id']);
                        
                        array_push($servicosContratados,array(
                            "nome"=>$moduloDescricao['nome'],
                            "valor"=>$clienteModulo['valor']
                            ));
                    }
                }
                //end: Módulos adicionais contratados pelo cliente

                //start: Gera nova fatura
                $faturasAtrasadas = $db_w2b->fetch_all_array("select sequencial, lancamento_id, valor, dt_vencimento from faturas where contratacao = 0 and compensado = 0 and cliente_id = $fatura_rcr[cliente_id] order by dt_vencimento");

                if(count($faturasAtrasadas) == 0) //Insere novo lançamento e novo boleto no wf da vm se não houver faturas vencidas
                {
                    $dtCompetencia = Lancamento::DtCompetenciaCalc($fatura_rcr['dt_vencimento'],$lancamento_rcr['dt_comp_mes_dif']);
                    $dtCompetencia = explode('/',$dtCompetencia);
                    $dtCompetencia = $dtCompetencia[1].'-'.$dtCompetencia[0].'-01';
                    
                    if(!$lancamentoProgramado){ //insere lançamento programado no wf da w2b se ainda não existir

                        $lancamento_prog = array(
                        "tipo" => "R",
                        "descricao" => $lancamento_rcr['descricao'],
                        "lancamento_pai_id" => 0,
                        "favorecido_id" => $lancamento_rcr['favorecido_id'],
                        "forma_pgto_id" => $lancamento_rcr['forma_pgto_id'],
                        "conta_id" => $lancamento_rcr['conta_id'],
                        "conta_id_origem" => 0,
                        "conta_id_destino" => 0,
                        "documento_id" => $lancamento_rcr['documento_id'],
                        "valor" => $lancamento_rcr['valor'],
                        "frequencia" => $lancamento_rcr['frequencia'],
                        "auto_lancamento" => $lancamento_rcr['auto_lancamento'],
                        "observacao" => $lancamento_rcr['observacao'],
                        "dt_emissao" => date("Y-m-d"),
                        "dt_vencimento" => $fatura_rcr['dt_vencimento'],
                        "dt_venc_ref" => $fatura_rcr['dt_vencimento'],
                        "dt_competencia" => $dtCompetencia,
                        "sab_dom" => $lancamento_rcr['sab_dom'],
                        "compensado" => 0,
                        "lancamento_recorrente_id" => $lancamento_rcr['id'],
                        //"boleto_id" => 0, //retirar esse campo da tabela lancamentos
                        "parcela_numero" => 1,
                        "qtd_parcelas" => 1
                        );
                        $db_wf->query_insert("lancamentos",$lancamento_prog);
                        $lancamento_prog_id = mysql_insert_id($db_wf->link_id);
                    }
                    
                    //insere registro do boleto no wf da w2b
                    $sequencial = $db_wf->fetch_assoc("select boleto_ano, sequencial from contas where id = ".$lancamento_rcr["conta_id"]." for update");

                    //verifica se o sequencial e ano do boleto devem ser reiniciados
                    //suporta apenas até o ano de 2115 e então começará a repetir
                    if($sequencial['boleto_ano']!=date('y')){
                        $novo_boleto_ano = $sequencial['boleto_ano'] * 1 + 1;
                        if($novo_boleto_ano==100)
                            $novo_boleto_ano = '00';
                        $db_wf->query("update contas set sequencial = 2, boleto_ano = '".$novo_boleto_ano."' where id = ".$lancamento_rcr['conta_id']);
                        $conta_sequencial = 1;
                    }else{
                        $db_wf->query("update contas set sequencial = sequencial + 1 where id = ".$lancamento_rcr['conta_id']);
                        $conta_sequencial = $sequencial['sequencial'];
                    }
                    
                    $sequencial = $conta_sequencial;
                    
                    //start: Insere novo boleto
                    $anoEmissao = date('y');
                    $nossoNumero = Recebimento::GerarNossoNumeroBoleto($codBanco,$contaFinanceira['carteira'],$sequencial,$anoEmissao,$contaFinanceira['convenio'],$contaFinanceira['agencia']);

                    $array_boleto = array(
                    'sequencial' => $sequencial,
                    'nosso_numero' => $nossoNumero,
                    'lancamento_id' => $lancamento_prog_id
                    );
                    
                    $db_wf->query_insert("boletos",$array_boleto);
                    
                    $boleto_id = mysql_insert_id($db_wf->link_id);
                    //end: Insere novo boleto

                    $valorTotalFatura = $lancamento_rcr["valor"];
                }
                else //Atualiza valor do lançamento no wf da vm se houver faturas vencidas
                {
                    $lancamento_prog_id = $faturasAtrasadas[0]["lancamento_id"];
                    
                    $sequencial = $faturasAtrasadas[0]["sequencial"];

                    $valorFaturasAtrasadas = 0;

                    foreach($faturasAtrasadas as $indice => $conteudo){
                        $valorFaturasAtrasadas += $faturasAtrasadas[$indice]["valor"];
                        unset($faturasAtrasadas[$indice]["lancamento_id"]); //Retira posição do array para diminuir consumo de memória no envio do json para api de mensagem
                        unset($faturasAtrasadas[$indice]["sequencial"]); //Retira posição do array para diminuir consumo de memória no envio do json para api de mensagem
                    }
                    
                    $valorTotalFatura = $lancamento_rcr["valor"] + $valorFaturasAtrasadas;

                    $db_wf->query("update lancamentos set valor = ".$valorTotalFatura.", dt_vencimento = '".$fatura_rcr["dt_vencimento"]."', dt_venc_ref = '".$fatura_rcr["dt_vencimento"]."' where id = ".$lancamento_prog_id);

                    //consulta id do boleto
                    $boleto = $db_wf->fetch_assoc("select id, nosso_numero from boletos where lancamento_id = $lancamento_prog_id");
                    $boleto_id = $boleto["id"];
                    $nossoNumero = $boleto['nosso_numero'];
                }
                
                //start: Monta dados da fatura enviada por e-mail
                $dadosDafatura = array(
                    "view" => "_Cobranca.php",
                    "inicioVigencia" => $vigencia_inicio,
                    "fimVigencia" => $vigencia_fim,
                    "periodo" => $periodo,
                    "servicosContratados" => $servicosContratados,
                    "faturasAtrasadas" => $faturasAtrasadas,
                    "chave" => "1-591013-$lancamento_prog_id-$boleto_id-$sequencial",
                    "valorTotal" => $valorTotalFatura
                    );
                //end: Monta dados da fatura enviada por e-mail

                //start: Insere nova fatura no banco de dados principal do WF
                $array_fatura = array(
                    "cliente_id" => $fatura_rcr["cliente_id"],
                    "sistema_id" => 1,
                    "lancamento_id" => $lancamento_prog_id,
                    "sequencial" => $sequencial,
                    "valor" => $lancamento_rcr["valor"],
                    "contratacao" => 0,
                    "compensado" => 0,
                    "dt_referencia" => $fatura_rcr["dt_vencimento"],
                    "dt_vencimento" => $fatura_rcr["dt_vencimento"],
                    "descricao" => json_encode($dadosDafatura)
                );

                $db_w2b->query_insert("faturas",$array_fatura);
                //end: Insere nova fatura no banco de dados principal do WF

                //end: Gera nova fatura

                //Start: Enviar boleto para o cliente

                $assunto = "Relacionamento Web Finanças";
                
                //consulta email do cliente
                $cliente_email = $db_w2b->fetch_assoc("select email, email_fin from clientes where id = ".$fatura_rcr["cliente_id"]);
                $cliente_email = ($cliente_email["email_fin"] == "")? $cliente_email["email"] : $cliente_email["email_fin"];

                self::emailEnviar($cliente_email,$assunto,$dadosDafatura);

                //End: Enviar boleto para o cliente

                $db_w2b->query("commit");
                $db_wf->query("commit");

                $msgLog .= date("d/m/Y - H:i:s")."\n";
                $msgLog .= "cliente_id: ".$fatura_rcr["cliente_id"]."\n";
                $msgLog .= "fatura_recorrente_id: ".$fatura_rcr["id"]."\n";
                $msgLog .= "lancamento_recorrente_id: ".$fatura_rcr["lancamento_recorrente_id"]."\n";
                $msgLog .= "dt_vencimento: ".$fatura_rcr["dt_vencimento"]."\n";
                $msgLog .= "Status: Fatura gerada com sucesso."."\n";
                $msgLog .= "Nosso número: ".$nossoNumero."\n";
                $msgLog .= "------------------------------------------------------------------"."\n\n";

            }
            catch(Exception $e){

                $db_w2b->query("rollback");
                $db_wf->query("rollback");

                $msgLog .= date("d/m/Y - H:i:s")."\n";
                $msgLog .= "cliente_id: ".$fatura_rcr["cliente_id"]."\n";
                $msgLog .= "fatura_recorrente_id: ".$fatura_rcr["id"]."\n";
                $msgLog .= "lancamento_recorrente_id: ".$fatura_rcr["lancamento_recorrente_id"]."\n";
                $msgLog .= "dt_vencimento: ".$fatura_rcr["dt_vencimento"]."\n";
                $msgLog .= "Status: ".$e->getMessage()."\n";
                $msgLog .= "------------------------------------------------------------------"."\n\n";
            }
        }

        $db_w2b->close();
        $db_wf->close();
        $db_wf_geral->close();

        if(count($faturas_rcr)==0){

            $msgLog .= date("d/m/Y - H:i:s")."\n";
            $msgLog .= "Status: Nenhuma fatura gerada."."\n";
            $msgLog .= "------------------------------------------------------------------"."\n\n";
        }

        self::Log($arquivoLog,$msgLog,true,'fabio@web2business.com.br','Log Faturas Web Finanças');
    }

	/*
	================================================================================================
	CONVIDAR CONTADOR
	================================================================================================
	*/

	function conviteContador($db,$array_dados){
		
		$enderecoArquivos = 'http://www.webfinancas.com/site/img/email_paginas/';
		
		//Conexão no banco da Web 2 Business
		$db_w2b = new Database('mysql.web2business.com.br','web2business','W2BSISTEMAS','web2business');
		
		$remetente_id = $array_dados['remetente_id']; 
		$destinatario_email = $array_dados['destinatario_email'];
		$dt_convite = date('Y-m-d H:m:s');
		
		//Acessa o nome do remetente
		$remetente = $db_w2b->fetch_assoc('select nome, email from clientes where id = '.$remetente_id);
	
		$assunto = $remetente['nome'].' enviou um convite';		
		
		//==========================================================
		//Conexão no banco do Web Finanças
		$db_wfp = new Database('mysql.webfinancas.com','webfinancas','W2BSISTEMAS','webfinancas');
		
		$destinatario_dados = $db_wfp->fetch_assoc('select cliente_db_id from usuarios where email = "'.$destinatario_email.'" and financeiro = 1');
	
		//Verifica se o destinatario já esta cadastrado
		if($destinatario_dados == true){ 
			
			//Acesso ao tabela de banco de dados dos clientes do Web Finanças
			$destinatario_db = $db_wfp->fetch_assoc('select cliente_id, db, db_senha from clientes_db where id = '.$destinatario_dados['cliente_db_id'].'');
			$destinatario_id = $destinatario_db['cliente_id'];
			
			//Conexão no banco do Destinatario
			$usuario = $destinatario_db['db']; $senha_db = $destinatario_db['db_senha']; $db_usuario = $destinatario_db['db'];

			$db_destinatario = new Database('mysql.webfinancas.com',$usuario,$senha_db,$db_usuario);
			
			$db_destinatario->query("insert into contador (dt_convite, email, contador_id, conectado) values ('".$dt_convite."', '".$remetente['email']."',".$remetente_id.", 0)");
			
			//Finaliza conexão com DB do destinatario
			$db_destinatario->close();
		 }
			 
		 //Finaliza conexão com DB da Web 2 Business
		 $db_w2b->close();
		 
		 //Finaliza conexão com DB do Web Finanças Prinicipal
		 $db_wfp->close();
		
		//==========================================================
		
		//Isere o convite no DB do Remetente
		if(empty($destinatario_id)){ $destinatario_id = '0'; }
		$db->query("insert into clientes (dt_convite, email, cliente_id, conectado) values ('".$dt_convite."', '".$destinatario_email."', ".$destinatario_id.", 0)");

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
				
					<b>'.$remetente_nome['nome'].'</b>	
			</p>
			<br>
			<div align="center"><a href="http://www.web2business.com.br/webfinancas/convite.php?id='.$remetente_id.'&email='.$destinatario_email.'&tp=2" target="_blank"><img src="http://www.webfinancas.com/site/img/email_paginas/bt_aceitar_convite.png"></a></div>
			
			</td>    
			<td width="50%" align="center" valign="bottom"> 
				 <img src="'.$enderecoArquivos.'mail.png" align="center" class="img" width="215" />	
			 </td>
			 
		</tr>
	</table>	
		
		';
		
		//Retorna a lista de destinatários aguardando conexão atualizada para o sistema
		$lista_destinatarios = $db->fetch_all_array('select email, conectado, date_format(dt_convite, "%d") as dia, date_format(dt_convite, "%m"), date_format(dt_convite, "%y") as ano from clientes');
		
		foreach($lista_destinatarios as $l){
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
										
		//CONECTADO: conectado = 1, NÃO CONECTADO: conectado = 0  								
		if($l['conectado'] == 1){ $status = 'uDone'; }else{ $status = 'uAlert'; }								
		//Gera a lista								
		$lista_conexoes .='<div class="newUpdate" >
                            <div class="'.$status.'">
                                <a href="javascript://" title=""><strong>'.$l['email'].'</strong></a>
                                <span>Aguardando</span>
                            </div>
                            <div class="uDate" style="float:right; padding-right:15px;" ><span class="uDay">'.$l['dia'].'</span>'.$mes.'/'.$l['ano'].'</div>
                        </div>';

		}
		//===================================================
		
		//Envia o convite por email para o Destinatario
		self::emailEnviar($destinatario_email,$assunto,$conteudo);
		
		//Fecha banco de dados db
		$db->close();
											
			$retorno = array("notificacao" => "Convite enviado com sucesso.", "situacao" => 1, "lista_conexoes" => $lista_conexoes);
			return $retorno;
	}

	//HABILITAR ÁREA DO CONTADOR
    //================================================================================================

    function ContadorHabilitar($db,$array_dados){
        $habilitar = $array_dados['habilitar'];
        $db->query('update usuarios set contador = '.$habilitar.' where id = '.$_SESSION['usuario_id']);
        if($habilitar==0){
            $_SESSION['contador_acesso'] = 0;
            $notificacao = 'Área do Contador desabilitada';
        }else{
            $_SESSION['contador_acesso'] = 1;
            $notificacao = 'Área do Contador habilitada';
        }
        $retorno = array('situacao'=>1, 'notificacao'=>$notificacao);
        return $retorno;
    }

    //HABILITAR CARNÊ LEÃO
    //================================================================================================

    function CarneLeaoHabilitar($db,$array_dados){
        $habilitar = $array_dados['habilitar'];
        $db->query('update usuarios set carne_leao = '.$habilitar.' where id = '.$_SESSION['usuario_id']);
        if($habilitar==0){
            $_SESSION['carne_leao'] = 0;
            $notificacao = 'Carnê leão desabilitado';
        }else{
            $_SESSION['carne_leao'] = 1;
            $notificacao = 'Carnê leão habilitado';
        }
        $retorno = array('situacao'=>1, 'notificacao'=>$notificacao);
        return $retorno;
    }

    //CANCELAR CONTRATAÇÃO
	//================================================================================================

    function CancelarContratacao(){
        
        if($_SESSION['cli_acesso_situacao'] == '3'){
            session_destroy();
            $urlCancelar = "https://www.webfinancas.com";
            
        }else{
            $urlCancelar = "https://www.webfinancas.com/sistema/perfilUsuario";
        }
        
        return array('urlCancelar'=>$urlCancelar);
    }
    
/*
========================================================================================================================
LOGO RECIBO
========================================================================================================================
*/
function logoRecibo($params){
    $db_w2b = new Database('mysql.web2business.com.br','web2business','W2BSISTEMAS','web2business');
    
    $array_insert['logo_recibo'] = $params['arquivo'];
    $db_w2b->query_update('clientes', $array_insert,'id ='.$params['cliente_id']);
    $db_w2b->close();
    return array('logo_recibo'=>$params['arquivo']);
    }


}


?>