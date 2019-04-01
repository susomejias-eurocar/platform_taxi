<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;

class UserController extends Controller
{
    public function loginAction(Request $request)
    {
        return $this->render('user/login.html.twig', array());
    }


    public function registerAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        if ($request->getMethod()=== "POST"){
            $user = new User();

            $email = $request->get('email');
            $password1 = $request->get('password1');
            $password2 = $request->get('password2');
            $phone = $request->get('phone');
    
            // dump($email);
            // die();
            if ($password1 === $password2){
                $user->setEmail($email);
                $user->setPassword($password1);
                $user->setPhone($phone);
                $user->setActive(0);
                $user->setPermission(1);

                
                //$user->setIdPermission(1);
        
                //$userRepository = $em->getRepository('AppBundle:User');
        
                $em->persist($user);
                
            }

            $em->flush();
        }

        return $this->render('user/register.html.twig', array());
    }
}
