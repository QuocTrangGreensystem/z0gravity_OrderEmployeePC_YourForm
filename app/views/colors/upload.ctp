<?php
$data = array(
	'result' => false,
);
if( !empty($result) ){
	$data = array(
		'result' => true,
		'message' => $this->Session->flash(),
		'data' => $result,
	);
}
echo json_encode($data);
?>
