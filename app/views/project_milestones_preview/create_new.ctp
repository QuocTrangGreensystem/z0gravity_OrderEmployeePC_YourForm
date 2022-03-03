<?php
$data = array(
    'result' => $result,
    // 'message' => $this->Session->flash(),
    'data' => $this->data,
	'new_index' => $new_index,
);
echo json_encode($data);
?>
