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

        public function send($view){
            
            $message = (new \Swift_Message('Hello Email'))
                ->setFrom('jesusmejias.eurocar@gmail.com')
                ->setTo('jesusmejias.jm@gmail.com')
                ->setBody(
                        $this->templating->render(
                            'mail/'. $view .'.html.twig',
                            array('name' => "Prueba")
                        ));
                $this->mailer->send($message);
        }

    }



?>