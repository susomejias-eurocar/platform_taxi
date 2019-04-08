<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;


class CarController extends Controller
{
    public function editAction($idCar)
    {
        $security_context = $this->get('security.context');
        $security_token = $security_context->getToken();
        $user = $security_token->getUser();
        $companyId = $user->getCompanys()->getId();
        $car = $this->getDoctrine()->getEntityManager()->getRepository("AppBundle:car")->findOneById($idCar);        
        return $this->render('car/content-panel-createCar.html.twig', array("car"=>$car,"user_type" => "company", "companyId" => $companyId,"carId" => $car->getid()));
        //return $this->redirect('login');
    }

    public function editAjaxAction(Request $request){

        $em = $this->getDoctrine()->getEntityManager();
        $plate = $request->get('plate');
        $trademark = $request->get('trademark');
        $model = $request->get('model');
        $version = $request->get('version');
        $state = $request->get('state');
        $idCar = $request->get('idCar');

        $response = array(
            "status" => false,
            "message" => "ERROR"
        );

        if(!preg_match("/^\d{4}[A-Z]{3}/", $plate)){
            $response = array(
                "status" => false,
                "message" => "Formato de matrícula incorrecto"
            );
        }else if (empty($plate) or empty($trademark) or empty($model) or empty($version) or empty($state) or empty($idCar)){
            $response = array(
                "status" => false,
                "message" => "rellene los campos"
            );
            
        }else{
            try{
                $car = $em->getRepository("AppBundle:Car")->findOneById($idCar);
                $car->setPlate($plate);
                $car->setTrademark($trademark);
                $car->setModel($model);
                $car->setVersion($version);
                $car->setState($state);
                $em->persist($car);
                $em->flush();
                $response = array(
                    "status" => true,
                    "message" => "Coche editado correctamente"
                );
            }catch(\Doctrine\DBAL\DBALException $e){
                $response = array(
                    "status" => false,
                    "message" => "La matrícula introducida ya está registrada"
                );
                return new JsonResponse($response);
            }
        }
        return new JsonResponse($response);
    }

    public function deleteAction(Request $request,$idCar){
        $em = $this->getDoctrine()->getEntityManager();
        $car = $em->getRepository("AppBundle:car")->findOneById($idCar);
        $em->remove($car);
        $em->flush();
        return $this->redirect($this->generateUrl('show_car'));
    }

}
