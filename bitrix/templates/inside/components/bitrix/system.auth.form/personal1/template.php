<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if ($arResult["FORM_TYPE"] == "login"):?>
<div class="auth-wind login-window">	<h1>бунд мю яюир</h1>
	<form method="post" action="<?=$arResult["AUTH_URL"]?>">
		<?if (strlen($arResult["BACKURL"]) > 0){?>
			<input type='hidden' name='backurl' value='<?=$arResult["BACKURL"]?>' />
		<?}?>
		<?foreach ($arResult["POST"] as $key => $value){?>
			<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
		<?}?>
		<input type="hidden" name="AUTH_FORM" value="Y" />
		<input type="hidden" name="TYPE" value="AUTH" />		<div class="form-input">			<input type="text" name="USER_LOGIN" maxlength="50" value="<?=$arResult["USER_LOGIN"]?>" placeholder="<?=GetMessage("AUTH_LOGIN")?>" />		</div>				<div class="form-input">			<input type="password" name="USER_PASSWORD" maxlength="50" placeholder="<?=GetMessage("AUTH_PASSWORD")?>" />				</div>		<input type="submit" name="Login" value="<?=GetMessage("AUTH_LOGIN_BUTTON")?>" class="btn red" />
		<?if($arResult["NEW_USER_REGISTRATION"] == "Y"){?>			<a href="<?=$arResult["AUTH_REGISTER_URL"]?>" rel="nofollow" class="btn gray"><?=GetMessage("AUTH_REGISTER")?></a>
		<?}?>				<a href="<?=$arResult["AUTH_FORGOT_PASSWORD_URL"]?>" rel="nofollow" class="forgot-link"><?=GetMessage("AUTH_FORGOT_PASSWORD_2")?></a>
	</form>
</div>
	<a href="<?=$arResult["AUTH_REGISTER_URL"]?>" onclick="return ShowLoginForm();"><?=GetMessage("AUTH_LOGIN_BUTTON")?></a>
<?else:?>
	<a href="<?=$APPLICATION->GetCurPageParam("logout=yes", Array("logout"))?>"><?=GetMessage("AUTH_LOGOUT_BUTTON")?></a>
<?endif?>