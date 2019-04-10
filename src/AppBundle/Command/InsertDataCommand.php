<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Entity\User;
use AppBundle\Entity\Driver;

class InsertDataCommand extends ContainerAwareCommand
{

    /** Use command
        php app/console users 8 ROLE_DRIVER true 13
     */
    protected function configure()
    {
        $this
            ->setName('users')
            ->setDescription('Inserta coches')
            ->addArgument(
                'company_id',
                InputArgument::REQUIRED,
                'Introduce id de la compañia'
            )
            ->addArgument(
                'role',
                InputArgument::REQUIRED,
                'Introduce rol: ROLE_COMPANY, ROLE_DRIVER'
            )
            ->addArgument(
                'isCreateDriver',
                InputArgument::REQUIRED,
                'Introduce numero de usuarios'
            )
            ->addArgument(
                'number_users',
                InputArgument::OPTIONAL,
                'Introduce numero de usuarios'
            )
            
            
            
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $role = $input->getArgument('role');
        $number_user = $input->getArgument('number_users');
        $company_id = $input->getArgument('company_id');
        $isCreateDriver = $input->getArgument('isCreateDriver'); 


        $translator = $this->getContainer()->get('translator');
        $em = $this->getContainer()->get('doctrine')->getManager();
        if (!$number_user){
            $number_user = 10;
        }
        for ($i=0; $i < $number_user; $i++) { 
            $company = $em->getRepository('AppBundle:Company')->findOneBy(
                array('id' => $company_id)
            );

            $permission = $em->getRepository('AppBundle:Permission')->findOneBy(
                array('id' => 1)
            );
            $user = new User();
            $user->setName("user" . $i);
            $user->setLastName("last_name" . $i);

            if ($role){
                $user->setRoles(array($role));
            }else{
                $user->setRoles(array("ROLE_COMPANY"));
            }
            $user->setEmail("correo" .$i."@gmail.com");
            $encoder = $this->getContainer()->get('security.password_encoder');
            $encoded = $encoder->encodePassword($user, "1234");
            $user->setPassword($encoded);
            $user->setPhone("600000000");
            $user->setActive(1);
            $user->setCompanys($company);
            $user->setPermission($permission);
            $em->persist($user);
            if ($isCreateDriver){

                $driver = new Driver();
                $driver->setUser($user);
                $driver->setState('Disponible');
                $driver->setCar(null);
                $em->persist($driver);
            }
            
        }
        $em->flush();
        

        $output->writeln($translator->trans('Create users with role' + $role));
        
    }
}

?>
