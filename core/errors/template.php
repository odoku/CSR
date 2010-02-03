<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
	"http://www.w3.org/TR/html4/strict.dtd">
<html lang="ja">
	<head>
		<meta http-equiv="content-type" content="text/html;charset=utf-8">
		<title><?php echo htmlspecialchars($status); ?>.</title>
	</head>
	<body>
		<h1><?php echo htmlspecialchars($status); ?>.</h1>
		
<?php if (CSR_DEVELOP_MODE && isset($debugMessage) && is_array($debugMessage)): ?>
<?php foreach ($debugMessage as $message): ?>
		<p><?php echo htmlspecialchars($message, ENT_QUOTES); ?></p>
<?php endforeach ?>
<?php endif ?>
<?php
	if (CSR_DEVELOP_MODE && isset($debugHelpFile) && file_exists($debugHelpFile)) require $debugHelpFile;
?>
	</body>
</html>