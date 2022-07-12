	<!-- Top fixed navigation -->
<!-- <div class="topNav">
    <div class="wrapper">
        <div class="userNav">
            <ul>
                <li><a href="#" title=""><img src="images/icons/topnav/mainWebsite.png" alt="" /><span>Main website</span></a></li>
                <li><a href="#" title=""><img src="images/icons/topnav/profile.png" alt="" /><span>Contact admin</span></a></li>
                <li><a href="#" title=""><img src="images/icons/topnav/messages.png" alt="" /><span>Support</span></a></li>
                <li><a href="login" title=""><img src="images/icons/topnav/settings.png" alt="" /><span>Settings</span></a></li>
            </ul>
        </div>
    </div>
</div> -->

<!-- Main content wrapper -->
<div class="loginWrapper">
    <div class="loginLogo" align="center"><img src="images/logo_webfinancas_fundo_branco.png" alt="" /></div>
    <div class="widget">
        <div class="title"><img src="images/icons/dark/logoff.png" alt="" class="titleIcon" /><h6>Login</h6></div>
        <form id="formLogin"  class="form">
						<input type="hidden" name="funcao" value="login"/>
            <fieldset>
                <div class="formRow">
                    <label for="login">E-mail:</label>
                    <div class="loginInput"><input type="email" name="email" class="required" id="email" autocapitalize="off"/></div>
                </div>
                
                <div class="formRow">
                    <label for="pass">Senha:</label>
                    <div class="loginInput"><input type="password" name="senha" class="required" id="senha" /></div>
                </div>
                
                <div class="loginControl">
                    <div class="rememberMe"><a href="javascript://void(0);" onClick="senhaRecuperar();">Esqueci minha senha</a></div>
                    <input type="button" value="Entrar" class="greenB logMeIn" onclick="login();"/>
                </div>
            </fieldset>
        </form>
    </div>
</div>    

