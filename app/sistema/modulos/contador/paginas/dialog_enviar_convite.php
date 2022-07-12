<!-- Convite Contador -->

<div id="dialog-convite-contador" style="height: auto; padding:0; text-align: center; display:none;" title="Convidar contador">

  <form id="formConviteContador" action="#" class="dialog">

    <input type="hidden" name="funcao" value="conviteContador" />

    <div class="fluid">

      <div class="formRow">

          <div class="span12">
             <label>Digite o e-mail do seu contador:</label>
             <input type="text" name="destinatario_email" value="" placeholder="E-mail do cliente" required/>
             <input type="hidden" name="remetente_id" value="<?php echo $_SESSION['cliente_id']; ?>"/>
          </div>

      </div>

      <div class="formRow">

          <div class="span12">

            <div id="lista_conexoes" class="updates scroll" style="height:auto; max-height: 300px;">
                    <?php
                    $convites = '';

                    $lista = $db->fetch_all_array('select id, email, contador_id, cliente_id, conectado, date_format(dt_convite, "%d/%m/%Y") as dt_convite, date_format(dt_inicio, "%d/%m/%Y") as dt_inicio, date_format(dt_final, "%d/%m/%Y") as dt_final, remetente from conexao where cliente_id = 0 and conectado = 0 order by dt_convite');
                
                    if($lista == true){
                    
                        foreach($lista as $l){

                            $m = $l['mes'];

                            if($m == 01){ $mes = 'Jan';}
                            elseif($m == 02){ $mes = 'Fev';}
                            elseif($m == 03){ $mes = 'Mar';}
                            elseif($m == 04){ $mes = 'Abr';}
                            elseif($m == 05){ $mes = 'Mai';}
                            elseif($m == 06){ $mes = 'Jun';}
                            elseif($m == 07){ $mes = 'Jul';}
                            elseif($m == 08){ $mes = 'Ago';}
                            elseif($m == 09){ $mes = 'Set';}
                            elseif($m == 10){ $mes = 'Out';}
                            elseif($m == 11){ $mes = 'Nov';}
                            else{ $mes = 'Dez';}

                            $icone = 'icon_conectar02.png';
                            $nome = $l['email'];

                            if($l['remetente'] == 1){
                                $menu = '
                                    <a href="'.$_SESSION['cliente_id'].'-'.$l['contador_id'].'-'.$l['id'].'" data-convite-row-id="'.$l['id'].'" original-title="Cancelar" class="smallButton btTBwf redB tipS excluirConvite"><img src="images/icons/light/close.png" width="10"></a>
                                    <a href="'.$_SESSION['cliente_id'].'-'.$l['id'].'" original-title="Reenviar convite" class="smallButton btTBwf greenB tipS reenviarConvites" ><img src="images/icons/light/mail.png" width="10"></a>
                                ';
                                $title_convite = 'Convite enviado: '.$l['dt_convite'];
                                $cor_conexao = "green";
                                $conexao='Aguardando convidado'; 
                            }else{
                                $menu = ' 
                                    <a href="'.$l['contador_id'].'-'.$_SESSION['cliente_id'].'-'.$l['id'].'" data-convite-row-id="'.$l['id'].'" original-title="Cancelar" class="smallButton btTBwf redB tipS excluirConvite"><img src="images/icons/light/close.png" width="10"></a>
                                    <a href="'.$l['contador_id'].'-'.$_SESSION['cliente_id'].'-'.$l['id'].'" data-convite-row-id="'.$l['id'].'" original-title="Aceitar convite" class="smallButton btTBwf blueB tipS aceitarConvite"><img src="images/icons/light/check.png" width="10"></a>
                                ';
                                $title_convite = 'Convite recebido: '.$l['dt_convite'];
                                $cor_conexao = "blue";
                                $conexao = utf8_encode('Aguardando você');
                            }

                            $convites .= '
                                <div class="newUpdate tipN" original-title="'.$title_convite.'" id="convite-row-'.$l['id'].'">
                                    <span class="lDespesa" style="margin-left:-22px;">
                                        <a href="javascript://" class="" style="cursor: default; color:#333"><b>'.$nome.'<b/>
                                        </a>
                                        <span class="'.$cor_conexao.'">
                                            '.$conexao.'
                                        </span>
                                    </span>				 	
                                    '.$menu.'					 
                                </div>	
                            ';
                        
                        }

                        echo $convites;

                        //$db_w2b->close();
                        //$db->close();
                    
                    }/*else{
                
                    echo '<div align="center"> <p>Não existem convites.</p> </div>';
                
                    }*/
                    ?>
                </div>
          </div>
       </div>

    </div>  <!-- fluid --> 

  </form>

</div><!-- Fim dialog -->

<!-- Convite Contador -->