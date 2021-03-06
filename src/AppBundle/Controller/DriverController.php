<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;


class DriverController extends Controller
{
    public function editAction($idDriver)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $security_context = $this->get('security.token_storage');
        $security_token = $security_context->getToken();
        $user = $security_token->getUser();
        $companyId = $user->getCompanys()->getId();
        $driver = $em->getRepository("AppBundle:Driver")->findOneById($idDriver);
        $companyService = $this->container->get("company_service");
        if ($driver == null)
            return $this->redirectToRoute('show_drivers');
        $idDriver = $driver->getId();
        if ($companyService->existDriver($companyId, $idDriver))
            return $this->redirectToRoute('show_drivers');
        $user = $em->getRepository("AppBundle:User")->findOneById($driver->getUser());
        $carDriver = $em->getRepository("AppBundle:Car")->findOneById($driver->getCar());
        $carService = $this->get("company_service");
        $cars = $carService->getCarWithoutDriver($companyId);

        return $this->render('company/content-panel-createDriver.html.twig', array("idDriver" => $idDriver, "carDriver" => $carDriver, "cars" => $cars, "driver" => $driver, "user" => $user, "user_type" => "company", "companyId" => $companyId, "driverId" => $driver->getid()));
    }

    /**
     * Edit driver
     * 
     * @param Request $request
     */
    public function editAjaxAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $idDriver = $request->get('idDriver');
        $idUser = $request->get('idUser');
        $name = $request->get('driverName');
        $lastName = $request->get('driverLastName');
        $email = $request->get('email');
        $idCar = $request->get('car');
        $phone = $request->get('phone');
        $user = $request->get('user');
        $state = $request->get('state');
        $password1 = $request->get('password1');
        $password2 = $request->get('password2');

        if ($password1 != $password2) {
            $response = array(
                "status" => false,
                "message" => "Las contraseñas no coinciden"
            );
        } elseif (empty($name) || empty($lastName) || empty($email) || empty($phone)) {
            $response = array(
                "status" => false,
                "message" => "Rellene los campos"
            );
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response = array(
                "status" => false,
                "message" => "El email no tiene un formato válido"
            );
        } elseif (strlen($phone) < 6) {
            $response = array(
                "status" => false,
                "message" => "El teléfono no puede tener de 6 caracteres"
            );
        } else {
            $driver = $em->getRepository("AppBundle:Driver")->findOneById($idDriver);
            $carDriver = $em->getRepository("AppBundle:Car")->findOneById($idCar);
            $user = $em->getRepository("AppBundle:User")->findOneById($idUser);
            if (!empty($password1)) {
                if ($password1 != $password2) {
                    $response = array(
                        "status" => false,
                        "message" => "Las contraseñas no coinciden"
                    );
                } elseif (strlen($password1) < 4) {
                    $response = array(
                        "status" => false,
                        "message" => "La contraseña no puede tener menos de 4 caracteres"
                    );
                } else {
                    $encoder = $this->container->get('security.password_encoder');
                    $encoded = $encoder->encodePassword($user, $password1);
                    $user->setPassword($encoded);
                }
            }
            $user->setName($name);
            $user->setLastName($lastName);
            $user->setEmail($email);
            $user->setPhone($phone);
            $driver->setCar($carDriver);
            $driver->setState($state);
            $em->persist($driver);
            $em->flush();
            $response = array(
                "status" => true,
                "message" => "Conductor actualizado correctamente"
            );
        }
        return new JsonResponse($response);
    }


    /**
     * Delete driver
     * 
     * @param Request $request
     */
    public function deleteAction(Request $request)
    {

        $idDriver = $request->get('idDriver');
        $em = $this->getDoctrine()->getEntityManager();
        $driver = $em->getRepository("AppBundle:Driver")->findOneById($idDriver);
        $user = $driver->getUser();
        //$em->remove($driver);
        $user->setActive(0);
        $em->persist($driver);
        $em->flush();
        return new JsonResponse(array(
            "status" => true,
            "message" => "El conductor ha sido eliminado"
        ));
    }


    /**
     * Show form for change state
     * 
     */
    public function showFormSetStateAction()
    {
        $security_context = $this->get('security.token_storage');
        $security_token = $security_context->getToken();
        $user = $security_token->getUser();
        $em = $this->getDoctrine()->getEntityManager();
        $driver_id = $em->getRepository("AppBundle:Driver")->getId($user->getId());
        $driver_state_now = $em->getRepository("AppBundle:Driver")->getState($user->getId());
        return $this->render('driver/content-panel-changeState.html.twig', array('idDriver' => $driver_id, 'state_now' => $driver_state_now));
    }

    /**
     * Set state for driver
     * 
     * @param Request $request
     */
    public function setStateAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $state = $request->get('state');
        $driver_id = $request->get('idDriver');
        if (empty($state) or $driver_id) {
            $response = array(
                "status" => false,
                "message" => "Parámetros incorrectos"
            );
        }

        $driver = $em->getRepository("AppBundle:Driver")->setState($driver_id, $state);
        $response = array(
            "status" => true,
            "message" => "Estado modificado correctamente"
        );
        return new JsonResponse($response);
    }

    /**
     * Unassign car to a driver
     */
    public function unassignCarAction(Request $request)
    {
        $idDriver = $request->get('idDriver');
        $em = $this->getDoctrine()->getEntityManager();
        $driver = $em->getRepository("AppBundle:Driver")->findOneById($idDriver);
        $driver->setCar(null);
        $em->persist($driver);
        $em->flush();
        $response = array(
            "status" => true,
            "message" => "Coche desasignado correctamente"
        );
        return new JsonResponse($response);
    }

    /**
     * Open the map view for driver
     */
    public function openMapAction(Request $request)
    {
        $security_context = $this->get('security.token_storage');

        $security_token = $security_context->getToken();

        $user = $security_token->getUser();

        $usersService = $this->get('user_service');

        $idDriver = $this->container->get("driver_service")->getId($user->getId());
        return $this->render('driver/content-panel-showMap.html.twig', array('idDriver' => $idDriver));
    }
}
