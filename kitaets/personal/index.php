<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Персональный раздел");
?>
<div class="bx_page">
	<p>В личном кабинете Вы можете проверить текущее состояние корзины, ход выполнения Ваших заказов, просмотреть или изменить личную информацию.</p>
	<p>
		<h2>Личная информация</h2>
		<a href="profile/">Изменить регистрационные данные</a>
	</p>
	<p>
		<h2>Заказы</h2>
		<a href="order/">Ознакомиться с состоянием заказов</a><br/>
		<a href="cart/">Посмотреть содержимое корзины</a><br/>
		<a href="order/">Посмотреть историю заказов</a><br/>
	</p>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
