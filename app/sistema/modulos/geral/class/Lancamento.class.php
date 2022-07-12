<?php

class Lancamento{

	var $lancamento_dados = array(
		"tipo" => "",
		"descricao" => "",
		"lancamento_pai_id" => "",
		"lancamento_recorrente_id" => "",
		"parcela_numero" => "",
		"qtd_parcelas" => "",
		"favorecido_id" => "",
		"forma_pgto_id" => "",
		"conta_id" => "",
		"conta_id_origem" => "",
		"conta_id_destino" => "",
		"documento_id" => "",
		"valor" => "",
		"frequencia" => "",
		"auto_lancamento" => "",
		"observacao" => "",
		"dt_emissao" => "",
		"dt_vencimento" => "",
		"dt_venc_ref" => "",
		"dt_compensacao" => "",
		"compensado" => "",
	);

/*
===========================================================================================
CONSTRUTOR
===========================================================================================
*/
	
	function __construct($db="",$array_dados=""){
		if($array_dados!=""){
			foreach($this->lancamento_dados as $chave => $valor){
				$this->lancamento_dados[$chave] = $array_dados[$chave];
			}
			$this->lancamento_dados[valor] = $db->valorToDouble($array_dados[valor]);
			$this->lancamento_dados[dt_emissao] = $db->data_to_sql($array_dados[dt_emissao]);
			$this->lancamento_dados[dt_vencimento] = $db->data_to_sql($array_dados[dt_vencimento]);
			$this->lancamento_dados[dt_compensacao] = $db->data_to_sql($array_dados[dt_compensacao]);
		}
	}


/*
===========================================================================================
INCLUÍR RECEBIMENTO
===========================================================================================
*/

	function recebimentosIncluir($db){
		$db->query_insert('lancamentos',$this->lancamento_dados);
		self::atualizarSaldoContaRcbt($db,$this->lancamento_dados[valor],$this->lancamento_dados[conta_id]);
		$retorno = array("situacao"=>1,"notificacao"=>"Recebimento lançado com sucesso.");
		return $retorno;
	}

/*
===========================================================================================
EDITAR RECEBIMENTO
===========================================================================================
*/
 
	function recebimentosEditar($db,$array_dados){

		//verifica se o saldo da conta continuará positivo após a alteração do valor do lançamento
		//==================================================================================================
		$valor_ini = $db->valorToDouble($array_dados[valor_ini]);
		$valor = $db->valorToDouble($array_dados[valor]);
		$array_conta = $db->fetch_assoc("select (vl_saldo + vl_credito) saldo_total, (limite_credito - vl_credito) credito_usado, vl_saldo, vl_credito, limite_credito from contas where id = ".$array_dados[conta_id_ini]);
		$saldo_total = $array_conta['saldo_total'];
		if($array_dados[conta_id_ini] != $array_dados[conta_id]){
			$novo_saldo = $saldo_total - $valor_ini;
		}else{
			$novo_saldo = $saldo_total - $valor_ini + $valor;
		}
		//==================================================================================================
		//fim da verificação

		if($novo_saldo >= 0){ //atualiza o valor do lançamento se o saldo não ficar negativo
			
			if($array_dados[conta_id_ini] == $array_dados[conta_id]){ //se a conta não mudou

				$limite_credito = $array_conta[limite_credito];

				if( ($valor_ini <= $array_conta[vl_saldo]) || ($limite_credito == 0) ){ //desconta e repoem o valor apenas no saldo da conta
					$db->query("update contas set vl_saldo = vl_saldo - $valor_ini + $valor where id = ".$array_dados[conta_id]);
				}else{ //desconta o valor inicial da conta e o restante desconta do cheque especial
					$valor_ini -= $array_conta[vl_saldo];
					$db->query("update contas set vl_saldo = 0, vl_credito = vl_credito - $valor_ini where id = ".$array_dados[conta_id]);
					$credito_usado = $db->fetch_assoc("select (limite_credito - vl_credito) credito_usado from contas where id = ".$array_dados[conta_id]);
					if($valor <= $credito_usado['credito_usado']){ //repoem apenas o cheque especial até o litime do novo valor
						$db->query("update contas set vl_credito = vl_credito + $valor where id = ".$array_dados[conta_id]);
					}else{ //repoem o cheque especial e o restante é creditado no saldo da conta
						$valor -= $credito_usado['credito_usado'];
						$db->query("update contas set vl_credito = $limite_credito, vl_saldo = vl_saldo + $valor where id = ".$array_dados[conta_id]);
					}
				}

			}else{ //se a conta mudou

				//desconta o valor todo da conta inicial
				if( ($valor_ini <= $array_conta[vl_saldo]) || ($limite_credito == 0) ){ //desconta apenas do saldo da conta
					$db->query("update contas set vl_saldo = vl_saldo - $valor_ini where id = ".$array_dados[conta_id_ini]);
				}else{
					$valor_ini -= $array_conta[vl_saldo]; //desconta do saldo da conta e o restante desconta do cheque especial
					$db->query("update contas set vl_saldo = 0, vl_credito = vl_credito - $valor_ini where id = ".$array_dados[conta_id_ini]);
				}

				//credita o valor todo na conta final
				$limite_credito = $db->fetch_assoc("select limite_credito from contas where id = ".$array_dados[conta_id]);
				$credito_usado = $db->fetch_assoc("select (limite_credito - vl_credito) credito_usado from contas where id = ".$array_dados[conta_id]);
				if( $valor <= $credito_usado['credito_usado'] ){ //repoem o cheque especial
					$db->query("update contas set vl_credito = vl_credito + $valor where id = ".$array_dados[conta_id]);
				}else{
					$valor -= $credito_usado['credito_usado']; //repoem o cheque especial e credita no saldo da conta o que sobrou
					$db->query("update contas set vl_saldo = vl_saldo + $valor, vl_credito = ".$limite_credito[limite_credito]." where id = ".$array_dados[conta_id]);
				}
							
			}
		
			$db->query_update('lancamentos',$this->lancamento_dados,'id = '.$array_dados[lancamento_id]);
			$retorno = array("situacao"=>1,"notificacao"=>"Lançamento atualizado com sucesso.");
			return $retorno;

			$atualizado = true;
		
		}else{
			$retorno = array("situacao"=>2,"notificacao"=>"A alteração do valor não pode ser realizada. O saldo da conta ficaria negativo.");
			return $retorno;
		}

	}	

/*
===========================================================================================
EXCLUIR RECEBIMENTO
===========================================================================================
*/	

