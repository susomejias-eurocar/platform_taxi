<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;


class CarController extends Controller
{
    /**
     * Muestra el formulario para editar coches
     * 
     * @param idCar $idCar
     */
    public function editAction($idCar)
    {
        $em = $this->getDoctrine()->getEntityManager();
        //Obtenemos el coche y la compañia para saber si existe y si pertenece a nuestra compañia
        $security_context = $this->get('security.token_storage');
        $security_token = $security_context->getToken();
        $user = $security_token->getUser();
        $companyId = $user->getCompanys()->getId();
        $companyService = $this->container->get("company_service");
        $car = $em->getRepository("AppBundle:car")->findOneById($idCar);        
        //Si el coche es nulo o no es de nuestra compañia, no lo editamos
        if($car == null)
            return $this->redirectToRoute('show_car');
        if($companyService->existCar($companyId,$car->getId()))
            return $this->redirectToRoute('show_car');
        return $this->render('car/content-panel-createCar.html.twig', array("car"=>$car,"user_type" => "company", "companyId" => $companyId,"carId" => $car->getid()));
    }

    /**
     * Edicion del coche
     * 
     * @param Request $request
     */
    public function editAjaxAction(Request $request){
        $em = $this->getDoctrine()->getEntityManager();
        //Obtenemos los datos del request
        $plate = $request->get('plate');
        $trademark = $request->get('trademark');
        $model = $request->get('model');
        $version = $request->get('version');
        $state = $request->get('state');
        $idCar = $request->get('idCar');
        $response = array(
            "status" => false,
            "message" => "ERROR"
        );
        //Hacemos las comprobaciones pertinentes
        if(!preg_match("/^\d{4}[A-Z]{3}/", $plate)){
            $response = array(
                "status" => false,
                "message" => "Formato de matrícula incorrecto"
            );
        }else if (empty($plate) or empty($trademark) or empty($model) or empty($version) or empty($state) or empty($idCar)){
            $response = array(
                "status" => false,
                "message" => "Rellene los campos"
            );
        }else{
            try{
                //Obtenemos el coche y lo modificamos
                $car = $em->getRepository("AppBundle:Car")->findOneById($idCar);
                $car->setPlate($plate);
                $car->setTrademark($trademark);
                $car->setModel($model);
                $car->setVersion($version);
                $car->setState($state);
                $em->persist($car);
                $em->flush();
                $response = array(
                    "status" => true,
                    "message" => "Coche editado correctamente"
                );
                //Comprobamos que la matrícula no se repita
            }catch(\Doctrine\DBAL\DBALException $e){
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
     * Elimina un coche
     * 
     * @param Request $request
     */
    public function deleteAction(Request $request){

        $em = $this->getDoctrine()->getEntityManager();
        $security_context = $this->get('security.token_storage');
        $security_token = $security_context->getToken();
        $user = $security_token->getUser();
        //Obtenemos el coche y la compañia para saber si existe y si pertenece a nuestra compañia
        $idCompany = $user->getCompanys()->getId();
        $companyService = $this->container->get("company_service");
        $idCar = $request->get('idCar');
        $car = $em->getRepository("AppBundle:car")->findOneById($idCar);
        //Si el coche no existe o no es de nuestra compañia, no lo eliminamos 
        if($car == null)
            return $this->redirectToRoute('show_car');
        if($companyService->existCar($idCompany,$car->getId()))
            return $this->redirectToRoute('show_car');
        $em->remove($car);
        $em->flush();
        return new JsonResponse(array(
            "status" => true,
            "message" => "El coche ha sido eliminado"
        ));
    }

}
