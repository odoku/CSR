<?php

require_once 'core/CSR.php';

CSR::plugin('mvc', 'Timer');

$csr = CSR::getInstance();
$csr->execute(array(
	'/'     => 'index/index',
	'/hoge' => 'hoge/index',
));