<?php

foreach ($this->data as $key => $data) {
    if (is_null($data)) {
        $this->data[$key] = '';
    }
}
$data = array(
    'result' => $result,
    'message' => $this->Session->flash(),
    'data' => $this->data,
);
echo json_encode($data);
?>
