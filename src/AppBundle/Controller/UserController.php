<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;
use AppBundle\Entity\Company;
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

            

            $email = $request->get('email');
            $password1 = $request->get('password1');
            $password2 = $request->get('password2');
            $phone = $request->get('phone');
            $companyName = $request->get('companyName');
            $companyAdress = $request->get('companyAdress'); 

    

            if ($password1 != $password2){
                $response = array(
                    "status" => false,
                    "message" => "Las contraseñas no coinciden"
                );
            }elseif (empty($email) or empty($phone) or empty($password1) or empty($password2) or empty($companyName) or empty($companyAdress)){
                $response = array(
                    "status" => false,
                    "message" => "Rellene los campos"
                );
            }elseif(strlen($phone) < 6){

                $response = array(
                    "status" => false,
                    "message" => "Formato del teléfono no válido, mínimo 6 números"
                );

            }elseif (strlen($password1) < 4){
                
                $response = array(
                    "status" => false,
                    "message" => "Contraseña demasiado corta, mínimo 4 caractres"
                );

            }else{


                $permissionFull = $em->getRepository('AppBundle:Permission')->findOneBy(
                    array('id'=> 1)
                );
                $user = new User();
                $user->setEmail($email);
                $user->setPassword($password1);
                $user->setPhone($phone);
                $user->setActive(0);
                $user->setPermission($permissionFull);

                //$user->setIdPermission(1);
        
                //$userRepository = $em->getRepository('AppBundle:User');
        
                $em->persist($user);
                

                $company = new Company();
                $company->setUser($user); 
                $company->setName($companyName);
                $company->setAddress($companyAdress);

                $em->persist($company);

                $em->flush();

                $response = array(
                    "status" => true,
                    "message" => "Registro correcto"
                );
            }

            $result->setContent(json_encode($response));

            return $result;

    }

    public function panelAction(Request $request)
    {

        $security_context = $this->get('security.context');

        $security_token = $security_context->getToken();

        $user = $security_token->getUser();

        $usersService = $this->get('user_service');

        $isCompany = $usersService->isTypeUser("company",$user->getId());

        $isDriver = $usersService->isTypeUser("company",$user->getId());

        if($user){
            if ($isCompany){
                $companyService = $this->get('company_service');

                $companyName = $companyService->getCompanyName($user->getId());

                return $this->render('user/panel.html.twig', array("user_type" => "company", "companyName" => $companyName[0]["name"]));
            }elseif($isDriver){
                return $this->render('user/panel.html.twig', array("user_type" => "driver"));
            }
        }

    }

}
