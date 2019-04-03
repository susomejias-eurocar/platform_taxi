<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Company;
use AppBundle\Entity\Driver;
use Doctrine\ORM\EntityRepository;
use PDO;
/**
 * CompanyRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CompanyRepository extends EntityRepository
{

    public function executeQuery($query, $parameters, $where = null, $groupBy = null, $orderBy = null, $having = null){
        
        /** @var EntityManager $em */
        $em = $this->getEntityManager();
        $sqlConnection = $em->getConnection();

        if(!is_null($where))
            $query .= $where;

        if(!is_null($groupBy))
            $query .= $groupBy;

        if(!is_null($having))
            $query .= $having;                

        if(!is_null($orderBy))
            $query .= $orderBy;
    
        // var_dump($query);
        // die();
        // Ejecutamos la consulta
        $qr = $sqlConnection->prepare($query);
        $qr->execute($parameters);

        /* Resultado de la Query */
        $result = $qr->fetchAll(PDO::FETCH_ASSOC);

        return $result;

    }

    public function getAllCars($params, $company_id){

        //$search = json_decode($params['search']['value'], true);

        // var_dump($params);
        // die();

        // dump($params);
        // die();
        // Definimos las columnas
        $columns = array(
            0 => 'plate',
            1 => 'trademark',
            2 => 'model',
            3 => 'version',
            4 => 'state'
        );

        // Inicializamos los strings que van a concatenar la consulta.
        $where = $query = $queryCount = $orderBy = $groupBy =  $having = "";

        $parameters = array();

        $query = "SELECT ca.plate,ca.trademark,ca.model,ca.version,ca.state FROM car as ca, company as co where ca.company_id = co.id and ca.company_id = :company_id";

        $queryCount = "SELECT COUNT(*) as total FROM car as ca, company as co where ca.company_id = co.id";

        // if (isset($search["employeeName"]) AND $search["employeeName"]){
        //     $where .= " AND e.name LIKE '%". $search["employeeName"] ."%'";
        // }

        
        // if (isset($search["employeeOffice"]) AND $search["employeeOffice"]){
        //     $where .= " AND office_name LIKE '%". $search["employeeOffice"] ."%'";
        // }

        //CREAMOS EL ORDER BY CON EL LIMIT aparte
        $limit=" ";
        
        if($params['length'] != -1 ){
            $limit="  LIMIT ".$params['start']." ,".$params['length']." ";
        }
        
        $orderBy .= " ORDER BY ". $columns[$params['order'][0]['column']]."   ".$params['order'][0]['dir'] . $limit;

        // CREAMOS EL GROUP BY aparte
        $groupBy .= " GROUP BY ca.id";

        $parameters = array();
        $parameters[":company_id"] = $company_id;

        // Ejecutamos todas las consultas que nos hacen falta para realizar el datatable
        $totalRecord = $this->executeQuery($queryCount, $parameters);   
        $totalRecordFilter = $this->executeQuery($queryCount, $parameters, $where);
        $data = $this->executeQuery($query, $parameters, $where, $groupBy, $orderBy, $having);

        $result = array (
            'draw'              => intval($params['draw']),
            'recordsTotal'      => $totalRecord[0]['total'],
            'recordsFiltered'   => $totalRecordFilter[0]['total'],
            'data'              => $data
        );

        return $result;

    }

    public function getAllDrivers($params, $company_id){

        //$search = json_decode($params['search']['value'], true);

        // var_dump($params);
        // die();

        // dump($params);
        // die();
        // Definimos las columnas
        $columns = array(
            0 => 'name',
            1 => 'last_name',
            2 => 'state',
        );

        // Inicializamos los strings que van a concatenar la consulta.
        $where = $query = $queryCount = $orderBy = $groupBy =  $having = "";

        $parameters = array();

        $query = "SELECT d.name, d.last_name, d.state FROM driver as d, company as co where d.company_id = co.id and d.company_id = :company_id";

        $queryCount = "SELECT COUNT(*) as total FROM driver as d, company as co where d.company_id = co.id";

        // if (isset($search["employeeName"]) AND $search["employeeName"]){
        //     $where .= " AND e.name LIKE '%". $search["employeeName"] ."%'";
        // }

        
        // if (isset($search["employeeOffice"]) AND $search["employeeOffice"]){
        //     $where .= " AND office_name LIKE '%". $search["employeeOffice"] ."%'";
        // }

        //CREAMOS EL ORDER BY CON EL LIMIT aparte
        $limit=" ";
        
        if($params['length'] != -1 ){
            $limit="  LIMIT ".$params['start']." ,".$params['length']." ";
        }
        
        $orderBy .= " ORDER BY ". $columns[$params['order'][0]['column']]."   ".$params['order'][0]['dir'] . $limit;

        // CREAMOS EL GROUP BY aparte
        $groupBy .= " GROUP BY d.id";

        $parameters = array();
        $parameters[":company_id"] = $company_id;

        // Ejecutamos todas las consultas que nos hacen falta para realizar el datatable
        $totalRecord = $this->executeQuery($queryCount, $parameters);   
        $totalRecordFilter = $this->executeQuery($queryCount, $parameters, $where);
        $data = $this->executeQuery($query, $parameters, $where, $groupBy, $orderBy, $having);

        $result = array (
            'draw'              => intval($params['draw']),
            'recordsTotal'      => $totalRecord[0]['total'],
            'recordsFiltered'   => $totalRecordFilter[0]['total'],
            'data'              => $data
        );

        return $result;

    }



    // sacar el usuario de una compañia con el id del usuario
    public function getCompanyNameAddress($user_id)
    {
        
        $sql = "SELECT company.id,company.name, company.address FROM company , user WHERE :user_id = company.user_id";
        $params = array(
            'user_id' => $user_id
        );

        $results = $this->getEntityManager()->getConnection()->executeQuery($sql, $params)->fetchAll();


        return $results;
    }
    // function getAllCars($idCompany)
    // {
    //     $em = $this->getEntityManager();
    //     $query = $em->createQuery(
    //         "SELECT car.plate, car.trademark, car.model, car.version FROM AppBundle\Entity\Car car, AppBundle\Entity\Company c WHERE car.company=c.id and c.id=:id"
    //     )->setParameter("id", $idCompany);
    //     return $query->getArrayResult();
    // }

    // function getAllDrivers($idCompany)
    // {
    //     $con = $this->getEntityManager();
    //     $query = $con->createQuery(
    //         "SELECT d.name, d.state, car.trademark FROM
    //         AppBundle\Entity\Driver d, AppBundle\Entity\Company c, AppBundle\Entity\User u, AppBundle\Entity\Car car
    //         WHERE u.id=c.user and d.company=c.id and (d.car=car.id or d.car is null) and c.id=:id"
    //     )->setParameter("id", $idCompany);
    //     return $query->getArrayResult();
    // }

    function getDriversWithoutCar($idCompany){
        $em = $this->getEntityManager();
        $query = $em->createQuery(
            "SELECT d.name, d.state, car.trademark FROM
            AppBundle\Entity\Driver d, AppBundle\Entity\Company c, AppBundle\Entity\User u, AppBundle\Entity\Car car
            WHERE u.id=c.user and d.company=c.id and d.car is null and c.id=:id"
        )->setParameter("id", $idCompany);
        return $query->getArrayResult();
    }

    function getDriversWithCar($idCompany){
        $em = $this->getEntityManager();
        $query = $em->createQuery(
            "SELECT d.name, d.state, car.trademark FROM
            AppBundle\Entity\Driver d, AppBundle\Entity\Company c, AppBundle\Entity\User u, AppBundle\Entity\Car car
            WHERE u.id=c.user and d.company=c.id and d.car=car.id and c.id=:id"
        )->setParameter("id", $idCompany);
        return $query->getArrayResult();
    }

    function getStoppedDrivers($idCompany){
        $em = $this->getEntityManager();
        $query = $em->createQuery(
            "SELECT d.name, d.state, car.plate, car.trademark, car.model, car.version FROM
            AppBundle\Entity\Driver d, AppBundle\Entity\Company c, AppBundle\Entity\User u, AppBundle\Entity\Car car
            WHERE u.id=c.user and d.company=c.id and d.state='stopped' and c.id=:id"
        )->setParameter("id", $idCompany);
        return $query->getArrayResult();
    }


    function getAvalaibledDrivers($idCompany){
        $em = $this->getEntityManager();
        $query = $em->createQuery(
            "SELECT d.name, d.state, car.plate, car.trademark, car.model, car.version FROM
            AppBundle\Entity\Driver d, AppBundle\Entity\Company c, AppBundle\Entity\User u, AppBundle\Entity\Car car
            WHERE u.id=c.user and d.company=c.id and d.state='avalaible' and c.id=:id"
        )->setParameter("id", $idCompany);
        return $query->getArrayResult();
    }

    function getUnavalaibledDrivers($idCompany){
        $em = $this->getEntityManager();
        $query = $em->createQuery(
            "SELECT d.name, d.state, car.plate, car.trademark, car.model, car.version FROM
            AppBundle\Entity\Driver d, AppBundle\Entity\Company c, AppBundle\Entity\User u, AppBundle\Entity\Car car
            WHERE u.id=c.user and d.company=c.id and d.state='avalaible' and c.id=:id"
        )->setParameter("id", $idCompany);
        return $query->getArrayResult();
    }

    function getDamagedCar($idCompany)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery(
            "SELECT car.plate, car.trademark, car.model, car.version, car.state FROM AppBundle\Entity\Car car, AppBundle\Entity\Company c WHERE car.state='damaged' and car.company=c.id and c.id=:id"
        )->setParameter("id", $idCompany);
        return $query->getArrayResult();
    }


    function getInRepairCar($idCompany)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery(
            "SELECT car.plate, car.trademark, car.model, car.version, car.state FROM AppBundle\Entity\Car car, AppBundle\Entity\Company c WHERE car.state='in repair' andcar.company=c.id and c.id=:id"
        )->setParameter("id", $idCompany);
        return $query->getArrayResult();
    }


    function getAvalaibleCar($idCompany)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery(
            "SELECT car.plate, car.trademark, car.model, car.version, car.state FROM AppBundle\Entity\Car car, AppBundle\Entity\Company c WHERE car.state='avalaible' and car.company=c.id and c.id=:id"
        )->setParameter("id", $idCompany);
        return $query->getArrayResult();

    }

    function getCarWithoutDriver($idCompany){
        $em = $this->getEntityManager();
        $con = $em->getConnection();
        $sql = "SELECT
        car.id, car.trademark, car.model, car.plate ,car.version
        FROM car, company WHERE car.company_id=company.id
        AND company.id=:id
        AND car.id NOT IN (SELECT car.id
        FROM car, driver
        WHERE car.id=driver.car_id)";
        $query = $con->prepare($sql);
        $query->bindValue("id",$idCompany);
        $query->execute();
        $results = $query->fetchAll();
        return $results;
    }

    public function addCarToCompany($idCar, $idCompany){



    }

}
