<?php require("php/Database.class.php");
$db = new Database('mysql.webfinancas.com','webfinancas','W2BSISTEMAS','webfinancas');
?>
<html>
<head>
<title>Web Finanças</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="HTML5 Template" />
<meta name="description" content="Web Finanças">

<!-- Mobile Metas -->
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<?php
echo '
<!-- Web Fonts  -->
		 <link href="site/css/fonts/google/fonts_google.css?family=Open+Sans:300,400,600,700,800|Shadows+Into+Light" rel="stylesheet" type="text/css"> 

		<!-- Libs CSS -->
		<link rel="stylesheet" href="css/bootstrap.css">
		<link rel="stylesheet" href="css/fonts/font-awesome/css/font-awesome.css">
		<link rel="stylesheet" href="vendor/owl-carousel/owl.carousel.css" media="screen">
		<link rel="stylesheet" href="vendor/owl-carousel/owl.theme.css" media="screen">
		<link rel="stylesheet" href="vendor/magnific-popup/magnific-popup.css" media="screen">

		<!-- Theme CSS -->
		<link rel="stylesheet" href="css/theme.css">
		<link rel="stylesheet" href="css/theme-elements.css">
		<link rel="stylesheet" href="css/theme-animate.css">

		<!-- Current Page Styles -->
		<link rel="stylesheet" href="vendor/rs-plugin/css/settings.css" media="screen">
		<link rel="stylesheet" href="vendor/circle-flip-slideshow/css/component.css" media="screen">

		<!-- Skin CSS -->
		<link rel="stylesheet" href="css/skins/blue.css">

		<!-- Custom CSS -->
		<link rel="stylesheet" href="css/custom.css">

		<!-- Responsive CSS -->
		<link rel="stylesheet" href="css/theme-responsive.css" />

		<!-- Head Libs -->
		<script src="vendor/modernizr.js"></script>

		<!--[if IE]>-->
			<link rel="stylesheet" href="css/ie.css">
		<![endif]-->
		';
?>

</head>
<body>

<div role="main" class="main">
				<div class="container">
					<div class="row">
						<div class="col-md-12 center">
							<div class="logo">
								<a href="https://www.webfinancas.com">
									<img src="img/logo_webfinancas_fundo_branco.png" alt="Web Finanças">
								</a>
							</div>
						</div>
					</div>
<div class="row">
						<div class="col-md-12">
							<hr class="tall">
						</div>
					</div>




<?php
$remetente_id = $_GET['id']; 
$destinatario_email = $_GET['email']; 
$tp = $_GET['tp'];
$conexao_id = $_GET['id_list']; //id da lista de convites do remetente

//----------------------------- Acesso a tabela do Cliente -----------------------------

	//Acesso ao tabela de banco de dados dos clientes do Web Finanças
	$remetente_db = $db->fetch_assoc('select db, db_senha from clientes_db where cliente_id = '.$remetente_id.'');
				
	//Conexão no banco do remetente
	$usuario_r = $remetente_db['db']; $senha_db_r = $remetente_db['db_senha']; $db_usuario_r = $remetente_db['db'];

	$db_remetente = new Database('mysql.webfinancas.com',$usuario_r,$senha_db_r,$db_usuario_r);
	
//--------------------------------------------------------------------------------------

//REMETENTE CONTADOR -> tp = 1
if($tp == 1){ 

    $informacao = 'cliente'; 
    $tp_sistema = 'financeiro'; 
    $msg_cliente_conectado = 'Existe uma conexão <b>ATIVA</b>, <br/> <b>FINALIZE</b> sua conexão atual antes de aceitar um novo convite.';
    $coluna_remetente_id = 'contador_id';
    $coluna_destinatario_id = 'cliente_id';

//REMETENTE CLIENTE -> tp = 2
}elseif($tp == 2){ 

    $informacao = 'contador'; 
    $tp_sistema = 'contador'; 
    $msg_cliente_conectado = 'O seu cliente possui uma conexão <b>ATIVA</b>, <br/> peça para que ele cancele essa conexão antes de iniciar uma outra.';
    $coluna_remetente_id = 'cliente_id';
    $coluna_destinatario_id = 'contador_id';

}
	
//==================================== Verificar se existe convite ativo ================================
	
$verificar_convite = $db_remetente->fetch_assoc('select id, conectado from conexao where id = '.$conexao_id);
	
