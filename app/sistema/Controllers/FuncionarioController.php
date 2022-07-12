<?php

require_once(ROOT_SISTEMA.'Models/Funcionario.php');

/**
 * FuncionarioController short summary.
 *
 * FuncionarioController description.
 *
 * @version 1.0
 * @author Fabio
 */
class FuncionarioController
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
        $funcionario = mysql_fetch_assoc(mysql_query('select f1.*, f2.nome as funcao from funcionarios f1 join func_funcoes f2 on f1.funcao_id = f2.id where f1.id = "'.$params['id'].'"', $this->db->link_id));

        if($funcionario['dt_nasc']!='')
            $funcionario['dt_nasc'] = DataBase::sql_to_data($funcionario['dt_nasc']);

        if($funcionario['rg_dt_emissao']!='')
            $funcionario['rg_dt_emissao'] = DataBase::sql_to_data($funcionario['rg_dt_emissao']);

        if($funcionario['pis_dt_inscricao']!='')
            $funcionario['pis_dt_inscricao'] = DataBase::sql_to_data($funcionario['pis_dt_inscricao']);

        if($funcionario['carteira_dt_emissao']!='')
            $funcionario['carteira_dt_emissao'] = DataBase::sql_to_data($funcionario['carteira_dt_emissao']);
        
        if($funcionario['dt_exame_admissional']!='')
            $funcionario['dt_exame_admissional'] = DataBase::sql_to_data($funcionario['dt_exame_admissional']);

        if($funcionario['dt_admissao']!='')
            $funcionario['dt_admissao'] = DataBase::sql_to_data($funcionario['dt_admissao']);

        if($funcionario['dt_demissao']!='')
            $funcionario['dt_demissao'] = DataBase::sql_to_data($funcionario['dt_demissao']);

        if($funcionario['salario']!='')
            $funcionario['salario'] = DataBase::valorFormat($funcionario['salario']);

        echo Util::array_to_json($funcionario);
    }

    /**
     * Criar funcionário
     * @param mixed $params
     */
    function Create($params){
        $funcionario = false;

        if($params['cpf']!=''){
            $funcionario = mysql_fetch_assoc(mysql_query('select id from funcionarios where cpf = "'.$params['cpf'].'"', $this->db->link_id));
            $funcionarioId = $funcionario['id'];
        }
        
        if($funcionario)
            echo Util::array_to_json(array('status' => 2, 'msg' => 'CPF já cadastrado.', 'funcionario_id' => $funcionario['id']));
        else{
            $funcionario = new Funcionario($params);

            $funcionarioId = $this->db->query_insert('funcionarios', $funcionario->fields);
            
            echo Util::array_to_json(array('status' => 1, 'msg' => 'Funcionário cadastrado com sucesso', 'funcionario_id' => $funcionarioId));


            //Envio de email para a contabilidade.
            $cliente_id = $_SESSION['cliente_id'];
            self::ListaEnvio($cliente_id);
        }
    }

    /**
     * Editar funcionario
     * @param mixed $params 
     */
    function Edit($params){
        
        $funcionario = mysql_fetch_assoc(mysql_query('select id from funcionarios where id = '.$params['id'], $this->db->link_id));
        
        if($funcionario){

            $funcionario = new Funcionario($params);

            $this->db->query_update('funcionarios', $funcionario->fields, "id = ".$params['id']);

            //if($editar['situacao']==1)
                echo Util::array_to_json(array('status' => 1, 'msg' => 'Funcionário atualizado com sucesso'));
            //else
              //  echo Util::array_to_json(array('status' => 0, 'msg' => 'Erro ao atualizar funcionario.'));

            //Envio de email para a contabilidade.
            $cliente_id = $_SESSION['cliente_id'];
            self::ListaEnvio($cliente_id);

        }else
            echo Util::array_to_json(array('status' => 0, 'msg' => 'Funcionário não encontrado.'));
    
    }

    /**
     * Excluir funcionario
     * @param mixed $params
     */
    function Delete($params){
        $this->db->query("delete from funcionarios where id = ".$params['id']);
        echo Util::array_to_json(array('status' => 1, 'msg' => 'Funcionário excluído com sucesso'));
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

        //$iTotalDisplayRecords = $this->db->numRows('select id from funcionarios');
        
        //Busca lançamentos que serão exibidos
        $aaData = array();
        
        if($sSearch==""){

            $queryFuncionario = "select id, nome, tel01, tel02, email01 from funcionarios";

        }else{
            
            $queryFuncionario = "select id, nome, tel01, tel02, email01 from funcionarios where nome like '%".$sSearch."%'";
        }
		
        $iTotalDisplayRecords = mysql_num_rows(mysql_query($queryFuncionario, $this->db->link_id));

        $queryFuncionario = mysql_query($queryFuncionario.' order by nome limit '.$iDisplayStart.",".$iDisplayLength, $this->db->link_id);

        while($funcionario = mysql_fetch_assoc($queryFuncionario)){
            /*
            $dadosFuncionario = '
            <div class="fluid">
            <div class="formRow">
                <span class="span9">
	                <a href="javascript://void(0);" style="cursor: default;" original-title="Funcionario" class="tipS" ><strong >'.$funcionario['nome'].'</strong></a>
                </span>											
															
                <span class="span3">
                    <a href="'.$funcionario['id'].'" title="Excluír" class="smallButton redB btTBwf tipS excluir-funcionario" id="link-exc-'.$funcionario['id'].'"><img src="images/icons/light/close.png" width="10"></a>										
                    <a href="'.$funcionario['id'].'" title="Editar" class="smallButton greyishB btTBwf tipS exibir-funcionario"><img src="images/icons/light/pencil.png" width="10"></a>
                </span>
            </div>
            <div class="formRow">
                <span class="span9">
                    <div class="tab_tel_cel"><img src="images/icons/dark/phone.png" style="margin-bottom:-3px;"> &nbsp;'.$funcionario['tel01'].' </div>	
			        <div class="tab_tel_cel"><img src="images/icons/dark/phone3.png" style="margin-bottom:-3px;"> &nbsp;'.$funcionario['tel02'].' </div>
			        <div class="tab_tel_cel"><img src="images/icons/dark/mail.png" style="margin-bottom:-3px;"> &nbsp;'.$funcionario['email01'].' </div>
                </span>
                <span class="span3">
                    <select>
                        <option>+ Opções</option>
                    </select>
                </span>
            </div>
            </div>
            ';
            */
            
            $dadosFuncionario = '
                <span class="lDespesa tbWF" >
	                <a href="javascript://void(0);" style="cursor: default;" original-title="Funcionario" class="tipS" ><strong>'.$funcionario['nome'].'</strong></a>
		                <span style="padding-top:5px;">	
			                <div class="tab_tel_cel"><img src="images/icons/dark/phone.png" style="margin-bottom:-3px;"> &nbsp;'.$funcionario['tel01'].' </div>	
			                <div class="tab_tel_cel"><img src="images/icons/dark/phone3.png" style="margin-bottom:-3px;"> &nbsp;'.$funcionario['tel02'].' </div>
			                <div class="tab_tel_cel"><img src="images/icons/dark/mail.png" style="margin-bottom:-3px;"> &nbsp;'.$funcionario['email01'].' </div>
		                </span>
                </span>											

                <div class="tbWFoption">
                    <a href="'.$funcionario['id'].'" title="Excluír" class="smallButton redB btTBwf tipS excluir-funcionario" id="link-exc-'.$funcionario['id'].'"><img src="images/icons/light/close.png" width="10"></a>										
                    <a href="'.$funcionario['id'].'" title="Editar" class="smallButton greyishB btTBwf tipS exibir-funcionario"><img src="images/icons/light/pencil.png" width="10"></a>
                    <select class="mais-opcoes" data-funcionario-id="'.$funcionario['id'].'" data-funcionario-nome="'.$funcionario['nome'].'" style="width:100px;position:relative;top:5px;right:35px">
                        <option value="0">+ Opções</option>
                        <option value="1">Faltas</option>
                        <option value="2">Horas Extras</option>
                        <option value="3">Alteração Salarial</option>
                        <option value="4">Alteração De Função</option>
                        <option value="5">Contribuições Sindicais</option>
                        <option value="6">Afastamentos</option>
                        <option value="7">Férias</option>
                        <option value="8">Dependentes</option>
                    </select>
                </div>
            ';
            
            array_push($aaData,array('funcionario'=>$dadosFuncionario));
        }

        $retorno = array('sEcho'=>$sEcho,'iTotalRecords'=>$iTotalDisplayRecords,'iTotalDisplayRecords'=>$iTotalDisplayRecords,'aaData'=>$aaData);
        
        echo json_encode($retorno);
        
    }




    
    /*
	================================================================================================
	INCLUSÃO NO SERVIÇO DE ENVIO DE EMAIL
	================================================================================================
	*/

    function ListaEnvio($cliente_id){
        
         $db_w2b = new Database('mysql.web2business.com.br','web2business','W2BSISTEMAS','web2business');  

         $cliente = $db_w2b->fetch_assoc('SELECT nome, email, parceiro_id FROM clientes WHERE id ='.$cliente_id);
         
         $contador = $db_w2b->fetch_assoc('SELECT nome, email_fin, logo_recibo FROM clientes WHERE id ='.$cliente['parceiro_id']);         
         /*=======================================================================================*/
        
           

         $enderecoArquivos = 'http://www.webfinancas.com/site/img/email_paginas/';
         if(!empty($contador['logo_recibo'])){ $logo = 'http://www.webfinancas.com/sistema/'.$contador['logo_recibo']; }else { $logo = 'http://www.webfinancas.com/site/img/logo_webfinancas_fundo_branco.png'; }

        $mensagem = '<style>
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
			
				    <td width="60%" align="center"><img src="'.$logo.'" align="middle" class="logo" /> </td>      
					<td width="40%"></td>
			
			    </tr>
			    <tr>
			
			    <td width="60%" align="justify" valign="top">
			
				    <br />
				
			     <h2> Olá, </h2>
				    <p style="line-height:20px;">O cliente <b>'.$cliente['nome'].'</b> solicitou a admissão de funcionário.<br>
                                                 Acesse o menu <b>solicitações</b> para visualizar os dados do funcionário. 

				    <br><br>	
				    Atenciosamente,
				    <br>	
				
					    <b>'.$contador['nome'].'</b>	
			    </p>
						
			    </td>    
			    <td width="40%" align="center" valign="bottom"> 
				     <img src="'.$enderecoArquivos.'documento.png" align="center" class="img" width="215" />	
			     </td>
			 
		    </tr>
	    </table>';

        $dadosEmail['Sistema'] = 'WebFinanças - Admissão de funcionário';
        $dadosEmail['ClienteId'] = $cliente_id;

        $dadosEmail['nomeRemetente'] = $cliente['nome'];
        $dadosEmail['emailRemetente'] = $contador['email_fin'];
        $dadosEmail['nomeDestinatario'] = $contador['nome'];
        $dadosEmail['emailDestinatario'] = $contador['email_fin'];
        $dadosEmail['assunto'] = 'Admissão de funcionário';
        $dadosEmail['mensagem'] = $mensagem;        
        $dadosEmail['situacao'] = 0;

         if(!empty($dadosEmail['emailDestinatario'])){
            $db_w2b->query_insert('servico_envio_email', $dadosEmail, $db_w2b->link_id);
        }

        //$db_w2b->close();

    }   


}


?>