<?php

class Conta{

	public $descricao;
	public $banco_id;
	public $agencia;
	public $agencia_dv;
    public $numero;
    public $numero_dv;
	public $vl_saldo_inicial;
	public $vl_saldo;
	public $limite_credito;
	public $vl_credito;
	public $contato;
	public $contato_email;
	public $contato_tel;
	public $observacao;
	public $carteira;
	public $convenio;
	public $variacao;
    public $nomeTitular;
	public $inscricao;
	public $cpf_cnpj;
	public $modalidade;
	public $custo_emissao;
    public $custo_compensacao;
    public $multa;
    public $juros;
	public $msg1;
	public $msg2;
	public $msg3;
	public $inst1;
	public $inst2;
	public $inst3;
    public $carne_leao;

/*
===========================================================================================
CONSTRUTOR
===========================================================================================
*/

	function __construct($dados=""){
		if($dados!=""){
			$dados['limite_credito'] = self::valorToDouble($dados['limite_credito']);
			$credito_usado = self::valorToDouble($dados['credito_usado']);
			$dados['vl_credito'] = $dados['limite_credito'] - $credito_usado;
			$dados['vl_saldo_inicial'] = self::valorToDouble($dados['vl_saldo_inicial']);
			$dados['custo_emissao'] = self::valorToDouble($dados['custo_emissao']);
            $dados['custo_compensacao'] = self::valorToDouble($dados['custo_compensacao']);
            $dados['multa'] = self::valorToDouble($dados['multa']);
            $dados['juros'] = self::valorToDouble($dados['juros']);
			$vars = get_class_vars(get_class($this));
			foreach($vars as $key => $value){
				if(array_key_exists($key,$dados)){
					$this->$key = $dados[$key];
				}
			}
			//BANESTES TEM O MESMO NÚMERO PARA CONTA E CONVÊNIO (verifica se outros bancos utilizam essa metodologia e fazer a atribuição quando o convenio estiver vazio)
			//$cod_banco = $db->fetch_assoc('select codigo from bancos where banco_id = '.$this->banco_id);
			//if($cod_banco['codigo']=='021')
				//$this->convenio = $this->numero;
			if($this->convenio=='')
				$this->convenio = $this->numero;
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

	function contasIncluir($db){
		$conta = self::getValues();
		$conta['vl_saldo'] = $conta['vl_saldo_inicial'];
		$conta['boleto_ano'] = date('y');
		$conta['sequencial'] = 1;
		$db->query_insert('contas',$conta);
        //Habilita ou Inabilita o Carnê Leão
        $carne_leao = $db->fetch_assoc('SELECT carne_leao FROM contas WHERE carne_leao = 1');        
        if($carne_leao == true){ $_SESSION['carne_leao'] = 1; }else{ $_SESSION['carne_leao'] = 0; }
		$retorno = array("notificacao"=>"Conta cadastrada com sucesso.");
		return $retorno;
	}

/*
===========================================================================================
INCLUÍR VIA AUTOCOMPLETAR
===========================================================================================
*/

	function contasIncluirAc($db,$dados){
		$conta_id = $db->query_insert('contas',array("descricao"=>$dados['descricao']));
		return $conta_id;
	}

/*
===========================================================================================
EDITAR
===========================================================================================
*/

	function contasEditar($db,$array_dados){       
        
		$limite_credito = self::valorToDouble($array_dados['limite_credito']);
		$limite_credito_ini = self::valorToDouble($array_dados['limite_credito_ini']);
		$credito_usado = self::valorToDouble($array_dados['credito_usado']);
		$vl_credito = $limite_credito - $credito_usado;
		
		$saldo_inicial = self::valorToDouble($array_dados['vl_saldo_inicial']);
		$saldo_inicial_ini = self::valorToDouble($array_dados['vl_saldo_inicial_ini']);

		$saldo_atualizado = true;
		$credito_atualizado = true;

		//========== Atualiza o limite de credito da conta ========
		//$query_conta = "select (limite_credito - vl_credito) credito_usado from contas where id = ".$array_dados['conta_id']." for update";
		//$registro = $db->fetch_assoc($query_conta);
		//$credito_usado = $registro['credito_usado'];
		//só realiza as operações de atualização se o limite de crédito atual e o novo limite de crédito forem diferentes e o novo limite for maior ou igual ao credito já usado
		if( ($limite_credito_ini != $limite_credito) ){
			if($limite_credito >= $credito_usado){
				mysql_query("update contas set vl_credito = (vl_credito - $limite_credito_ini + $limite_credito), limite_credito = $limite_credito where id = ".$array_dados['conta_id']);
			}else{
				$credito_atualizado = false;
			}
		}

		//========== Atualiza o saldo inicial da conta ========
		//só realiza as operações de atualização se o saldo inicial atual e o novo saldo inicial forem diferentes
		if($saldo_inicial_ini != $saldo_inicial){ 
			$query_conta = "select (vl_saldo + vl_credito) saldo_total, vl_saldo, vl_credito, limite_credito from contas where id = ".$array_dados['conta_id']." for update";
			$registro = $db->fetch_assoc($query_conta);
			$saldo_total = $registro['saldo_total'];
			$novo_saldo = $saldo_total - $saldo_inicial_ini + $saldo_inicial;
			if($novo_saldo >= 0){
				$limite_credito = $registro['limite_credito'];
				if( ($saldo_inicial_ini <= $registro['vl_saldo']) || ($limite_credito == 0) ){
					$db->query("update contas set vl_saldo = vl_saldo - $saldo_inicial_ini + $saldo_inicial, vl_saldo_inicial = $saldo_inicial where id = ".$array_dados['conta_id']);
				}else{
					$saldo_inicial_ini -= $registro['vl_saldo'];
					$db->query("update contas set vl_saldo = 0, vl_credito = vl_credito - $saldo_inicial_ini where id = ".$array_dados['conta_id']);
					$credito_usado = $db->fetch_assoc("select (limite_credito - vl_credito) credito_usado from contas where id = ".$array_dados['conta_id']);
					if($saldo_inicial <= $credito_usado['credito_usado']){
						$db->query("update contas set vl_credito = vl_credito + $saldo_inicial, vl_saldo_inicial = $saldo_inicial where id = ".$array_dados['conta_id']);
					}else{
						$saldo_inicial -= $credito_usado['credito_usado'];
						$db->query("update contas set vl_credito = $limite_credito, vl_saldo = vl_saldo + $saldo_inicial, vl_saldo_inicial = $saldo_inicial where id = ".$array_dados['conta_id']);
					}
				}
			}else{
				$saldo_atualizado = false;
			}
		}

		$conta = self::getValues();
		
		$conta['vl_credito'] = $vl_credito;
		
		unset($conta['vl_saldo']);
		unset($conta['vl_saldo_inicial']);
		//unset($conta['vl_credito']);
		unset($conta['limite_credito']);
		
		$db->query_update('contas',$conta,'id = '.$array_dados['conta_id']);
        
        //Habilita ou Inabilita o Carnê Leão
        $carne_leao = $db->fetch_assoc('SELECT carne_leao FROM contas WHERE carne_leao = 1');
        if($carne_leao == true){ $_SESSION['carne_leao'] = 1; }else{ $_SESSION['carne_leao'] = 0; }

		if( $credito_atualizado && $saldo_atualizado ){
			$retorno = array("situacao" => 1,"notificacao"=>"Conta atualizada com sucesso.");
			return $retorno;
		}elseif(!$credito_atualizado && !$saldo_atualizado){
			$retorno = array("situacao" => 2,"notificacao"=>"Não foi possível atualizar o saldo inicial e o limite de crédito da conta.");
			return $retorno;
		}else if(!$credito_atualizado){
			$retorno = array("situacao" => 3,"notificacao"=>"Não foi possível atualizar o limite de crédito da conta.");
			return $retorno;
		}else{
			$retorno = array("situacao" => 4,"notificacao"=>"Não foi possível atualizar o saldo inicial da conta.");
			return $retorno;
		}

	}	

/*
===========================================================================================
EXCLUIR
===========================================================================================
*/	

	function contasExcluir($db,$conta_id){
		$lancamentos = $db->fetch_assoc("select count(*) qtd_lancamentos from lancamentos where conta_id = ".$conta_id." limit 0,1");
		if($lancamentos['qtd_lancamentos'] == 0){
			$db->query("delete from contas where id = ".$conta_id);
			$retorno = array("situacao" => 1,"notificacao"=>"Conta excluída com sucesso.");
		}else{
			$retorno = array("situacao" => 2,"notificacao"=>"A exclusão não é possível. Existem lançamentos associados á conta.");
		}
		return $retorno;
	}


/*
===========================================================================================
EXIBIR
===========================================================================================
*/

	function contasVisualizar($db,$conta_id){
		$conta_visualizar = $db->fetch_array($db->query("select * from contas where id = ".$conta_id));
		$conta_visualizar['vl_saldo_inicial'] = self::valorFormat($conta_visualizar['vl_saldo_inicial']);
		$conta_visualizar['credito_usado'] = self::valorFormat($conta_visualizar['limite_credito'] - $conta_visualizar['vl_credito']);
		$conta_visualizar['limite_credito'] = self::valorFormat($conta_visualizar['limite_credito']);
		$conta_visualizar['custo_emissao'] = self::valorFormat($conta_visualizar['custo_emissao']);
        $conta_visualizar['custo_compensacao'] = self::valorFormat($conta_visualizar['custo_compensacao']);
        $conta_visualizar['multa'] = self::valorFormat($conta_visualizar['multa']);
        $conta_visualizar['juros'] = self::valorFormat($conta_visualizar['juros']);
		$span_carteira = '';
		if($conta_visualizar['banco_id']!=0){
			$banco = $db->fetch_array($db->query("select codigo, nome from bancos where id = ".$conta_visualizar['banco_id']));
			$conta_visualizar['bancoNome'] = $banco['nome'];
			$span_carteira = self::carteira($banco['codigo']);
			if($banco['codigo']=='021')
				$conta_visualizar['convenio']='';
		}
		return array('conta'=>$conta_visualizar,'span_carteira'=>$span_carteira,'banco_codigo'=>$banco['codigo']); //'span_carteira'=>$span_carteira,
	}

/*
===========================================================================================
LISTAR
===========================================================================================
*/

	//Lista das contas bancárias
	function contasListar($db){
		$array_contas = $db->fetch_all_array("select id, banco_id, numero, vl_saldo, descricao from contas order by descricao");
		
		$contas_listar ='
			<table cellpadding="0" cellspacing="0" border="0" class="display tblContas">
				<thead>
				<tr style="border-bottom: 1px solid #e7e7e7;">
                <th> 
                    <table width="100%"><tr>
                      <td>Descrição</td>
                      <td width="60">Opções</td>
                    </td></tr></table>
                </th> 
              </tr>
				</thead>
				<tbody>
		';
		
		foreach($array_contas as $conta){
		$id_banco = $conta['banco_id'];
		$banco = $db->fetch_assoc("select * from bancos where id = ".$id_banco);	
		if(!empty($banco[logo])){ $logo_banco = $banco[logo];  
		}else{ $logo_banco = 'bank.png'; }
		if(empty($banco[nome])){ $instituicaoFinanceira = 'Livro de Caixa'; }else{ $instituicaoFinanceira = '(<b>'.$banco[codigo].'</b>) '.$banco[nome]; }	
	
			$contas_listar .= '
				<tr class="gradeA" id="row'.$conta['id'].'">
								<td class="updates newUpdate">
												
										<div class="uDate tbWF" align="center" style="padding-right:8px; padding-bottom: 5px; margin-right:-8px; "> <img src="images/bancos/'.$logo_banco.'" alt="" class="floatL" style="-webkit-border-radius : 2px; -moz-border-radius: 2px;"></div>
											<span class="lDespesa tbWF" >
												<a href="javascript://void(0);" style="cursor: default;" original-title="Descrição" class="tipS" ><strong >'.$conta[descricao].'</strong></a>
													<span original-title="Instituição Financeira" class="tipN">'.$instituicaoFinanceira.'</span>
											</span>											
															
										<div class="tbWFoption">										
												<a href="'.$conta[id].'" original-title="Excluir" class="smallButton btTBwf redB tipS contasExcluir"><img src="images/icons/light/close.png" width="10"></a>		
												<a href="javascript://void(0);" original-title="Editar" class="smallButton btTBwf greyishB tipS"  onClick="contasVisualizar('.$conta[id].')"><img src="images/icons/light/pencil.png" width="10"></a>											
											</div>
																																													
										<div class="tbWFvalue tipS" original-title="Saldo atual">R$ '.number_format($conta[vl_saldo],2,',','.').' </div>
				
							</td> 
			';
		}
		
 	  $contas_listar .= '</tbody></table>';
		return $contas_listar;
	}

/*
===========================================================================================
SALDO
===========================================================================================
*/

	function contasSaldo($db){

		$array_contas = $db->fetch_all_array("
			select c.id, c.numero, c.vl_saldo, c.descricao, c.vl_credito, (c.vl_saldo + c.vl_credito) total, b.id banco_id, b.nome nm_banco
			from contas c, bancos b
			where c.banco_id = b.id
				order by b.nome, c.descricao
		");
		
		$contas_saldo = "";
		foreach($array_contas as $conta){
			$contas_saldo .= '
					<div class="userRow">
							<img src="images/bank.png" alt="" class="floatL"/>
							<ul class="leftList">
									<li><a href="#" title=""><strong>'.$conta['nm_banco'].'</strong></a></li>
									<li>'.$conta['descricao'].'</li>
							</ul>

					</div>
														 
					<div class="orderRow">
							<ul class="leftList">
									<li>Saldo:</li>
									<li>C. Especial:</li>
									<li><b>Total:</b></li>
							</ul>
							<ul class="rightList">
									<li>R$ '.number_format($conta['vl_saldo'],2,',','.').'</li>
									<li>R$ '.number_format($conta['vl_credito'],2,',','.').'</li>
									<li><strong class="grey">R$ '.number_format($conta['total'],2,',','.').'</strong></li>
							</ul>
					</div>
					
			 <div class="cLine"></div>
			';
		}		
	
		return $contas_saldo;
	
	}

/*
===========================================================================================
FORMATAÇÃO
===========================================================================================
*/
	
	//converte valores para inserir no banco de dados
	function valorToDouble($valor){
		$vl_double = str_replace('.', '', $valor);
		$vl_double = str_replace(',', '.', $vl_double);
		return $vl_double * 1;
	}
	
	//formata os valores retornados do banco de dados
	function valorFormat($valor){
		$vl_format = number_format($valor,2,',','.');
		return $vl_format;
	}

/*
===========================================================================================
CARTEIRA
===========================================================================================
*/

	function carteira($cod_banco){
	
		switch($cod_banco){
	
			case '001': //Banco do Brasil
				$span_carteira = '
					<label>Carteira</label>
					<select name="carteira">
						<option value=""></option>
						<option value="11">11</option>
						<option value="16">16</option>
						<option value="17">17</option>
						<option value="18">18</option>
					</select>
				';
			break;
				
			//Caixa Econômica 
			//80,81,82 são carteiras sicob sem registro
			//CR - Cobrança Rápida
			//CS - Cobrança Simples
			//SAD - Cobrança SAD
			//SI - Cobrança Simplificada
			//SIG11 - SIG Com Registro - Emissão pelo Banco - 11 = 1 com registro; 1 emitido pelo banco
			//SIG14 - SIG Com Registro - Emissão pelo Cedente - 11 = 1 com registro; 4 emitido pelo cedente
			//SIG21 - SIG Sem Registro - Emissão pelo Banco - 21 = 2 sem registro; 1 emitido pelo banco
			//SIG24 - SIG Sem Registro - Emissão pelo Cedente - 21 = 2 sem registro; 4 emitido pelo cedente
			//SR - Cobrança Sem Registro - assumir que esta carteira é a SIGCB sem registro emitida pelo cedente
			//SR-14 - Cobrança Sem Registro Nosso Número 14 Dígitos
			//SR5 - SINCO - Sem Registro
				
			case '104': 
				$span_carteira = '
					<label>Carteira</label>
					<select name="carteira">
						<option value=""></option>
                        <option value="14">14</option>
                        <option value="24">24</option>
						<option value="80">80</option>
						<option value="81">81</option>
						<option value="82">82</option>
					</select>
				';
			break;
			
			case '756': //Bancoob / Sicoob
				$span_carteira = '
					<label>Carteira</label>
					<select name="carteira">
						<option value=""></option>
						<option value="1" selected>1</option>
					</select>
				';
			break;
	
			case '033': //Santander
				$span_carteira = '
					<label>Carteira</label>
					<select name="carteira">
						<option value=""></option>
						<option value="101">101</option>
						<option value="102">102</option>
					</select>
				';
			break;
	
			case '021': //Banestes
				$span_carteira = '
					<label>Carteira</label>
					<select name="carteira">
						<option value="11" selected>11</option>
						<option value="13">13</option>
					</select>
				';
			break;

            case '237': //Bradesco
				$span_carteira = '
					<label>Carteira</label>
					<select name="carteira">
						<option value="09" selected>09</option>
					</select>
				';
                break;
	
		}
		
		return $span_carteira;
		
	}

    
/*
===========================================================================================
VISUALIZAR BOLETOS (JANELA)
===========================================================================================
*/

    function visualizarBoletos($db,$dados){
        
        if($dados["or"] == 1){ 
            $outrasRemessas1 = "WHERE lancamentos.compensado = 0";
            $outrasRemessas2 = "";
        }else{ 
            $outrasRemessas1 = "WHERE remessa_id = 0 and lancamentos.compensado = 0";
            $outrasRemessas2 = "WHERE remessa_id = 0";
        }

        $banco = $db->fetch_assoc("SELECT agencia, numero FROM contas WHERE id =".$dados['id']);
        
        
        // ===================== Listar boletos =====================
        //$array_boletos = $db->fetch_all_array("SELECT id, lancamento_id FROM boletos ".$outrasRemessas);
        //$verificar = $db->numRows("SELECT id, lancamento_id FROM boletos ".$outrasRemessas);
        
        
        $array_boletos = $db->fetch_all_array("SELECT boletos.id, boletos.lancamento_id FROM boletos INNER JOIN lancamentos ON boletos.lancamento_id = lancamentos.id ".$outrasRemessas1." ORDER BY lancamentos.dt_vencimento");
        $verificar = $db->numRows("SELECT id, lancamento_id FROM boletos ".$outrasRemessas2);
        
          
          $num = 0;
          $dt_controle = "";
          foreach($array_boletos as $boletos){
                            

                $lanc_boletos = $db->fetch_assoc("SELECT descricao, conta_id, DATE_FORMAT(dt_emissao,'%d/%m/%Y') AS dt_emissao, DATE_FORMAT(dt_vencimento,'%d/%m/%Y') AS dt_vencimento, favorecido_id, valor FROM lancamentos WHERE id =".$boletos['lancamento_id']." AND conta_id =".$dados['id']);
               
                if($lanc_boletos['conta_id'] == $dados['id']){
                    //Quantidade total de boletos
                    $qtdBoletos ++;
                    
                  $count+=1; //Conta a quantidade de boletos para colocar o bg e número na lista
                  $num+=1;//Conta a quantidade de boletos para colocar o bg e número na lista
                  
                  $fav_boletos = $db->fetch_assoc("SELECT nome, cpf_cnpj FROM favorecidos WHERE id =".$lanc_boletos['favorecido_id']);
                
                  
                
                // Ajusta a data
                $mes = explode('/', $lanc_boletos['dt_vencimento']);
                $mesAno = $mes['1'].'/'.$mes['2'];
                $classChbox = $mes['1'].$mes['2'];
                $aspas = '"';
                
                if(empty($dt_controle)){ $dt_controle = $mesAno; 
                    $listaBoletos .= "<tr class='gradeA' height='40' style='background:#f2f2f2'><td colspan='4' align='center'> <b>Mês: ".$mesAno."</b> </td>
                                                           <td align='center'><input type='checkbox' class='chall".$classChbox." check1' title='Marcar todos do mês ".$mesAno."' onClick='chbox(".$aspas.$classChbox.$aspas.");'/> </td></tr>";
                    $count = 0;
                }                    
                
                elseif($mesAno != $dt_controle){
                    $listaBoletos .= "<tr class='gradeA' height='40' style='background:#f2f2f2'><td colspan='4' align='center'> <b>Mês: ".$mesAno."</b> </td>
                                                                   <td align='center'><input type='checkbox' class='chall".$classChbox." check1' title='Marcar todos do mês ".$mesAno."' onClick='chbox(".$aspas.$classChbox.$aspas.");'/> </td></tr>";
                    $count = 0;
                }
                
                if($count % 2){ $bgColor = "background-color:#f4f4f4;"; }else{ $bgColor = "background-color:FFF"; }
                
                //Data para controle
                $dt_controle = $mesAno;
                
                $listaBoletos .= "<tr class='gradeA' style='border-bottom: 1px solid #DDD; ".$bgColor." '>                                           
                                           <td align='center'><b>".$num."</b></td>
                                           <td align='left'><b>".$fav_boletos['nome']."<br> CPF/CPNJ: </b>".$fav_boletos['cpf_cnpj']."</td>
                                           <td>".$lanc_boletos['dt_vencimento']."</td>
                                           <td align='right'>".number_format($lanc_boletos['valor'],2,',','.')."</td>
                                           <td><input type='checkbox' class='".$classChbox." check1' name='boletosId".$qtdBoletos."' value='".$boletos['id']."' ></td>
                                       </tr>";
                
                //Valor total dos boletos
                $totalBoletos = $totalBoletos + $lanc_boletos['valor'];
           }
                }
            //Verifica se existe boletos, se não existir inseri a mensagem abaixo.
            if(empty($qtdBoletos)){ 
            
            $listaBoletos = "<tr height='50' align='center'><td  colspan='5' align='center'> Não existem boletos. </td></tr>";
        }  
        
        $listaBoletos .="<input type='hidden' name='totalBoletos' value='".$qtdBoletos."'>";
        // ===================== Fim listar boletos =====================
        //registra a quantidade de boletos
        $totalBoletos = number_format($totalBoletos,2,',','.');
        
        $retorno = array("situacao" => 1, "listaBoletos" => $listaBoletos, "agencia" => $banco['agencia'], "conta" => $banco['numero'], "qtdBoletos" => $qtdBoletos,  "totalBoletos" => $totalBoletos);
        
        return $retorno;
    }

/*
===========================================================================================
GERAR ARQUIVO REMESSA
===========================================================================================
*/    
    
    function gerarRemessa($db,$dados){
        
        $db->query('start transaction');

        try
        {
            $totalBoletos = $dados['totalBoletos'];
            
            $queryContaFinanceira = "SELECT c.*, b.codigo as codigo_banco, b.nome as nome_banco
                FROM contas c 
                join bancos b on c.banco_id = b.id
                WHERE c.id = ".$dados['conta_id'];

            $contaFinanceira = $db->fetch_assoc($queryContaFinanceira);
            
            //start: Cria uma remessa na tabela remessa
            $remessa['dt_cadastro'] = date('Y-m-d');
            $remessa['banco_id'] = $contaFinanceira['banco_id'];
            $remessa['conta_id'] = $dados['conta_id'];
            
            $dados['banco_id'] = $contaFinanceira['banco_id'];
            
            $remessa_id = $db->query_insert("boletos_remessa", $remessa); //Já esta retornando a id, agora preciso inserir a id em cada boleto na tabela boletos e depois atualizar o restante dos dados na tabela boletos_remessa.
            //end: Cria uma remessa na tabela remessa

            //Registrar o código da remessa dentro de cada boleto 
            $dados_boletos = array();
            $c = 1;       
            while($c <= $totalBoletos){             
                
                if(isset($dados['boletosId'.$c])){
                    
                    $boletos['remessa_id'] = $remessa_id;
                    $db->query_update("boletos",$boletos," id =".$dados['boletosId'.$c]);
                    $i ++;
                    
                    //Cria array com as informações dos boletos
                    $info_boletos = $db->fetch_assoc("SELECT id, lancamento_id, nosso_numero FROM boletos WHERE id =".$dados['boletosId'.$c]);
                    $lancamento = $db->fetch_assoc("SELECT favorecido_id, valor, dt_emissao, dt_vencimento, parcela_numero FROM lancamentos WHERE id =".$info_boletos['lancamento_id']);
                    $favorecido = $db->fetch_assoc("SELECT inscricao, cpf_cnpj, nome, logradouro, numero, complemento, bairro, cidade, uf, cep FROM favorecidos WHERE id =".$lancamento['favorecido_id']);
                    
                    //Calcula o valor total
                    $valorTotal = $valorTotal + $lancamento['valor'];
                    
					$dados_boleto = array('id'.$i => $info_boletos['id'],
										'nosso_numero'.$i => $info_boletos['nosso_numero'], 
                                        'favorecido_id'.$i => $lancamento['favorecido_id'], 
                                        'valor'.$i => $lancamento['valor'], 
                                        'dt_emissao'.$i => $lancamento['dt_emissao'], 
										'dt_vencimento'.$i => $lancamento['dt_vencimento'],
										'parcela'.$i => $lancamento['parcela_numero'],
                                        'inscricao'.$i => $favorecido['inscricao'],
                                        'cpf_cnpj'.$i => $favorecido['cpf_cnpj'],
                                        'nome'.$i => $favorecido['nome'],
                                        'logradouro'.$i => $favorecido['logradouro'],
                                        'numero'.$i => $favorecido['numero'],
                                        'complemento'.$i => $favorecido['complemento'],
                                        'bairro'.$i => $favorecido['bairro'],
                                        'cidade'.$i => $favorecido['cidade'],
                                        'uf'.$i => $favorecido['uf'],
                                        'cep'.$i => $favorecido['cep']);     
                    
                    $dados_boletos = $dados_boletos + $dados_boleto;
                    
                }

                $c ++;
            }
            
            //start: Criar arquivo de remessa
            $remessa_arquivo = new Remessa($contaFinanceira['codigo_banco']);
            $retorno_arquivo = $remessa_arquivo->banco->ArquivoRemessa($contaFinanceira, $remessa_id, $i, $dados_boletos);
            
            $info_arquivo = array("nome" => $retorno_arquivo['nome_arquivo'], "numero_remessa" => $retorno_arquivo['numero_remessa'], "valor" => $valorTotal);
            $db->query_update("boletos_remessa", $info_arquivo, " id =".$remessa_id);
            
            $db->query('commit');
            //end: Criar arquivo de remessa

            $retorno = array('status'=>1, "nome_arquivo" => $retorno_arquivo['nome_arquivo']);

            return $retorno;
        }
        catch(Exception $e)
        {
            $db->query('rollback');

            $retorno = array('status'=>0);
            return $retorno;
        }
    }

    /*
===========================================================================================
GERAR ARQUIVO REMESSA DE UMA REMESSA JÁ EXISTENTE
===========================================================================================
*/    
    
    function gerarRemessaBotao($db, $dados){
        
        $queryContaFinanceira = "SELECT c.*, b.codigo as codigo_banco, b.nome as nome_banco
                FROM contas c 
                join bancos b on c.banco_id = b.id
                WHERE c.id = ".$dados['conta_id'];

        $contaFinanceira = $db->fetch_assoc($queryContaFinanceira);
        
        $boletos_remessa = $db->fetch_all_array("SELECT id FROM boletos WHERE remessa_id =".$dados['remessa_id']);

        $dados_boletos = array();

        foreach($boletos_remessa as $boletos){   

                $i ++;
                
                //Cria array com as informações dos boletos
                $info_boletos = $db->fetch_assoc("SELECT id, lancamento_id, nosso_numero FROM boletos WHERE id =".$boletos['id']);
                $lancamento = $db->fetch_assoc("SELECT favorecido_id, valor, dt_emissao, dt_vencimento, parcela_numero FROM lancamentos WHERE id =".$info_boletos['lancamento_id']);
                $favorecido = $db->fetch_assoc("SELECT inscricao, cpf_cnpj, nome, logradouro, numero, complemento, bairro, cidade, uf, cep FROM favorecidos WHERE id =".$lancamento['favorecido_id']);
                
                //Calcula o valor total
                //$valorTotal = $valorTotal + $lancamento['valor'];
                
				$dados_boleto = array('id'.$i => $info_boletos['id'],
									   'nosso_numero'.$i => $info_boletos['nosso_numero'], 
                                       'favorecido_id'.$i => $lancamento['favorecido_id'], 
                                       'valor'.$i => $lancamento['valor'], 
                                       'dt_emissao'.$i => $lancamento['dt_emissao'], 
									   'dt_vencimento'.$i => $lancamento['dt_vencimento'],
									   'parcela'.$i => $lancamento['parcela_numero'],
                                       'inscricao'.$i => $favorecido['inscricao'],
                                       'cpf_cnpj'.$i => $favorecido['cpf_cnpj'],
                                       'nome'.$i => $favorecido['nome'],
                                       'logradouro'.$i => $favorecido['logradouro'],
                                       'numero'.$i => $favorecido['numero'],
                                       'complemento'.$i => $favorecido['complemento'],
                                       'bairro'.$i => $favorecido['bairro'],
                                       'cidade'.$i => $favorecido['cidade'],
                                       'uf'.$i => $favorecido['uf'],
                                       'cep'.$i => $favorecido['cep']);

                $dados_boletos = $dados_boletos + $dados_boleto;
        }
     
        //start: Criar arquivo de remessa
        $remessa_arquivo = new Remessa($contaFinanceira['codigo_banco']);
        $retorno_arquivo = $remessa_arquivo->banco->ArquivoRemessa($contaFinanceira, $dados['remessa_id'], $i, $dados_boletos);
        //end: Criar arquivo de remessa

        $retorno = array("nome_arquivo" => $retorno_arquivo['nome_arquivo']);

        return $retorno;
    }
    
/*
===========================================================================================
LISTAR REMESSA
===========================================================================================
*/
        
    function listarRemessa($db){
    
         
              $array_arquivo = $db->fetch_all_array("select id, date_format(dt_cadastro, '%d/%m/%Y') as dt_cadastro, nome, conta_id, banco_id, numero_remessa, valor
                                                    from boletos_remessa
                                                    order by id desc");
              foreach($array_arquivo as $arquivo){ 

                 $nome_banco = $db->fetch_assoc("SELECT nome FROM bancos WHERE id =".$arquivo['banco_id']);
                 $qtd_boletos = $db->numRows("SELECT id FROM boletos WHERE remessa_id =".$arquivo['id']);
							
                $retorno .= '
								<tr class="gradeA odd"" id="row'.$arquivo['id'].'">
                                    <td style="display:none;">'.$arquivo['id'].'</td>
								    <td align="center">'.$arquivo['dt_cadastro'].'</td> 
                                    <td align="left">'.$arquivo['nome'].'</td> 
                                    <td align="left">'.$nome_banco['nome'].'</td>
                                    <td align="center">'.$arquivo['numero_remessa'].'</td> 
                                    <td align="center">'.$qtd_boletos.'</td> 
                                    <td align="right">'.number_format($arquivo['valor'],2,',','.').'</td> 
                                    <td><a href="javascript://" class="button brownB" style="margin: 1px;" onClick="javascript:gerarRemessaBotao('.$arquivo['conta_id'].','.$arquivo['banco_id'].','.$arquivo['id'].');"><span>Gerar</span></a></td> 
                                </tr>
						  ';
              }
              
             return $retorno;
    } 
}

?>