<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<form name="<?echo $arResult['FILTER_NAME']."_form"?>" action="<?echo $arResult['FORM_ACTION']?>" method="get" class="ts-form ts-filter">

	<?foreach($arResult['ITEMS'] as $arItem):
		if(array_key_exists("HIDDEN", $arItem)):
			echo $arItem['INPUT'];
		endif;
	endforeach;?>
	<?if(!empty($arParams['FILTER_TITLE'])):?>
	<?endif;?>
	<div class="ts-items">
		<?foreach($arResult['ITEMS'] as $arItem):?>
			<?if(!array_key_exists("HIDDEN", $arItem)):?>
				<?=$arItem['INPUT'];?>
			<?endif?>
		<?endforeach;?>
		
		<input type="hidden" name="set_filter" value="Y" />
		<button type="submit" name="set_filter" value="<?=GetMessage("IBLOCK_SET_FILTER")?>" class="btn red"><?=GetMessage("IBLOCK_SET_FILTER");?></button>
		<button type="submit" name="del_filter" value="<?=GetMessage("IBLOCK_DEL_FILTER")?>" class="btn grey"><?=GetMessage("IBLOCK_DEL_FILTER");?></button>
	</div>
</form>
