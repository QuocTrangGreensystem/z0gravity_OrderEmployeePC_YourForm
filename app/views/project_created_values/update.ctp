<?php

$data = array(
    'result' => $success,
    'message' => $this->Session->flash(),
    'data' => $this->data,
);
echo json_encode($data);
?>