if($verificar_convite==false){
			
	echo'
		<div class="row">
				<div class="col-md-12 center">
						<h3>CONVITE EXPIRADO</h3>
					<p class="lead" style="font-size:18px;">
						Por favor, solicite ao remetente o reenvio do convite.<br><br>
						<a href="https://www.webfinancas.com/sistema" class="btn btn-primary" id="l1" >WEB FINANÇAS</a>
					</p>
				</div>
			</div>
	';	
			
}elseif($verificar_convite['conectado']==1){

    echo'
		<div class="row">
				<div class="col-md-12 center">
						<h3>CONECTADO</h3>
					<p class="lead" style="font-size:18px;">
						Esta conexão já está ativa.<br><br>
						<a href="https://www.webfinancas.com/sistema" class="btn btn-primary" id="l1" >WEB FINANÇAS</a>
					</p>
				</div>
			</div>
	';

}else{

	//Busca informações sobre o destinatário registrado na tabela de usuários
	$destinatario_dados = $db->fetch_assoc('select cliente_db_id from usuarios where email = "'.$destinatario_email.'"'); //and $tp_sistema = 1

	//Verifica se o destinatario está cadastrado

	//Destinatário cadastrado
    if($destinatario_dados == true){
		
		//Acesso ao tabela de banco de dados dos clientes do Web Finanças
		$destinatario_db = $db->fetch_assoc('select cliente_id, db, db_senha from clientes_db where id = '.$destinatario_dados['cliente_db_id'].'');
		$destinatario_id = $destinatario_db['cliente_id'];
		
		//Conexão no banco do Destinatario
		$usuario = $destinatario_db['db']; $senha_db = $destinatario_db['db_senha']; $db_usuario = $destinatario_db['db'];
		$db_destinatario = new Database('mysql.webfinancas.com',$usuario,$senha_db,$db_usuario);
		
		//Verifica se o destinatário já tem uma conexxão ativa com algum contador
        $veri_dest = $db_destinatario->fetch_assoc('select conectado from conexao where conectado = 1');

		//======================== DESTINATÁRIO CADASTRADO E JÁ TEM UMA CONEXÃO COM OUTRO CONTADOR ==========================
        if($veri_dest == true && $tp == 1){ 
			echo '			 
			<div class="row">
					<div class="col-md-12 center">
						<h3>CONEXÃO NÃO ESTABELECIDA</h3>
						<p class="lead" style="font-size:18px;">'.$msg_cliente_conectado.'</p>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12 center" >
						<hr class="tall">
					</div>
					<h4 align="center">COMO ACEITAR UMA NOVA CONEXÃO?</h4>				
				</div>                  
                    
				<div class="row">
					<div class="col-md-12">
						<div class="row">
							<div class="col-md-3">
								<div class="feature-box secundary">
									<div class="feature-box-icon">
										<i class="icon icon-lock"></i>
									</div>
									<div class="feature-box-info">
										<h4 class="shorter">Acesse o sistema</h4>
											<p class="tall">Primeiro você deve acessar o sistema com o seu login e senha. <br>
                                        <a href="https://www.webfinancas.com/sistema"> Entrar no Web Finanças </a> 
                                    </p>
									</div>
								</div>
							</div>
								<div class="col-md-3">
								<div class="feature-box secundary">
									<div class="feature-box-icon">
										<i class="icon icon-bars"></i>
									</div>
									<div class="feature-box-info">
										<h4 class="shorter">Menu contador</h4>
											<p class="tall">Após o acesso ao sistema você deve localizar o menu do contador, que fica localizado no menu lateral.
                                    </p>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="feature-box secundary">
									<div class="feature-box-icon">
										<i class="icon icon-minus-square"></i>
									</div>
									<div class="feature-box-info">
										<h4 class="shorter">Cancelar conexão</h4>
											<p class="tall">Para cancelar a conexão com o seu contador você deve localizá-lo na aba contabilidade e clicar no botão "Cancelar Conexão".
                                    </p>
									</div>
								</div>
							</div>
                            <div class="col-md-3">
								<div class="feature-box secundary">
									<div class="feature-box-icon">
										<i class="icon icon-check-square-o"></i>
									</div>
									<div class="feature-box-info">
										<h4 class="shorter">Nova conexão</h4>
											<p class="tall">Após cancelar a conexão atual, você deve clicar no botão "Convidar Contador" e aceitar o convite do seu novo contador. Pronto a sua conexão esta estabelecida.
                                    </p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>';
			
		//======================== DESTINATÁRIO CADASTRADO E NÃO TEM NENHUMA CONEXÃO COM OUTRO ESCRITÓRIO ==========================

		}else{
			

			//TABELA DESTINATARIO -> Atualiza os dados da conexão entre as partes 
			$dados_conexao = array('dt_inicio' => date('Y-m-d H:m:s'),'conectado' => '1');
			$db_destinatario->query_update('conexao', $dados_conexao, $coluna_remetente_id.' = '.$remetente_id.' AND conectado = 0');


			//TABELA REMETENTE
			
			//Acesso ao tabela de banco de dados dos clientes do Web Finanças
			$remetente_db = $db->fetch_assoc('select db, db_senha from clientes_db where cliente_id = '.$remetente_id.'');
						
			//Conexão no banco do remetente
			$usuario_r = $remetente_db['db']; $senha_db_r = $remetente_db['db_senha']; $db_usuario_r = $remetente_db['db'];
		
			$db_remetente = new Database('mysql.webfinancas.com',$usuario_r,$senha_db_r,$db_usuario_r);
			
				// Atualiza os dados da conexão entre as partes
				$dados_conexao02 = array('dt_inicio' => date('Y-m-d H:m:s'),'conectado' => '1');
				$db_remetente->query_update('conexao',$dados_conexao02," id = ".$conexao_id.' AND (conectado = 0 OR conectado =3)');
			
				//Pega o nome do escritório
				$db_w2b = new Database('mysql.web2business.com.br','web2business','W2BSISTEMAS','web2business');
				$nome_escritorio = $db_w2b->fetch_assoc('select nome, email from clientes where id = '.$remetente_id.'');
			
			echo'
			 	<div class="row">
						<div class="col-md-12 center">
								<h3>CONEXÃO EFETUADA COM SUCESSO</h3>
							<p class="lead" style="font-size:18px;">
								Agora você possui uma conexão com <br> <b>'.$nome_escritorio['nome'].'</b>.<br><br>
								<a href="https://www.webfinancas.com/sistema" class="btn btn-primary" id="l1" >ENTRAR NO WEB FINANÇAS</a>
                            </p>
						</div>
					</div>
			';			
				
			/*echo"<script>location.href='https://www.webfinancas.com/sistema';</script>";	*/		
		            
        }
		
	//==================== DESTINATÁRIO NÃO ESTA CADASTRADO ======================
	
	}else{
			//Pega o nome do escritório
			$db_w2b = new Database('mysql.web2business.com.br','web2business','W2BSISTEMAS','web2business');
			$nome_escritorio = $db_w2b->fetch_assoc('select nome from clientes where id = '.$remetente_id.'');
			
			echo'
			 	<div class="row">
						<div class="col-md-12 center">
								<h3>USUÁRIO NÃO CADASTRADO</h3>
							<p class="lead" style="font-size:18px;">
								Para estabelecer uma conexão com <b>'.$nome_escritorio['nome'].'</b>,<br> você precisa se cadastrar no Web Finanças.<br><br> Cadastre-se agora. É simples, fácil e rápido.<br><br>

								<a href="https://www.webfinancas.com/?id='.$remetente_id.'&email='.$destinatario_email.'&id_list='.$conexao_id.'#cadastro" class="btn btn-primary" id="l1" >CADASTRE-SE</a>
                            </p>
						</div>
					</div>
			';		
		
			//Direciona o cliente para fazer o cadastro dentro do Web Finanças
			/* echo "<script>location.href='https://www.webfinancas.com/?id=$cliente_id#cadastro';</script>";  */
	}


} // final verificação convite ativo 

