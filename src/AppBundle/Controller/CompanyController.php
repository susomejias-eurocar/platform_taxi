<?php

namespace AppBundle\Controller;

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
     */
    public function registerAction(Request $request)
    {
        return $this->render('user/register.html.twig', array());
    }

    /**
     * Registra una compañia
     *
     */
    public function registerAjaxAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        //Obtenemos los datos del request
        $name = $request->get('name');
        $lastName = $request->get('lastName');
        $email = $request->get('email');
        $password1 = $request->get('password1');
        $password2 = $request->get('password2');
        $phone = $request->get('phone');
        $companyName = $request->get('companyName');
        $companyAddress = $request->get('companyAdress');
        //Hacemos las comprobaciones pertinentes
        if ($password1 != $password2) {
            $response = array(
                "status" => false,
                "message" => "Las contraseñas no coinciden"
            );
        } elseif (empty($email) or empty($phone) or empty($password1) or empty($password2) or empty($companyName) or empty($companyAddress)) {
            $response = array(
                "status" => false,
                "message" => "Rellene los campos"
            );
        } elseif (strlen($phone) < 6) {
            $response = array(
                "status" => false,
                "message" => "Formato del teléfono no válido, mínimo 6 números"
            );
        } elseif (!preg_match("/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.{8,})/", $password1)) {
            $response = array(
                "status" => false,
                "message" => "La contraseña debe contener al menos una maýuscula, una mínúscula, un número y al menos 8 caracteres"

            );
        } else {
            try {
                //Creamos la entidad Compañia y Usuario y las seteamos
                $company = new Company();
                $company->setName($companyName);
                $company->setAddress($companyAddress);
                $em->persist($company);
                $user = new User();
                $user->setName($name);
                $user->setLastName($lastName);
                $user->setActive(0);
                $user->setRoles(array("ROLE_COMPANY"));
                $user->setEmail($email);
                $encoder = $this->container->get('security.password_encoder');
                $encoded = $encoder->encodePassword($user, $password1);
                $user->setPassword($encoded);
                $user->setPhone($phone);
                $user->setCompanys($company);
                $em->persist($user);
                $em->flush();
                //Comprobamos que los atributos unique no se repiten
            } catch (\Doctrine\DBAL\DBALException $e) {
                $response = array(
                    "status" => false,
                    "message" => "El correo o nombre de la empresa ya está registrado"
                );
                return new JsonResponse($response);
            }
            $response = array(
                "status" => true,
                "message" => "Registro correcto",
                "text" => "Para completar el registro revise su correo electrónico </br></br>" . $email
            );
            //Enviamos el correo de confirmación de registro
            $mailService = $this->container->get("mail_service");
            $mailService->send('register',$email,$name, 'http://localhost/platform_taxi/web/app_dev.php/register/confirm?tokenRegister=',  $user->getTokenRegister());
            return new JsonResponse($response);
        }
    }

    /**
     * Muestra el formulario de registro
     */
    public function registerUserAction()
    {
        $security_context = $this->get('security.token_storage');
        $security_token = $security_context->getToken();
        $user = $security_token->getUser();
        $companyId = $user->getCompanys()->getId();
        if ($user) {
            return $this->render('company/content-panel-registerUser.html.twig', array("user_type" => "company", "companyId" => $companyId));
        }
    }

    /**
     * Registra el usuario
     */
    public function registerUserAjaxAction(Request $request)
    {
        $response = array(
            "status" => false,
            "message" => "ERROR"
        );
        $em = $this->getDoctrine()->getManager();
        //Recogemos los datos del request
        $name = $request->get('name');
        $lastName = $request->get('lastName');
        $email = $request->get('email');
        $password1 = $request->get('password1');
        $password2 = $request->get('password2');
        $phone = $request->get('phone');
        $companyId = $request->get('idCompany');
        //Hacemos las comprobaciones pertinentes
        if ($password1 != $password2) {
            $response = array(
                "status" => false,
                "message" => "Las contraseñas no coinciden"
            );
        } elseif (empty($name) or empty($lastName) or empty($email) or empty($phone) or empty($password1) or empty($password2)) {
            $response = array(
                "status" => false,
                "message" => "Rellene los campos"
            );
        } elseif (strlen($phone) < 6) {
            $response = array(
                "status" => false,
                "message" => "Formato del teléfono no válido, mínimo 6 números"
            );
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response = array(
                "status" => false,
                "message" => "El email no tiene un formato válido"
            );
        } elseif (!preg_match("/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.{8,})/", $password1)) {
            $response = array(
                "status" => false,
                "message" => "Contraseña demasiado corta, mínimo 4 caracteres"
            );
        } else {
            try {

                $company = $em->getRepository('AppBundle:Company')->findOneById($companyId);
                $user = new User();
                $encoder = $this->container->get('security.password_encoder');
                $encoded = $encoder->encodePassword($user, $password1);
                $user->setName($name);
                $user->setLastName($lastName);
                $user->setEmail($email);
                $user->setPassword($encoded);
                $user->setPhone($phone);
                $user->setCompanys($company);
                $user->setRoles(array("ROLE_DRIVER"));
                $em->persist($user);
                $em->flush();
            } catch (UniqueConstraintViolationException $e) {
                $response = array(
                    "status" => false,
                    "message" => "El correo ya está registrado"
                );
                return new JsonResponse($response);
            }
            $response = array(
                "status" => true,
                "message" => "Registro correcto"
            );
            return new JsonResponse($response);
        }
    }

    /**
     * Muestra la vista de todos los coches.
     *
     */
    public function showCarsAction()
    {
        $security_context = $this->get('security.token_storage');
        $security_token = $security_context->getToken();
        $user = $security_token->getUser();
        if ($user) {
            if ($this->get('security.context')->isGranted('ROLE_COMPANY')) {
                return $this->render('company/content-panel-showCars.html.twig', array("user_type" => "company"));
            }
        }
    }

    /**
     * Devuelve la lista de todos los coches para mostrarlo en el datatables
     *
     * @param Request $request
     */
    public function listCarsAction(Request $request)
    {
        $security_context = $this->get('security.token_storage');
        $security_token = $security_context->getToken();
        $user = $security_token->getUser();
        $companyService = $this->get('company_service');
        $companyService = $this->get('company_service');
        $companyId = $user->getCompanys()->getId();
        $params = $request->request->all();
        $getAllCarsCompany = $companyService->getAllCars($params, $companyId);
        $response = new Response();
        $response->setContent(json_encode($getAllCarsCompany));
        return $response;
    }


    /**
     * Muestra la vista con todos los conductores
     *
     */
    public function showDriversAction()
    {
        $security_context = $this->get('security.token_storage');
        $security_token = $security_context->getToken();
        $user = $security_token->getUser();
        $usersService = $this->get('user_service');
        if ($user) {
            return $this->render('company/content-panel-showDrivers.html.twig', array("user_type" => "company"));
        }
    }

    /**
     * Devuelve la lista de todos los conductores para mostrarlo en el datatables
     *
     * @param Request $request
     */
    public function listDriversAction(Request $request)
    {
        $security_context = $this->get('security.token_storage');
        $security_token = $security_context->getToken();
        $user = $security_token->getUser();
        $companyService = $this->get('company_service');
        $companyService = $this->get('company_service');
        $params = $request->request->all();
        $companyId = $user->getCompanys()->getId();
        $getAllDriversCompany = $companyService->getAllDrivers($params, $companyId);
        $response = new Response();
        $response->setContent(json_encode($getAllDriversCompany));
        return $response;
    }

    /**
     * Muestra el formulario para crear un coche
     *
     * @param Request $request
     */
    public function registerDriverAction(Request $request)
    {
        $security_context = $this->get('security.token_storage');
        $security_token = $security_context->getToken();
        $user = $security_token->getUser();
        $companyId = $user->getCompanys()->getId();
        if ($user) {
            $cars = $this->get("company_service")->getCarWithoutDriver($companyId);
            return $this->render('company/content-panel-createDriver.html.twig', array("cars" => $cars, "user_type" => "company", "companyId" => $companyId));
        }
    }

    /**
     * Añade un conductor a la compañia
     *
     * @param Request $request
     */
    public function addDriverAction(Request $request)
    {
        $response = array(
            "status" => false,
            "message" => "ERROR"
        );
        $em = $this->getDoctrine()->getManager();
        //Recogemos los datos del request
        $email = $request->get('email');
        $password1 = $request->get('password1');
        $password2 = $request->get('password2');
        $phone = $request->get('phone');
        $driverName = $request->get('driverName');
        $driverLastName = $request->get('driverLastName');
        $idCar = $request->get('car');
        $idCompany = $request->get('idCompany');
        //Hacemos las comprobaciones pertinentes
        if ($idCar == 0)
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
        } elseif (!preg_match("/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.{8,})/", $password1)) {
            $response = array(
                "status" => false,
                "message" => "La contraseña debe contener al menos una maýuscula, una mínúscula, un número y al menos 8 caracteres"

            );
        } else {
            try {
                //Creamos las entidades Usuario y Conductor para meterlas en la bbdd
                $user = new User();
                $user->setName($driverName);
                $user->setLastName($driverLastName);
                $user->setRoles(array("ROLE_DRIVER"));
                $user->setEmail($email);
                $encoder = $this->container->get('security.password_encoder');
                $encoded = $encoder->encodePassword($user, $password1);
                $user->setPassword($encoded);
                $user->setPhone($phone);
                $user->setCompanys($this->container->get('company_service')->getCompany($idCompany));
                $em->persist($user);
                $driver = new Driver();
                $driver->setUser($user);
                $driver->setCar($car);
                $driver->setState('Disponible');
                $em->persist($driver);
                $em->flush();
                $response = array(
                    "status" => true,
                    "message" => "Registro correcto"
                );
                //Controlamos que no se repitan los campos unique
            } catch (\Doctrine\DBAL\DBALException $e) {
                $response = array(
                    "status" => false,
                    "message" => $e->getMessage()
                );
                return new JsonResponse($response);
            }
        }
        return new JsonResponse($response);
    }

    /**
     * Get all avalaible driverss
     *
     */
    public function listAllDriversAction()
    {
        $service = $this->container->get('company_service');
        $drivers = $service->getAvalaibledDrivers(1);
        return new JsonResponse($drivers);
    }

    /**
     * Muestra el formulario de registrar un coche
     */
    public function registerCarAction()
    {
        $security_context = $this->get('security.token_storage');
        $security_token = $security_context->getToken();
        $user = $security_token->getUser();
        $companyId = $user->getCompanys()->getId();
        $cars = $this->get("company_service")->getCarWithoutDriver($companyId);
        return $this->render('car/content-panel-createCar.html.twig', array("cars" => $cars, "user_type" => "company", "companyId" => $companyId));
    }

    /**
     * Añade un coche a la compañia
     * @param Request $request
     */
    public function addCarAction(Request $request)
    {
        $response = array(
            "status" => false,
            "message" => "ERROR"
        );
        //Obtenemos los datos del request
        $em = $this->getDoctrine()->getManager();
        $plate = $request->get('plate');
        $trademark = $request->get('trademark');
        $model = $request->get('model');
        $version = $request->get('version');
        $state = $request->get('state');
        $idCompany = $request->get('idCompany');
        //Hacemos las comprobaciones pertinentes
        if (!preg_match("/^\d{4}[A-Z]{3}/", $plate)) {
            $response = array(
                "status" => false,
                "message" => "Formato de matrícula incorrecto"
            );
        } else if (empty($plate) or empty($trademark) or empty($model) or empty($version) or empty($state)) {
            $response = array(
                "status" => false,
                "message" => "Rellene los campos"
            );
        } else {
            try {
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
                return new JsonResponse($response);
                //Controlamos que no se repitan los campos unique
            } catch (\Doctrine\DBAL\DBALException $e) {
                $response = array(
                    "status" => false,
                    "message" => "La matrícula introducida ya está registrada"
                );
                return new JsonResponse($response);
            }
        }
        return new JsonResponse($response);
    }

    /**
     * Muestra el formulario de asignación de coche
     */
    public function asignCarToDriverAction()
    {
        $security_context = $this->get('security.token_storage');
        $security_token = $security_context->getToken();
        $user = $security_token->getUser();
        $companyService = $this->get('company_service');
        $companyId = $user->getCompanys()->getId();
        $cars = $companyService->getCarWithoutDriver($companyId);
        $drivers = $companyService->getDriversWithoutCar($companyId);
        return $this->render('car/content-panel-asignCar.html.twig', array("drivers" => $drivers, "cars" => $cars, "user_type" => "company",  "companyId" => $companyId));
    }

    /**
     * Asigna un coche a un conuctor
     * @param Request $request
     */
    public function asignCarAction(Request $request)
    {
        $response = array(
            "status" => false,
            "message" => "ERROR"
        );
        $em = $this->getDoctrine()->getManager();
        $idDriver = $request->get('idDriver');
        $idCar = $request->get('idCar');
        $this->get('company_service')->asignCarToCompany($idDriver, $idCar);
        $response = array(
            "status" => true,
            "message" => "Se ha asignado el coche al conductor"
        );
        return new JsonResponse($response);
    }

    /**
     * Muestra la vista del mapa
     */
    public function openMapAction(Request $request)
    {
        return $this->render('company/content-panel-showMap.html.twig', array());
    }

    /**
     * Muestra el formulario de edición de la compañia
     *
     */
    public function showEditCompanyAction()
    {
        return $this->render('company/content-panel-editCompany.html.twig', array());
    }

    /**
     * Edita la información de la compañia
     *
     * @param Request $request
     */
    public function editCompanyAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        //Recogemos los datos del request
        $userName = $request->get('userName');
        $lastName = $request->get('lastName');
        $email = $request->get('email');
        $phone = $request->get('phone');
        $password = $request->get('password');
        $password2 = $request->get('password2');
        $companyName = $request->get('companyName');
        $companyAddress = $request->get('companyAddress');
        $security_context = $this->get('security.token_storage');
        $security_token = $security_context->getToken();
        $user = $security_token->getUser();
        $response = array(
            "status" => false,
            "message" => "Error"
        );
        if (empty($userName) or empty($lastName) or empty($email) or empty($phone)) {
            $response = array(
                "status" => false,
                "message" => "Rellene los campos"
            );
        } else if ($password !== $password2) {
            $response = array(
                "status" => false,
                "message" => "Las contraseñas no coinciden"
            );
        } else {
            try {
                $user->setName($userName);
                //$user->setActive(0);
                $user->setLastName($lastName);
                $user->setEmail($email);
                if ($password === $password2 and !empty($password) and !empty($password2)) {
                    $encoder = $this->container->get('security.password_encoder');
                    $encoded = $encoder->encodePassword($user, $password);
                    $user->setPassword($encoded);
                }
                $user->setPhone($phone);
                $company = $em->getRepository('AppBundle:Company')->findOneById($user->getCompanys()->getId());
                $company->setName($companyName);
                $company->setAddress($companyAddress);
                $em->persist($user);
                $em->persist($company);
                $response = array(
                    "status" => true,
                    "message" => "Edición completada con éxito"
                );
            } catch (\Doctrine\DBAL\DBALException $e) {
                $response = array(
                    "status" => false,
                    "message" => "EL correo o nombre de compañia introducidos ya existen"
                );
            }
        }
        $em->flush();

        return new JsonResponse($response);
    }
}
