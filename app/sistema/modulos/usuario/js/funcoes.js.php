<?php 
/*
echo ' 
<script type="text/javascript" src="modulos/usuario/js/funcoes.js"></script>
';
*/
/* ======== Contagem regressiva ======== */
$db_wf = new mysqli('mysql.webfinancas.com', 'webfinancas', 'W2BSISTEMAS', 'webfinancas');
$cliente_trial = mysqli_fetch_assoc(mysqli_query($db_wf,'select date_format(dt_cadastro, "%d-%m-%Y") dt_cadastro, dt_cadastro dt_cadastro_2 from clientes_trial where cliente_id ='.$cliente_id));
$db_wf->close();

if(!empty($cliente_trial)){
	$data = $cliente_trial['dt_cadastro_2'];
	$data = strtotime("+30 days",strtotime($data));
	$hoje = date('Y-m-d H:i:s');
	$hoje = strtotime($hoje);
	$diferenca = $data - $hoje;
	$faltamDias = (int)floor( $diferenca / (60 * 60 * 24));
	/*
	//Cálcula a data de expiração
	$data = $cliente_trial['dt_cadastro'];
	$data = date('d-m-Y', strtotime("+15 days",strtotime($data)));
	list ($dia,$mes,$ano) = explode('-', $data);
	
	// o mês tem que ser diminuido -1 porque o plugin entende que jan = 0, fev = 1 e assim por diante
	//$hora = date('H'); $min = date('i'); $seg = date('s');
	//Falta somar a data e colocar o horário inicial que o plugin deve começar a contar, porque ele esta pegando a do pc local.	 endTime: new Date('.$ano.','.$mes.','.$dia.')
	
	// Define os valores a serem usados
	$data_inicial = date('d-m-Y');
	$data_final = $data;
	// Cria uma função que retorna o timestamp de uma data no formato DD/MM/AAAA
	function geraTimestamp($data) {
	$partes = explode('-', $data);
	return mktime(0, 0, 0, $partes[1], $partes[0], $partes[2]);
	}
	// Usa a função criada e pega o timestamp das duas datas:
	$time_inicial = geraTimestamp($data_inicial);
	$time_final = geraTimestamp($data_final);
	// Calcula a diferença de segundos entre as duas datas:	
	$diferenca = $time_final - $time_inicial; // 19522800 segundos
	// Calcula a diferença de dias
	$faltamDias = (int)floor( $diferenca / (60 * 60 * 24)); // 225 dias
	// Exibe uma mensagem de resultado:
	//echo "A diferença entre as datas ".$data_inicial." e ".$data_final." é de <strong>".$faltamDias."</strong> dias";
	
	$VerfaltamDias = $faltamDias;
	if($VerfaltamDias > 0){
		$faltamDias = $faltamDias - 1; // -1 	
  	if($faltamDias < 10){ $faltamDias = '0'.$faltamDias; }
	}else{ $faltamDias = '00';}
	*/

	// ==== Cálculo de Horas ===
	$restoDias = ($diferenca / (60 * 60 * 24)) - $faltamDias;
	$faltamHoras = (int)floor($restoDias * 24);
	$restoHoras = $restoDias * 24 - $faltamHoras;
	$faltaMinutos = (int)floor($restoHoras * 60);
	
	if($faltamDias<10)
		$faltamDias = '0'.$faltamDias;

	if($faltamHoras<10)
		$faltamHoras = '0'.$faltamHoras;

	if($faltaMinutos<10)
		$faltaMinutos = '0'.$faltaMinutos;

	$interbaloAtual = $faltamHoras.':'.$faltaMinutos.':00';
	
	/*
	$inicio = date('H:i:s');
	$fim = '24:00:00'; 
	// Converte as duas datas para um objeto DateTime do PHP
	// PARA O PHP 5.3 OU SUPERIOR
	$inicio = DateTime::createFromFormat('H:i:s', $inicio);
	// PARA O PHP 5.2
	// $inicio = date_create_from_format('H:i:s', $inicio);
	$fim = DateTime::createFromFormat('H:i:s', $fim);
	// $fim = date_create_from_format('H:i:s', $fim);
	$intervalo = $inicio->diff($fim);
	// Formata a diferença de horas para
	// aparecer no formato 00:00:00 na página
	//$interbaloAtual = $intervalo->format('%H:%I:%S');
	if($VerfaltamDias <= -1){ $interbaloAtual = '00:00:00'; }
	//Chama uma função quando o período for finalizado -> timerEnd: function(){ alert("end!"); }
	*/
	
echo '
		
		<script src="js/counter/js/jquery.countdown.js"></script>
    <link href="js/counter/css/media.css" rel="stylesheet" type="text/css" />
    <script>
      $(function(){
        $(".digits").countdown({
          image: "js/counter/img/digits.png",
          format: "dd:hh:mm:ss",
					startTime: "'.$faltamDias.':'.$interbaloAtual.'"          
        });
      });
    </script>

';
}


