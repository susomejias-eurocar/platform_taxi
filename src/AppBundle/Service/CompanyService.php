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



        public function insertCompany($data){
            $company = new Company();
            $this->entityManager->persist($company);
            $this->entityManager->flush();
        }

        public function getDriversWithoutCar($idCompany){
            $em = $this->entityManager->getConnection();
            $car = $em->getRepository("AppBundle:Company")->getDriversWithoutCar($idCompany);
            return $car;
        }

        public function getDriversWithCar($idCompany){
            $em = $this->entityManager;
            $car = $em->getRepository("AppBundle:Company")->getDriversWithCar($idCompany);
            return $car;
        }

        public function getStoppedDrivers($idCompany){
            $em = $this->entityManager;
            $car = $em->getRepository("AppBundle:Company")->getStoppedDrivers($idCompany);
            return $car;
        }

        public function getAvalaibledDrivers($idCompany){
            $em = $this->entityManager;
            $car = $em->getRepository("AppBundle:Company")->getAvalaibledDrivers($idCompany);
            return $car;
        }

        public function getUnavalaibledDrivers($idCompany){
            $em = $this->entityManager;
            $car = $em->getRepository("AppBundle:Company")->getUnavalaibledDrivers($idCompany);
            return $car;
        }

        public function getDamagedCar($idCompany){
            $em = $this->entityManager;
            $car = $em->getRepository("AppBundle:Company")->getDamagedCar($idCompany);
            return $car;
        }

        public function getInRepairCar($idCompany){
            $em = $this->entityManager;
            $car = $em->getRepository("AppBundle:Company")->getInRepairCar($idCompany);
            return $car;
        }


        public function getAvalaibleCar($idCompany){
            $em = $this->entityManager;
            $car = $em->getRepository("AppBundle:Company")->getAvalaiblerCar($idCompany);
            return $car;
        }        
    }


?>