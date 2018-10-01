<?
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();
/**
 * Bitrix vars
 *
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 * @global CMain $APPLICATION
 * @global CUser $USER
 */
?>
<div class="kff-feedback">
<?if(!empty($arResult["ERROR_MESSAGE"])):
	foreach($arResult["ERROR_MESSAGE"] as $v)
		ShowError($v);
endif;

if(strlen($arResult["OK_MESSAGE"]) > 0):
	?><div class="kff-ok-text"><?=$arResult["OK_MESSAGE"]?></div><?
else:
?>

<form action="<?=$APPLICATION->GetCurPage()?>" method="POST">
<?=bitrix_sessid_post()?>
	<div class="kff-tab-form">

		<?if ($arParams["USE_FIELD_NAME"]=="Y"):?>
			<div class="kff-name"><input type="text" name="f_name" placeholder="Your Name*" value="<?=$arResult["NAME"]?>"<?if($arParams["CHECK_FIELD_NAME"]=="Y"):?> required="required"<?endif?>></div>
		<?endif;?>

		<?if ($arParams["USE_FIELD_PHONE"]=="Y"):?>
			<div class="kff-phone"><input type="text" name="f_phone" placeholder="Phone*" value="<?=$arResult["PHONE"]?>"<?if($arParams["CHECK_FIELD_PHONE"]=="Y"):?> required="required"<?endif?>></div>
		<?endif;?>

		<?if ($arParams["USE_FIELD_EMAIL"]=="Y"):?>
			<div class="kff-email"><input type="text" name="f_email" placeholder="E-mail*" value="<?=$arResult["EMAIL"]?>"<?if($arParams["CHECK_FIELD_EMAIL"]=="Y"):?> required="required"<?endif?>></div>
		<?endif;?>

		<?if ($arParams["USE_FIELD_TEXT"]=="Y"):?>
			<div class="kff-text"><textarea name="f_text" placeholder="Question" rows="3"<?if($arParams["CHECK_FIELD_TEXT"]=="Y"):?> required="required"<?endif?>><?=$arResult["TEXT"]?></textarea></div>
		<?endif;?>

		<?if($arParams["USE_CAPTCHA"] == "Y"):?>
		<div> <!-- class="ooove-capture" -->
			<!-- div class="kff-ctext"><?//=GetMessage("KFF_CAPTCHA")?></div -->
				<input type="hidden" name="captcha_sid" value="<?=$arResult["capCode"]?>">
				<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["capCode"]?>" width="180" height="40" alt="CAPTCHA">
			<!-- div class="kff-ctext"><?//=GetMessage("KFF_CAPTCHA_CODE")?></div -->
				<input type="text" name="captcha_word" size="30" maxlength="50" value="">
		</div>
		<?endif;?>

		<div style="padding:10px;background:#fff;border-radius:5px;margin-bottom:20px;">
<?$APPLICATION->IncludeComponent(
	"bitrix:main.userconsent.request", 
	".default", 
	array(
		"ID" => "1",
		"IS_CHECKED" => "Y",
		"AUTO_SAVE" => "N",
		"IS_LOADED" => "Y",
		"REPLACE" => array(
			"button_caption" => "Отправить",
			"fields" => array(
				0 => "Email",
				1 => "Телефон",
				2 => "Имя",
			),
		),
		"COMPONENT_TEMPLATE" => ".default",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO"
	),
	false
);?>
</div>

			<input type="hidden" name="PARAMS_HASH" value="<?=$arResult["PARAMS_HASH"]?>">
	<input type="submit" name="submit" value="<?=$arParams["SUBMIT_TITLE"]?>" class="btn btn-primary btn-xs" onclick="yaCounter41240939.reachGoal('CallBackFirst');">
	</div>
</form>
<?endif;?>
</div>