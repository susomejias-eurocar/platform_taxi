<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;


class DriverController extends Controller
{
    public function editAction($idDriver)
    {
        $security_context = $this->get('security.context');
        $security_token = $security_context->getToken();
        $user = $security_token->getUser();
        $companyId = $user->getCompanys()->getId();
        $driver = $this->getDoctrine()->getEntityManager()->getRepository("AppBundle:Driver")->findOneById($idDriver);        
        $idDriver = $driver->getId();
        $user = $this->getDoctrine()->getEntityManager()->getRepository("AppBundle:User")->findOneById($driver->getUser());
        $carDriver = $this->getDoctrine()->getEntityManager()->getRepository("AppBundle:Car")->findOneById($driver->getCar());
        $cars = $this->get("company_service")->getCarWithoutDriver($companyId);
        
        return $this->render('company/content-panel-createDriver.html.twig', array("idDriver" =>$idDriver, "carDriver"=> $carDriver, "cars" => $cars, "driver"=>$driver,"user" => $user, "user_type" => "company", "companyId" => $companyId,"driverId" => $driver->getid()));
    }

    public function editAjaxAction(Request $request){
        $em = $this->getDoctrine()->getEntityManager();
        $idDriver = $request->get('idDriver');
        $idUser = $request->get('idUser');
        $name = $request->get('driverName');
        $lastName = $request->get('driverLastName');
        $email = $request->get('email');
        $idCar = $request->get('car');
        $phone = $request->get('phone');
        $user = $request->get('user');
        $password1 = $request->get('password1');
        $password2 = $request->get('password2');
        if($password1!=$password2){
            $response = array(
                "status" => false,
                "message" => "Las contraseñas no coinciden"
            );
        }else{
            $driver = $em->getRepository("AppBundle:Driver")->findOneById($idDriver);
            $carDriver = $this->getDoctrine()->getEntityManager()->getRepository("AppBundle:Car")->findOneById($idCar);
            $user = $this->getDoctrine()->getEntityManager()->getRepository("AppBundle:User")->findOneById($idUser);
            $user->setName($name);
            $user->setLastName($lastName);
            $user->setEmail($email);
            $user->setPhone($phone);
            $encoder = $this->container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($user, $password1);
            $user->setPassword($encoded);
            $driver->setCar($carDriver);        
            $em->persist($driver);
            $em->flush();
            $response = array(
                "status" => true,
                "message" => "Conductor actualizado correctamente"
            );
        }
        return new JsonResponse($response);

    }

    public function deleteAction($idDriver){
        $em = $this->getDoctrine()->getEntityManager();
        $driver = $em->getRepository("AppBundle:Driver")->findOneById($idDriver);
        $em->remove($driver);
        $em->flush();
        return $this->redirectToRoute('show_car');
    }


    public function showFormSetStateAction()
    {
        $security_context = $this->get('security.context');
        $security_token = $security_context->getToken();
        $user = $security_token->getUser();
        $em = $this->getDoctrine()->getEntityManager();
        $driver_id = $em->getRepository("AppBundle:Driver")->getId($user->getId());
        $driver_state_now = $em->getRepository("AppBundle:Driver")->getState($user->getId());
        return $this->render('driver/content-panel-changeState.html.twig', array('idDriver'=> $driver_id , 'state_now' => $driver_state_now));
    }

    public function setStateAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $state = $request->get('state');
        $driver_id = $request->get('idDriver');
        if (empty($state) or $driver_id){
            $response = array(
                "status" => false,
                "message" => "Parámetros incorrectos"
            );
        }

        $driver = $em->getRepository("AppBundle:Driver")->setState($driver_id,$state);
        $response = array(
            "status" => true,
            "message" => "Estado modificado correctamente"
        );
        return new JsonResponse($response);
    }

}
