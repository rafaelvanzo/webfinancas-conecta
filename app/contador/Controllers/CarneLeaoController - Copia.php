<?php
require_once(ROOT_SISTEMA.'Models/CarneLeao.php');
require_once(ROOT_SISTEMA.'php/Database.class.php');
require_once(ROOT_SISTEMA.'Controllers/Crc32.Class.php');
require("../php/MPDF/mpdf.php");

/**
 * CarneLeaoController short summary.
 *
 * CarneLeaoController description.
 *
 * @version 1.0
 * @author Rafael Vanzo
 */

class CarneLeaoController
{

    private $db;
    private $dbCliente;
	private $db_w2b;

    /**
     * Construtor
     * @param Database $dbConnection 
     */
    function __construct(Database $dbConnection = null){
        $this->db = $dbConnection;
		$this->db_w2b = new Database('mysql.web2business.com.br','web2business','W2BSISTEMAS','web2business');
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
     * Summary of DataTable
     * @param mixed $db
     * @param mixed $params
     * @return string
     */
    function DataTable($params){
        
		/* ===== Id Contabilidade ===== */
		
		$Cliente_id = $_SESSION['cliente_id'];//$_SESSION['cliente_id']; // Conecta -> 342 e lexData -> 244
		
		/* ===== Id Contabilidade ===== */

        $filtro = $params['filtro'];
        $filtro = str_replace('\"','"',$filtro);
        $filtro = str_replace("\'","'",$filtro);
        $filtro = json_decode($filtro, true);

        //if($filtro['cliente_id']!=0){            

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

            $Query = "SELECT id, nome, email, cpf_cnpj FROM clientes 
							     WHERE parceiro_id = ".$Cliente_id;

        }else{
            
            $Query = "select id, nome, email, cpf_cnpj FROM clientes 
								WHERE parceiro_id = ".$Cliente_id." and (nome like '%".$sSearch."%' || email like '%".$sSearch."%')";
        }
        
        $iTotalDisplayRecords = mysql_num_rows(mysql_query($Query, $this->db_w2b->link_id));

        $Query = $Query.' order by nome desc limit '.$iDisplayStart.",".$iDisplayLength;

        $Query = mysql_query($Query, $this->db_w2b->link_id);

        while($dados = mysql_fetch_assoc($Query)){

            //===== Verifica se o cliente tem carne leão ===== 

            self::DbClienteConn($dados['id']);

            $ContaCarneLeao = mysql_num_rows(mysql_query("SELECT id FROM contas WHERE carne_leao = '1'", $this->dbCliente->link_id));

            // ===== Verifica se o cliente tem carne leão ===== 
            
            if($ContaCarneLeao > 0){
                
                $Info = '"'.$dados['id'].'", "'.$dados['nome'].'", "'.$dados['cpf_cnpj'].'"';

                $opcoes = "
								<a href='javascript://' title='' class='button blueB' style='padding: 6px;' onClick='javascript:OpenModal(".$Info.");'>Gerar arquivo</a>
								";  
                
            }else{
                
                $opcoes = 'Não possui conta Carne Leão';

            }
            
            array_push($aaData,array('id' => str_pad($dados['id'], 8, "0", STR_PAD_LEFT), 'nome' => $dados['nome'], 'email' => $dados['email'], 'opcoes'=>$opcoes));
            

        }

		
		if($iTotalDisplayRecords > 0){

            $retorno = array('sEcho'=>$sEcho,'iTotalRecords'=>$iTotalDisplayRecords,'iTotalDisplayRecords'=>$iTotalDisplayRecords,'aaData'=>$aaData);
            
        }else{
            
			$retorno = array('sEcho'=>$sEcho,'iTotalRecords'=>0,'iTotalDisplayRecords'=>0,'aaData'=>array());
            
		}

        echo json_encode($retorno);
    }
    
    
	


    /* ===================================================== */
    /* --------- Gerar arquivo p/ IRPF (Carne Leão) -------- */
    /* ===================================================== */

    /*
    /* Gera o Header do arquivo 
     */

