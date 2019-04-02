<?php

namespace AppBundle\Service;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;

    class UserService{

        private $entityManager;

        public function __construct(EntityManagerInterface $em)
        {
            $this->entityManager = $em;
        }

        public function isTypeUser($table,$user_id){

            $isTypeUser = $this->entityManager->getRepository("AppBundle:User")->isTypeUser($table,$user_id);

            return $isTypeUser;
        }


    }



?>