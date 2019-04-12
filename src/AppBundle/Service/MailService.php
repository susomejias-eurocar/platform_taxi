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

        public function send($view, $email,$username, $url, $token){
            
            $message = (new \Swift_Message('Hola ' . $username ))
                ->setFrom('jesusmejias.eurocar@gmail.com')
                ->setTo($email)
                ->setBody(
                        $this->templating->render(
                            'mail/'. $view .'.html.twig',
                            array('username' => $username, 'url' => $url. '' .$token)
                        ), 'text/html');
                $this->mailer->send($message);
                return;
        }

    }



?>