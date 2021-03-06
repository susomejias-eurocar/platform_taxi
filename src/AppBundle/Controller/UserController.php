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

    /**
     * Login action
     * 
     * @param Request $request
     */
    public function loginAction(Request $request)
    {
        $msgError = "";
        if ($request->get('msgError')){
            $msgError = $request->get('msgError');
        };
        $security_context = $this->get('security.token_storage');
        $security_token = $security_context->getToken();
        $user = $security_token->getUser();
        if ($user != "anon." && $user->getActive()){
            return $this->redirect($this->generateUrl('panel'));
        }
        $authenticationUtils = $this->get('security.authentication_utils');
        $hasError = false;
        try{
            $error = $authenticationUtils->getLastAuthenticationError();
        }catch(Exception $e){
            if($e instanceof BadCredentialsException)
                $hasError = true;
        }
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render(
                'user/login.html.twig', array(
                    'last_username' => $lastUsername,
                    'error' => $error,
                    'hasError' => $hasError,
                    'msgError' => $msgError
        ));        
    }

    /**
     * Render login view with message
     *
     * @return void
     */
    public function loginFailureAction()
    {        
        return $this->render(
                'user/login.html.twig', array(
                    'msgError' => 'Usuario o contraseña incorrecta'
        ));        
    }

    /**
     * Open panel for driver or company
     * 
     * @param Request $request
     */
    public function panelAction(Request $request)
    {
        $security_context = $this->get('security.token_storage');
        $security_token = $security_context->getToken();
        $user = $security_token->getUser();
        $usersService = $this->get('user_service');
        
        if(!$user->getActive()){ 
            return $this->redirect($this->generateUrl('login', array('msgError' => 'El usuario esta inactivo en el sistema.')));
        }
            if($user){
            if($this->get('security.context')->isGranted('ROLE_COMPANY')){
                return $this->render('user/panel.html.twig', array());
            }else if ($this->get('security.context')->isGranted('ROLE_DRIVER')){
                $em = $this->getDoctrine()->getManager();
                $idDriver = $this->container->get("driver_service")->getId($user->getId());
                return $this->render('user/panel.html.twig', array("idDriver" => $idDriver));
            }

        }
        return $this->redirect("logout");
    }

}
