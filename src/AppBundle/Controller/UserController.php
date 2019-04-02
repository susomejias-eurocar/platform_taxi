<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function loginAction(Request $request)
    {//Llamamos al servicio de autenticacion
        $authenticationUtils = $this->get('security.authentication_utils');
        
        // conseguir el error del login si falla
        $error = $authenticationUtils->getLastAuthenticationError();

        // ultimo nombre de usuario que se ha intentado identificar
        $lastUsername = $authenticationUtils->getLastUsername();
        
        return $this->render(
                'user/login.html.twig', array(
                    'last_username' => $lastUsername,
                    'error' => $error,
        ));
    }


    public function registerAction(Request $request)
    {
        return $this->render('user/register.html.twig', array());
    }

    public function registerAjaxAction(Request $request)
    {

        $result = new Response();


        $response = array(
            "status" => false,
            "message" => "ERROR"
        );

        

        $em = $this->getDoctrine()->getManager();

            $user = new User();

            $email = $request->get('email');
            $password1 = $request->get('password1');
            $password2 = $request->get('password2');
            $phone = $request->get('phone');
    

            if ($password1 != $password2){
                $response = array(
                    "status" => false,
                    "message" => "Las contraseñas no coinciden"
                );
            }elseif (empty($email) or empty($phone) or empty($password1) or empty($password2)){
                $response = array(
                    "status" => false,
                    "message" => "Rellene los campos"
                );
            }else{

                $user->setEmail($email);
                $user->setPassword($password1);
                $user->setPhone($phone);
                $user->setActive(0);
                $user->setPermission(1);

                //$user->setIdPermission(1);
        
                //$userRepository = $em->getRepository('AppBundle:User');
        
                $em->persist($user);
                $em->flush();

                $response = array(
                    "status" => true,
                    "message" => "Registro correcto"
                );
            }

            $result->setContent(json_encode($response));

            return $result;

    }

}
