<?php

require 'vendor/autoload.php';

function email($to, $subject, $message) {
    $sendgrid = new SendGrid("mmlcasag", "q1Q!w2W@");
    $email    = new SendGrid\Email();
    
    $email->addTo($to)
	      ->addTo("fabibr@gmail.com")
          ->setFrom("fabibr@gmail.com")
          ->setSubject($subject)
          ->setHtml($message);
    
    $sendgrid->send($email);
}