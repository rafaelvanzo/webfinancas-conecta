<?php

class Favorecido{

	public $nome;
	public $inscricao;
	public $cpf_cnpj;
	public $tp_favorecido;
	public $logradouro;
	public $numero;
	public $bairro;
	public $cidade;
	public $uf;
	public $cep;
	public $complemento;
	public $email;
	public $telefone;
	public $celular;
	public $tp_conta;
	public $banco_id;
	public $agencia;
	public $conta;
	public $observacao;
	public $cliente_ctr_id;
	public $cliente_plc_id;
	public $fornecedor_ctr_id;
	public $fornecedor_plc_id;

/*
===========================================================================================
CONSTRUTOR
===========================================================================================
*/
	
	function __construct($dados=""){
		if($dados!=""){
			$vars = get_class_vars(get_class($this));
			foreach($vars as $key => $value){
				if(array_key_exists($key,$dados)){
					$this->$key = $dados[$key];
				}
			}
		}
	}

/*
===========================================================================================
PEGAR VALOR DAS VARIÁVEIS
===========================================================================================
*/

	function getValues(){
		$dados = array();
		$vars = get_class_vars(get_class($this));
		foreach($vars as $key => $value){
			$dados[$key] = $this->$key;
		}
		return $dados;
	}

/*
===========================================================================================
INCLUÍR
===========================================================================================
*/

	function favorecidosIncluir($db){
		$favorecido = self::getValues();
		$favorecidoId = $db->query_insert('favorecidos',$favorecido);
		$retorno = array("notificacao"=>"Favorecido cadastrado com sucesso.", "id" => $favorecidoId);
		return $retorno;
	}
	
/*
===========================================================================================
INCLUÍR VIA AUTO COMPLETAR
===========================================================================================
*/

	function favorecidosIncluirAc($db,$dados){
		$favorecido_id = $db->query_insert('favorecidos',array("nome"=>$dados['nome']));
		return $favorecido_id;
	}

/*
===========================================================================================
EDITAR
===========================================================================================
*/

	function favorecidosEditar($db,$dados){
		$favorecido = self::getValues();
		$db->query_update('favorecidos',$favorecido,"id = ".$dados['favorecido_id']);
		$retorno = array("situacao" => 1, "notificacao" => "Favorecido atualizado com sucesso.");
		return $retorno;
	}	

/*
===========================================================================================
EXCLUIR
===========================================================================================
*/	

	function favorecidosExcluir($db,$favorecido_id){
		$lancamentos = $db->fetch_assoc("select count(*) qtd_lancamentos from lancamentos where favorecido_id = ".$favorecido_id." limit 0,1");
		if($lancamentos['qtd_lancamentos'] == 0){
			$db->query("delete from favorecidos where id = ".$favorecido_id);
			$retorno = array("situacao" => 1,"notificacao"=>"Favorecido excluído com sucesso.");
		}else{
			$retorno = array("situacao" => 2,"notificacao"=>"A exclusão não é possível. Existem lançamentos associados ao favorecido.");
		}
		return $retorno;
	}


/*
===========================================================================================
VISUALIZAR
===========================================================================================
*/

	function favorecidosVisualizar($db,$favorecido_id){
		$favorecido_visualizar = $db->fetch_array($db->query("select * from favorecidos where id = ".$favorecido_id));
		if($favorecido_visualizar['banco_id']!=0){
			$banco = $db->fetch_array($db->query("select nome from bancos where id = ".$favorecido_visualizar['banco_id']));
			$favorecido_visualizar['bancoNome'] = $banco['nome'];
		}
		if($favorecido_visualizar['cliente_ctr_id']!=0){
			$cliente_ctr = $db->fetch_assoc('select nome from centro_resp where id = '.$favorecido_visualizar['cliente_ctr_id']);
			$favorecido_visualizar['cliente_ctr'] = $cliente_ctr['nome'];
		}
		if($favorecido_visualizar['cliente_plc_id']!=0){
			$cliente_plc = $db->fetch_assoc('select nome from plano_contas where id = '.$favorecido_visualizar['cliente_plc_id']);
			$favorecido_visualizar['cliente_plc'] = $cliente_plc['nome'];
		}
		if($favorecido_visualizar['fornecedor_ctr_id']!=0){
			$fornecedor_ctr = $db->fetch_assoc('select nome from centro_resp where id = '.$favorecido_visualizar['fornecedor_ctr_id']);
			$favorecido_visualizar['fornecedor_ctr'] = $fornecedor_ctr['nome'];
		}
		if($favorecido_visualizar['fornecedor_plc_id']!=0){
			$fornecedor_plc = $db->fetch_assoc('select nome from plano_contas where id = '.$favorecido_visualizar['fornecedor_plc_id']);
			$favorecido_visualizar['fornecedor_plc'] = $fornecedor_plc['nome'];
		}
		return $favorecido_visualizar;
	}

/*
===========================================================================================
LISTAR
===========================================================================================
*/

