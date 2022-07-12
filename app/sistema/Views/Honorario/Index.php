<!-- <script> alert(window.innerWidth); </script> -->

 <!-- Título -->
    <div class="titleArea">
        <div class="wrapper">
            <div class="pageTitle">
                <h2>Honorários</h2>
               <!-- <span>Do your layouts deserve better than Lorem Ipsum.</span> -->
            </div>
        </div>
    </div>    
    <!-- Fim título -->  
    

   <!-- Breadcrumbs
<div class="wrapper">  
   <div class="bc" style="margin:2px 0 0 0;">
            <ul id="breadcrumbs" class="breadcrumbs">
                 <li class="">
                      <a href="#">Geral</a>
	               </li>
                 <li class="current">
                      <a href="#">Funcionários</a>
                 </li>
            </ul>
	</div>  
</div> Fim Breadcrumbs -->

	  <div class="wrapper">
      <!--
      <span class="line">
      </span>
      -->
      <div class="divider">
      	<span></span>
      </div>
    </div>

    <br />

    <!-- Botões -->
    <!--
    <div class="wrapper">        	    
        <a href="#" title="" class="button greenB" style="margin: 5px;" id="open-modal-arquivo"><img src="images/icons/light/add.png" alt="" class="icon"/><span>Novo Arquivo</span></a>
    </div>
    
    <div class="line"></div>
    -->
    
<!-- Main content wrapper -->
<div class="wrapper">
    
    <!-- filtro -->
    <div class="fulid">

         <div class="span12">
            
            <input name="mes" id="mes" type="text" class="monthpicker" value="<?php echo date('m/Y')?>" readonly style="width:100px;text-align:center;">
            <a href="javascript://void(0);" title="" class="button basic" id="btn-pesquisar" style="position:relative;top:10px;width:38px;"><img src="images/icons/dark/magnify.png" alt="" class="icon"></a>
            
         </div>

    </div>

    <!-- Tabela de honorários -->
    <div class="widget">
        <div id="div-honorarios">
            <table class="display" id="dTable-honorarios">
                <thead>
                    <tr>
                        <th>Vencimento</th>
                        <th>Valor</th>
                        <th>Compensado</th>
                        <th>Visualizado</th>
                        <th>Opções</th>
                    </tr>
                </thead>
                <tbody></tbody>
		    </table>
        </div>
    </div>
 
</div> 
