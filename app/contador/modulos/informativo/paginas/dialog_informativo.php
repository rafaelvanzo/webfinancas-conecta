<!-- Convite Contador -->

<div id="dialog-informativo" style="height: auto; padding:0; text-align: center; display:none;" title="Novo informativo">
  <form id="formInfo" action="#" class="dialog"> 
   <!-- <input type="hidden" id="campoId" name="id" value="" /> -->   
<div class="fluid">
                 
        <div class="formRow">   
             <span class="span6">
                <label>Título:</label>
                <input type="text" name="titulo" placeholder="Título" class="titulo required" />
             </span>         
            <span class="span2">
                <label>Data de Ínicio:</label>
                <input type="text" name="dt_inicio" class="dt_inicio datepicker maskDate required" placeholder="__/__/____" value="<?php echo date('d/m/Y'); ?>"/>
             </span>
             <span class="span2">
                <label>Data Final:</label>
                <input type="text" name="dt_final" class="dt_final datepicker maskDate" placeholder="__/__/____"/>
             </span>
             <span class="span2">
                <label>Situação:</label>
                <select name="situacao" class="situacao">
                    <option value="1">Ativo</option>
                    <option value="0">Inativo</option>
                </select>
             </span>     
         </div>
    <div class="linha" ></div>
        <div class="formRow">
            <span class="span12">
                <label>Descrição:</label>
               <textarea name="descricao" placeholder="Escreva o informativo" class="descricao required" style="text-align: justify; height:250px;"></textarea>
             </span>
        </div>
    
    </div>  <!-- fluid --> 

  </form>
</div><!-- Fim dialog --> 

<!-- Convite Contador -->