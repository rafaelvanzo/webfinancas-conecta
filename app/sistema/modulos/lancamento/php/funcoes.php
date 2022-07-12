<?php
session_start();
require_once("../../../php/db_conexao.php");
require_once("../class/Lancamento.class.php");
require_once("../../../php/swiftMailer/lib/swift_required.php");

define('ERRO','Falha ao executar a operção. Por favor, tente novamente.');
define('SALDO','Saldo insuficiente.');
define('LNCT_PRG','Lançamento programado com sucesso.');
define('LNCT_EDT','Lançamento atualizado com sucesso..');
define('LNCT_EXC','Lançamento(s) excluído(s) com sucesso.');
define('LNCT_CMP','Lançamento compensado com sucesso.');

function VerificaPermissaoUsuario($permissao_id){
    if(!in_array($permissao_id,$_SESSION['permissoes']))
        return array('status'=>2,'notificacao'=>'Usuário sem permissão para esta operação.');
    else
        return array('status'=>1);
}

/*
===========================================================================================
ARRAY PARA TESTE
===========================================================================================
*/
/*
$_REQUEST = array(
	'funcao'=>'recibo',
	'lancamento_id'=>'356',
	'valor_ini'=>'1',
	'conta_id_origem_ini'=>'20',
	'conta_id_destino_ini'=>'21',
	'tipo'=>'T',
	'sab_dom'=>'1',
	'descricao'=>'teste descricao',
	'favorecido_id'=>'125',
	'conta_id_origem'=>'20',
	'conta_id_destino'=>'21',
	'dt_competencia'=>'05/2015',
	'dt_emissao'=>'22/05/2015',
	'dt_vencimento'=>'22/05/2015',
	'valor'=>'1.000,00',
	'observacao'=>''
);
*/

/*
===========================================================================================
INICIALIZAR OBJETO
===========================================================================================
*/
$tp_lnct = $_REQUEST['tipo'];

if($tp_lnct=='R'){
	require_once("../class/Recebimento.class.php");
	$lancamento = new Recebimento($db,$_REQUEST);
}elseif($tp_lnct=='P'){
	require_once("../class/Pagamento.class.php");
	$lancamento = new Pagamento($db,$_REQUEST);
}elseif($tp_lnct=='T'){
	require_once("../class/Transferencia.class.php");
	$lancamento = new Transferencia($db,$_REQUEST);
}

