 <!-- start: título -->
    <div class="titleArea">
        <div class="wrapper">
            <div class="pageTitle">
                <h2>Usuários</h2>
            </div>
        </div>
    </div>    
    <!-- end: título -->  

	<div class="wrapper">
        <div class="divider">
      	    <span></span>
        </div>
    </div>

    <br />

    <!-- Botões -->
    <div class="wrapper">        	    
        <a href="#" title="" class="button greenB" style="margin: 5px;" id="btn-novo-usuario"><img src="images/icons/light/add.png" alt="" class="icon"/><span>Novo Usuário</span></a>
        <a href="#" title="" class="button greenB" style="margin: 5px;" id="btn-novo-grupo"><img src="images/icons/light/add.png" alt="" class="icon"/><span>Novo Grupo</span></a>
    </div>

    
<!--   <div class="line"></div>
    
<!-- Main content wrapper -->
<div class="wrapper">
    
    <!-- Dynamic table -->
    <div class="widget">
        
        <div class="tab-bs">

            <ul class="nav nav-tabs" id="abas" style="margin-top:1px;">
                <li class="active"><a data-target="#aba-01" data-toggle="tab">Contas</a></li>
                <li><a data-target="#aba-02" data-toggle="tab">Grupos</a></li>
            </ul>

            <div class="tab-nav-divider"></div> <!-- divisão entre menu e conteúdo das abas -->

            <div class="tab-content">

                <!-- start: aba 1 -->
                <div class="tab-pane active" id="aba-01" style="width:auto;"> <!-- a classe tab-pane está com a largura fixa, precisa ser alterado no css -->

                    <div id="div-usuarios">
                        <table class="display" id="dTableUsuarios">
		                    <tbody>
		                    </tbody>
		                </table>
                    </div>

                </div>
                <!-- end: aba 1 -->
        
                <!-- start: aba 2 -->
                <div class="tab-pane" id="aba-02" style="width:auto;"> <!-- a classe tab-pane está com a largura fixa, precisa ser alterado no css -->
                
                    <div id="div-grupos">
                        <table class="display" id="dTableGrupos">
		                    <tbody>
		                    </tbody>
		                </table>
                    </div>
                
                </div>
                <!-- end: aba 2 -->

            </div>

        </div>
        
    </div>
 
    <!-- start: dialogs -->

    <?php include("usuario_dialogs.php"); ?>

    <!-- end: dialogs -->

</div> 

