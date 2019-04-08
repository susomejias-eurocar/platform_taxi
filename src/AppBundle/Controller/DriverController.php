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
        $user = $this->getDoctrine()->getEntityManager()->getRepository("AppBundle:User")->findOneById($driver->getUser());        
        return $this->render('driver/register.html.twig', array("driver"=>$driver,"user" => $user, "user_type" => "company", "companyId" => $companyId,"driverId" => $driver->getid()));
    }

    public function editAjaxAction(Request $request){
        $em = $this->getDoctrine()->getEntityManager();
        $idDriver = $request->get('iDriver');
        $driver = $em->getRepository("AppBundle:Driver")->findOneById($idDriver);        
        $em->persist($driver);
        $em->flush();
        return new RedirectResponse($this->generateUrl('show_car'));

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
        return $this->render('driver/content-panel-changeState.html.twig', array('driver_id'=> $driver_id , 'state_now' => $driver_state_now));
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
