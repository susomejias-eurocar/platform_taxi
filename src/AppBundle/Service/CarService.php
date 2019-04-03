<?php

namespace AppBundle\Service;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\Car;
use Symfony\Component\HttpFoundation\Request;

    class CarService{

        private $entityManager;

        public function __construct(EntityManagerInterface $em)
        {
            $this->entityManager = $em;
        }

        public function insertCar($data){

            //$entityManager = $this->em->getDoctrine()->getManager();
            $car = new Car();
            $car->setPlate($data[0]);
            $car->setTrademark($data[1]);
            $car->setModel($data[2]);
            $car->setVersion($data[3]);
            $car->setCompanyId($data[4]);
            $this->entityManager->persist($car);
            $this->entityManager->flush();

        }

        public function getCar($id){
            $em = $this->entityManager;
            $car = $em->getRepository("AppBundle:Car")->findOneBy(["id" => $id]);
            $carReturn = new Car();
            $carReturn->setCompany($car->getCompany());
            $carReturn->setTrademark($car->getTrademark());
            $carReturn->setModel($car->getModel());
            $carReturn->setVersion($car->getVersion());            
            $carReturn->setPlate($car->getPlate());
            

            return $carReturn;
            /*
            $em = $this->getDoctrine()->getRepository('AppBundle:Car');
            $car = $em->getCar(1);
            var_dump($car);
            */
        }

        public function setState($idCar,$state){
            $em = $this->entityManager;
            $em->getRepository("AppBundle:Car")->setState($idCar,$state);
        }



    }



?>