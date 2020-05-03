<?php
//INCLUDE CONFIG FILE
include 'config.php';

//CONTENT BODY
$content = 'Hello World';

//TEMPLATE
echo $html->template(TEMPLATE_JSON, ['html' => $content, 'class' => '']);
