<?php
foreach( $toggle_data as $val){
	$new_data[] = $val;
}
$data = array(
    'result' => $result,
    'data' => !empty($new_data) ? $new_data : '',
	'field' => !empty($fields) ? $fields : '',
);
if( $result ) { $data['message'] = $this->Session->flash(); }
echo json_encode($data);
die;
?>