    function ArquivoHeader($ContasFinanceirasCarneLeao, $Dados, $NomeArquivo){

        /* ===== Configuração ===== */
        
        $VersaoCarneLeao = $Dados['VersaoCarneLeao'];

        $NomeDeclarante = $Dados['NomeDeclarante'];
        $CodigoP = $Dados['CodigoP'];								
        $RegistroP = $Dados['RegistroP'];								
        $CPF = $Dados['CPF'];
        $AnoDeclaracao = $Dados['AnoDeclaracao'];
        $AnoCompetencia = $Dados['AnoCompetencia'];

        $Nome = str_pad($NomeDeclarante, 60, ' ', STR_PAD_RIGHT);														
        $CodigoProf = str_pad($CodigoP, 4, ' ', STR_PAD_RIGHT);
        $RegistroProf = str_pad($RegistroP, 20, ' ', STR_PAD_RIGHT);

        /* ===== Configuração ===== */


        /* ===== Geração do Header ===== */ 

        $QuebraLinhaTxt = "\r\n";			// Quebra de linha TXT
        
        $ArquivoTxt = '';

        $ArquivoTxt = 'IRCLEAO   '.$AnoDeclaracao.$AnoCompetencia.$CPF.$VersaoCarneLeao.$QuebraLinhaTxt;
        $ArquivoTxt .= '01CLEAO'.$AnoCompetencia.'     '.$AnoCompetencia.$CPF.$Nome.$CodigoProf.$RegistroProf.'          '.$QuebraLinhaTxt;



		/* ===== Query - Contas Carne Leão ===== */
		
		/* ===== Retorna o valor total de cada mes ===== */

        $c = 1;

        while($c <= 12){	
			
            $dtIni = "$AnoCompetencia-$c-01";
            $dtFim = date('Y-m-t',strtotime($dtIni));

            $queryLancamentos = "SELECT SUM(valor) valor
                    FROM lancamentos a
                    JOIN favorecidos b ON a.favorecido_id = b.id
                    WHERE a.dt_compensacao >= '$dtIni' AND a.dt_compensacao <= '$dtFim'
                        AND a.conta_id in ($ContasFinanceirasCarneLeao)
                        AND a.compensado = 1 
                        AND a.tipo = 'R'
                        AND b.inscricao = 'cpf'
                        ORDER BY a.dt_compensacao, a.id";

            //$queryLancamentos = 'SELECT replace(SUM(lancamentos.valor), ".", "") as valor, favorecidos.cpf_cnpj as cpf_cnpj
            //								FROM lancamentos
            //							JOIN favorecidos ON lancamentos.favorecido_id = favorecidos.id
            //						WHERE  (lancamentos.dt_compensacao BETWEEN "'.$AnoCompetencia.'-'.$c.'-01" AND "'.date($AnoCompetencia.'-'.$c.'-t').'") AND ('.$ContasFinanceirasCarneLeao.' AND lancamentos.compensado = 1 AND tipo = "R" AND favorecidos.inscricao = "cpf") ORDER BY lancamentos.dt_compensacao ASC ';

            $Lancamentos  = $this->dbCliente->fetch_assoc($queryLancamentos);
            
            $valor = str_replace('.', '', $Lancamentos['valor']);

            if($c == 1)
                $ArquivoTxt .= '02JANEIRO        '.str_pad($valor,13, '0',STR_PAD_LEFT).'0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000'.$QuebraLinhaTxt;
            
            if($c == 2)
                $ArquivoTxt .= '02FEVEREIRO      '.str_pad($valor,13, '0',STR_PAD_LEFT).'0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000'.$QuebraLinhaTxt;
            
            if($c == 3)
                $ArquivoTxt .= '02MARCO          '.str_pad($valor,13, '0',STR_PAD_LEFT).'0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000'.$QuebraLinhaTxt;
            
            if($c == 4)
                $ArquivoTxt .= '02ABRIL          '.str_pad($valor,13, '0',STR_PAD_LEFT).'0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000'.$QuebraLinhaTxt;
            
            if($c == 5)
                $ArquivoTxt .= '02MAIO           '.str_pad($valor,13, '0',STR_PAD_LEFT).'0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000'.$QuebraLinhaTxt;
            
            if($c == 6)
                $ArquivoTxt .= '02JUNHO          '.str_pad($valor,13, '0',STR_PAD_LEFT).'0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000'.$QuebraLinhaTxt;
            
            if($c == 7)
                $ArquivoTxt .= '02JULHO          '.str_pad($valor,13, '0',STR_PAD_LEFT).'0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000'.$QuebraLinhaTxt;
            
            if($c == 8)
                $ArquivoTxt .= '02AGOSTO         '.str_pad($valor,13, '0',STR_PAD_LEFT).'0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000'.$QuebraLinhaTxt;
            
            if($c == 9)
                $ArquivoTxt.= '02SETEMBRO       '.str_pad($valor,13, '0',STR_PAD_LEFT).'0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000'.$QuebraLinhaTxt;
            
            if($c == 10)
                $ArquivoTxt .= '02OUTUBRO        '.str_pad($valor,13, '0',STR_PAD_LEFT).'0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000'.$QuebraLinhaTxt;	
            
            if($c == 11)
                $ArquivoTxt .= '02NOVEMBRO       '.str_pad($valor,13, '0',STR_PAD_LEFT).'0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000'.$QuebraLinhaTxt;
            
            if($c == 12)
                $ArquivoTxt .= '02DEZEMBRO       '.str_pad($valor,13, '0',STR_PAD_LEFT).'0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000'.$QuebraLinhaTxt;
            
            $c += 1;
        }

		/* ===== Retorna o valor total de cada mes ===== */


		/* ===== Gera e grava no arquivo ===== */
		
        //Variável arquivo armazena o nome e extensão do arquivo.
        $arquivo = $NomeArquivo.".DEC";
        
        //Variável $fp armazena a conexão com o arquivo e o tipo de ação.
        $fp = fopen('../ArquivosCarneLeao/'.$arquivo, "a+");
        
        //Escreve no arquivo aberto.
        fwrite($fp, $ArquivoTxt);		//Quebra de linha TXT "\r\n"
        
        
        //Fecha o arquivo.
        fclose($fp);

		/* ===== Gera e grava no arquivo ===== */


        /* ===== Chama a função para adicionar cada lançamento ===== */

        self::ArquivoLinhasValores($ContasFinanceirasCarneLeao, $AnoCompetencia, $NomeArquivo);
    }



