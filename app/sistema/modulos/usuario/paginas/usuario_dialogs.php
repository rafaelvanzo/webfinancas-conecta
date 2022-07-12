<!-- start: dialog usuário -->
<div id="dialog-usuario" style="padding:0px;display:none;" title="">
    <form id="form-usuario" class="dialog"> 
        <input type="hidden" id="form-funcao" name="funcao" value="" />
        <input type="hidden" id="usuario-id" name="id" value="" />
        <div class="fluid">

            <!--
            <div class="nNote nWarning hideit" style="display:none;">
                <p></p>
            </div>
            -->

            <div class="formRow">
                    <span class="span12">
                    <label>Nome:</label>
                    <input type="text" name="nome" class="required" id="nome"/>
                    </span>
                </div>
            <div class="formRow">   
                <span class="span12">
                    <label>E-mail:</label>
                    <input type="text" name="email" class="required" id="email"/>
                    </span>
            </div>
            <div class="formRow" id="div-senha">
                <span class="span12">
                    <label>Senha:</label>
                    <input type="password" name="senha" class="required" id="senha-novo-usuario"/>
                    </span>
            </div>
            <div class="formRow">
                <span class="span12">
                    <label>Grupo:</label>
                    <select name="grupo_id" class="required" id="grupo">
                    </select>
                    </span>
            </div>
            <div class="formRow">
                    <span class="span12">
                    <label>Situação:</label>
                    <select name="situacao" id="situacao">
                        <option value="1">Ativo</option>
                        <option value="0">Inativo</option>
                    </select>
                    </span>
                </div>
            <br />
        </div>
    </form>
</div>
<!-- end: dialog usuário -->

<!-- start: dialog grupo -->
<div id="dialog-grupo" style="padding:0px;display:none;" title="">
    <form id="form-grupo" class="dialog"> 
        <input type="hidden" id="form-grupo-funcao" name="funcao" value="" />
        <input type="hidden" id="grupo-id" name="id" value="" />
        <div class="fluid">

            <!--
            <div class="nNote nWarning hideit" style="display:none;">
                <p></p>
            </div>
            -->

            <div class="formRow">
                <span class="span12">
                <label>Nome:</label>
                <input type="text" name="nome" class="required" id="nome-grupo"/>
                </span>
            </div>

            <table class="sTable table-hover" id="tbl-modulos" style="width:100%">
                <thead>
                    <tr>
                        <th>Módulos</th>
                        <th>Ler</th>
                        <th>Incluír</th>
                        <th>Editar</th>
                        <th>Excluír</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    /*
                    $dbWf = new Database("mysql.webfinancas.com","webfinancas","W2BSISTEMAS","webfinancas");
                    $modulos = $dbWf->fetch_all_array('select id, nome from sis_modulos where sistema_id = 1 order by id');
                    var_dump($modulos);
                    foreach($modulos as $modulo){
                        $nomeModulo = $modulo['nome'];
                        $permissoesId = '';
                        $permissoes = $dbWf->fetch_all_array('select id, nome from sis_permissoes where modulo_id = '.$modulo['id']);
                            foreach($permissoes as $permissao){
                                $permissoesId .= '<td><input type="checkbox" value="'.$permissao['id'].'"/></td>';
                            }
                        echo '<tr><td>'.$nomeModulo.'</td>'.$permissoesId.'</tr>';
                    }
                    */
                    ?>
                </tbody>
		    </table>

            <br />
        </div>
    </form>
</div>
<!-- end: dialog grupo -->