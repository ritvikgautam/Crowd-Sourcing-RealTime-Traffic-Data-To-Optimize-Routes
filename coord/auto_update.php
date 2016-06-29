<?php
	while(1)
	{
		include 'make_json.php';
		$cmd = 'curl -H "Content-Type: application/json" --data @/var/www/html/coord/traffic.json http://localhost:8989/datafeed';
		exec($cmd);
		sleep(120);
	}
?>