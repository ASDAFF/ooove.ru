<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="auth-wind login-window"><h1>Забыли пароль?</h1>
<?ShowMessage($arParams["~AUTH_RESULT"]);?>
<form name="bform" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>">
<?if (strlen($arResult["BACKURL"]) > 0){?>	<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" /><?}?>
	<input type="hidden" name="AUTH_FORM" value="Y">
	<input type="hidden" name="TYPE" value="SEND_PWD">
	<div class="form-input"><input type="text" name="USER_LOGIN" maxlength="50" value="<?=$arResult["LAST_LOGIN"]?>" placeholder="<?=GetMessage("AUTH_LOGIN")?>" /></div>		<div class="form-input"><input type="text" name="USER_EMAIL" maxlength="255" placeholder="E-Mail" /></div>
	<input type="submit" name="send_account_info" value="<?=GetMessage("AUTH_SEND")?>" class="btn red" />
	<a href="<?=$arResult["AUTH_AUTH_URL"]?>" class="btn gray"><?=GetMessage("AUTH_AUTH")?></a>
</form>
<script>
	document.bform.USER_LOGIN.focus();</script>
</div>