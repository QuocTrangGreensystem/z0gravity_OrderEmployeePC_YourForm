<?php
$data = array(
    'result' => $result ? 'success' : 'failed',
    'message' => $this->Session->flash(),
    'data' => $data
);
echo json_encode($data);
