<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;
use AppBundle\Entity\Company;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\Security\Acl\Exception\Exception;


class UserController extends Controller
{
    public function loginAction(Request $request)
    {

        $security_context = $this->get('security.context');
        $security_token = $security_context->getToken();
        $user = $security_token->getUser();

        
        if ($user != "anon."){
            return $this->redirect($this->generateUrl('panel'));
        }
        
        //Llamamos al servicio de autenticacion
        $authenticationUtils = $this->get('security.authentication_utils');
        
        // conseguir el error del login si falla
        $hasError = false;
        try{
            $error = $authenticationUtils->getLastAuthenticationError();
        }catch(Exception $e){
            if($e instanceof BadCredentialsException)
                $hasError = true;
        }
        
        //dump($error['BadCredentialsException']);
        
        //if(strtolower($error['message'])=="bad credentials.")
            //$hasError = true;


        // ultimo nombre de usuario que se ha intentado identificar
        $lastUsername = $authenticationUtils->getLastUsername();
        
        return $this->render(
                'user/login.html.twig', array(
                    'last_username' => $lastUsername,
                    'error' => $error,
                    'hasError' => $hasError
        ));        
    }

    public function loginFailureAction()
    {        
        return $this->render(
                'user/login.html.twig', array(
                    'msgError' => 'Usuario o contraseña incorrecta'
        ));        
    }




    public function panelAction(Request $request)
    {

        $security_context = $this->get('security.context');

        $security_token = $security_context->getToken();

        $user = $security_token->getUser();

        $usersService = $this->get('user_service');

        if($user){
            if($this->get('security.context')->isGranted('ROLE_COMPANY')){
                return $this->render('user/panel.html.twig', array());
            }else if ($this->get('security.context')->isGranted('ROLE_DRIVER')){
                $em = $this->getDoctrine()->getManager();
                $idDriver = $this->container->get("driver_service")->getId($user->getId());
                return $this->render('user/panel.html.twig', array("idDriver" => $idDriver));
            }

        }
        $hasError = false;
        return $this->redirect("logout");
    }

}