	//Lista dos favorecidos
	function favorecidosListar($db){
		$favorecidos_listar ='
			<table cellpadding="0" cellspacing="0" border="0" class="display tblFavorecidos">
			<thead>
			<tr style="border-bottom: 1px solid #e7e7e7;">
			<th> 
					<table width="100%"><tr>
						<td>Nome</td>
						<td width="60">Opções</td>
					</td></tr></table>
			</th> 
			</tr>
			</thead>
			<tbody>
		';

		$array_favorecidos = $db->fetch_all_array("select id, nome, telefone, email from favorecidos order by nome");
		foreach($array_favorecidos as $favorecido){

			$favorecidos_listar .= '
				<tr class="gradeA" id="row'.$favorecido['id'].'">
								<td class="updates newUpdate">												
										<div class="uDate tbWF" align="center" style="border:1px solid #CCC; background:#FCFCFC; padding-right:8px; margin-right:-8px; padding-bottom: 5px; -webkit-border-radius : 5px; -moz-border-radius: 5px;"> <img src="images/icons/middlenav/user.png" alt="" class="floatL" ></div>
											<span class="lDespesa tbWF" >
												<a href="javascript://void(0);" style="cursor: default;" original-title="Favorecido" class="tipS" ><strong >'.$favorecido['nome'].'</strong></a>
													<span style="padding-top:5px;">	
														<div class="tab_tel_cel"><img src="images/icons/dark/phone.png" style="margin-bottom:-3px;"> &nbsp;'.$favorecido['telefone'].' </div>	
														<div class="tab_tel_cel"><img src="images/icons/dark/phone3.png" style="margin-bottom:-3px;"> &nbsp;'.$favorecido['celular'].' </div>
														<div class="tab_tel_cel"><img src="images/icons/dark/mail.png" style="margin-bottom:-3px;"> &nbsp;'.$favorecido['email'].' </div>
													</span>
											</span>											
															
										<div class="tbWFoption">
		                	<a href="'.$favorecido['id'].'" original-title="Excluír" class="smallButton redB btTBwf tipS favorecidosExcluir" ><img src="images/icons/light/close.png" width="10"></a>										
											<a href="javascript://void(0);" original-title="Editar" class="smallButton greyishB btTBwf tipS" onClick="favorecidosVisualizar('.$favorecido['id'].')"><img src="images/icons/light/pencil.png" width="10"></a>  
											</div>

				
							</td> 
				</tr>			
			';
		}
		
 	  $favorecidos_listar .= '</tbody></table>';
		return $favorecidos_listar;
	}

/*
===========================================================================================
IMPORTAR
===========================================================================================
*/

