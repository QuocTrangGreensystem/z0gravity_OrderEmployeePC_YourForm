<?php 
die(json_encode(array(
	'result' => $result,
	'data' => $data,
	'message' => $this->Session->flash(),
)));
exit;