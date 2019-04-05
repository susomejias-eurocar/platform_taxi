<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\User;
use AppBundle\Entity\Driver;
use AppBundle\Entity\Car;
use AppBundle\Entity\Company;
use Symfony\Component\HttpFoundation\JsonResponse;

class CompanyController extends Controller
{

    /**
     * Renderiza la vista de registro de compañia
     *
     * @param Request $request
     * @return void
     */
    public function registerAction(Request $request)
    {
        return $this->render('user/register.html.twig', array());
    }

    /**
     * Registra una comapañia
     *
     * @param Request $request
     * @return void
     */
    public function registerAjaxAction(Request $request)
    {
        $response = array(
            "status" => false,
            "message" => "ERROR"
        );
        $em = $this->getDoctrine()->getManager();
        $name = $request->get('name');
        $lastName = $request->get('lastName');

            $email = $request->get('email');
            $password1 = $request->get('password1');
            $password2 = $request->get('password2');
            $phone = $request->get('phone');
            $companyName = $request->get('companyName');
            $companyAddress = $request->get('companyAdress'); 
            if ($password1 != $password2){
                $response = array(
                    "status" => false,
                    "message" => "Las contraseñas no coinciden"
                );
            }elseif (empty($email) or empty($phone) or empty($password1) or empty($password2) or empty($companyName) or empty($companyAddress)){
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
                try{
                    $permissionFull = $em->getRepository('AppBundle:Permission')->findOneBy(
                        array('id'=> 1)
                    );
                    $company = new Company();
                    $company->setName($companyName);
                    $company->setAddress($companyAddress);
                    $em->persist($company);
                    $user = new User();
                    $user->setName($name);
                    $user->setLastName($lastName);

                    $user->setRoles(array("ROLE_COMPANY"));
                    $user->setEmail($email);
                    $encoder = $this->container->get('security.password_encoder');
                    $encoded = $encoder->encodePassword($user, $password1);
                    $user->setPassword($encoded);
                    $user->setPhone($phone);
                    $user->setActive(0);
                    $user->setCompanys($company);
                    $user->setPermission($permissionFull);
                    $em->persist($user);
                    $em->flush();
                }catch(UniqueConstraintViolationException $e){
                    $response = array(
                        "status" => false,
                        "message" => "El correo ya está registrado"
                    );
                    $result->setContent(json_encode($response));
                    return $result;
                }
                $response = array(
                    "status" => true,
                    "message" => "Registro correcto"
                );
                return new JsonResponse($response);
            }
    }

    public function registerUserAction(){
        $security_context = $this->get('security.context');
        $security_token = $security_context->getToken();
        $user = $security_token->getUser();
        $companyId = $user->getCompanys()->getId();
        if($user){
            return $this->render('company/content-panel-registerUser.html.twig', array("user_type" => "company", "companyId" => $companyId));
        }
    }

