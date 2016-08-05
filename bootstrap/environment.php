<?php
/*
|--------------------------------------------------------------------------
| Detect The Application Environment
|--------------------------------------------------------------------------
|
| Laravel takes a dead simple approach to your application environments
| so you can just specify a machine name for the host that matches a
| given environment, then we will automatically detect it for you.
|
*/
$env = $app->detectEnvironment(function(){
	$environmentPath = __DIR__.'/../.env';
	if (file_exists($environmentPath))
	{
		# We get the enviroment name
		$setEnv = trim(file_get_contents($environmentPath));
	  	$environmentFile = __DIR__.'/../.'.$setEnv.'.env';

	  	if (file_exists($environmentFile)) {
	  		$configs = explode(PHP_EOL, file_get_contents($environmentFile));
	  		foreach ($configs as $config) {
	  			if(strlen($config) > 0) {
	  				putenv($config);
	  			}
	  		}
	  	}
	  	
	}
});