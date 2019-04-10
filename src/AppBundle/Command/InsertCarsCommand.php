<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Entity\User;
use AppBundle\Entity\Car;

class InsertCarsCommand extends ContainerAwareCommand
{

    /** Use command
        php app/console users 8 ROLE_DRIVER true 13
     */
    protected function configure()
    {
        $this
            ->setName('cars')
            ->setDescription('Inserta coches')
            ->addArgument(
                'company_id',
                InputArgument::REQUIRED,
                'Introduce id de la compañia'
            )
            ->addArgument(
                'number_cars',
                InputArgument::REQUIRED,
                'Introduce el numero de coches'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $number_cars = $input->getArgument('number_cars');
        $company_id = $input->getArgument('company_id');


        $translator = $this->getContainer()->get('translator');
        $em = $this->getContainer()->get('doctrine')->getManager();
        if (!$number_cars){
            $number_cars = 10;
        }

        $arr_letter = ['A','B','C','D','E','F','G','H','I', 'J', 'K'];

        for ($i=0; $i < $number_cars; $i++) { 
            $company = $em->getRepository('AppBundle:Company')->findOneBy(
                array('id' => $company_id)
            );
  
           
            $car = new Car();
            $output->writeln($translator->trans("123". $i ."" . $arr_letter[rand(0,$i)] . $arr_letter[rand(0,$i)] . $arr_letter[rand(0,$i)]));

            $car->setPlate( "123". $i ."" . $arr_letter[rand(0,$i)] . $arr_letter[rand(0,$i)] . $arr_letter[rand(0,$i)]);
            $car->setTrademark("Mercedes");
            $car->setModel("claseA");
            $car->SetVersion("2011");
            $car->setCompany($company);
            $car->setState("Disponible");

            $em->persist($car);

            
        }
        $em->flush();
        

        $output->writeln($translator->trans('Create users with role'));
        
    }
}

?>
