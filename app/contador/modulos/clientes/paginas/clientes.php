<!-- Título -->
    <div class="titleArea">
        <div class="wrapper">
            <div class="pageTitle">
                <h2>Clientes</h2>
               <!-- <span>Do your layouts deserve better than Lorem Ipsum.</span> -->
            </div>

	     </div>
    </div>    
    <!-- Fim título -->  
 
	<div class="wrapper">
      <!--
      <span class="line">
      </span>
      -->
      <div class="divider">
      	<span></span>
      </div>
    </div>
     
<!-- main content wrapper -->
<div class="wrapper">
         
    <br />

	<a href="#" title="" class="button greenB" style="margin: 5px;" onclick="addDialog();"><img src="../../sistema/images/icons/light/users.png" alt="" class="icon"><span>Novo cliente</span></a>
     
    <!-- =================== Palco =================== -->
           
     <!-- Organiza o layout -->


      <div class="fluid">

        <div class="span12">
	
			<!-- Dynamic table -->
  <div class="widget">
     <!-- <div class="title"><img src="images/icons/dark/money2.png" alt="" class="titleIcon" /><h6><span class="blue"> Lançamentos </span> </h6></div> -->
      <div id="mensagens">

		<table class="display dTableInformativo" id="dTableInfo">
		    <tbody>
		    </tbody>
		</table>

      </div>
  </div>


              </div>

            </div>

      </div>
       

       
    </div> <!-- Fim Fluid -->
     
 
    <!-- ====== Fim do Palco ====== -->
 
    <!-- ====== *** UI Dialogs *** ====== -->

    <?php include("dialog_clientes.php"); ?>
  
    <!-- ====== *** Fim UI Dialogs *** ====== -->
  
</div>



