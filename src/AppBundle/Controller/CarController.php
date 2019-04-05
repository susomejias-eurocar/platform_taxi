<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;


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
        $car = $em->getRepository("AppBundle:Car")->findOneById($idCar);
        $car->setPlate($plate);
        $car->setTrademark($trademark);
        $car->setModel($model);
        $car->setVersion($version);
        $car->setState($state);
        $em->persist($car);
        $em->flush();
        return new RedirectResponse($this->generateUrl('show_car'));

    }

    public function deleteAction($idCar){
        $em = $this->getDoctrine()->getEntityManager();
        $car = $em->getRepository("AppBundle:car")->findOneById($idCar);
        $em->remove($car);
        $em->flush();
        return $this->redirectToRoute('show_car');
    }

}