	function recebimentosExcluir($db,$lancamento_id){
		
		$lancamento = $db->fetch_assoc("select tipo, valor, favorecido_id, conta_id from lancamentos where id = ".$lancamento_id);

		//verifica se o saldo ficará negativo após a exclusão
		$conta = $db->fetch_assoc("select (vl_saldo + vl_credito) saldo_total, (limite_credito - vl_credito) credito_usado, vl_saldo, vl_credito, limite_credito from contas where id = ".$lancamento[conta_id]);
		$saldo_total = $conta[saldo_total];
		$novo_saldo = $saldo_total - $lancamento[valor];
		//fim da verificação do saldo

		if( $novo_saldo >= 0){
		
			if( ($lancamento[valor] <= $conta[vl_saldo]) || ($conta[limite_credito] == 0) ){
				$db->query("update contas set vl_saldo = vl_saldo - ".$lancamento[valor]." where id = ".$lancamento[conta_id]);
			}else{
				$valor -= $conta['vl_saldo'];
				$db->query("update contas set vl_saldo = 0, vl_credito = vl_credito - ".$lancamento[valor]." where id = ".$lancamento[conta_id]);
			}			
			
			$db->query("delete from lancamentos where id = ".$lancamento_id);

			$retorno = array("situacao" => 1,"notificacao"=>"Lançamento excluído com sucesso.","conta_id"=>$lancamento[conta_id]);
			
		}else{
			$retorno = array("situacao" => 2,"notificacao"=>"A exclusão não é possível. O saldo da conta ficaria negativo.");
		}

		return $retorno;

	}

/*
===========================================================================================
INCLUÍR PAGAMENTO
===========================================================================================
*/
	
	function pagamentosIncluir($db){
		$conta = $db->fetch_assoc("select (vl_saldo + vl_credito) saldo_total, vl_saldo from contas where id = ".$this->lancamento_dados[conta_id]);
		if($conta[saldo_total]>=$this->lancamento_dados[valor]){
			$db->query_insert('lancamentos',$this->lancamento_dados);
			self::atualizarSaldoContaPgto($db,$this->lancamento_dados[valor],$this->lancamento_dados[conta_id],$conta[vl_saldo]);
			$retorno = array("situacao"=>1,"notificacao"=>"Pagamento lançado com sucesso.");
			return $retorno;
		}else{
			$retorno = array("situacao"=>2,"notificacao"=>"Saldo insuficiente.");
			return $retorno;
		}
	}

/*
===========================================================================================
EDITAR PAGAMENTO
===========================================================================================
*/