switch($_REQUEST['funcao']){

	/*
	===========================================================================================
	TESTE
	===========================================================================================
	*/

	//case "teste":
		//$lancamento->anexoExcluir($db,419);
	//break;

	/*
	===========================================================================================
	INCLUÍR
	===========================================================================================
	*/

	case "lancamentoIncluir":
        $verificaPermissao = VerificaPermissaoUsuario(3);
        if($verificaPermissao['status'] == 2){
            echo json_encode($verificaPermissao);
            break;
        }
        
		try{
			$db->query('start transaction');
			$lancamento_id = $lancamento->lancamentoIncluir($db,$_REQUEST);
			$db->query('commit');
			if($lancamento_id){

                //start: excluir após verificação do erro
                if($_REQUEST['tipo']=='R' || $_REQUEST['tipo']=='P'){
                    $lancamento->VerificarSaldo($db,$_REQUEST['conta_id'],$_REQUEST,$lancamento_id);
                }else{
                    $lancamento->VerificarSaldo($db,$_REQUEST['conta_id_origem'],$_REQUEST,$lancamento_id);
                    $lancamento->VerificarSaldo($db,$_REQUEST['conta_id_destino'],$_REQUEST,$lancamento_id);
                }
				//end: excluir após verificação do erro

                $contas_saldo_listar = $lancamento->contasSaldoListar($db,$_REQUEST['conta_id'],$_SESSION['total_disponivel']);
				$retorno = array("situacao"=>1,"notificacao"=>LNCT_PRG,/*"lancamentos" => $lancamentos_listar,*/"contas_saldo"=>$contas_saldo_listar['contas_saldo'],"saldo_total"=>$contas_saldo_listar['saldo_total'],"saldo"=>$contas_saldo_listar['saldo'],"credito"=>$contas_saldo_listar['credito'],'compensado'=>1,'lancamento_id'=>$lancamento_id);
			}else{
				$retorno = array('situacao'=>2,'notificacao'=>SALDO);
			}
		}
		catch(Exception $e){
			$db->query('rollback');
			$retorno = array('situacao'=>0,'notificacao'=>$e->getMessage());
		}
		$db->close();
		$retorno = json_encode($retorno);
		echo $retorno;
	break;

	/*
	===========================================================================================
	EDITAR
	===========================================================================================
	*/

	case "lancamentoEditar":
        $verificaPermissao = VerificaPermissaoUsuario(4);
        if($verificaPermissao['status'] == 2){
        echo json_encode($verificaPermissao);
        break;
        }

        try{
			$db->query('start transaction');
			$editar = $lancamento->lancamentoEditar($db,$_REQUEST);
			$db->query('commit');
			if($editar){
				
                //start: excluir após verificação do erro
                if($_REQUEST['tipo']=='R' || $_REQUEST['tipo']=='P'){
                    $lancamento->VerificarSaldo($db,$_REQUEST['conta_id'],$_REQUEST,$_REQUEST['lancamento_id']);
                }else{
                    $lancamento->VerificarSaldo($db,$_REQUEST['conta_id_origem'],$_REQUEST,$_REQUEST['lancamento_id']);
                    $lancamento->VerificarSaldo($db,$_REQUEST['conta_id_destino'],$_REQUEST,$_REQUEST['lancamento_id']);
                }
				//end: excluir após verificação do erro

                $contas_saldo_listar = $lancamento->contasSaldoListar($db,$_REQUEST['conta_id'],$_SESSION['total_disponivel']);
				$retorno = array("situacao"=>1,"notificacao"=>LNCT_EDT,/*"lancamentos" => $lancamentos_listar,*/"contas_saldo"=>$contas_saldo_listar['contas_saldo'],"saldo_total"=>$contas_saldo_listar['saldo_total'],"saldo"=>$contas_saldo_listar['saldo'],"credito"=>$contas_saldo_listar['credito'],'compensado'=>1);
			}else{
				$retorno = array('situacao'=>2,'notificacao'=>SALDO);
			}
		}
		catch(Exception $e){
			$db->query('rollback');
			$retorno = array('situacao'=>0,'notificacao'=>$e->getMessage());
		}
		$db->close();
		$retorno = json_encode($retorno);
		echo $retorno;
	break;

	/*
	===========================================================================================
	EDITAR RECORRENTE
	===========================================================================================
	*/

	case "lancamentoRcrEditar":
        $verificaPermissao = VerificaPermissaoUsuario(8);
        if($verificaPermissao['status'] == 2){
        echo json_encode($verificaPermissao);
        break;
        }

        try{
			$db->query('start transaction');
			$editar = $lancamento->lancamentoRcrEditar($db,$_REQUEST);
			$db->query('commit');
			if($editar){
				
                //start: excluir após verificação do erro
                $lancamento->VerificarSaldo($db,$_REQUEST['conta_id'],$_REQUEST,$editar['lancamento_id']);
                //end: excluir após verificação do erro

				$contas_saldo_listar = $lancamento->contasSaldoListar($db,$_REQUEST['conta_id'],$_SESSION['total_disponivel']);
				$retorno = array("situacao"=>1,"notificacao"=>LNCT_EDT,/*"lancamentos"=>$lancamentos_listar,*/"contas_saldo"=>$contas_saldo_listar['contas_saldo'],"saldo_total"=>$contas_saldo_listar['saldo_total'],"saldo"=>$contas_saldo_listar['saldo'],"credito"=>$contas_saldo_listar['credito'],'compensado'=>1);
			}else{
				$retorno = array('situacao'=>2,'notificacao'=>SALDO);
			}
		}
		catch(Exception $e){
			$db->query('rollback');
			$retorno = array('situacao'=>0,'notificacao'=>$e->getMessage());
		}
		$db->close();
		$retorno = json_encode($retorno);
		echo $retorno;
	break;

	/*
	===========================================================================================
	EXCLUÍR
	===========================================================================================
	*/

	case "lancamentoExcluir":
        $verificaPermissao = VerificaPermissaoUsuario(5);
        if($verificaPermissao['status'] == 2){
        echo json_encode($verificaPermissao);
        break;
        }

		try{
			//start: excluir após verificação do erro
            $dadosLancamentoTeste = $db->fetch_assoc('select * from lancamentos where id = '.$_REQUEST['lancamento_id']);
            //end: excluir após verificação do erro

            $db->query('start transaction');
			$excluir = $lancamento->lancamentoExcluir($db,$_REQUEST);
			$db->query('commit');
			if($excluir){

                //start: excluir após verificação do erro
                if($_REQUEST['tipo']=='R' || $_REQUEST['tipo']=='P'){
                    $lancamento->VerificarSaldo($db,$dadosLancamentoTeste['conta_id'],$dadosLancamentoTeste,$_REQUEST['lancamento_id']);
                }else{
                    $lancamento->VerificarSaldo($db,$dadosLancamentoTeste['conta_id_origem'],$dadosLancamentoTeste,$_REQUEST['lancamento_id']);
                    $lancamento->VerificarSaldo($db,$dadosLancamentoTeste['conta_id_destino'],$dadosLancamentoTeste,$_REQUEST['lancamento_id']);
                }
				//end: excluir após verificação do erro

                $contas_saldo_listar = $lancamento->contasSaldoListar($db,$_SESSION['total_disponivel']);
				$retorno = array("situacao"=>1,"notificacao"=>LNCT_EXC,/*"lancamentos" => $lancamentos_listar,*/"contas_saldo"=>$contas_saldo_listar['contas_saldo'],"saldo_total"=>$contas_saldo_listar['saldo_total'],"saldo"=>$contas_saldo_listar['saldo'],"credito"=>$contas_saldo_listar['credito'],'compensado'=>1);
			}else{
				$retorno = array('situacao'=>2,'notificacao'=>SALDO);
			}
		}
		catch(Exception $e){
			$db->query('rollback');
			$retorno = array('situacao'=>0,'notificacao'=>$e->getMessage());
		}
		$db->close();
		$retorno = json_encode($retorno);
		echo $retorno;
	break;

    /*
	===========================================================================================
	EXCLUÍR LNCT PARCELADO
	===========================================================================================
     */

	case "lancamentoExcluirParcelado":
        $verificaPermissao = VerificaPermissaoUsuario(5);
        if($verificaPermissao['status'] == 2){
            echo json_encode($verificaPermissao);
            break;
        }

		try{
			$db->query('start transaction');
			$excluir = $lancamento->lancamentoExcluirParcelado($db,$_REQUEST);
			$db->query('commit');
			if($excluir){
				$contas_saldo_listar = $lancamento->contasSaldoListar($db,$_SESSION['total_disponivel']);
				$retorno = array("situacao"=>1,"notificacao"=>LNCT_EXC,/*"lancamentos" => $lancamentos_listar,*/"contas_saldo"=>$contas_saldo_listar['contas_saldo'],"saldo_total"=>$contas_saldo_listar['saldo_total'],"saldo"=>$contas_saldo_listar['saldo'],"credito"=>$contas_saldo_listar['credito'],'compensado'=>1);
			}else{
				$retorno = array('situacao'=>2,'notificacao'=>SALDO);
			}
		}
		catch(Exception $e){
			$db->query('rollback');
			$retorno = array('situacao'=>0,'notificacao'=>$e->getMessage());
		}
		$db->close();
		$retorno = json_encode($retorno);
		echo $retorno;
        break;

    
	/*
	===========================================================================================
	EXCLUÍR RECORRENTE
	===========================================================================================
	*/

	case "lancamentoRcrExcluir":
        $verificaPermissao = VerificaPermissaoUsuario(9);
        if($verificaPermissao['status'] == 2){
        echo json_encode($verificaPermissao);
        break;
        }

		$lancamento->atualizarVencimentoRcr($db,$_REQUEST['lancamento_id']);
		$db->close();
		$retorno = array('situacao'=>1,"notificacao"=>LNCT_EXC);
		$retorno = json_encode($retorno);
		echo $retorno;
	break;
	
	/*
	===========================================================================================
	COMPENSAR
	===========================================================================================
	*/
	
	case "lancamentoCompensar":
        $verificaPermissao = VerificaPermissaoUsuario(4);
        if($verificaPermissao['status'] == 2){
        echo json_encode($verificaPermissao);
        break;
        }

        try{
			$db->query('start transaction'); 
			$compensar = $lancamento->lancamentoCompensar($db,$_REQUEST);
			$db->query('commit');
			if($compensar){
				
                //start: excluir após verificação do erro
                if($_REQUEST['tipo']=='R' || $_REQUEST['tipo']=='P'){
                    $lancamento->VerificarSaldo($db,$_REQUEST['conta_id'],$_REQUEST,$_REQUEST['lancamento_id']);
                }else{
                    $lancamento->VerificarSaldo($db,$_REQUEST['conta_id_origem'],$_REQUEST,$_REQUEST['lancamento_id']);
                    $lancamento->VerificarSaldo($db,$_REQUEST['conta_id_destino'],$_REQUEST,$_REQUEST['lancamento_id']);
                }
                //end: excluir após verificação do erro

				$contas_saldo_listar = $lancamento->contasSaldoListar($db,$_REQUEST['conta_id'],$_SESSION['total_disponivel']);
				$retorno = array("situacao"=>1,"notificacao"=>LNCT_CMP,/*"lancamentos" => $lancamentos_listar,*/"contas_saldo"=>$contas_saldo_listar['contas_saldo'],"saldo_total"=>$contas_saldo_listar['saldo_total'],"saldo"=>$contas_saldo_listar['saldo'],"credito"=>$contas_saldo_listar['credito'],);
			}else{
				$retorno = array('situacao'=>2,'notificacao'=>SALDO);
			}
		}
		catch(Exception $e){
			$db->query('rollback');
			$retorno = array('situacao'=>0,'notificacao'=>$e->getMessage());
		}
		$db->close();
		$retorno = json_encode($retorno);
		echo $retorno;
	break;
	
	/*
	===========================================================================================
	COMPENSAR RECORRENTE
	===========================================================================================
	*/

	case "lancamentoRcrCompensar":
        $verificaPermissao = VerificaPermissaoUsuario(4);
        if($verificaPermissao['status'] == 2){
        echo json_encode($verificaPermissao);
        break;
        }

        try{
			$db->query('start transaction');
			$compensar = $lancamento->lancamentoRcrCompensar($db,$_REQUEST);
			$db->query('commit');
			if($compensar){
				
                //start: excluir após verificação do erro
                $lancamento->VerificarSaldo($db,$_REQUEST['conta_id'],$_REQUEST,$compensar['lancamento_id']);
                //end: excluir após verificação do erro

				$contas_saldo_listar = $lancamento->contasSaldoListar($db,$_REQUEST['conta_id'],$_SESSION['total_disponivel']);
				$retorno = array("situacao"=>1,"notificacao"=>LNCT_CMP,/*"lancamentos" => $lancamentos_listar,*/"contas_saldo"=>$contas_saldo_listar['contas_saldo'],"saldo_total"=>$contas_saldo_listar['saldo_total'],"saldo"=>$contas_saldo_listar['saldo'],"credito"=>$contas_saldo_listar['credito'],);
			}else{
				$retorno = array('situacao'=>2,'notificacao'=>SALDO);
			}
		}
		catch(Exception $e){
			$db->query('rollback');
			$retorno = array('situacao'=>0,'notificacao'=>ERRO);
		}
		$db->close();
		$retorno = json_encode($retorno);
		echo $retorno;
	break;

	/*
	===========================================================================================
	BOLETOS
	===========================================================================================
	*/
	
	case "boletosImprimir":
        try{
            $db->query('start transaction');
            $editar = $lancamento->lancamentoEditar($db,$_REQUEST);
            //$lancamentos_listar = $lancamento->lancamentosListar($db,$_REQUEST);
            $boleto_chave = $lancamento->boletosChaveGerar($db,$_REQUEST,$_SESSION['cliente_id']);
            $db->query('commit');
            $retorno = array("chave"=>$boleto_chave,"notificacao"=>LNCT_EDT);
        }catch(Exception $e){
            $db->query('rollback');
			$retorno = array('situacao'=>false,'notificacao'=>$e->getMessage());
        }
        $db->close();
        $retorno = json_encode($retorno);
        echo $retorno;
    break;

	case "boletosImprimirRcr":
        try{
            $db->query('start transaction');
            $boleto_chave = $lancamento->boletosChaveGerarRcr($db,$_REQUEST,$_SESSION['cliente_id']);
            //$lancamentos_listar = $lancamento->lancamentosListar($db,$_REQUEST);
            $db->query('commit');
            $retorno = array("chave"=>$boleto_chave,"notificacao"=>LNCT_EDT);
        }catch(Exception $e){
            $db->query('rollback');
			$retorno = array('situacao'=>false,'notificacao'=>$e->getMessage());
        }
		$db->close();
		$retorno = json_encode($retorno);
		echo $retorno;
	break;

	/*
	===========================================================================================
	EXIBIR
	===========================================================================================
	*/

	case "lancamentosExibir":
        $verificaPermissao = VerificaPermissaoUsuario(2);
        if($verificaPermissao['status'] == 2){
        echo json_encode($verificaPermissao);
        break;
        }

		$lancamento_dados = $lancamento->lancamentosExibir($db,$_REQUEST);
		$retorno = array("lancamento"=>$lancamento_dados['lancamento'],"ctr_plc_lancamentos"=>$lancamento_dados['ctr_plc_lancamentos'],"anexos"=>$lancamento_dados['anexos'],'cliente_id'=>$_SESSION['cliente_id']); //cliente_id é para mapear a pasta de arquivos do cliente
		$retorno = json_encode($retorno);
		$db->close();
		echo $retorno;
	break;

	/*
	===========================================================================================
	EXIBIR RECORRENTE
	===========================================================================================
	*/

	case "lancamentosRcrExibir":
        $verificaPermissao = VerificaPermissaoUsuario(2);
        if($verificaPermissao['status'] == 2){
        echo json_encode($verificaPermissao);
        break;
        }

		$lancamento_dados = $lancamento->lancamentosRcrExibir($db,$_REQUEST);
		$retorno = array("lancamento"=>$lancamento_dados['lancamento'],"ctr_plc_lancamentos"=>$lancamento_dados['ctr_plc_lancamentos']);
		$retorno = json_encode($retorno);
		$db->close();
		echo $retorno;
	break;

	/*
	===========================================================================================
	FILTRAR
	===========================================================================================
	*/

	case "lancamentosFiltrar":
		$retorno = Lancamento::lancamentosListar($db,$_REQUEST);
		$db->close();
		echo $retorno;
	break;

  	/*
	===========================================================================================
	FILTRAR
	===========================================================================================
	*/

	case "DataTableAjax":
		$retorno = Lancamento::DataTableAjax($db,$_REQUEST);
		$db->close();
		echo $retorno;
	break;

    /*
    case "DataTableAjaxTeste":
		
        $filtro = array(
            "dt_ativo" =>'mes',
            "dt_ini" => "01/03/2016",
            "dt_fim" => "31/03/2016",
            "tp_venc" =>"'av','v'",
            "conta_id" => "1,2",
            "tp_lnct" =>"'R','P','T'",
            "valor" =>"",
            "centro_resp_id" => "4",
            "plano_contas_id" =>"",		
            "favorecido_id" =>"",
            "documento_id" =>"",
            "forma_pgto_id" =>"",
            "parcelado" => false
        );
        
        $params = array(
            'filtro' => $filtro,
            'sSearch' => '',
            'sEcho' => '',
            'iDisplayStart' => 0,
            'iDisplayLength' => 10
            );

        $retorno = Lancamento::DataTableAjaxTeste($db,$params);
		$db->close();
		echo $retorno;
        break;
        */
	/*
	===========================================================================================
	RECIBO
	===========================================================================================
	*/
	
	case "recibo":
		require_once("../class/ValorExtenso.class.php");
		require_once("../../../php/MPDF/mpdf.php");
		$recibo = $lancamento->recibo($db,$_REQUEST);
		//$retorno = array("situacao"=>$recibo['situacao'],"recibo"=>$recibo['recibo']);
		//$retorno = json_encode($retorno);
		$db->close();
		//echo $retorno;
	break;

	/*
	===========================================================================================
	ANEXO EXCLUIR
	===========================================================================================
	*/
	
	case "anexoExcluir":
		Lancamento::anexoExcluir($db,'',$_REQUEST['anexo_id']);
		$db->close();
	break;

	/*
	===========================================================================================
	AUTO COMPENSAR
	===========================================================================================
	*/
	
	case "lnctAutoCompensar":
		require_once("../class/Recebimento.class.php");
		require_once("../class/Pagamento.class.php");
		require_once("../class/Transferencia.class.php");
		Lancamento::lnctAutoCompensar();
	break;

	//FATURA PRIME HALL - Essa função é chamada no arquivo sistema/modulos/cronjob/php pelo cronjob da hospedagem do sistema perfil contas
	//===========================================================================================
	
    case "FaturasGerarPrimeHall":
        require_once("../class/Recebimento.class.php");        
		Lancamento::FaturasGerarPrimeHall();
    break;

    case "anexo":
		$lancamento = new Lancamento();
        echo json_encode(array('anexos'=>$lancamento->anexosExibir($db,30)));
        echo '<br>';
        echo json_encode(array('arquivo'=>'MigraÃÃoWebFinanÃas.docx','tamanho'=>'0123kb'));
        echo '<br>';
        //echo $lancamento->anexosExibir($db,30);
		break;
		
	case "BlqLiberarLanc":
		$lancamento = new Lancamento();
		$retorno = $lancamento->BlqLiberarLanc($db, $_REQUEST);
		$db->close();
		$retorno = json_encode($retorno);
		echo $retorno;
		break;

}
?>
