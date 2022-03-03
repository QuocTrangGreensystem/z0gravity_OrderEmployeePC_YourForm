<?php

echo json_encode($this->data + array(
            'message' => $this->Session->flash()));
?>