echo '
    <!-- Vendor -->
    <script src="vendor/jquery/jquery.js"></script>
    <script src="vendor/jquery.appear/jquery.appear.js"></script>
    <script src="vendor/jquery.easing/jquery.easing.js"></script>
    <script src="vendor/jquery-cookie/jquery-cookie.js"></script>
    <script src="vendor/bootstrap/bootstrap.js"></script>
    <script src="vendor/common/common.js"></script>
    <script src="vendor/jquery.validation/jquery.validation.js"></script>
    <script src="vendor/jquery.stellar/jquery.stellar.js"></script>
    <script src="vendor/jquery.easy-pie-chart/jquery.easy-pie-chart.js"></script>
    <script src="vendor/jquery.gmap/jquery.gmap.js"></script>
    <script src="vendor/isotope/jquery.isotope.js"></script>
    <script src="vendor/owlcarousel/owl.carousel.js"></script>
    <script src="vendor/jflickrfeed/jflickrfeed.js"></script>
    <script src="vendor/magnific-popup/jquery.magnific-popup.js"></script>
    <script src="vendor/vide/vide.js"></script>
		
    <!-- Theme Base, Components and Settings -->
    <script src="js/theme.js"></script>
		
    <!-- Specific Page Vendor and Views -->
    <script src="vendor/rs-plugin/js/jquery.themepunch.tools.min.js"></script>
    <script src="vendor/rs-plugin/js/jquery.themepunch.revolution.min.js"></script>
    <script src="vendor/circle-flip-slideshow/js/jquery.flipshow.js"></script>
    <script src="js/views/view.home.js"></script>
		
    <!-- Theme Custom -->
    <script src="js/custom.js"></script>
		
    <!-- Theme Initialization Files -->
    <script src="js/theme.init.js"></script>
';
?>

</body>
</html>