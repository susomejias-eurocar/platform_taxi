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

        /**
         * Get a company by id
         *
         * @param [type] $id
         * @return void
         */
        public function getCompany($id){
            $em = $this->entityManager;
            $car = $em->getRepository("AppBundle:Company")->findOneBy(["id" => $id]);
            return $car;
        }

        /**
         * Get information from a company by user id
         *
         * @param [type] $user_id
         * @return void
         */
        public function getCompanyInfo($user_id){

            $companyName = $this->entityManager->getRepository("AppBundle:Company")->getCompanyInfo($user_id);

            return $companyName;
        }

        /**
         * Get all the cars of a company
         *
         * @param [type] $params
         * @param [type] $idCompany
         * @return void
         */
        public function getAllCars($params,$idCompany)
        {
            $cars = $this->entityManager->getRepository("AppBundle:Company")->getAllCars($params,$idCompany);
            return $cars;
        }

        /**
         * Get all the cars of a compa
         *
         * @param [type] $params
         * @param [type] $idCompany
         * @return void
         */
        public function getAllDrivers($params,$idCompany)
        {
            $drivers = $this->entityManager->getRepository("AppBundle:Company")->getAllDrivers($params,$idCompany);
            return $drivers;
        }

        /**
         * Insert a new company
         *
         * @param [type] $data
         * @return void
         */
        public function insertCompany($data){
            $company = new Company();
            $this->entityManager->persist($company);
            $this->entityManager->flush();
        }

        /**
         * Get all driver of company without car
         *
         * @param [type] $idCompany
         * @return void
         */
        public function getDriversWithoutCar($idCompany){
            $em = $this->entityManager;
            $car = $em->getRepository("AppBundle:Company")->getDriversWithoutCar($idCompany);
            return $car;
        }

        /**
         * Get all driver of company with car
         *
         * @param [type] $idCompany
         * @return void
         */
        public function getDriversWithCar($idCompany){
            $em = $this->entityManager;
            $car = $em->getRepository("AppBundle:Company")->getDriversWithCar($idCompany);
            return $car;
        }

        /**
         * Get stopped drivers of company
         *
         * @param [type] $idCompany
         * @return void
         */
        public function getStoppedDrivers($idCompany){
            $em = $this->entityManager;
            $car = $em->getRepository("AppBundle:Company")->getStoppedDrivers($idCompany);
            return $car;
        }

        /**
         * Get avalaibled drivers of company
         *
         * @param [type] $idCompany
         * @return void
         */
        public function getAvalaibledDrivers($idCompany){
            $em = $this->entityManager;
            $car = $em->getRepository("AppBundle:Company")->getAvalaibledDrivers($idCompany);
            return $car;
        }

        /**
         * Get unavalaibled drivers of company
         *
         * @param [type] $idCompany
         * @return void
         */
        public function getUnavalaibledDrivers($idCompany){
            $em = $this->entityManager;
            $car = $em->getRepository("AppBundle:Company")->getUnavalaibledDrivers($idCompany);
            return $car;
        }

        /**
         * Get damaged car of company
         *
         * @param [type] $idCompany
         * @return void
         */
        public function getDamagedCar($idCompany){
            $em = $this->entityManager;
            $car = $em->getRepository("AppBundle:Company")->getDamagedCar($idCompany);
            return $car;
        }

        /**
         * Get repair car of company
         *
         * @param [type] $idCompany
         * @return void
         */
        public function getInRepairCar($idCompany){
            $em = $this->entityManager;
            $car = $em->getRepository("AppBundle:Company")->getInRepairCar($idCompany);
            return $car;
        }

    }


?>