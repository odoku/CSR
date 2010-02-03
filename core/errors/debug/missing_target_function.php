<p>
	URIのルーティングには成功しましたが、ルーティング先のターゲットを処理する関数が存在しませんでした。<br>
	関数が処理可能なターゲットを判断する為の正規表現を確認して下さい。
</p>
<?php
	$request = $this->_dispatcher->getRequest();
	$matchedRoute = each($this->_dispatcher->getMatchedRoute());
	reset($this->_dispatcher->getMatchedRoute());
?>
<h2>Route &amp; Target</h2>
<p><?php echo htmlspecialchars($matchedRoute['key'] . ' => ' . $matchedRoute['value']); ?></p>
<h2>Target Regexp &amp; Functions</h2>
<ul>
<?php foreach ($this->_targetFunctions as $regexp => $function): ?>
<?php
	if (is_array($function)) {
		list($class, $method) = $function;
		if (is_object($class)) $class = get_class($class);
		$function = $class . '::' . $method;
	}
?>
	<li><?php echo htmlspecialchars($regexp . ' => ' . $function . '()'); ?></li>
<?php endforeach ?>
</ul>
<h2>解決策</h2>
<p>ルーティングされたターゲットを、いずれかの関数に対応する正規表現にマッチする様にして下さい。</p>
<p>または、関数が処理可能なターゲットを判断する為の正規表現を、ルーティングされたターゲットにマッチする様にして下さい。</p>