	function pagamentosEditar($db,$array_dados){

		//verifica se o saldo da conta continuará positivo após a alteração do valor do lançamento
		//==================================================================================================
		$valor_ini = $db->valorToDouble($array_dados[valor_ini]);
		$valor = $db->valorToDouble($array_dados[valor]);
		if($array_dados[conta_id_ini] == $array_dados[conta_id]){
			$array_conta = $db->fetch_assoc("select (vl_saldo + vl_credito) saldo_total, (limite_credito - vl_credito) credito_usado, vl_saldo, vl_credito, limite_credito from contas where id = ".$array_dados[conta_id_ini]);
			$novo_saldo = $array_conta[saldo_total] + $valor_ini - $valor;
		}else{
			$array_conta = $db->fetch_assoc("select (vl_saldo + vl_credito) saldo_total, (limite_credito - vl_credito) credito_usado, vl_saldo, vl_credito, limite_credito from contas where id = ".$array_dados[conta_id]);
			$novo_saldo = $array_conta[saldo_total] - $valor;
		}
		//==================================================================================================
		//fim da verificação

		if($novo_saldo >= 0){ //atualiza o valor do lançamento se o saldo não ficar negativo
			
			if($array_dados[conta_id_ini] == $array_dados[conta_id]){ //se a conta não mudou

				if( $valor_ini <= $array_conta[credito_usado] ){ //se o valor inicial for menor do que o crédito usado devolve o valor inicial e retira o novo valor do crédito disponível
					$db->query("update contas set vl_credito = vl_credito + ".$valor_ini." - ".$valor." where id = ".$array_dados[conta_id_ini]);
				}else{
					$valor_ini -= $array_conta[credito_usado]; //se o valor inicial for maior do que o crédito usado devolve o valor inicial para o crédito e para o saldo da conta
					$db->query("update contas set vl_saldo = vl_saldo + ".$valor_ini.", vl_credito = ".$array_conta[limite_credito]." where id = ".$array_dados[conta_id_ini]);
					$conta_saldo = $db->fetch_assoc("select vl_saldo from contas where id = ".$array_dados[conta_id_ini]);
					if($valor <= $conta_saldo[vl_saldo]){
						$db->query("update contas set vl_saldo = vl_saldo - ".$valor." where id = ".$array_dados[conta_id_ini]);
					}else{
						$valor -= $conta_saldo[vl_saldo];
						$db->query("update contas set vl_saldo = 0, vl_credito = vl_credito - ".$valor." where id = ".$array_dados[conta_id_ini]);
					}
				}

			}else{ //se a conta mudou

				//credita o valor todo na conta inicial
				if( $valor_ini <= $array_conta[credito_usado] ){
					$db->query("update contas set vl_credito = vl_credito + ".$valor_ini." where id = ".$array_dados[conta_id_ini]);
				}else{
					$valor_ini -= $credito_usado;
					$db->query("update contas set vl_saldo = vl_saldo + ".$valor_ini.", vl_credito = ".$array_conta[limite_credito]." where id = ".$array_dados[conta_id_ini]);
				}

				//debita o valor todo da conta final
				if($valor <= $array_conta[vl_saldo]){
					$db->query("update contas set vl_saldo = vl_saldo - ".$valor." where id = ".$array_dados[conta_id]);
				}else{
					$valor -= $array_conta[vl_saldo];
					$db->query("update contas set vl_saldo = 0, vl_credito = vl_credito - ".$valor." where id = ".$array_dados[conta_id]);
				}
							
			}

			$db->query_update('lancamentos',$this->lancamento_dados,'id = '.$array_dados[lancamento_id]);
			$retorno = array("situacao"=>1,"notificacao"=>"Lançamento atualizado com sucesso.");
			return $retorno;

		}else{
			$retorno = array("situacao"=>2,"notificacao"=>"A alteração do valor não pode ser realizada. O saldo da conta ficaria negativo.");
			return $retorno;
		}

	}	

/*
===========================================================================================
EXCLUIR PAGAMENTO
===========================================================================================
*/	

