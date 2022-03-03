<?php
$data = array(
    'result' => $result,
    'message' => $this->Session->flash(),
    'data' => $data
);
echo json_encode($data);