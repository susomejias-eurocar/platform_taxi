<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;


class SecurityController extends Controller
{

    /**
     * Envía un email de confirmación al registrarse un usuario
     *
     * @param Request $request
     * @return void
     */
    public function sendEmailConfirmationAction(Request $request){
        $email = $request->get('email');
        $em = $this->getDoctrine()->getManager();
        // Si no existe el usuario, no continuamos
        if(!$user = $em->getRepository('AppBundle:User')->findOneByEmail($email))
            return new JsonResponse(array(
                "status" => false,
                "message" => "El correo electrónico no existe en el sistema"
            ));
        //Obtenemos los datos del usuario para rellenar el email
        $tokenRegister = $user->getTokenRegister();
        $username = $user->getUsername();
        $serviceMailer = $this->container->get("mail_service");
        //Enviamos el email al usuario y lo devolvemos a la pantalla de login
        $serviceMailer->send("register", $username, $email, $tokenRegister);
        return $this->redirect('login');
    }

    /**
     * Confirma el registro del usuario, cambiado el estado de activo a 1 y borrando su token y fecha
     *
     * @param Request $request
     * @return void
     */
    public function registerConfirmationAction(Request $request)
    {
        //Comprobamos que nos llegue el token via GET, si no es así, redirigimos al login
        if (!$tokenRegister = $request->get('tokenRegister')) {
            return $this->redirect("logout");
        }
        $em = $this->getDoctrine()->getManager();
        //Obtenemos el usuario a partir del token, si no existe, no continuamos        
        if (!$user = $em->getRepository('AppBundle:User')->findOneByTokenRegister($tokenRegister)) {
            return new JsonResponse(array(
                "status" => false,
                "message" => "El token no es válido"
            ));
        }
        //Si la fecha actual es mayor a la fecha de vencimiento del token, no continuamos
        elseif (strtotime(date("d-m-Y")) > strtotime($user->getExpirationTokenRegister())) {
            return new JsonResponse(array(
                "status" => false,
                "message" => "El token ha vencido, se le enviará otro al correo electrónico"
            ));
        }
        //Activamos el usuario y ponemos el token y fecha de registro null
        $user->setActive(1);
        $user->setTokenRegister(null);
        $user->setExpirationTokenRegister(null);
        //Persistimos el usuario en la bbdd
        $em->persist($user);
        $em->flush();
           return $this->redirectToRoute("login", array(
            "status" => true,
            "message" => "El registro de ha confirmado correctamente"
        ));
    }

    /**
     * Genera el token para recuperar contraseña y envía el correo al usuario
     *
     * @param Request $request
     * @return void
     */
    public function createTokenPasswordAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $email = $request->get('email');
        if($email=="")
            return new JsonResponse(array(
                "status" => false,
                "message" => "No existe ninguna cuenta con ese email"
            ));
        elseif (!$user = $em->getRepository('AppBundle:User')->findOneByEmail($email)) {
            return new JsonResponse(array(
                "status" => false,
                "message" => "No existe ninguna cuenta con ese email"
            ));
        }
        //Creamos el token y la fecha de vencimiento de este
        $user->setTokenPassword(bin2hex(random_bytes(64)));
        $user->setExpirationTokenPassword(date("d-m-Y",strtotime(date("d-m-Y")."+ 1 days")));
        //Persistimos en la bbdd
        $em->persist($user);
        $em->flush($user);
        return new JsonResponse(array(
            "status" => false,
            "message" => "Se ha enviado un correo electrónico para restablecer la contraseña"
        ));

    }

    /**
     * Muestra el formulario de recuperar contraseña
     *
     * @param Request $request
     * @return void
     */
    public function showFormRestorePasswordAction(Request $request)
    {
        //Comprobamos que nos llegue el token via GET, si no es así, redirigimos al login
        if (!$tokenPassword = $request->get('tokenPassword')) {
            return $this->redirect("logout");
        }
        $em = $this->getDoctrine()->getManager();
        //Obtenemos el usuario a partir del token, si no existe, no continuamos        
        if (!$user = $em->getRepository('AppBundle:User')->findOneByTokenPassword($tokenPassword)) {
            $response = array(
                "status" => false,
                "message" => "El token no es válido"
            );
            return new JsonResponse($response);
        }
        //Si la fecha actual es mayor a la fecha de vencimiento del token, no continuamos
        elseif (strtotime(date("d-m-Y")) > strtotime($user->getExpirationTokenPassword())) {
            return new JsonResponse(array(
                "status" => false,
                "message" => "El token ha vencido, se enviará otro correo electrónico con uno nuevo"
            ));
        }
        return $this->render(".html.twig", array("user"=>$user, "token" => $tokenPassword));
    }

    /**
     * Cambia la contraseña de un usuario
     *
     * @param Request $request
     * @return void
     */
    public function changePaswordAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $password1 = $request->get('password1');
        $password2 = $request->get('password2');
        $email = $request->get('email');
        //Comprobamos que existe el usuario con el email obtenido del formulario
        if (!$user = $em->getRepository('AppBundle:User')->findOneByEmail($email)) {
            return new JsonResponse(array(
                "status" => false,
                "message" => "El correo electrónico no existe en el sistema"
            ));
        }
        //Comprobamos que el token del usuario es el mismo que el que nos llega
        $tokenPassword = $request->get('tokenPassword');
        if($tokenPassword!=$user->getTokenPassword())
            return new JsonResponse(array(
                "status" => false,
                "message" => "El token no es válido"
            ));
        //Comprobamos que las contraseñas introducidas sean válidas
        elseif($password1=="" || $password2=="")
            return new JsonResponse(array(
                "status" => false,
                "message" => "Las contraseñas no pueden estar vacías"
            ));
        elseif(!preg_match("/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.{8,})/", $password1))        
            return new JsonResponse(array(
                "status" => false,
                "message" => "La contraseña debe contener al menos una maýuscula, una mínúscula, un número y al menos 8 caracteres"
            ));
        //Cambiamos la contraseña del usuario y quitamos el token de contraseña y sy fecha de vencimiento
        $user->setPassword($password1);
        $user->setTokenPassword(null);
        $user->setExpirationTokenPassword(null);
        $em->persist($user);
        $em->flush();
        return new JsonResponse(array(
            "status" => false,
            "message" => "La contraseña se ha cambiado correctamente"
        ));
    }
}