	function pagamentosExcluir($db,$lancamento_id){
		
		$lancamento = $db->fetch_assoc("select tipo, valor, favorecido_id, conta_id from lancamentos where id = ".$lancamento_id);

		$conta = $db->fetch_assoc("select (vl_saldo + vl_credito) saldo_total, (limite_credito - vl_credito) credito_usado, vl_saldo, vl_credito, limite_credito from contas where id = ".$lancamento[conta_id]);
		
		if( $lancamento[valor] <= $conta[credito_usado] ){
			$db->query("update contas set vl_credito = vl_credito + ".$lancamento[valor]." where id = ".$lancamento[conta_id]);
		}else{
			$lancamento[valor] -= $conta[credito_usado];
			$db->query("update contas set vl_saldo = vl_saldo + ".$lancamento[valor].", vl_credito = ".$conta[limite_credito]." where id = ".$lancamento[conta_id]);
		}
		
		$db->query("delete from lancamentos where id = ".$lancamento_id);

		$retorno = array("situacao"=>1,"notificacao"=>"Lançamento excluído com sucesso.","conta_id"=>$lancamento[conta_id]);
		return $retorno;
	
	}


/*
===========================================================================================
INCLUÍR TRANSFERÊNCIA
===========================================================================================
*/

function transferenciasIncluir($db){

	$conta_origem = $db->fetch_assoc("select (vl_saldo + vl_credito) saldo_total, vl_saldo from contas where id = ".$this->lancamento_dados[conta_id_origem]);
	if($conta_origem[saldo_total]>=$this->lancamento_dados[valor]){
		$db->query_insert('lancamentos',$this->lancamento_dados);
		self::atualizarSaldoContaTrans($db,$this->lancamento_dados[valor],$this->lancamento_dados[conta_id_origem],$this->lancamento_dados[conta_id_destino],$conta_origem[vl_saldo]);
		$retorno = array("situacao"=>1,"notificacao"=>"Transferência lançada com sucesso.");
		return $retorno;
	}else{
		$retorno = array("situacao"=>2,"notificacao"=>"Saldo insuficiente.");
		return $retorno;
	}

}

/*
===========================================================================================
EDITAR TRANSFERÊNCIA
===========================================================================================
*/

function transferenciasEditar($db,$array_dados){

		$conta_id_origem_ini = $array_dados[conta_id_origem_ini];
		$conta_id_origem = $array_dados[conta_id_origem];
		$conta_id_destino_ini = $array_dados[conta_id_destino_ini];		
		$conta_id_destino = $array_dados[conta_id_destino];
		$valor_ini = $db->valorToDouble($array_dados[valor_ini]);
		$valor = $db->valorToDouble($array_dados[valor]);
		
		//verifica se a atualização pode ser feita na conta de origem
		//==================================================================================================
		if($conta_id_origem_ini == $conta_id_origem){
			$conta_origem = $db->fetch_assoc("select (vl_saldo + vl_credito) saldo_total, (limite_credito - vl_credito) credito_usado, vl_saldo, vl_credito, limite_credito from contas where id = ".$conta_id_origem_ini);
			$saldo_total_origem = $conta_origem[saldo_total];
			$novo_saldo_origem = $saldo_total_origem + $valor_ini - $valor;
		}else{
			$conta_origem = $db->fetch_assoc("select (vl_saldo + vl_credito) saldo_total, (limite_credito - vl_credito) credito_usado, vl_saldo, vl_credito, limite_credito from contas where id = ".$conta_id_origem);
			$saldo_total_origem = $conta_origem[saldo_total];
			$novo_saldo_origem = $saldo_total_origem - $valor;
		}
		//==================================================================================================

		//verifica se a atualização pode ser feita na conta de destino
		//==================================================================================================
		$conta_destino = $db->fetch_assoc("select (vl_saldo + vl_credito) saldo_total, (limite_credito - vl_credito) credito_usado, vl_saldo, vl_credito, limite_credito from contas where id = ".$conta_id_destino_ini);
		$saldo_total_destino = $conta_destino[saldo_total];
		if($conta_id_destino_ini == $conta_id_destino){
			$novo_saldo_destino = $saldo_total_destino - $valor_ini + $valor;
		}else{
			$novo_saldo_destino = $saldo_total_destino - $valor_ini;
		}
		//==================================================================================================

		if($novo_saldo_origem >= 0 && $novo_saldo_destino >= 0){ //verifica se o saldo das contas de origem e destino ficarão no minimo iguais a zero

			//operações realizadas na conta de origem
			if($conta_id_origem_ini == $conta_id_origem){
	
				$credito_usado_origem = $conta_origem[credito_usado];
				$limite_credito_origem = $conta_origem[limite_credito];
	
				if( $valor_ini <= $credito_usado_origem ){
					$db->query("update contas set vl_credito = vl_credito + ".$valor_ini." - ".$valor." where id = ".$conta_id_origem);
				}else{
					$valor_ini -= $credito_usado_origem;
					$db->query("update contas set vl_saldo = vl_saldo + ".$valor_ini.", vl_credito = ".$limite_credito_origem." where id = ".$conta_id_origem);
					$saldo_conta_origem = $db->fetch_assoc("select vl_saldo from contas where id = ".$conta_id_origem);
					if($valor <= $saldo_conta_origem[vl_saldo]){
						$db->query("update contas set vl_saldo = vl_saldo - ".$valor." where id = ".$conta_id_origem);
					}else{
						$valor -= $saldo_conta_origem[vl_saldo];
						$db->query("update contas set vl_saldo = 0, vl_credito = vl_credito - ".$valor." where id = ".$conta_id_origem);
					}
				}

			}else{

				//devolve os valores para a conta de origem inicial
				$conta_origem_ini = $db->fetch_assoc("select (limite_credito - vl_credito) credito_usado, limite_credito from contas where id = ".$conta_id_origem_ini);
				$credito_usado_origem = $conta_origem_ini[credito_usado];
				$limite_credito_origem = $conta_origem_ini[limite_credito];
			
				if($valor_ini <= $credito_usado_origem){
					$db->query("update contas set vl_credito = vl_credito + ".$valor_ini." where id = ".$conta_id_origem_ini);
				}else{
					$valor_ini -= $credito_usado_origem;
					$db->query("update contas set vl_credito = ".$limite_credito_origem.", vl_saldo = vl_saldo + ".$valor_ini." where id = ".$conta_id_origem_ini);
				}

				//debita os valores da conta de origem final
				$saldo_conta_origem = $db->fetch_assoc("select vl_saldo from contas where id = ".$conta_id_origem);
				if($valor <= $saldo_conta_origem[vl_saldo]){
					$db->query("update contas set vl_saldo = vl_saldo - $vl_documento where id = ".$conta_id_origem);
				}else{
					$valor -= $saldo_conta_origem[vl_saldo];
					$db->query("update contas set vl_saldo = 0, vl_credito = vl_credito - ".$valor." where id = ".$conta_id_origem);
				}

			}
			
			$valor_ini = $db->valorToDouble($array_dados[valor_ini]);
			$valor = $db->valorToDouble($array_dados[valor]);

			//operações realizadas na conta de destino
			if($conta_id_destino_ini == $conta_id_destino){
				
				if( ($valor_ini <= $conta_destino[vl_saldo]) || ($conta_destino[limite_credito] == 0) ){
					$db->query("update contas set vl_saldo = vl_saldo - ".$valor_ini." + ".$valor." where id = ".$conta_id_destino);
				}else{
					$valor_ini -= $registro_destino['vl_saldo'];
					$db->query("update contas set vl_saldo = 0, vl_credito = vl_credito - ".$valor_ini." where id = ".$conta_id_destino);
					$credito_usado_destino = $db->fetch_assoc("select (limite_credito - vl_credito) credito_usado from contas where id = ".$conta_id_destino);
					if($valor <= $credito_usado_destino[credito_usado]){
						$db->query("update contas set vl_credito = vl_credito + ".$valor." where id = ".$conta_id_destino);
					}else{
						$valor -= $credito_usado_destino[credito_usado];
						$db->query("update contas set vl_credito = ".$conta_destino[limite_credito].", vl_saldo = vl_saldo + ".$valor." where id = ".$conta_id_destino);
					}
				}

			}else{

				//debita os valores da conta de destino inicial
				$saldo_conta_destino_inicial = $db->fetch_assoc("select vl_saldo from contas where id = ".$conta_id_destino_ini);
				if($valor_ini <= $saldo_conta_destino_inicial[vl_saldo]){
					$db->query("update contas set vl_saldo = vl_saldo - ".$valor_ini." where id = ".$conta_id_destino_ini);
				}else{
					$vl_documento_inicial -= $saldo_conta_destino_inicial['vl_saldo'];
					$db->query("update contas set vl_saldo = 0, vl_credito = vl_credito - ".$valor_ini." where id = ".$conta_id_destino_ini);
				}

				//credita os valores na conta de destino final
				$conta_destino_final = $db->fetch_assoc("select (limite_credito - vl_credito) credito_usado, limite_credito from contas where id = ".$conta_id_destino);

				if($valor <= $conta_destino_final[credito_usado]){
					$db->query("update contas set vl_credito = vl_credito + ".$valor." where id = ".$conta_id_destino);
				}else{
					$valor -= $conta_destino_final[credito_usado];
					$db->query("update contas set vl_credito = ".$conta_destino_final[limite_credito].", vl_saldo = vl_saldo + ".$valor." where id = ".$conta_id_destino);
				}
			
			}
			
			$db->query_update('lancamentos',$this->lancamento_dados,'id = '.$array_dados[lancamento_id]);
			$retorno = array("situacao"=>1,"notificacao"=>"Lançamento atualizado com sucesso.");
			return $retorno;

		}else{
			$retorno = array("situacao"=>2,"notificacao"=>"A alteração do valor não pode ser realizada. O saldo da conta ficaria negativo.");
			return $retorno;
		}

}

/*
===========================================================================================
EXCLUÍR TRANSFERÊNCIA
===========================================================================================
*/

function transferenciasExcluir($db,$array_dados){

	$lancamento = $db->fetch_assoc("select valor, conta_id_origem, conta_id_destino from lancamentos where id = ".$array_dados[lancamento_id]);
	$valor = $lancamento[valor];
	
	//verifica se o saldo da conta de destino ficará negativo
	$conta_destino = $db->fetch_assoc("select (vl_saldo + vl_credito) saldo_total, (limite_credito - vl_credito) credito_usado, vl_saldo, vl_credito, limite_credito from contas where id = ".$lancamento[conta_id_destino]);
	$novo_saldo_destino = $conta_destino[saldo_total] - $valor;
	//fim verifica se o saldo da conta de destino ficará negativo
	
	if( $novo_saldo_destino >= 0){
	
		//debita valor da conta de destino
		if( ($valor <= $conta_destino[vl_saldo]) || ($conta_destino[limite_credito] == 0) ){
			$db->query("update contas set vl_saldo = vl_saldo - ".$valor." where id = ".$lancamento[conta_id_destino]);
		}else{
			$debito = $valor - $conta_destino[vl_saldo];
			$db->query("update contas set vl_saldo = 0, vl_credito = vl_credito - ".$debito." where id = ".$lancamento[conta_id_destino]);
		}			
		//fim debita valor da conta de destino
		
		//credita valor na conta de origem
		$conta_origem = $db->fetch_assoc("select (vl_saldo + vl_credito) saldo_total, (limite_credito - vl_credito) credito_usado, vl_saldo, vl_credito, limite_credito from contas where id = ".$lancamento[conta_id_origem]);
		if( $valor <= $conta_origem[credito_usado] ){
			$db->query("update contas set vl_credito = vl_credito + ".$valor." where id = ".$lancamento[conta_id_origem]);
		}else{
			$valor -= $conta_origem[credito_usado];
			$db->query("update contas set vl_saldo = vl_saldo + ".$valor.", vl_credito = ".$conta_origem[limite_credito]." where id = ".$lancamento[conta_id_origem]);
		}			
		//fim credita valor na conta de origem

		$db->query("delete from lancamentos where id = ".$array_dados[lancamento_id]);
		$retorno = array("situacao"=>1,"notificacao"=>"Lançamento excluído com sucesso.");
		return $retorno;

	}else{
		$retorno = array("situacao"=>2,"notificacao"=>"A alteração do valor não pode ser realizada. O saldo da conta de destino ficaria negativo.");
		return $retorno;
	}

}

/*
===========================================================================================
VISUALIZAR
===========================================================================================
*/

