<?php

namespace AppBundle\Service;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\Driver;
use Symfony\Component\HttpFoundation\Request;

    class DriverService{

        private $entityManager;

        public function __construct(EntityManagerInterface $em)
        {
            $this->entityManager = $em;
        }

        public function insertDriver($data){

            //$entityManager = $this->em->getDoctrine()->getManager();
            $driver = new Driver();
            $driver->setPlate($data[0]);
            $driver->setTrademark($data[1]);
            $driver->setModel($data[2]);
            $driver->setVersion($data[3]);
            $driver->setCompanyId($data[4]);
            $this->entityManager->persist($driver);
            $this->entityManager->flush();

        }

        public function getDriver($id){
            $em = $this->entityManager;
            $car = $em->getRepository("AppBundle:Driver")->findOneBy(["id" => $id]);
            return $car;
            /*
            $em = $this->getDoctrine()->getRepository('AppBundle:Car');
            $car = $em->getCar(1);
            var_dump($car);
            */
        }
        
    }



?>