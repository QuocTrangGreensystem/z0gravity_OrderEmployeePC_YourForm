<?php

class TicketAttachmentsController extends AppController {
	public $uses = array('Ticket', 'TicketAttachment');

	private $mime_types = array(
        "pdf"=>"application/pdf"
        ,"exe"=>"application/octet-stream"
        ,"zip"=>"application/zip"
        ,"docx"=>"application/msword"
        ,"doc"=>"application/msword"
        ,"xls"=>"application/vnd.ms-excel"
        ,"xlsx"=>"application/vnd.ms-excel"
        ,"ppt"=>"application/vnd.ms-powerpoint"
        ,"gif"=>"image/gif"
        ,"png"=>"image/png"
        ,"jpeg"=>"image/jpg"
        ,"jpg"=>"image/jpg"
        ,"mp3"=>"audio/mpeg"
        ,"wav"=>"audio/x-wav"
        ,"mpeg"=>"video/mpeg"
        ,"mpg"=>"video/mpeg"
        ,"mpe"=>"video/mpeg"
        ,"mov"=>"video/quicktime"
        ,"avi"=>"video/x-msvideo"
        ,"3gp"=>"video/3gpp"
        ,"css"=>"text/css"
        ,"jsc"=>"application/javascript"
        ,"js"=>"application/javascript"
        ,"php"=>"text/html"
        ,"htm"=>"text/html"
        ,"html"=>"text/html",
    	'mp4' => 'video/mp4'
    );

	public function download($ticket_id, $attachment_id){
		$ticket = $this->Ticket->read(null, $ticket_id);
		if( !empty($ticket) ){
			$attachment = $this->TicketAttachment->read(null, $attachment_id);
			if( !empty($attachment) ){
				//download
				$file = $this->getPath($ticket_id) . $attachment['TicketAttachment']['file'];
				$this->output($file);
			}
		}
		die('file not found');
	}

	public function delete($ticket_id, $attachment_id){
		$ticket = $this->Ticket->read(null, $ticket_id);
		if( !empty($ticket) ){
			$attachment = $this->TicketAttachment->read(null, $attachment_id);
			if( !empty($attachment) ){
				$this->TicketAttachment->delete($attachment_id);
				// delete file
				@unlink($this->getPath($ticket_id) . $attachment['TicketAttachment']['file']);
				// delete attachment
				if( !empty($attachment['TicketAttachment']['thumbnail']) ){
					@unlink($this->getPath($ticket_id) . $attachment['TicketAttachment']['thumbnail']);
				}
				die('1');
			}
		}
		die('0');
	}

	private function mime($file){
		$ext = pathinfo($file, PATHINFO_EXTENSION);
		if( !isset($this->mimeTypes[$ext]) ){
			return 'txt';
		}
		return $this->mimeTypes[$ext];
	}

	private function output($file){
		header("Content-type: application/octet-stream");
		header("Content-Disposition: filename=\"" . basename($file) . "\"");
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
	    readfile($file);
	    die;
	}

	protected function getPath($id) {
        $path = FILES . $this->employee_info['Company']['id'] . DS . 'tickets' . DS . $id . DS;
        return $path;
    }


}