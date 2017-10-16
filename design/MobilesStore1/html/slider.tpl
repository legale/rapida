<div class="theme-default"><div id="slider" class="nivoSlider">
{*
	Одна строка - ОДИН рекламный блок. Просто вставьте ссылку и замените старый ФАЙЛ slider_ХХХ.jpg изображения в папке
	design/тема сайта/images/images_theme на свой вариант такого же размера
*}

	<a href="куда_ведет_ссылка_1_блока"><img src="design/{$settings->theme|escape}/images/images_theme/slider_001.jpg" alt=""/></a>
	<a href="куда_ведет_ссылка_2_блока"><img src="design/{$settings->theme|escape}/images/images_theme/slider_002.jpg" alt=""/></a>
	<a href="куда_ведет_ссылка_3_блока"><img src="design/{$settings->theme|escape}/images/images_theme/slider_003.jpg" alt=""/></a>
	<a href="куда_ведет_ссылка_4_блока"><img src="design/{$settings->theme|escape}/images/images_theme/slider_004.jpg" alt=""/></a>
	<a href="куда_ведет_ссылка_5_блока"><img src="design/{$settings->theme|escape}/images/images_theme/slider_005.jpg" alt=""/></a>

</div></div>
<script type="text/javascript" src="design/{$settings->theme|escape}/js/jquery.nivo.slider.js"></script>
<script type="text/javascript">
$(window).load(function() {
$('#slider').nivoSlider();
});
</script>