	function lancamentosExibir($db,$array_dados){
		if($array_dados[tp_lancamento]=="T"){
			$query = "
				select l.id, l.tipo, l.descricao, concat(b.nome,' - ',c.descricao) conta_origem, concat(b2.nome,' - ',c2.descricao) conta_destino,
				l.conta_id_origem, l.conta_id_destino, date_format(l.dt_emissao, '%d/%m/%Y') dt_emissao, date_format(l.dt_vencimento, '%d/%m/%Y') dt_vencimento, 
				date_format(l.dt_compensacao, '%d/%m/%Y') dt_compensacao, l.valor, l.observacao
				from lancamentos l, contas c, bancos b, contas c2, bancos b2
				where l.id = ".$array_dados[lancamento_id]."
					and l.conta_id_origem = c.id
					and c.banco_id = b.id
					and l.conta_id_destino = c2.id
					and c2.banco_id = b2.id
			";
		}else{
			$query = "
				select l.id, l.tipo, l.descricao, f.nome favorecido, f.id favorecido_id, concat(b.nome,' - ',c.descricao) conta, c.id conta_id,
				date_format(l.dt_emissao, '%d/%m/%Y') dt_emissao, date_format(l.dt_vencimento, '%d/%m/%Y') dt_vencimento, 
				date_format(l.dt_compensacao, '%d/%m/%Y') dt_compensacao, l.valor, l.documento_id, l.forma_pgto_id, l.observacao
				from lancamentos l, contas c, favorecidos f, bancos b
				where l.id = ".$array_dados[lancamento_id]."
					and l.conta_id = c.id
					and l.favorecido_id = f.id
					and c.banco_id = b.id
			";
		}
		$lancamentos_visualizar = $db->fetch_array($db->query($query));
		$lancamentos_visualizar[valor] = number_format($lancamentos_visualizar[valor],2,',','.');
		return $lancamentos_visualizar;
	}

/*
===========================================================================================
ATUALIZAR SALDO DA CONTA PARA RECEBIMENTO
===========================================================================================
*/

function atualizarSaldoContaRcbt($db,$valor,$conta_id){
	$array_conta = $db->fetch_assoc("select vl_credito, limite_credito from contas where id = ".$conta_id);
	if($array_conta[vl_credito] == $array_conta[limite_credito]){
		$db->query("update contas set vl_saldo = vl_saldo + ".$valor." where id = ".$conta_id);
	}else{
		$credito_usado = $array_conta[limite_credito] - $array_conta[vl_credito];
		if($valor <= $credito_usado){
			$db->query("update contas set vl_credito = vl_credito + ".$valor." where id = ".$conta_id); //repoem somente o cheque especial usado
		}else{
			$valor -= $credito_usado;
			$db->query("update contas set vl_saldo = vl_saldo + ".$valor.", vl_credito = vl_credito + ".$credito_usado." where id = ".$conta_id); //repoem o cheque especial usado e o saldo da conta
		}
	}
}

/*
===========================================================================================
ATUALIZAR SALDO DA CONTA PARA PAGAMENTO
===========================================================================================
*/

function atualizarSaldoContaPgto($db,$valor,$conta_id,$conta_saldo){
	if($conta_saldo>=$valor){ //desconta o debito inteiro do saldo da conta
		$db->query("update contas set vl_saldo = vl_saldo - ".$valor." where id = ".$conta_id);
	}else{ //desconta uma parte no saldo da conta, se houver credito, e o restante do saldo do cheque especial
		$valor -= $conta_saldo; //abate do debito o restante de saldo que havia na conta
		$db->query("update contas set vl_saldo = 0, vl_credito = vl_credito - ".$valor." where id = ".$conta_id); //abate o restante do debito do saldo do cheque especial
	}
}

/*
===========================================================================================
ATUALIZAR SALDO DA CONTA PARA TRANSFERÊNCIA
===========================================================================================
*/

function atualizarSaldoContaTrans($db,$valor,$conta_id_origem,$conta_id_destino,$conta_saldo){
	//debita a conta de origem
	if($conta_saldo>=$valor){ //desconta o debito inteiro do saldo da conta
		$db->query("update contas set vl_saldo = vl_saldo - ".$valor." where id = ".$conta_id_origem);
	}else{ //desconta uma parte no saldo da conta, se houver credito, e o restante do saldo do cheque especial
		$debito = $valor - $conta_saldo; //abate do debito o restante de saldo que havia na conta
		$db->query("update contas set vl_saldo = 0, vl_credito = vl_credito - ".$debito." where id = ".$conta_id_origem); //abate o restante do debito do saldo do cheque especial
	}
	//fim debita a conta de origem
	//credita conta de destino
	$conta_destino = $db->fetch_assoc("select vl_credito, limite_credito from contas where id = ".$conta_id_destino);
	if($conta_destino[vl_credito] == $conta_destino[limite_credito]){
		$db->query("update contas set vl_saldo = vl_saldo + ".$valor." where id = ".$conta_id_destino);
	}else{
		$credito_usado = $conta_destino[limite_credito] - $conta_destino[vl_credito];
		if($vl_transferencia <= $credito_usado){
			$db->query("update contas set vl_credito = vl_credito + ".$valor." where id = ".$conta_id_destino); //repoem somente o cheque especial usado
		}else{
			$db->query("update contas set vl_saldo = vl_saldo + ".$valor." - ".$credito_usado.", vl_credito = ".$conta_destino[limite_credito]." where id = ".$conta_id_destino); //repoem o cheque especial usado e o saldo da conta
		}
	}
	//fim credita conta de destino
}

/*
===========================================================================================
LISTAR
===========================================================================================
*/

//Lista dos lançamentos
function lancamentosListar($db,$array_dados,$tp_busca=""){
	
	$nome_conta = $db->fetch_assoc("select concat(b.nome,' - ',c.descricao) nome_conta from contas c, bancos b where c.id = ".$array_dados[conta_id]." and c.banco_id = b.id");

	if($tp_busca==""){
		//colocar aqui o intervalo do mês corrente
		$dt_ini = date('Y').'-'.date('m').'-01';
		$dt_fim = mktime(0,0,0,date('m')+1,'00',date('Y'));
		$dt_fim = date('Y-m-d',$dt_fim);
	}elseif($tp_busca=="periodo"){
		$dt_ini = $db->data_to_sql($array_dados[dt_ini]);
		$dt_fim = $db->data_to_sql($array_dados[dt_fim]);
	}else{
		$dt_ini = date('Y').'-'.$array_dados[mes].'-01';
		$dt_fim = mktime(0,0,0,$array_dados[mes]+1,'00',date('Y'));
		$dt_fim = date('Y-m-d',$dt_fim);
	}
	
	$lancamentos_listar ='
		<table cellpadding="0" cellspacing="0" border="0" class="display dTable">
		<thead>
		<tr>
			<th>Compensação</th>
			<th>Descrição</th>
			<th>Valor</th>
			<th width="100">Opções</th>
		</tr>
		</thead>
		<tbody>
	';

	$query_lancamentos = "
		(select id, tipo, date_format(dt_compensacao, '%d/%m/%Y') dt_compensacao, descricao, valor, conta_id_origem, conta_id_destino
		 from lancamentos
		 where conta_id = ".$array_dados[conta_id]."
			and dt_compensacao >= '".$dt_ini."'
			and dt_compensacao <= '".$dt_fim."')
	
		 union all
	
		(select id, tipo, date_format(dt_compensacao, '%d/%m/%Y') dt_compensacao, descricao, valor, conta_id_origem, conta_id_destino
		 from lancamentos
		 where conta_id_origem = ".$array_dados[conta_id]."
			and dt_compensacao >= '".$dt_ini."'
			and dt_compensacao <= '".$dt_fim."')
			
		 union all
	
		(select id, tipo, date_format(dt_compensacao, '%d/%m/%Y') dt_compensacao, descricao, valor, conta_id_origem, conta_id_destino
		 from lancamentos
		 where conta_id_destino = ".$array_dados[conta_id]."
			and dt_compensacao >= '".$dt_ini."'
			and dt_compensacao <= '".$dt_fim."')			
		
		order by dt_compensacao
	";
	
	$array_lancamentos = $db->fetch_all_array($query_lancamentos);

	foreach($array_lancamentos as $lancamento){
		if($lancamento[tipo]=="R"){
			$classe_excluir = "recebimentosExcluir";
			$valor = '<font color="#009900">R$ '.number_format($lancamento['valor'],2,',','.').'</font>';
		}elseif($lancamento[tipo]=="P"){
			$classe_excluir = "pagamentosExcluir";
			$valor = '<font color="#FF0000">R$ '.number_format($lancamento['valor'],2,',','.').'</font>';
		}else{
			$classe_excluir = "transferenciasExcluir";
			if($lancamento[conta_id_destino]==$array_dados[conta_id]){
				$valor = '<font color="#009900">R$ '.number_format($lancamento['valor'],2,',','.').'</font>';
			}else{
				$valor = '<font color="#FF0000">R$ '.number_format($lancamento['valor'],2,',','.').'</font>';				
			}
		}
		$lancamentos_listar .= '
			<tr class="gradeA">
				<td>'.$lancamento['dt_compensacao'].'</td>
				<td>'.$lancamento['descricao'].'</td>
				<td>'.$valor.'</td>
				<td class="center">
					<a href="javascript://void(0);" title="Editar" class="smallButton" style="margin: 5px;" onClick="lancamentosExibir('.$lancamento[id].',\''.$lancamento[tipo].'\')"><img src="images/icons/dark/pencil.png" alt=""></a>
					<a href="'.$lancamento[id].'" title="Excluír" class="smallButton '.$classe_excluir.'" style="margin: 5px;"><img src="images/icons/dark/close.png" alt=""></a></td>
				</td>
			</tr>
		';
	}
	
	$lancamentos_listar .= '</tbody></table>';
	$retorno = array("lancamentos"=>$lancamentos_listar,"nome_conta"=>$nome_conta[nome_conta]);
	return $retorno;
}

/*
===========================================================================================
LISTAR SALDO DAS CONTAS
===========================================================================================
*/

function contasSaldoListar($db,$conta_id){
	$saldo_total = 0;
	$array_contas = $db->fetch_all_array("select c.id, b.nome, b.logo, c.descricao, c.vl_saldo, c.vl_credito from contas c, bancos b where c.banco_id = b.id order by b.nome");
	$contas_saldo = "";
	foreach($array_contas as $conta){
		$marcacao = "";
		if($conta[id]==$conta_id){
			$marcacao = '<img src="images/icons/icon-checked-conta.png" border="0"/>';
		}
		
		if(!empty($conta['logo'])){ $banco_logo = $conta['logo']; }else{ $banco_logo = "bank.png"; }
		
		$contas_saldo .= '
			<div class="userRow">
					<img src="images/bancos/'.$banco_logo.'" alt="" class="floatL">
					<ul class="leftList">
							<li><a href="#" title="" onClick="lancamentosListar(\''.$conta['id'].'\')"><strong>'.$conta['nome'].'</strong></a></li>
							<li style="font-size: 9px;">'.$conta['descricao'].'</li>
					</ul>
					<div class="rightList">'.$marcacao.'</div>
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
							<li><strong class="grey">R$ '.number_format($conta['vl_saldo']+$conta['vl_credito'],2,',','.').'</strong></li>
					</ul>
			</div>
			<div class="cLine"></div>
		';
		$saldo_total += $conta['vl_saldo']+$conta['vl_credito'];
	}
	$contas = array("contas_saldo"=>$contas_saldo,"saldo_total"=>"R$ ".number_format($saldo_total,2,',','.'));
	return $contas;
}

}


?>