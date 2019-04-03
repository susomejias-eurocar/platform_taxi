<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;;

class CompanyController extends Controller
{
    public function listCarsAction(Request $request)
    {

        $security_context = $this->get('security.context');

        $security_token = $security_context->getToken();

        $user = $security_token->getUser();

        $companyService = $this->get('company_service');

        $companyNameAddress = $companyService->getCompanyNameAddress($user->getId());

        $companyService = $this->get('company_service');

        $params = $request->request->all();
        $getAllCarsCompany = $companyService->getAllCars($params,$companyNameAddress[0]["id"]);

        $response = new Response();

        $response->setContent(json_encode($getAllCarsCompany));
        return $response;

    }

    public function showCarsAction()
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

                $companyNameAddress = $companyService->getCompanyNameAddress($user->getId());

                return $this->render('company/content-panel.html.twig', array("user_type" => "company", "companyName" => $companyNameAddress[0]["name"], "companyAddress"=> $companyNameAddress[0]["address"], "companyId" => $companyNameAddress ));
            }elseif($isDriver){
                
                return $this->render('driver/content-panel.html.twig', array("user_type" => "driver"));
            }
        }

        //return $this->render('company/content-panel.html.twig', array("user_type" => "company"));
    }
   public function registerDriverAction(Request $request)
    {
        $cars = $this->get("company_service")->getCarWithoutDriver(1);
        return $this->render('driver/register.html.twig', array("cars"=>$cars));
    }

    public function addDriverAction(Request $request)
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
        $driverName = $request->get('driverName');
        $driverLastName = $request->get('driverLastName');
        $idCar = $request->get('car');
        if($idCar==0)
            $car = null;
        else
            $car = $this->container->get('car_service')->getCar($idCar);



        if ($password1 != $password2) {
            $response = array(
                "status" => false,
                "message" => "Las contraseñas no coinciden"
            );
        } elseif (empty($email) or empty($phone) or empty($password1) or empty($password2) or empty($driverName) or empty($driverLastName)) {
            $response = array(
                "status" => false,
                "message" => "Rellene los campos"
            );
        } elseif (strlen($phone) < 6) {

            $response = array(
                "status" => false,
                "message" => "Formato del teléfono no válido, mínimo 6 números"
            );
        } elseif (strlen($password1) < 4) {

            $response = array(
                "status" => false,
                "message" => "Contraseña demasiado corta, mínimo 4 caractres"
            );
        } else {


            $permissionFull = $em->getRepository('AppBundle:Permission')->findOneBy(
                array('id' => 1)
            );
            $user = new User();
            $user->setEmail($email);

            $encoder = $this->container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($user, $password1);

            $user->setPassword($encoded);

            $user->setPhone($phone);
            $user->setActive(1);
            $user->setPermission($permissionFull);

            $em->persist($user);


            $driver = new Driver();
            $driver->setUser($user);
            $driver->setName($driverName);
            $driver->setLastName($driverLastName);
            $driver->setCar($car);
            $driver->setState('register');

            $em->persist($driver);

            $em->flush();

            $response = array(
                "status" => true,
                "message" => "Registro correcto"
            );
        }

        $result->setContent(json_encode($response));

        return $result;
    }

    public function listAllDriversAction()
    {
        $service = $this->container->get('company_service');
        $drivers = $service->getAvalaibledDrivers(1);
        return new JsonResponse($drivers);
    }
}
