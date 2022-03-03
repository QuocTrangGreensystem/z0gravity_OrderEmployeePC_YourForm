<?php
$this->data['message'] = $this->Session->flash();
$this->data['auto_validate'] = !empty($auto_validate);
echo json_encode( $this->data);