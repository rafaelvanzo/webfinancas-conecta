<?php

require_once(ROOT_SISTEMA.'Models/Arquivo.php');

/**
 * ArquivoController short summary.
 *
 * ArquivoController description.
 *
 * @version 1.0
 * @author Fabio
 */
class ArquivoController
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
     * Summary of Details
     * @param mixed $params 
     */
    function Details($params){
        $arquivo = mysql_fetch_assoc(mysql_query('select * from lnct_anexos where id = "'.$params['id'].'"', $this->db->link_id));

        if($arquivo['dt_cadastro']!='')
            $arquivo['dt_cadastro'] = DataBase::sql_to_data($arquivo['dt_cadastro']);

        echo Util::array_to_json($arquivo);
    }

    /**
     * Criar funcionário
     * @param mixed $params
     */
    function Create($params){
        $arquivo = new Arquivo($params);

        $arquivoId = $this->db->query_insert('lnct_anexos', $arquivo->fields);
            
        echo Util::array_to_json(array('status' => 1, 'msg' => 'Arquivo cadastrada com sucesso', 'falta_id' => $arquivoId));
    }

    /**
     * Editar falta
     * @param mixed $params 
     */
    function Edit($params){
        
        $arquivo = mysql_fetch_assoc(mysql_query('select id from lnct_anexos where id = '.$params['id'], $this->db->link_id));
        
        if($arquivo){

            $arquivo = new Arquivo($params);

            $this->db->query_update('lnct_anexos', $arquivo->fields, "id = ".$params['id']);

            //if($editar['situacao']==1)
                echo Util::array_to_json(array('status' => 1, 'msg' => 'Arquivo atualizada com sucesso'));
            //else
              //  echo Util::array_to_json(array('status' => 0, 'msg' => 'Erro ao atualizar falta.'));

        }else
            echo Util::array_to_json(array('status' => 0, 'msg' => 'Arquivo não encontrada.'));
    
    }

    /**
     * Excluir falta
     * @param mixed $params
     */
    function Delete($params){
        $this->db->query("delete from lnct_anexos where id = ".$params['id']);
        echo Util::array_to_json(array('status' => 1, 'msg' => 'Arquivo excluída com sucesso'));
    }

    /**
     * Summary of DataTable
     * @param mixed $db
     * @param mixed $params
     * @return string
     */
    function DataTable($params){

        //filtro do data table
        $sSearch = $params["sSearch"];
        $sEcho = $params["sEcho"];
        $iDisplayStart = $params["iDisplayStart"];
        $iDisplayLength = $params["iDisplayLength"];
        //$iTotalRecords = $db->numRows('select id from lancamentos');
        $iTotalDisplayRecords = 0;
        
        //start: período
        $mes = $params['mes'];
        $ano = $params['ano'];
        //$dtIni = $ano.'-'.$mes.'-01';
        //$dtIni2 = date('Y-m-d', strtotime('-1 month', strtotime($dtIni)));
        //$dtFim = date('Y-m-d', strtotime('+1 month', strtotime($dtIni)));
        //end: período

        //Busca lançamentos que serão exibidos
        $aaData = array();
        
        if($sSearch==""){ //(dt_visualizacao >= '".$dtIni2."' and dt_visualizacao < '".$dtFim."') and 

            $queryArquivo = "select l.*, c.nome as classificacao, t.nome as tp_documento
                            from lnct_anexos l 
                            join arq_classificacao c on l.classificacao_id = c.id 
                            join arq_tp_documento t on l.tp_documento_id = t.id
                            where (  MONTH(dt_competencia) = '".$params['mes']."' AND YEAR(dt_competencia) = '".$params['ano']."' and dt_visualizacao <= '".date('Y-m-d')."' )";
        }else{
            
            $queryArquivo = "select l.*, c.nome as classificacao, t.nome as tp_documento
                            from lnct_anexos l 
                            join arq_classificacao c on l.classificacao_id = c.id 
                            join arq_tp_documento t on l.tp_documento_id = t.id
                            where ( MONTH(dt_competencia) = '".$params['mes']."' AND YEAR(dt_competencia) = '".$params['ano']."' and dt_visualizacao <= '".date('Y-m-d')."' )
                              and (l.nome_arquivo_org like '%".$sSearch."%' || c.nome like '%".$sSearch."%' || t.nome like '%".$sSearch."%')";
        }
		
        $iTotalDisplayRecords = mysql_num_rows(mysql_query($queryArquivo, $this->db->link_id));

        $queryArquivo = mysql_query($queryArquivo.' order by l.id desc limit '.$iDisplayStart.",".$iDisplayLength, $this->db->link_id);

        while($arquivo = mysql_fetch_assoc($queryArquivo)){
            
            $dtCompetencia = substr($this->db->sql_to_data($arquivo['dt_competencia']),3);
            $dtCadastro = $this->db->sql_to_data($arquivo['dt_cadastro']);
            $visualizado = ($arquivo['visualizado']==1)? 'Sim' : 'Não';
            $visualizadoMobile = ($arquivo['visualizado']==1)? 'Visualizado' : 'Não visualizado';
            $opcoes = '<a href="" title="Download" class="smallButton greyishB download" data-nome="'.$arquivo['nome_arquivo'].'" data-nome-org="'.$arquivo['nome_arquivo_org'].'" data-id="'.$arquivo['id'].'"><img src="images/icons/light/download.png" width="10"></a>';

            $nomeArquivo = '<b>'.$arquivo['nome_arquivo_org'].'</b>';

            $nomeArquivoMobile = '<span class="span-file-mobile" style="float:left;width:55%;word-wrap:break-word;text-align:left;">'.
                $nomeArquivo.' <br>'.
                $opcoes.
                '</span>';

            $dadosArquivoMobile = '<span class="span-file-mobile" style="float:right;width:40%;text-align:left;">'.
                'Cadastro: '.$dtCadastro.' <br>'.
                'Vencimento: '.$dtCompetencia.' <br>'.
                $visualizadoMobile.' <br>'.
                $arquivo['tp_documento'].' <br>'.
                $arquivo['classificacao'].
                '</span>';

            $mobile = $nomeArquivoMobile.
                $dadosArquivoMobile;

            array_push($aaData,array(
                'arquivo'=>$nomeArquivo,
                'tp_documento'=>$arquivo['tp_documento'],
                'classificacao'=>$arquivo['classificacao'],
                'visualizado'=>$visualizado,
                'dt_cadastro'=>$dtCadastro,
                'dt_competencia'=>$dtCompetencia,
                'opcoes'=>$opcoes,
                'mobile'=>$mobile));
        }

        $retorno = array('sEcho'=>$sEcho,'iTotalRecords'=>$iTotalDisplayRecords,'iTotalDisplayRecords'=>$iTotalDisplayRecords,'aaData'=>$aaData);
        
        echo Util::array_to_json($retorno);
        
    }

    /**
     * Summary of ExcluirArquivo
     * @param mixed $db
     * @param mixed $params
     */
    function ExcluirArquivo($db,$params){
		if($params['lancamento_id']!=''){
			$anexos = $db->fetch_all_array("select id, nome_arquivo from lnct_anexos where lancamento_id = ".$params['lancamento_id']);
			foreach($anexos as $anexo){
				$arquivo = "../../../php/uploads/".$anexo["nome_arquivo"];
				if(file_exists($arquivo)){
					unlink($arquivo);
				}
				$db->query("delete from lnct_anexos where id = ".$anexo["id"]);
			}
		}elseif($params['arquivo_id']!=''){
			$anexo = $db->fetch_assoc('select nome_arquivo from lnct_anexos where id = '.$params['arquivo_id']);
			$arquivo = "../../../php/uploads/".$anexo["nome_arquivo"];
			if(file_exists($arquivo)){
				unlink($arquivo);
			}
			$db->query("delete from lnct_anexos where id = ".$params['arquivo_id']);
		}
	}

    /**
     * Summary of DownloadArquivo
     * @param mixed $params
     */
    function DownloadArquivo($params){
        //$arquivo = $this->db->fetch_assoc('select nome_arquivo, nome_arquivo_org where id = '.$params['id']);
        $this->db->query('update lnct_anexos set visualizado = 1 where id = '.$params['documento_id']);
        header('Content-Type: application/force-download');
        header('Content-Disposition:attachment;filename="'.$params['nome_org'].'"');
        readfile(ROOT_SISTEMA.'uploads/cliente_'.$_SESSION['cliente_id'].'/'.$params['nome']);
        
        //header("Content-Disposition:attachment;filename='teste.pdf'");
        //readfile("../php/uploads/cliente_134/134_comprovanteCludia04032015_1.pdf");
    }
}


?>