<?php
die(json_encode(array(
	'result' => $result,
	'message' => $this->Session->flash(),
	'data' => $this->data
)));