    public function registerUserAjaxAction(Request $request)
    {
        $response = array(
            "status" => false,
            "message" => "ERROR"
        );
        $em = $this->getDoctrine()->getManager();
            $name = $request->get('name');
            $lastName = $request->get('lastName');
            $email = $request->get('email');
            $password1 = $request->get('password1');
            $password2 = $request->get('password2');
            $phone = $request->get('phone');
            $permissionsId = $request->get('companyName');
            $companyId = $request->get('idCompany');

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
                try{
                    $permission = $em->getRepository('AppBundle:Permission')->findOneBy(
                        array('id'=> $permissionsId)
                    );
                    $company = $em->getRepository('AppBundle:Company')->findOneBy(
                        array('id'=> $companyId)
                    );
                    $user = new User();                    
                    $encoder = $this->container->get('security.password_encoder');
                    $encoded = $encoder->encodePassword($user, $password1);
                    $user->setName($name);
                    $user->setLastName($lastName);
                    $user->setEmail($email);
                    $user->setPassword($encoded);
                    $user->setPhone($phone);
                    $user->setActive(0);
                    $user->setPermission($permission);
                    $user->setCompanys($company);
                    $em->persist($user);
                    $em->flush();
                }catch(UniqueConstraintViolationException $e){
                    $response = array(
                        "status" => false,
                        "message" => "El correo ya está registrado"
                    );
                    $result->setContent(json_encode($response));
                    return $result;
                }
                $response = array(
                    "status" => true,
                    "message" => "Registro correcto"
                );

                return new JsonResponse($response);

            }
    }

    /**
     * Show the view for the datatable.
     *
     * @return void
     */
    public function showCarsAction()
    {
        $security_context = $this->get('security.context');
        $security_token = $security_context->getToken();
        $user = $security_token->getUser();
        $usersService = $this->get('user_service');
        if($user){
            if ($this->get('security.context')->isGranted('ROLE_COMPANY')) {
                return $this->render('company/content-panel-showCars.html.twig', array("user_type" => "company"));
            }
        }   
    }

    /**
     * Make a request for ajax and get all the cars of a company.
     *
     * @param Request $request
     * @return void
     */
    public function listCarsAction(Request $request)
    {
        $security_context = $this->get('security.context');
        $security_token = $security_context->getToken();
        $user = $security_token->getUser();
        $companyService = $this->get('company_service');
        $companyService = $this->get('company_service');
        $companyId = $user->getCompanys()->getId();
        $params = $request->request->all();
        $getAllCarsCompany = $companyService->getAllCars($params,$companyId);
        $response = new Response();
        $response->setContent(json_encode($getAllCarsCompany));
        return $response;
    }


    /**
     * Show the view for the datatable.
     *
     * @return void
     */
    public function showDriversAction()
    {
        $security_context = $this->get('security.context');
        $security_token = $security_context->getToken();
        $user = $security_token->getUser();
        $usersService = $this->get('user_service');
        if($user){
            return $this->render('company/content-panel-showDrivers.html.twig', array("user_type" => "company"));
        }

    }

    /**
     * Make a request for ajax and get all the drivers of a company.
     *
     * @param Request $request
     * @return void
     */
    public function listDriversAction(Request $request)
    {
        $security_context = $this->get('security.context');
        $security_token = $security_context->getToken();
        $user = $security_token->getUser();
        $companyService = $this->get('company_service');
        $companyService = $this->get('company_service');
        $params = $request->request->all();
        $companyId = $user->getCompanys()->getId();
        $getAllDriversCompany = $companyService->getAllDrivers($params,$companyId);
        $response = new Response();
        $response->setContent(json_encode($getAllDriversCompany));
        return $response;
    }

    /**
     * Show view to create a driver.
     *
     * @param Request $request
     * @return void
     */
    public function registerDriverAction(Request $request)
    {
        $security_context = $this->get('security.context');
        $security_token = $security_context->getToken();
        $user = $security_token->getUser();
        $companyId = $user->getCompanys()->getId();
        if($user){
            $cars = $this->get("company_service")->getCarWithoutDriver($companyId);
            return $this->render('company/content-panel-createDriver.html.twig', array("cars"=>$cars,"user_type" => "company", "companyId" => $companyId));
        }    
    }

    /**
     * Receives form data by ajax by post
     *
     * @param Request $request
     * @return void
     */
    public function addDriverAction(Request $request)
    {
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
        $idCompany = $request->get('idCompany');
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
            $user->setName($driverName);
            $user->setLastName($driverLastName);
            $user->setRoles(array("ROLE_DRIVER"));
            $user->setEmail($email);
            $encoder = $this->container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($user, $password1);
            $user->setPassword($encoded);
            $user->setPhone($phone);
            $user->setActive(1);
            $user->setPermission($permissionFull);
            $user->setCompanys($this->container->get('company_service')->getCompany($idCompany));
            $em->persist($user);
            $driver = new Driver();
            $driver->setUser($user);
            $driver->setCar($car);
            $driver->setState('register');
            $em->persist($driver);
            $em->flush();
            $response = array(
                "status" => true,
                "message" => "Registro correcto"
            );
        }
        return new JsonResponse($response);
    }

    /**
     * Get all avalaible driverss
     *
     * @return void
     */
    public function listAllDriversAction()
    {
        $service = $this->container->get('company_service');
        $drivers = $service->getAvalaibledDrivers(1);
        return new JsonResponse($drivers);
    }

    /**
     * Renderiza la vista de registro de coche
     *
     * @return void
     */
    public function registerCarAction(){
        $security_context = $this->get('security.context');
        $security_token = $security_context->getToken();
        $user = $security_token->getUser();
        $usersService = $this->get('user_service');
        $companyId = $user->getCompanys()->getId();
        $cars = $this->get("company_service")->getCarWithoutDriver($companyId);
        return $this->render('car/content-panel-createCar.html.twig', array("cars"=>$cars,"user_type" => "company", "companyId" => $companyId));
    }

    public function addCarAction(Request $request){
        $response = array(
            "status" => false,
            "message" => "ERROR"
        );
        $em = $this->getDoctrine()->getManager();
        $plate = $request->get('plate');
        $trademark = $request->get('trademark');
        $model = $request->get('model');
        $version = $request->get('version');
        $state = $request->get('state');
        $idCompany = $request->get('idCompany');
        if (empty($plate) or empty($trademark) or empty($model) or empty($version) or empty($state)) {
            $response = array(
                "status" => false,
                "message" => "Rellene los campos"
            );
        }else {
            $permissionFull = $em->getRepository('AppBundle:Permission')->findOneBy(
                array('id' => 1)
            );
            $car = new Car();
            $car->setPlate($plate);
            $car->setTrademark($trademark);
            $car->setModel($model);
            $car->setVersion($version);
            $car->setState($state);
            $car->setCompany($this->container->get('company_service')->getCompany($idCompany));
            $em->persist($car);
            $em->flush();
            $response = array(
                "status" => true,
                "message" => "Registro correcto"
            );
        }
        return new JsonResponse($response);
    }

    /**
     * Assign a car to a driver
     *
     * @return void
     */
    public function asignCarToDriverAction(){
        $security_context = $this->get('security.context');
        $security_token = $security_context->getToken();
        $user = $security_token->getUser();
        $usersService = $this->get('user_service');
        $companyService = $this->get('company_service');
        $companyId = $user->getCompanys()->getId();
        $cars = $companyService->getCarWithoutDriver($companyId);
        $drivers = $companyService->getDriversWithoutCar($companyId);
        return $this->render('car/content-panel-asignCar.html.twig', array("drivers"=> $drivers, "cars"=>$cars,"user_type" => "company",  "companyId" => $companyId));
    }

    public function asignCarAction(Request $request){
        $response = array(
            "status" => false,
            "message" => "ERROR"
        );
        $em = $this->getDoctrine()->getManager();
        $idDriver = $request->get('idDriver');
        $idCar = $request->get('idCar');
        $this->get('company_service')->asignCarToCompany($idDriver,$idCar);
        $response = array(
            "status" => true,
            "message" => "Se ha asignado el coche al conductor"
        );
        return new JsonResponse($response);
    }
}
