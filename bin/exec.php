<?php
/**
 * Script that executes stuff in a cross-platform way
 */

if ((count($argv)) != 2) {
	echo "Usage: php {$argv[0]} <<JSON_ENCODED_DATA>>\n";
	exit(1);
} else {
	$data = json_decode($argv[1], true);
    if (!array_key_exists('cmd', $data)) {
        echo "'cmd' key must exist\n";
		exit(1);
	} else {
		$cmd = $data['cmd'];
		$env = array_key_exists('env', $data) ? $data['env'] : array();
        var_dump($cmd, $env);
		$passthru = array_key_exists('passthru', $data) ? $data['passthru'] : false;
		foreach ($env as $k=>$v) {
			putenv($k.'='.escapeshellarg($v));
		};
		if (!defined('PHP_WINDOWS_VERSION_BUILD') && $passthru) {
            passthru($cmd, $returnvar);
			exit($returnvar);
        } else {
            exec($cmd, $output, $returnvar);
			echo implode("\n", $output);
			exit($returnvar);
        };
	};
};
