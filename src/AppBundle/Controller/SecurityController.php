<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;


class SecurityController extends Controller
{

    public function emailConfirmationAction(Request $request){
        
        //Comprobamos que nos llegue el token via GET, si no es así, redirigimos al login
        if(!$tokenRegister = $request->get('tokenRegister')){
            return $this->redirect("logout");
        }
        $response = array(
            "status" => false,
            "message" => "ERROR"
        );
        $em = $this->getDoctrine()->getManager();
        //Obtenemos el usuario a partir del token, si no existe, no continuamos        
        if(!$user = $em->getRepository('AppBundle:User')->findOneByTokenRegister($tokenRegister)){            
            $response = array(
                "status" => false,
                "message" => "El token no existe"
            );
            return new JsonResponse($response);
        }
        //Si la fecha actual es mayor a la fecha de vencimiento del token, no continuamos
        elseif(strtotime(date("d-m-Y"))>strtotime($user->getExpirationTokenRegister())){
            $response = array(
                "status" => false,
                "message" => "El token ha vencido"
            );
            return new JsonResponse($response);
        }
            
        $user->setActive(1);
        $user->setTokenRegister(null);
        $user->setExpirationTokenRegister(null);
        $em->persist($user);
        $em->flush();
        $mailService = $this->container->get('mail_service');

        $mailService->send('register');
        $response = array(
            "status" => true,
            "message" => "El registro de ha confirmado correctamente"
        );
        return $this->redirectToRoute("login", array("responseToken" => $response));
    }

    public function forgottenPasswordAction(Request $request){

    }

}
