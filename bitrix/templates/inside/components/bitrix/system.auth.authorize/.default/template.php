<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="auth-wind login-window"><h1>ВХОД НА САЙТ</h1>
<?
ShowMessage($arParams["~AUTH_RESULT"]);
ShowMessage($arResult['ERROR_MESSAGE']);
?>
<form name="form_auth" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>">
	<input type="hidden" name="AUTH_FORM" value="Y" />
	<input type="hidden" name="TYPE" value="AUTH" />
	<?if (strlen($arResult["BACKURL"]) > 0):?>
		<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
	<?endif?>
	<?foreach ($arResult["POST"] as $key => $value){?>
		<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
	<?}?>
	<div class="form-input">
		<input type="text" name="USER_LOGIN" maxlength="50" value="<?=$arResult["LAST_LOGIN"]?>" placeholder="<?=GetMessage("AUTH_LOGIN")?>" />
	</div>	
	<div class="form-input">
		<input type="password" name="USER_PASSWORD" maxlength="50" placeholder="<?=GetMessage("AUTH_PASSWORD")?>" />
		<?if($arResult["SECURE_AUTH"]):?>			<span class="bx-auth-secure" id="bx_auth_secure" title="<?echo GetMessage("AUTH_SECURE_NOTE")?>" style="display:none">				<div class="bx-auth-secure-icon"></div>			</span>
			<noscript>				<span class="bx-auth-secure" title="<?echo GetMessage("AUTH_NONSECURE_NOTE")?>">
					<div class="bx-auth-secure-icon bx-auth-secure-unlock"></div>				</span>
			</noscript>
			<script>
				document.getElementById('bx_auth_secure').style.display = 'inline-block';
			</script>
		<?endif?>
	</div>
	<input type="submit" name="Login" value="<?=GetMessage("AUTH_AUTHORIZE")?>" class="btn red" />
	<?if ($arParams["NOT_SHOW_LINKS"] != "Y"){?>		<noindex>
			<?if($arResult["NEW_USER_REGISTRATION"] == "Y" && $arParams["AUTHORIZE_REGISTRATION"] != "Y"){?>
				<a href="<?=$arResult["AUTH_REGISTER_URL"]?>" rel="nofollow" class="btn gray"><?=GetMessage("AUTH_REGISTER")?></a>			<?}?>
			<a href="<?=$arResult["AUTH_FORGOT_PASSWORD_URL"]?>" rel="nofollow" class="forgot-link"><?=GetMessage("AUTH_FORGOT_PASSWORD_2")?></a>
		</noindex>	<?}?>
</form>
<script>
	<?if (strlen($arResult["LAST_LOGIN"])>0){?>		try{document.form_auth.USER_PASSWORD.focus();}catch(e){}
	<?}else{?>		try{document.form_auth.USER_LOGIN.focus();}catch(e){}	<?}?>
</script>
</div>