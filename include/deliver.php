<?php

require_once('include/cli_startup.php');
require_once('include/zot.php');


function deliver_run($argv, $argc) {

	cli_startup();

	$a = get_app();

	if($argc < 2)
		return;

	logger('deliver: invoked: ' . print_r($argv,true), LOGGER_DEBUG);

	for($x = 1; $x < $argc; $x ++) {
		$r = q("select * from outq where outq_hash = '%s' limit 1",
			dbesc($argv[$x])
		);
		if($r) {
			$result = zot_zot($r[0]['outq_posturl'],$r[0]['outq_notify']); 
			if($result['success']) {
				zot_process_response($result, $r[0]);				
			}
			else {
				$y = q("update outq set outq_updated = '%s' where outq_hash = '%s' limit 1",
					dbesc(datetime_convert()),
					dbesc($argv[$x])
				);
			}
		}
	}
}

if (array_search(__file__,get_included_files())===0){
  deliver_run($argv,$argc);
  killme();
}
