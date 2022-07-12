<!-- <script> alert(window.innerWidth); </script> -->

 <!-- Título -->
    <div class="titleArea">
        <div class="wrapper">
            <div class="pageTitle">
                <h2>Custeio</h2>
               <!-- <span>Do your layouts deserve better than Lorem Ipsum.</span> -->
            </div>
        </div>
    </div>    
    <!-- Fim título -->  


	  <div class="wrapper">

      <div class="divider">
      	<span></span>
      </div>
    </div>

    <br />


    
<!-- Main content wrapper -->
<div class="wrapper">
    

<div class="fluid">
 
        <div class="span9">
      

            <a href="javascript://" title="" class="button blueB" id="open-modal-lancamentos" style="margin: 5px;" ><span>Composição do custeio</span></a>

            <a href="#" title="" class="button greenB" style="margin: 5px;" id="open-modal-custeio"><img src="images/icons/light/add.png" alt="" class="icon"/><span>Novo Custeio</span></a>

            <a href="http://cbhpo.com.br/downloads/planilhaCBHPO.xlsx" title="" class="button greyishB" style="margin: 5px;" id="open-modal-custeio" target="_blank"><span>Tabela de custo CHBPO</span></a>

            <input name="mes" id="mes" type="text" class="monthpicker" value="<?php echo date('m/Y')?>" readonly style="width:100px;text-align:center;">

            <a href="javascript://void(0);" title="" class="button basic" id="btn-pesquisar" style="position:relative;top:10px;width:38px;"><img src="images/icons/dark/magnify.png" alt="" class="icon"></a>
            
            </div>


 
        <div class="span3">

                    <div class="widget" style="margin-top:17px;">

                            <div class="formRowB">
                                <h5>Custo Total : <span class="total"></span></h5>
                                <h5>Custo / hora : <span class="horas"></span></h5>
                                <h5>Custo / dia : <span class="dias"></span></h5>
                                <h5>Custo / minuto : <span class="minutos"></span></h5>                               
                            </div>

                    </div>

        </div>

</div>


    <!-- filtro -->
    <div class="fulid"></div>

    <!-- Tabela de honorários -->
    <div class="widget">
        <div id="div-honorarios">
            <table class="display" id="dTable-custeio">
                <thead>
                    <tr>
                        <th align="left" width="auto">Nome</th>
                        <th  align="center" width="20">Tempo</th>
                        <th  align="center" width="130">Tipo de calculo</th>
                        <th  align="right" width="120">Custo Fixo</th>
                        <th  align="right" width="120">Custo Variável</th>
                        <th  align="right" width="120">Total</th>
                        <th  align="right" width="120">valor práticado </th>
                        <th  align="right" width="70">Opções</th>
                    </tr>
                </thead>
                <tbody></tbody>
		    </table>
        </div>
    </div>
 
</div> 


    <!-- ====== *** UI Dialogs *** ====== -->

    <?php include("Dialogs.php"); ?>

    <!-- ====== *** Fim UI Dialogs *** ====== -->