$cliente_cod = $_SESSION['cliente_id'];
echo "
<script>
/*
========================================================================================================================
PLUPLOAD LOGO RECIBO
========================================================================================================================
*/
$(window).load(function () {

    var uploader = new plupload.Uploader({
        runtimes: 'html5', //'flash,silverlight,html4',
        chunk_size: '1mb', //Divide o arquivo em partes para fazer o upload
        browse_button: 'pickfiles', // you can pass an id...
        container: document.getElementById('container'), // ... or DOM Element itself

        //flash_swf_url: '../js/Moxie.swf',
        //silverlight_xap_url: '../js/Moxie.xap',

        /* ========= Configuração da URL =========  
       | ?dir = CAMINHO DO ARQUIVO -> Indique o caminho para onde a imagem será gravada 
       | &nome = NOME -> Informar o nome para o arquivo 
       | &nomeDinamico=ARQ_ -> Insira nomeDinamico e escreva o prefixo de sua preferencia ex: ARQ_ para o arquivo para criar o nome único dinâmicamente  */

        url: '/sistema/modulos/usuario/php/upload.php?dir=../../../uploads/cliente_".$cliente_cod."&nomeDinamico=logo_',
        // =======================================

        multi_selection: true, //True para multiplos arquivos e False para um por vez
        unique_names: false,
        rename: true,

        filters: {
            max_file_size: '2mb', //Tamnho máximo do arquivo
            mime_types: [
                { title: 'Image files', extensions: 'jpg,jpeg,gif,png' }, //Tipo de arquivos máximo do arquivo
            ]
        },
        resize: {
            width: 200,   //Redimencionar largura do arquivo precisa informar a altura quando o crop: false para que ele mantenha a proporcionalidade baseado no width item.
            height: 200,  //Redimencionar altura do arquivo precisa informar a largura quando o crop: false para que ele mantenha a proporcionalidade baseado no height item.
            crop: false   //Se tiver TRUE ele corta a imagem utilizando a largura e altura informados, caso o contrário ele redimenciona de acordo com o primeiro item width ou height. O segundo item mantém a proporcionalidade.
        },
        //=============================  Init  ===============================
        init: {
            
            FilesAdded: function (up, files) {
                uploader.start(); //Após anexar o arquivo ele inicia o donwload.
            },
            BeforeUpload: function (up, file) {
                $('.img').attr('src', 'images/loaders/loader9.gif');
            },
            FileUploaded: function (up, file, response) {
            
            var obj = jQuery.parseJSON(response.response); 
            var arquivo = 'uploads/cliente_".$cliente_cod."/' + obj.nomeArquivo;
            
            logoRecibo(arquivo, ".$cliente_cod.");
            
            //$('.img').attr('src', 'uploads/cliente_".$cliente_cod."/' + obj.nomeArquivo);  
            //console.log(JSON.stringify(response));
            },
            Error: function (up, err) {
                $('.nFailure p').html(err.message + '<br>(Tamanho máximo: 2 MB)');
                $('.nFailure').slideDown();
                setTimeout(function () { $('.nFailure').slideUp() }, 5000);
            }
        }
    });

    uploader.init();

});
</script>";
?>