    /* ===== Gera a linha do arquivo ===== */

    function ArquivoLinhasValores($ContasFinanceirasCarneLeao, $AnoCompetencia, $NomeArquivo){ 
        
        $queryLinhas = "SELECT date_format(a.dt_compensacao, '%m%Y') as data, replace(a.valor, '.', '') as valor, replace(replace(b.cpf_cnpj,'-',''),'.','') as cpf_cnpj
                    FROM lancamentos a
                    JOIN favorecidos b ON a.favorecido_id = b.id
                    WHERE a.dt_compensacao >= '$AnoCompetencia-01-01' AND a.dt_compensacao <= '$AnoCompetencia-12-31'
                        AND a.conta_id in ($ContasFinanceirasCarneLeao)
                        AND a.compensado = 1 
                        AND a.tipo = 'R'
                        AND b.inscricao = 'cpf'
                        ORDER BY a.dt_compensacao, a.valor";

        //$queryLinhas = 'SELECT date_format(lancamentos.dt_compensacao, "%m%Y") as data,  replace(lancamentos.valor, ".", "") as valor,  replace(replace(favorecidos.cpf_cnpj,"-",""),".","") as cpf_cnpj 
	    //FROM lancamentos
	    //JOIN favorecidos ON  lancamentos.favorecido_id = favorecidos.id
	    //WHERE (lancamentos.dt_compensacao BETWEEN "'.$AnoCompetencia.'-01-01" AND "'.date($AnoCompetencia.'-12-t').'") AND ('.$ContasCarneLeao.' AND lancamentos.compensado = 1 AND tipo = "R" AND favorecidos.inscricao = "cpf") ORDER BY lancamentos.dt_compensacao ASC ';

        $Linhas = $this->dbCliente->fetch_all_array($queryLinhas); 

        /* ===== Número desconhecido (Não consta no manual) ===== */
        $NumDesc = '1000';
        
        $Retorno = '';
        
        foreach($Linhas as $Linhas){
            

            $Valor = str_pad($Linhas['valor'],13, '0',STR_PAD_LEFT);
            
			if(strlen($Linhas['cpf_cnpj']) == 11)
                $Retorno .= '03'.$Linhas['data'].$NumDesc.$Linhas['cpf_cnpj'].$Linhas['cpf_cnpj'].$Valor."\r\n";
            
        }
        

        /* ===== Gera e grava no arquivo ===== */

        //Variável arquivo armazena o nome e extensão do arquivo.
        $arquivo = $NomeArquivo.".DEC";
        
        //Variável $fp armazena a conexão com o arquivo e o tipo de ação.
        $fp = fopen('../ArquivosCarneLeao/'.$arquivo, "a+");

        //Escreve no arquivo aberto.
        fwrite($fp, $Retorno);				//Quebra de linha TXT "\r\n"
        
        //Fecha o arquivo.
        fclose($fp);

        /* ===== Gera e grava no arquivo ===== */	


    }


