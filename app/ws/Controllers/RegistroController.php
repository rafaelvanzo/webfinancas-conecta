<?php

require_once('Models/Registro.php');

/**
 * RegistroController short summary.
 *
 * RegistroController description.
 *
 * @version 1.0
 * @author Fabio
 */
class RegistroController
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
     * Cadastra cliente na W2B e usuário no WF
     * @param mixed $params 
     */
    function Create($params){
        
        $dbW2b = new Database("mysql.web2business.com.br","web2business","W2BSISTEMAS","web2business");

        $dbW2b->query('start transaction');
        
        $this->db->query('start transaction');
        
        try
        {
            //Verifica se cliente já existe no Web Finanças
            $clienteIsExist = $this->db->fetch_assoc('select id from usuarios where email = "'.$params['cliente']['email'].'"');
            
            if($clienteIsExist){

                echo json_encode(array('status' => 2, 'cliente_id' => $clienteIsExist['id']), JSON_NUMERIC_CHECK);

            }else{

                //Cadastra cliente na W2B
                $params['cliente']['ws_cliente_id'] = $params['ws_cliente_id'];
                $cliente = new Cliente($params['cliente']);
                $cliente->fields['dt_cadastro'] = date('Y-m-d H:i:s');
                $clienteId = $dbW2b->query_insert('clientes', $cliente->fields);

                //Aloca banco de dados do cliente
                $clienteDb = $this->db->fetch_assoc('select min(id) id from clientes_db where situacao = 0 for update');
                $this->db->query('update clientes_db set cliente_id = '.$clienteId.', situacao = 1 where id = '.$clienteDb['id']);

                //Cadastra usuário no WF
                $array_usuario = array(
                        'cliente_id' => $clienteId,
                        'cliente_db_id' => $clienteDb['id'],
                        'email' => $cliente->fields['email'],
                        'senha' => md5('123456'),
                        'situacao' => 1,
                        'dt_cadastro' => date('Y-m-d H:i:s'),
                        'financeiro' => 1,
                        'contador' => 0,
                        'grupo_id' => 1,
                        'carne_leao' => 0
                    );
                $usuario = new Usuario($array_usuario);
                $usuarioId = $this->db->query_insert('usuarios', $usuario->fields);

                //registra situação de acesso do cliente no web finanças
                $array_situacao['cliente_id'] = $clienteId;
                $array_situacao['situacao'] = 1;
                $this->db->query_insert('cli_acesso_situacao', $array_situacao);

                //registra sistema contratado pelo cliente
                $array_sistema = array("sistema_id"=>1,"cliente_id"=>$clienteId);
                $dbW2b->query_insert('sistemas_clientes',$array_sistema);

                //registra plano contratado pelo cliente
                $array_plano = $params['plano'];
                $array_plano['ano'] = date('Y');
                $array_plano['cliente_id'] = $clienteId;
                $array_plano['dt_cadastro'] = date('Y-m-d H:i:s');
                $this->db->query_insert('clientes_planos', $array_plano);

                //Incrementa contador de cliente
                $this->db->query('update contador_clientes set qtd = qtd + 1 where id = 1');

                //Confirma cadastros no banco de dados
                $dbW2b->query('commit');
                $this->db->query('commit');

                //Envia email de confirmação para o usuário cadastrado
                self::EnviarEmail($usuarioId, $usuario->fields['email']);

                echo json_encode(array('status' => 1, 'cliente_id' => $clienteId));
            }
        }
        catch(Exception $e)
        {
            $dbW2b->query('rollback');
            $this->db->query('rollback');
            echo json_encode(array('status' => 0, 'erro' => $e->getMessage()));
        }
        
    }

    /**
     * Envia email de confirmação ao novo usuário cadastrado
     * @param integer $usuario_id 
     * @param string $email 
     */
    function EnviarEmail($usuario_id, $email){

        require_once(ROOT.'site/php/php-mailer/class.phpmailer.php');
        
        $validade = date('d/m/Y', strtotime("+1 days"));
        
        $enderecoArquivos = 'http://www.webfinancas.com/site/img/email_paginas/';
        
        $message = '
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
	
	            .img { width: 400px; }

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
			    <td width="50%" align="center" valign="top">
				    <br>
			        <h3> Confirmação de cadastro! </h3>
				    <p>Confirme o seu cadastro clicando no botão abaixo. </p> <br><br>
					    <a href="https://www.webfinancas.com/site/php/cadastro-ativar.php?id='.$usuario_id.'&token='.$token.'&email='.$email.'" target="_blank" class="btn btn-primary" ><b>CONFIRMAR</b></a>
				    <p></p>
			    </td>    
			    <td width="50%" align="center" valign="bottom"> 
				     <img src="'.$enderecoArquivos.'macbook.png" align="right" class="img" width="400" />	
			     </td>
		    </tr>
		    <tr>			
				<td colspan="2" align="center" valign="top"> 
					<span style="font-size:11px; color:999;">	
					** Leia atentamente os <a href="https://www.webfinancas.com/termosUso" target="blank">termos de uso</a>. ** <br/>
					** A confirmação do seu cadastro é válida por um período de 24h e poderá ser efetuada até o dia <b>'.$validade.'</b>. **
					</span>
				</td>			 
		    </tr>
	    </table>
		';
		
		// ================================================================================
		
		$mail = new PHPMailer();
        
		$mail->IsSMTP();                                // Set mailer to use SMTP
		
		// Optional Settings
        $mail->Host = 'smtp.webfinancas.com'; //'carteiroexpress.com';					  // Specify main and backup server
        $mail->SMTPAuth = true;                         // Enable SMTP authentication
        $mail->Username = 'contato@webfinancas.com'; //no-reply';		  							// SMTP username
        $mail->Password = 'W2BSISTEMAS'; //'7061c986e';                  // SMTP password
		//$mail->SMTPSecure = 'tls';                    // Enable encryption, 'ssl' also accepted
		$mail->Port = 587;								// 465

		$mail->From = 'contato@webfinancas.com'; 		//$email;
		$mail->FromName = 'Web Finanças'; 				//$_POST['name'];
		$mail->AddAddress($email);						// Add a recipient
		//$mail->AddReplyTo($email, $name);
        
		$mail->IsHTML(true);                            // Set email format to HTML
		
		$mail->CharSet = 'UTF-8';

		$subject = 'Web Finanças - Confirmação de cadastro';

		$mail->Subject = $subject;
		$mail->Body    = $message;
        
        $mail->Send();
    }

    /**
     * Retorna clientes na W2B que sejam do parceiro que fez a requisição e que ainda não estejam sincronizados no Doc Monitor
     * @param mixed $params 
     */
    function SincronizarClientesDocMonitor($params){
        
        $dbW2b = new Database("mysql.web2business.com.br","web2business","W2BSISTEMAS","web2business");

        try
        {
            $dbW2b->query('start transaction');

            //Busca clientes
            $clientes = $dbW2b->fetch_all_array('select id as ClienteId, nome as Nome, cpf_cnpj as CpfCnpj, sinc_doc_monitor as Sincronizado from clientes where parceiro_id = '.$params['parceiro_id']);

            if(count($clientes)>0)
                foreach($clientes as $cliente){
                    if($cliente['sinc_doc_monitor']==0)
                        $dbW2b->query('update clientes set sinc_doc_monitor = 1 where id = '.$cliente['ClienteId']);
                }

            $dbW2b->query('commit');

            echo json_encode(array('status'=>1,'clientes'=>$clientes),JSON_NUMERIC_CHECK);
        }
        catch(Exception $e)
        {
            $dbW2b->query('rollback');
            echo $e->getMessage();
        }
        
    }

    /**
     * Retorna cliente na W2B pela id, que seja do parceiro
     * @param mixed $params
     */
    function GetClienteParceiro($params){

        //require_once(ROOT.'sistema/php/db_config.php');

        $dbW2b = new Database('mysql.web2business.com.br','web2business','W2BSISTEMAS','web2business');

        $cliente = $dbW2b->fetch_assoc('select id as ClienteId, nome as Nome, cpf_cnpj as CpfCnpj from clientes where parceiro_id = '.$params['parceiro_id'].' and id = '.$params['cliente_id']);

        if($cliente)
            echo json_encode(array('status'=>1,'cliente'=>$cliente));
        else
            echo json_encode(array('status'=>0,'msg'=>'Cliente não foi encontrado.'), JSON_NUMERIC_CHECK);
    }

    /**
     * Retorna todos os clientes na W2B que sejam do parceiro
     * @param mixed $params
     */
    function GetAllClientesParceiro($params){

        //require_once(ROOT.'sistema/php/db_config.php');

        $dbW2b = new Database('mysql.web2business.com.br','web2business','W2BSISTEMAS','web2business');

        $clientes = $dbW2b->fetch_all_array('select id as ClienteId, nome as Nome, cpf_cnpj as CpfCnpj from clientes where parceiro_id = '.$params['parceiro_id']);

        if(count($clientes)>0)
            echo json_encode(array('status'=>1,'clientes'=>$clientes));
        else
            echo json_encode(array('status'=>0,'msg'=>'Nenhum cliente cadastrado.'), JSON_NUMERIC_CHECK);
    }
}

?>