   					<!-- Caixa de Dialogo Conta a Receber -->  
              <div id="dialog-finalizar-contrato" style="height: auto; padding: 0 20px 0 20px; font-weight: bold; font-size: 12px; text-align: center; vertical-align: middle;" title="Alerta">
             
                    <!-- Usual wizard with ajax -->
        <div class="widget">
            <div class="title"><img src="images/icons/dark/pencil.png" alt="" class="titleIcon" /><h6>Wizard with ajax form submit</h6></div>
			<form id="wizard1" method="post" action="submit.html" class="form">
                <fieldset class="step" id="w1first">
                    <h1>First step description</h1>
                    <div class="formRow">
                        <label>Username:</label>
                        <div class="formRight"><input type="text" name="username" id="username" /></div>
                    </div>
                    <div class="formRow">
                        <label>Password:</label>
                        <div class="formRight"><input type="password" name="pw" id="pw" /></div>
                    </div>
                    <div class="formRow">
                        <label>Email:</label>
                        <div class="formRight"><input type="text" name="mail" id="mail" /></div>
                    </div>
                </fieldset>
                <fieldset id="w1confirmation" class="step">
                    <h1>Second step description</h1>
                    <div class="formRow">
                        <label>Your city:</label>
                        <div class="formRight"><input type="text" name="city" id="city" /></div>
                    </div>
                    <div class="formRow">
                        <label>Something more:</label>
                        <div class="formRight"><input type="text" name="smth" id="smth" /></div>
                    </div>
                </fieldset>
				<div class="wizButtons"> 
                    <div class="status" id="status1"></div>
					<span class="wNavButtons">
                        <input class="basic" id="back1" value="Back" type="reset" />
                        <input class="blueB ml10" id="next1" value="Next" type="submit" />
                    </span>
				</div>
			</form>
			<div class="data" id="w1"></div>
        </div>
        
        <!-- Wizard with custom fields validation -->
        <div class="widget">
            <div class="title"><img src="images/icons/dark/pencil.png" alt="" class="titleIcon" /><h6>Wizard with custom fields validation</h6></div>
			<form id="wizard2" method="post" action="submit.html" class="form">
                <fieldset class="step" id="w2first">
                    <h1>First step description</h1>
                    <div class="formRow">
                        <label>Username:<span class="req">*</span></label>
                        <div class="formRight"><input type="text" name="bazinga" /></div>
                    </div>
                    <div class="formRow">
                        <label>Password:</label>
                        <div class="formRight"><input type="password" name="pw1" id="pw1" /></div>
                    </div>
                    <div class="formRow">
                        <label>Email:<span class="req">*</span></label>
                        <div class="formRight"><input type="text" name="email" /></div>
                    </div>
                </fieldset>
                <fieldset id="w2confirmation" class="step">
                    <h1>Second step description</h1>
                    <div class="formRow">
                        <label>Your city:</label>
                        <div class="formRight"><input type="text" name="city1" id="city1" /></div>
                    </div>
                    <div class="formRow">
                        <label>Something more:</label>
                        <div class="formRight"><input type="text" name="smth1" id="smth1" /></div>
                    </div>
                </fieldset>
				<div class="wizButtons"> 
                    <div class="status" id="status2"></div>
					<span class="wNavButtons">
                        <input class="basic" id="back2" value="Back" type="reset" />
                        <input class="blueB ml10" id="next2" value="Next" type="submit" />
                    </span>
				</div>
			</form>
			<div class="data" id="w2"></div>
        </div>
             
              </div><!-- Fim dialog --> 