    function CriarArquivo($ClienteId, $AnoDec){

        /* ===== Pré-Configuração ===== */

        $VersaoCarneLeao = array('2016' => '110','2017' => '100');		/* Versão do Carne Leão */
        $CodigoP = array('0' => '000', '1' => '225', '2' => '226');		/* Códigos das profissões dentro do carne leão -> 000 - Outras ocupações | 225 - Médico | 226 - Dentista */
        $TipoProfissao = 0;												/* Defina qual será o tipo de profissão que selecionará no arrau $CodigoP */

        /* ===== Pré-Configuração ===== */
		
        $AnoDeclaracao = $AnoDec;
        $AnoCompetencia = $AnoDeclaracao - 1;
        $RegConselhoProf = '';												/* Registro do conselho do profissional Ex.: CRM123456 */

        /* ===== Conecta nos dados do cliente na W2B ===== */
        
        $DadosCliente = $this->db_w2b->fetch_assoc('SELECT nome, replace(replace(cpf_cnpj,"-",""),".","") as cpf_cnpj FROM clientes WHERE id ='.$ClienteId);

        $NomeDeclarante = self::removeCaracteresEspeciais($DadosCliente['nome']);	
        $CPF = $DadosCliente['cpf_cnpj'];

        
        /* ===== Conecta nos dados do cliente na W2B ===== */
        
        /* ===== Cria o array para enviar para outras funções ===== */

        $Dados = array('VersaoCarneLeao' => $VersaoCarneLeao[$AnoCompetencia],
                        'CodigoP' => $CodigoP[$TipoProfissao],					
                        'NomeDeclarante' => $NomeDeclarante,
                        'CPF' => $CPF,
                        'RegistroP' => $RegConselhoProf,
                        'AnoCompetencia' => $AnoCompetencia, 
                        'AnoDeclaracao' => $AnoDeclaracao);
        
        /* ===== Conexão db Cliente ===== */
        /*
        $db_cliente = $db_wf->fetch_assoc('SELECT db, db_senha FROM clientes_db WHERE cliente_id ='.$ClienteId);

        $host = "mysql.webfinancas.com";
        $usuario = $db_cliente['db'];
        $senha = $db_cliente['db_senha'];
        $db_usuario = $db_cliente['db'];

        $db = new Database($host,$usuario,$senha,$db_usuario);*/


        self::DbClienteConn($ClienteId);


        /* ===== Conexão db Cliente ===== */


        /* ===== Seleciona as contas que utilizam o carne leão ===== */

        $contasFinanceirasCarneLeao = $this->dbCliente->fetch_all_array('SELECT id FROM contas WHERE carne_leao = 1');
        $contasFinanceirasId = array();
        foreach($contasFinanceirasCarneLeao as $contaFinanceiraId){
            array_push($contasFinanceirasId,$contaFinanceiraId["id"]);
        }
        $contasFinanceirasCarneLeaoId = join(",",$contasFinanceirasId);

        //$n = 1;
        //$ContasCarneLeao = '';
        //foreach($Contas as $Contas){
		
		//if($n > 1){ $OR = ' OR lancamentos.conta_id'; }else{ $OR = '';}

		//$ContasCarneLeao .= $OR.$Contas['id'];
		
		//$n = 1;
        //}

        // ===== Nome do Arquivo =====
        //MODELO: 11066149763-LEAO-2017-2017-EXPORTA-IRPF2018.DEC

        $NomeArquivo = $CPF.'-LEAO-'.$AnoCompetencia.'-'.$AnoCompetencia.'-EXPORTA-IRPF'.$AnoDeclaracao;

        //===== Seleciona as contas que utilizam o carne leão =====

        self::ArquivoHeader($contasFinanceirasCarneLeaoId, $Dados, $NomeArquivo);

        // ===== Gera o Hash =====

        self::main($NomeArquivo.'.DEC');

        return $NomeArquivo.'.DEC';
    }


    /*
    /* Remover caracter e colocar a string em maiusculo 
     */

    function removeCaracteresEspeciais($string){

        $tr = strtr(

            $string,

            array (

              'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A',
              'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E',
              'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ð' => 'D', 'Ñ' => 'N',
              'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O',
              'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Ŕ' => 'R',
              'Þ' => 's', 'ß' => 'B', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a',
              'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e',
              'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
              'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o',
              'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y',
              'þ' => 'b', 'ÿ' => 'y', 'ŕ' => 'r', '´' => '', '`' => '', '-' => ''
            )
        );

        return strtoupper($tr);

    }






