<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?if (is_array($arResult['ITEMS']) && count($arResult['ITEMS']) > 0):?>
<div class="catalog-list clearfix">
<?foreach ($arResult['ITEMS'] as $arItem):?>
	<div class="item">
		<a href="<?=$arItem['DETAIL_URL']?>">
			<div class="img-item">
				<?if($arItem['PICTURE']['SRC']):?><img src="<?=$arItem['PICTURE']['SRC']?>" alt="" /><?endif;?>
			</div>
			<span class="name-item"><?=$arItem['NAME']?></span>
		</a>
	</div>
<?endforeach;?>
</div>
<?endif;?>