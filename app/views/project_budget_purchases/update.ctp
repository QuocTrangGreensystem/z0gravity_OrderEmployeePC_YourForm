<?php

$data = array(
    'result' => $result,
    'message' => $this->Session->flash(),
    'data' => $this->data
);
echo json_encode($data);
?>