    /* ====================================================== */
    /* ------- Gerar Hash para o arquivo (Carne Leão) ------- */
    /* ====================================================== */

    function main($NomeArquivo)
    {
        $pLong = new PLong();
        $crc32 = new Crc32();
        $hashCalculado = null;
        $hashCalculadoLinhaAnterior = 0;

        $myfile = fopen("../ArquivosCarneLeao/".$NomeArquivo, "a+") or die("Unable to open file!");
        
        while(!feof($myfile)) {
            
            //echo fgets($myfile) . "<br>";        
            
            $linha = fgets($myfile);
            $linha = str_ireplace("\r\n","",$linha);
            
            if(strlen($linha) > 0){

                if ($hashCalculadoLinhaAnterior != 0)
                    $pLong->setValue($hashCalculadoLinhaAnterior);
                
                $hash = $crc32->CalcCrc32($linha, strlen($linha), $pLong);
                $hashCalculadoLinhaAnterior = $hash;
                $hashCalculado = $crc32->getStrCrc32();//$hashCalculado .= $crc32->getStrCrc32().' <br>';
            }
        }
        

        /* ===== Grava o Hash ===== */
        fwrite($myfile, '99'.$hashCalculado);


        fclose($myfile);
        
    }




    /* ===================================================== */
    /* -------- Verificar se os CPFs estão corretos -------- */
    /* ===================================================== */

	/* 
     * Verifica os CPFs, alerta se estiver errado, 
     * caso contrario chama a função para gerar o arquivo 
     */

