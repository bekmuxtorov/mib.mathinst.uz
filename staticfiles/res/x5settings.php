<?php

/*
|-------------------------------
|	GENERAL SETTINGS
|-------------------------------
*/

$imSettings['general'] = array(
	'url' => 'http://mib.mathinst.uz/',
	'homepage_url' => 'http://mib.mathinst.uz/index.html',
	'icon' => 'http://mib.mathinst.uz/favImage.png',
	'version' => '16.3.1.1',
	'sitename' => 'Matematika instituti byulleteni',
	'lang_code' => 'ru-RU',
	'public_folder' => '',
	'salt' => '8e7kxsny2cdy9x65udw2edky24o7kyyuyu279iv',
);
/*
|-------------------------------
|	PASSWORD POLICY
|-------------------------------
*/

$imSettings['password_policy'] = array(
	'required_policy' => false,
	'minimum_characters' => '6',
	'include_uppercase' => false,
	'include_numeric' => false,
	'include_special' => false,
);
/*
|-------------------------------
|	Captcha
|-------------------------------
*/ImTopic::$captcha_code = "		<div class=\"x5captcha-wrap\">
			<label>Проверочное слово:</label><br />
			<input type=\"text\" class=\"imCpt\" name=\"imCpt\" maxlength=\"5\" />
		</div>
";


$imSettings['admin'] = array(
	'icon' => 'admin/images/logo_hvioqhj4.png',
	'theme' => 'orange',
	'extra-dashboard' => array(),
	'extra-links' => array()
);

// End of file x5settings.php