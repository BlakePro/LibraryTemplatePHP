<?php
//INCLUDE CONFIG FILE
include 'config.php';

//EXMAPLE TEMPLATE
$html = 'Hello World';

//ECHO TEMPLATE
echo $template->template(TEMPLATE_JSON, ['html' => $html]);
