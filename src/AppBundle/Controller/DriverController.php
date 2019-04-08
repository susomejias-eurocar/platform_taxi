<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;


class DriverController extends Controller
{
    public function editAction($idDriver)
    {
        $security_context = $this->get('security.context');
        $security_token = $security_context->getToken();
        $user = $security_token->getUser();
        $usersService = $this->get('user_service');
        $companyId = $user->getCompanys()->getId();
        $driver = $this->getDoctrine()->getEntityManager()->getRepository("AppBundle:Driver")->findOneById($idDriver);        
        $user = $this->getDoctrine()->getEntityManager()->getRepository("AppBundle:User")->findOneById($driver->getUser());        
        return $this->render('driver/register.html.twig', array("driver"=>$driver,"user" => $user, "user_type" => "company", "companyId" => $companyId,"driverId" => $driver->getid()));
        //return $this->redirect('login');
    }

    public function editAjaxAction(Request $request){
        $em = $this->getDoctrine()->getEntityManager();
        $name = $request->get('name');
        $lastName = $request->get('lastName');
        $email = $request->get('email');
        $phone = $request->get('phone');
        $state = $request->get('state');
        $iDriver = $request->get('iDriver');
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

}
