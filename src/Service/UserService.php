<?php
namespace AppBundle\Service;

class UserService{

    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }


    public function register()
    {
        $userRepository =  $this->repository = $em->getRepository(User::class);

        $userRepository->register();
    }


}

?>