	function favorecidosImportar($db,$array_dados){

		$cliente_id = $array_dados['cliente_id'];
		$usuario_id = $array_dados['usuario_id'];

		$caminho_arquivos = "../temp/".$cliente_id.'_'.$usuario_id.'_*.xls';
		$array_arquivos = glob($caminho_arquivos);
		foreach($array_arquivos as $arquivo){
			
			$xls = new Spreadsheet_Excel_Reader($arquivo);
			$xls->read($arquivo);
			//$linhas = $xls->sheets[0]['numRows'];
			$colunas = $xls->sheets[0]['numCols'];
			$i=1;
			$linha1 = '';
			$linha2 = '';
			$linha_select = '';
			while($i <= $colunas){
				$linha_select .= '
					<td style="min-width:150px">
						<select name="select'.$i.'" class="selectCampo" id="select'.$i.'" onChange="selectCampo(\'select'.$i.'\');" onClick="optionIni(\'select'.$i.'\');">
							<option value="0"> Selecione </option>
							<option value="1"> Não importar este campo </option>
							<option value="nome"> Nome </option>
							<option value="cpf"> CPF </option>
							<option value="cnpj"> CNPJ </option>
							<option value="email"> Email </option>
							<option value="telefone"> Telefone </option>
							<option value="celular"> Celular </option>
							<option value="logradouro"> Logradouro </option>
							<option value="numero"> Nº </option>
							<option value="complemento"> Complemento </option>
							<option value="bairro"> Bairro </option>
							<option value="cidade"> Cidade </option>
							<option value="uf"> UF </option>
							<option value="cep"> CEP </option>
						</select>
					</td>
				';
				$linha1 .= '<td>'.utf8_encode($xls->sheets[0]['cells'][1][$i]).'</td>';
				$linha2 .= '<td>'.utf8_encode($xls->sheets[0]['cells'][2][$i]).'</td>';
				$i++;
			}
			$tabela = '
			<table cellpadding="0" cellspacing="0" width="100%" class="sTable" id="tblFavImport">
				<tbody>
					<tr>
						'.$linha_select.'
					</tr>
					<tr>
						'.$linha1.'
					</tr>
					<tr>
						'.$linha2.'
					</tr>
				</tbody>
			</table>
			';
			//unlink($arquivo);
		}
		return $tabela;

	}

/*
===========================================================================================
FINALIZAR IMPORTAÇÃO
===========================================================================================
*/

function favorecidosImportarFim($db,$array_dados){
	$cliente_id = $array_dados['cliente_id'];
	$usuario_id = $array_dados['usuario_id'];
	$removeHeader = $array_dados['removeHeaderCkb'];
	$caminho_arquivos = "../temp/".$cliente_id.'_'.$usuario_id.'_*.xls';
	$array_arquivos = glob($caminho_arquivos);
	$arquivo = $array_arquivos[0];
	$xls = new Spreadsheet_Excel_Reader($arquivo);
	$xls->read($arquivo);
	$linhas = $xls->sheets[0]['numRows'];
	$colunas = $xls->sheets[0]['numCols'];
	if($removeHeader){$i=2;}else{$i=1;}
	while($i<=$linhas){
		$favorecido = array();
		$j = 1;
		while($j<=$colunas){
			if($array_dados['select'.$j]!='0' && $array_dados['select'.$j]!='1'){
				$campo = $array_dados['select'.$j];
				$favorecido[$campo] = $xls->sheets[0]['cells'][$i][$j];
			}
			$j++;
		}
		if(array_key_exists('cpf',$favorecido)){
			$favorecido['inscricao'] = 'cpf';
			$favorecido['cpf_cnpj'] = $favorecido['cpf'];
			unset($favorecido['cpf']);
		}
		if(array_key_exists('cnpj',$favorecido)){
			$favorecido['inscricao'] = 'cnpj';
			$favorecido['cpf_cnpj'] = $favorecido['cnpj'];
			unset($favorecido['cnpj']);
		}
		$favorecido['tp_favorecido'] = 3;
		$db->query_insert('favorecidos',$favorecido);
		$i++;
	}
	unlink($arquivo);
}

/*
===========================================================================================
EXCLUÍR ARQUIVOS IMPORTADOS
===========================================================================================
*/

	function arquivosExcluir($array_dados){
		$cliente_id = $array_dados['cliente_id'];
		$usuario_id = $array_dados['usuario_id'];
		$caminho_arquivos = "../temp/".$cliente_id.'_'.$usuario_id.'_*.xls';
		$array_arquivos = glob($caminho_arquivos);
		foreach($array_arquivos as $arquivo){
			unlink($arquivo);
		}
	}

/*
===========================================================================================
EXPORTAR FAVORECIDOS
===========================================================================================
*/

