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

class CompanyController extends Controller
{

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

                $result->setContent(json_encode($response));

                return $result;
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

        $isCompany = $usersService->isTypeUser("company",$user->getId());

        $isDriver = $usersService->isTypeUser("company",$user->getId());

        if($user){
            if ($isCompany){
                $companyService = $this->get('company_service');

                $getCompanyInfo = $companyService->getCompanyInfo($user->getId());

                return $this->render('company/content-panel-showCars.html.twig', array("user_type" => "company", "companyName" => $getCompanyInfo[0]["name"], "companyAddress"=> $getCompanyInfo[0]["address"], "companyId" => $getCompanyInfo[0]['id'] ));
            }elseif($isDriver){
                
                return $this->render('driver/content-panel.html.twig', array("user_type" => "driver"));
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

        $getCompanyInfo = $companyService->getCompanyInfo($user->getId());

        $companyService = $this->get('company_service');

        $params = $request->request->all();
        $getAllCarsCompany = $companyService->getAllCars($params,$getCompanyInfo[0]["id"]);


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

        $isCompany = $usersService->isTypeUser("company",$user->getId());

        $isDriver = $usersService->isTypeUser("company",$user->getId());

        if($user){
            if ($isCompany){
                $companyService = $this->get('company_service');

                $getCompanyInfo = $companyService->getCompanyInfo($user->getId());

                return $this->render('company/content-panel-showDrivers.html.twig', array("user_type" => "company", "companyName" => $getCompanyInfo[0]["name"], "companyAddress"=> $getCompanyInfo[0]["address"], "companyId" => $getCompanyInfo[0]['id'] ));
            }elseif($isDriver){
                
                return $this->render('company/content-panel-showDrivers.html.twig', array("user_type" => "driver"));
            }
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

        $getCompanyInfo = $companyService->getCompanyInfo($user->getId());

        $companyService = $this->get('company_service');

        $params = $request->request->all();
        $getAllDriversCompany = $companyService->getAllDrivers($params,$getCompanyInfo[0]["id"]);

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

        $usersService = $this->get('user_service');

        $isCompany = $usersService->isTypeUser("company",$user->getId());

        $isDriver = $usersService->isTypeUser("company",$user->getId());

        if($user){
            if ($isCompany){
                $companyService = $this->get('company_service');

                $getCompanyInfo = $companyService->getCompanyInfo($user->getId());
                $cars = $this->get("company_service")->getCarWithoutDriver($getCompanyInfo[0]['id']);
                return $this->render('company/content-panel-createDriver.html.twig', array("cars"=>$cars,"user_type" => "company", "companyName" => $getCompanyInfo[0]["name"], "companyAddress"=> $getCompanyInfo[0]["address"], "companyId" => $getCompanyInfo[0]['id']));
            }elseif($isDriver){
                
                return $this->render('driver/content-panel.html.twig', array("user_type" => "driver"));
            }
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
            $driver->setCompany($this->container->get('company_service')->getCompany($idCompany));

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

    public function registerCarAction(){
        $security_context = $this->get('security.context');

        $security_token = $security_context->getToken();

        $user = $security_token->getUser();

        $usersService = $this->get('user_service');

        $isCompany = $usersService->isTypeUser("company",$user->getId());

        $isDriver = $usersService->isTypeUser("company",$user->getId());

        if($user){
            if ($isCompany){
                $companyService = $this->get('company_service');

                $getCompanyInfo = $companyService->getCompanyInfo($user->getId());
                $cars = $this->get("company_service")->getCarWithoutDriver($getCompanyInfo[0]['id']);
                return $this->render('car/content-panel-createCar.html.twig', array("cars"=>$cars,"user_type" => "company", "companyName" => $getCompanyInfo[0]["name"], "companyAddress"=> $getCompanyInfo[0]["address"], "companyId" => $getCompanyInfo[0]['id']));
            }elseif($isDriver){
                
                return $this->render('driver/content-panel.html.twig', array("user_type" => "driver"));
            }
        }  
    }

    public function addCarAction(Request $request){
        $result = new Response();


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

        $result->setContent(json_encode($response));

        return $result;
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

        $isCompany = $usersService->isTypeUser("company",$user->getId());

        $isDriver = $usersService->isTypeUser("company",$user->getId());

        if($user){
            if ($isCompany){
                $getCompanyInfo = $companyService->getCompanyInfo($user->getId());
                $cars = $companyService->getCarWithoutDriver($getCompanyInfo[0]['id']);
                $drivers = $companyService->getDriversWithoutCar($getCompanyInfo[0]['id']);

                return $this->render('car/content-panel-asignCar.html.twig', array("drivers"=> $drivers, "cars"=>$cars,"user_type" => "company", "companyName" => $getCompanyInfo[0]["name"], "companyAddress"=> $getCompanyInfo[0]["address"], "companyId" => $getCompanyInfo[0]['id']));
            }elseif($isDriver){
                
                return $this->render('driver/content-panel.html.twig', array("user_type" => "driver"));
            }
        }   
    }

    public function asignCarAction(Request $request){
        $result = new Response();


        $response = array(
            "status" => false,
            "message" => "ERROR"
        );

        $em = $this->getDoctrine()->getManager();


        $idDriver = $request->get('idDriver');
        $idCar = $request->get('idCar');

        $companyService = $this->get('company_service')->asignCarToCompany($idDriver,$idCar);
        
        $response = array(
            "status" => true,
            "message" => "Registro correcto"
        );
        

        $result->setContent(json_encode($response));

        return $result;
    }

}
