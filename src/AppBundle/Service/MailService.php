<?php

namespace AppBundle\Service;
use Doctrine\ORM\EntityManagerInterface;

    class MailService{

        private $entityManager;
        private $mailer;
        private $templating;
    

        public function __construct(EntityManagerInterface $em,\Swift_Mailer $mailer, \Twig_Environment $templating)
        {
            $this->entityManager = $em;
            $this->mailer = $mailer;
            $this->templating = $templating;
        }

        public function send($view, $username, $email, $tokenRegister){
            
            $message = (new \Swift_Message('Hello Email'))
                ->setFrom('jesusmejias.eurocar@gmail.com')
                ->setTo($email)
                ->setBody(
                        $this->templating->render(
                            'mail/'. $view .'.html.twig',
                            array('username' => $username, 'url' => "http://127.0.0.1:8000/emailConfirmation?tokenRegister=".$tokenRegister)
                        ));
                $this->mailer->send($message);
        }

    }



?>