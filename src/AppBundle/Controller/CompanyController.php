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




}
