<?php
    class AppError extends ErrorHandler {
        function accessDenied($params) {
            $this->controller->set('msg', $params['msg']);
            $this->_outputMessage('access_denied');
        }
    }
?>