	function ExportarArquivo(){

		$Cliente_id = $_REQUEST['cliente_id'];
		$Profissional_CPF = $_REQUEST['Profissional_CPF'];
		$AnoDeclaracao = $_REQUEST['AnoDeclaracao'];
        $AnoCompetencia = $AnoDeclaracao - 1;
		
		self::DbClienteConn($Cliente_id);
		

        $favorecidos = '';
        
        $c = 0;

        //while($dados = mysqli_fetch_all ($Query)){
        
        $dadosFavorecidos = $this->dbCliente->fetch_all_array("
                select distinct b.favorecido_id, a.nome, a.cpf_cnpj
                from favorecidos a
                join lancamentos b on a.id = b.favorecido_id
                where b.dt_compensacao >= '$AnoCompetencia-01-01' and b.dt_compensacao <= '$AnoCompetencia-12-31'
                and b.compensado = 1
                and b.tipo = 'R'
                order by a.nome");

        foreach($dadosFavorecidos as $dados){
			
            if(strlen($dados['cpf_cnpj']) == 14){
                
				
                $ValidaCPF = self::validarCPF($dados['cpf_cnpj']);
                
                if($ValidaCPF == FALSE){
                    
                    $c += 1;
                    $favorecidos .= '<tr><td align="left">'.$c.') '.$dados['nome'].'</td><td>'.$dados['cpf_cnpj'].'</td><td> CPF inválido </td></tr>';
                    
                }
                
                if($dados['cpf_cnpj'] == $Profissional_CPF){

                    $c += 1;
                    $favorecidos .= '<tr><td align="left">'.$c.') '.$dados['nome'].'</td><td>'.$dados['cpf_cnpj'].'</td><td> CPF igual ao do títular </td></tr>';
                    
                }


            }
            

        }
		
		
        if($c > 0){
            
            $Retorno = '<h6 style="text-align:left;margin-left:15px;">Favorecidos com CPF inválidos</h6>

						<div class="linha"></div>  

						<p style="font-size:10px;text-align:left;margin-left:15px; margin-top:-8px; margin-bottom:10px;">
						O sistema identificou que existem CPF inválidos ou igual ao do títular.</p>

						<div style="padding-left:15px; margin-bottom:5px; width:690; height:300px; overflow-y: scroll;"">

						<table width="590 "cellspacing="5px" >
							<thead>
								<th>Nome</th>
								<th>CPF</th>								
								<th>Erro</th>
							</thead>
							<tbody>'.$favorecidos.'</tbody></table>
							
						</div>	';

            
            echo json_encode(array('status'=> 0, 'Retorno'=> $Retorno));  
			
        }else{ 
            
            //Se estiver tudo certo gera o arquivo.
            $NomeArquivo = self::CriarArquivo($Cliente_id, $AnoDeclaracao);

            echo json_encode(array('status'=> 1, 'Retorno'=> $NomeArquivo));



        }
		
	}


	/* 
     * Valida o CPF 
     */
	function validarCPF( $cpf = '') { 

		$cpf = str_pad(preg_replace('/[^0-9]/', '', $cpf), 11, '0', STR_PAD_LEFT);
		// Verifica se nenhuma das sequências abaixo foi digitada, caso seja, retorna falso
		if ( strlen($cpf) != 11 || $cpf == '00000000000' || $cpf == '11111111111' || $cpf == '22222222222' || $cpf == '33333333333' || $cpf == '44444444444' || $cpf == '55555555555' || $cpf == '66666666666' || $cpf == '77777777777' || $cpf == '88888888888' || $cpf == '99999999999') {
			return FALSE;
		} else { 
			// Calcula os números para verificar se o CPF é verdadeiro
			for ($t = 9; $t < 11; $t++) {
				for ($d = 0, $c = 0; $c < $t; $c++) {
					$d += $cpf{$c} * (($t + 1) - $c);
				}
				$d = ((10 * $d) % 11) % 10;
				if ($cpf{$c} != $d) {
					return FALSE;
				}
			}
			return TRUE;
		}
	}



	/* 
     * Imprimir lista de lançamentos Carne Leão 
     */
	function ImprimirMovimento()
	{
		$ClienteId = '708';
		$AnoCompetencia = '2017';

        /* ===== Conecta nos dados do cliente na W2B ===== */
        
        $DadosCliente = $this->db_w2b->fetch_assoc('SELECT nome, replace(replace(cpf_cnpj,"-",""),".","") as cpf_cnpj FROM clientes WHERE id ='.$ClienteId);

        $NomeDeclarante = $DadosCliente['nome'];	
        $CPF = $DadosCliente['cpf_cnpj'];


        /* ===== Conecta nos dados do cliente na W2B ===== */

        self::DbClienteConn($ClienteId);


        /* ===== Seleciona as contas que utilizam o carne leão ===== */

        $Contas = $this->dbCliente->fetch_all_array('SELECT id FROM contas WHERE carne_leao = 1');

        $n = 1;
        $ContasCarneLeao = '';
        foreach($Contas as $Contas){
            
            if($n > 1){ $OR = ' OR lancamentos.conta_id'; }else{ $OR = '';}

            $ContasCarneLeao .= $OR.$Contas['id'];
            
            $n = 1;
        }



        /* ===== Retorna o valor total de cada mes ===== */

        $c = 1;
        $conteudo = '';

        while($c <= 12){	
			
            $Lancamentos  = $this->dbCliente->fetch_all_array('SELECT lancamentos.*, date_format(lancamentos.dt_compensacao, "%d/%m/%Y") as data, format(lancamentos.valor, 2, "de_DE") as valorFormatado, favorecidos.cpf_cnpj as cpf_cnpj
													FROM lancamentos
													JOIN favorecidos ON lancamentos.favorecido_id = favorecidos.id
													WHERE  (lancamentos.dt_compensacao BETWEEN "'.$AnoCompetencia.'-'.$c.'-01" AND "'.date($AnoCompetencia.'-'.$c.'-t').'") AND ('.$ContasCarneLeao.' AND lancamentos.compensado = 1 AND favorecidos.inscricao = "cpf") ORDER BY lancamentos.dt_compensacao ASC ');
			
            $cont = 1;

            $Valor = 0;
            $VlDedutivel = 0;
            $VlNaoDedutivel = 0;

            $Mes = array(1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril', 5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro');
            
            $MesAno = $Mes[$c].' de '.$AnoCompetencia;

            $conteudo = '<tr bgcolor="#CCC" height="40">
										<td colspan="3" align="center"><font style="font-size:25px;">Livro Caixa</font></td>
									</tr>
								<tr><td  colspan="3"><b>'.$MesAno.'</b></td></tr>';


            foreach($Lancamentos as $Lancamentos){				
                
                
                $categoria = $this->dbCliente->fetch_assoc('SELECT plano_contas.nome as nome, plano_contas.dedutivel as dedutivel 
																	FROM plano_contas 
																	JOIN ctr_plc_lancamentos ON ctr_plc_lancamentos.plano_contas_id = plano_contas.id
																	WHERE ctr_plc_lancamentos.id = '.$Lancamentos['id']);
                

                if($Lancamentos['tipo'] == 'R' && strlen($Lancamentos['cpf_cnpj']) == 14){
                    

                    $conteudo .=  '<tr bgcolor="#CCC" height="5"><td  colspan="3" ></td></tr>
														<tr>
															<td><b>Lançamentos nº :  &nbsp;&nbsp;&nbsp;&nbsp;  </b> '.$cont.' </td>
															<td><b>Data : </b>  &nbsp;&nbsp;&nbsp;&nbsp;  '.$Lancamentos['data'].' </td>
															<td><b>Valor : </b>  &nbsp;&nbsp;&nbsp;&nbsp;  '.$Lancamentos['valorFormatado'].' </td>
														</tr>
														<tr><td  colspan="3"><b>Conta :  &nbsp;&nbsp;&nbsp;&nbsp;  </b> '.$categoria['nome'].'</td></tr>
														<tr><td  colspan="3"><b>CPF do Titular do Pagamento :  &nbsp;&nbsp;&nbsp;&nbsp;  </b> '.$Lancamentos['cpf_cnpj'].'</td></tr>
														<tr><td  colspan="3"><b>CPF do Beneficiário do Pagamento : </b>  &nbsp;&nbsp;&nbsp;&nbsp;  '.$Lancamentos['cpf_cnpj'].'</td></tr>
														<tr><td  colspan="3"><b>Histórico : </b>  &nbsp;&nbsp;&nbsp;&nbsp;  '.$Lancamentos['descricao'].'</td></tr>
														<tr><td  colspan="3"></td></tr>';

                    /* ===== Soma o valor das receitas de pessoa física ===== */
                    $Valor = $Lancamentos['valor'] + $Valor;


                }else if($Lancamentos['tipo'] == 'P'){
                    
                    $conteudo .=  '<tr bgcolor="#CCC" height="5"><td  colspan="3" ></td></tr>
												<tr>
													<td><b>Lançamentos nº : </b>  &nbsp;&nbsp;&nbsp;&nbsp;  '.$cont.' </td>
													<td><b>Data : </b>  &nbsp;&nbsp;&nbsp;&nbsp;  '.$Lancamentos['data'].' </td>
													<td><b>Valor : </b>  &nbsp;&nbsp;&nbsp;&nbsp;  '.$Lancamentos['valorFormatado'].' </td>
												</tr>
												<tr><td  colspan="3"><b>Conta : </b>  &nbsp;&nbsp;&nbsp;&nbsp;  '.$categoria['nome'].'</td></tr>
												<tr><td  colspan="3"><b>Histórico : </b>  &nbsp;&nbsp;&nbsp;&nbsp;  '.$Lancamentos['descricao'].'</td></tr>
												<tr><td  colspan="3"></td></tr>';


                    /* ===== Executa a soma das despesas dedutiveis e não dedutiveis ===== */
                    if($categoria['dedutivel'] == 1){

                        $VlDedutivel = $Lancamentos['valor'] + $VlDedutivel;
                        
                    }else{
                        
                        $VlNaoDedutivel = $Lancamentos['valor'] + $VlNaoDedutivel;
                        
                    }

                }

                $cont += 1;
                
            }



			/* ===== Total de cada mês ===== */

			$Excesso = $VlDedutivel - $Valor;
			if($Excesso <= 0){ $Excesso = '000'; }
			
			$TotalMes = '<tr bgcolor="#CCC" height="40">
							<td colspan="3" align="center"><font style="font-size:25px;">Livro Caixa</font></td>
						</tr>
						<tr bgcolor="#CCC"  height="40">
							<td colspan="3" align="center"><b>Totais em '.$MesAno.'</b></td>
						</tr>
						<tr>
							<td colspan="2"><b>1 - Rendimento recebido de pessoas físicas relativo a trabalho não assalariado</b></td>
							<td align="right">R$ '.number_format($Valor, 2, ',', '.').'</td>
						</tr>
						<tr>
							<td colspan="2"><b>1 - Rendimento recebido de pessoas físicas relativo a trabalho não assalariado</b></td>
							<td align="right">R$ '.number_format($Valor, 2, ',', '.').'</td>
						</tr>
						<tr>
							<td colspan="2"><b>2 - Rendimento não sujeito à retenção de imposto de renda na fonte recebido de pessoas<br> jurídicas e relativo a trabalho não assalariado</b></td>
							<td align="right">R$ 0,00</td>
						</tr>
						<tr>
							<td colspan="2"><b>3 - Rendimento sujeito à retenção de imposto de renda na fonte recebido de pessoas jurídicas<br> e relativo a trabalho não assalariado</b></td>
							<td align="right">R$ 0,00</td>
						</tr>
						<tr>
							<td colspan="2"><b>4 - Rendimento recebido do exterior relativo a trabalho não assalariado</b></td>
							<td align="right">R$ 0,00</td>
						</tr>
						<tr>
							<td colspan="2"><b>5 - Rendimentos recebido do exterior relativo a outros rendimentos</b></td>
							<td align="right">R$ 0,00</td>
						</tr>
						<tr>
							<td colspan="2"><b>6 - Total do limite mensal de livro caixa (1 + 2 + 3 + 4)</b></td>
							<td align="right">R$ '.number_format($Valor, 2, ',', '.').'</td>
						</tr>
						<tr>
							<td colspan="2"><b>7 - Despesas dedutíveis</b></td>
							<td align="right">R$ '.number_format($VlDedutivel, 2, ',', '.').'</td>
						</tr>
						<tr>
							<td colspan="2"><b>8 - Despesas não dedutíveis</b></td>
							<td align="right">R$ '.number_format($VlNaoDedutivel, 2, ',', '.').'</td>
						</tr>
						<tr>
							<td colspan="2"><b>9 - Excesso de dedução de livro caixa até o mês anterior</b></td>
							<td align="right">R$ 0,00</td>
						</tr>
						<tr>
							<td colspan="2"><b>10 - Despesas dedutíveis do mês</b></td>
							<td align="right">R$ '.number_format($VlDedutivel, 2, ',', '.').'</td>
						</tr>
						<tr>
							<td colspan="2"><b>11 - Total (9 + 10)</b></td>
							<td align="right">R$ '.number_format($VlDedutivel, 2, ',', '.').'</td>
						</tr>
						<tr>
							<td colspan="2"><b>12 - Excesso de dedução de livro caixa para o mês seguinte (11 - 6, se positivo)</b></td>
							<td align="right">R$ '.number_format($Excesso, 2, ',', '.').'</td>
						</tr>
						<tr>
							<td colspan="2"><b>13 - Valor de dedução de livro caixa (6 ou 11, o menor dos dois)</b></td>
							<td align="right">R$ '.number_format($VlDedutivel, 2, ',', '.').'</td>
						</tr>
						';
            /* ===== Total de cada mês ===== */


            
            $Impressao .= $conteudo.$TotalMes;

            $c += 1;
        }

        /* ===== Documento final para impressão ===== */
        $ImpressaoFinal =  '<div align="center"border="1" style="font-family:arial; font-size: 11px;">
								<table cellspacing="10" width="800">'
                                .utf8_decode($Impressao).
                            '</table></div>';
        
        //Gerar PDF
        self::GerarPDF($ImpressaoFinal, 'Cabeçalho', 'Rodapé');


	}


	function GerarPDF($Conteudo, $Cabecalho, $Rodape)
	{
        
		$mpdf=new mPDF('c'); 
        
	    $mpdf->SetDisplayMode('fullpage');

	    $stylesheet = file_get_contents('../../../css/css_relatorios.css');
        
	    $mpdf=new mPDF('pt_BR','A4','','',10,10,29,18,5,8); // A4-L - Pagina estilo Paisagem (Horizontal)
	    //$mpdf=new mPDF('pt_BR',$orientation,'','',10,10,29,18,5,8); //cria um novo container PDF no formato A4 com orientação customizada ex.:class mPDF ([ string $mode [, mixed $format [, float $default_font_size [, string $default_font [, float $margin_left , float $margin_right , float $margin_top , float $margin_bottom , float $margin_header , float $margin_footer [, string $orientation ]]]]]])
	    $mpdf->useSubstitutions=false;
	    $mpdf->simpleTables = true;
	    $mpdf->SetHTMLHeader($Cabecalho);
	    $mpdf->SetHTMLFooter($Rodape);
	    $mpdf->WriteHTML($stylesheet,1);	// The parameter 1 tells that this is css/style only and no body/html/text
	    $mpdf->WriteHTML($Conteudo);

        //Visualização na tela
        $mpdf->Output($nomeRelatorio.'.pdf','I');

        //Download
        //$nomeRelatorio = 'Relatório_Movimentação_Financeira';


	}

}
?>