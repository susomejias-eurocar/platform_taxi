<?php

namespace AppBundle\Service;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\Company;
use Symfony\Component\HttpFoundation\Request;

    class CompanyService{

        private $entityManager;

        public function __construct(EntityManagerInterface $em)
        {
            $this->entityManager = $em;
        }

        public function getCompanyName($user_id){

            $companyName = $this->entityManager->getRepository("AppBundle:Company")->getCompanyName($user_id);

            return $companyName;
        }


    }



?>