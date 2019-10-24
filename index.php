<?php
include 'config.php'

//EXMAPLE TEMPLATE
$html = 'Hello World';
echo $template->template(TEMPLATE_JSON, ['html' => $html]);
