<?php

    $GLOBALS['phone'] = '+7 (905) 242-11-12';
	$GLOBALS['phone_href'] = '+79052421112';
    $GLOBALS['phone_rich'] = '+79052421112';
	
	$GLOBALS['phone_alt'] = '+7 (905) 242-11-12';
	$GLOBALS['phone_href_alt'] = '+79052421112';
    $GLOBALS['phone_rich_alt'] = '+79052421112';
	
/*
    $GLOBALS['phone'] = '+7 (900) 283-96-58';
	$GLOBALS['phone_href'] = '+79002839658';
    $GLOBALS['phone_rich'] = '+79002839658';
	
	$GLOBALS['phone_alt'] = '+7 (900) 283-96-58';
	$GLOBALS['phone_href_alt'] = '+79002839658';
    $GLOBALS['phone_rich_alt'] = '+79002839658';
*/

	$GLOBALS['wa_link'] = 'https://wa.me/79052421112';
	// $GLOBALS['wa_link'] = 'https://wa.me/79002839658';
	$GLOBALS['telegram'] = 'https://t.me/tretyakdnua';
	
	$GLOBALS['email'] = 'marketing@sochicharter.ru';
	$GLOBALS['address'] = 'Сочи, ул.Войкова, 1/1';
	$GLOBALS['address_adler'] = 'Адлер, пгт. Сириус, Морской бульвар 1';
	$GLOBALS['address_ur'] = 'Россия, Краснодарский Край, Сочи, ул.Пластунская, 194/14, пом.80-101';
	$GLOBALS['yandex_maps'] = 'https://yandex.ru/maps/-/CCUsQHcePC';
	$GLOBALS['google_maps'] = 'https://g.page/sochicharter?share';
?>

<?php
	$GLOBALS['isAdler'] = strpos($_SERVER["REQUEST_URI"], '/adler/') === 0 ? 1 : 0;
	$GLOBALS['isLazarevskoe'] = strpos($_SERVER["REQUEST_URI"], '/lazarevskoe/') === 0 ? 1 : 0;
	$GLOBALS['isSochi'] = ($GLOBALS['isAdler'] === 0 && $GLOBALS['isLazarevskoe'] === 0) === true ? 1 : 0;
?>