	function favExport($db){

		$arr_favorecidos = $db->fetch_all_array("select * from favorecidos order by nome");
		
		$sheet = array();
		$sheet[] = array('Nome','CPF/CNPJ','Email','Telefone','Celular','Logradouro','Nº','Complemento','Bairro','Cidade','UF','CEP','Observação');
		
		foreach($arr_favorecidos as $favorecido){
			$sheet[] = array(
				$favorecido['nome'],
				$favorecido['cpf_cnpj'],
				$favorecido['email'],
				$favorecido['telefone'],
				$favorecido['celular'],
				$favorecido['logradouro'],
				$favorecido['numero'],
				$favorecido['complemento'],
				$favorecido['bairro'],
				$favorecido['cidade'],
				$favorecido['uf'],
				$favorecido['cep'],
				$favorecido['observacao']
			);
		}
		
		$workbook = new Spreadsheet_Excel_Writer();
		
		$format_und =& $workbook->addFormat();
		$format_und->setBottom(2);//thick
		$format_und->setBold();
		$format_und->setColor('black');
		$format_und->setFontFamily('Arial');
		$format_und->setSize(8);
		
		$format_reg =& $workbook->addFormat();
		$format_reg->setColor('black');
		$format_reg->setFontFamily('Arial');
		$format_reg->setSize(8);
		
		$rowcount = count($sheet);
		$colcount = count($sheet[0]);

		$worksheet =& $workbook->addWorksheet('Favorecidos');

		$worksheet->setColumn(0,0, 6.14);//setColumn(startcol,endcol,float)
		$worksheet->setColumn(1,3,15.00);
		$worksheet->setColumn(4,4, 8.00);
		
		for( $j=0; $j<$rowcount; $j++ )
		{
				for($i=0; $i<$colcount;$i++)
				{
						$fmt  =& $format_reg;
						if ($j==0)
								$fmt =& $format_und;

						if (isset($sheet[$j][$i]))
						{
								$data=$sheet[$j][$i];
								$worksheet->write($j, $i, utf8_decode($data), $fmt);
						}
				}
		}

		$workbook->send('Favorecidos.xls');
		$workbook->close();
	}

/*
===========================================================================================
DATA TABLE AJAX
===========================================================================================
*/

    function DataTableAjax($db,$params){

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

            $queryFavorecido = "select id, nome, telefone, email from favorecidos";

        }else{
            
            $queryFavorecido = "select id, nome, telefone, email from favorecidos where nome like '%".$sSearch."%'";
        }
		
        $iTotalDisplayRecords = mysql_num_rows(mysql_query($queryFavorecido, $db->link_id));

        $queryFavorecido = mysql_query($queryFavorecido.' order by nome limit '.$iDisplayStart.",".$iDisplayLength, $db->link_id);

        while($favorecido = mysql_fetch_assoc($queryFavorecido)){

            $dadosFavorecido = '
                <div class="uDate tbWF" align="center" style="border:1px solid #CCC; background:#FCFCFC; padding-right:8px; margin-right:-8px; padding-bottom: 5px; -webkit-border-radius : 5px; -moz-border-radius: 5px;"> <img src="images/icons/middlenav/user.png" alt="" class="floatL" ></div>
                <span class="lDespesa tbWF" >
	                <a href="javascript://void(0);" style="cursor: default;" original-title="Favorecido" class="tipS" ><strong >'.$favorecido['nome'].'</strong></a>
		                <span style="padding-top:5px;">	
			                <div class="tab_tel_cel"><img src="images/icons/dark/phone.png" style="margin-bottom:-3px;"> &nbsp;'.$favorecido['telefone'].' </div>	
			                <div class="tab_tel_cel"><img src="images/icons/dark/phone3.png" style="margin-bottom:-3px;"> &nbsp;'.$favorecido['celular'].' </div>
			                <div class="tab_tel_cel"><img src="images/icons/dark/mail.png" style="margin-bottom:-3px;"> &nbsp;'.$favorecido['email'].' </div>
		                </span>
                </span>											
															
                <div class="tbWFoption">
                    <a href="'.$favorecido['id'].'" original-title="Excluír" class="smallButton redB btTBwf tipS favorecidosExcluir" id="link-exc-'.$favorecido['id'].'"><img src="images/icons/light/close.png" width="10"></a>										
                    <a href="javascript://void(0);" original-title="Editar" class="smallButton greyishB btTBwf tipS" onClick="favorecidosVisualizar('.$favorecido['id'].')"><img src="images/icons/light/pencil.png" width="10"></a>  
                </div>
            ';

            array_push($aaData,array('favorecido'=>$dadosFavorecido));
        }

        $retorno = array('sEcho'=>$sEcho,'iTotalRecords'=>$iTotalDisplayRecords,'iTotalDisplayRecords'=>$iTotalDisplayRecords,'aaData'=>$aaData);
        
        return json_encode($retorno);
        
    }

}


?>