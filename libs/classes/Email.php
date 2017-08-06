<?php
/**
 * @package Simple, Secure Login
 * @author John Crossley <john@suburbanarctic.com>
 * @copyright John Crossley 2012
 * @version 3.0
 **/

class Email {

    public
        $to,
        $from,
        $subject,
        $message;


    public function to($to) {
        $this->to = $to;
    }

    public function from($from) {
        $this->from = $from;
    }

    public function subject($subj) {
        $this->subject = $subj;
    }

    public function message($message) {
        $this->message = $message;
    }

    public function send() {
        $headers = 'From: ' . $this->from . "\r\n" .
            'Reply-To: ' . $this->from . "\r\n" .
            'X-Mailer: PHP/' . phpversion();
        if ( mail($this->to, $this->subject, $this->message, $headers) )
            return true;
        else
            return false;
    }



}