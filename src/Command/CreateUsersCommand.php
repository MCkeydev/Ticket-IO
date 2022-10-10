<?php

namespace App\Command;

use App\Entity\Operateur;
use App\Entity\Service;
use App\Entity\Technicien;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:createDevUser',
    description: 'Add a short description for your command',
)]
class CreateUsersCommand extends Command
{
    private UserPasswordHasherInterface $hasher;
    private UserRepository $userRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(UserPasswordHasherInterface $hasher, UserRepository $userRepository, EntityManagerInterface $entityManager ,string $name = null)
    {
        parent::__construct($name);
        $this->hasher= $hasher;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('userType',InputArgument::REQUIRED, 'Type of user you wish to create')
            ->addArgument('mail', InputArgument::REQUIRED, 'Email used to identify user')
            ->addArgument('password', InputArgument::REQUIRED, 'Password of the user')
            ->addArgument('roles', InputArgument::IS_ARRAY, 'Roles of the user');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $userType = $input->getArgument('userType');
        $mail = $input->getArgument('mail');
        $password = $input->getArgument('password');
        $roles = $input->getArgument('roles');

        switch ($userType) {
            case 'user' :
                $user = new User();
                break;
            case 'technicien' :
                $user = new Technicien();
                $service = new Service();
                $service->setNom('Test');
                $user->setService($service);
                $this->entityManager->persist($service);
                break;
            case 'operateur' :
                $user = new Operateur();
                break;
        }

        $user->setEmail($mail);
        $user->setRoles($roles);
        $user->setPassword($this->hasher->hashPassword($user, $password));
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return Command::SUCCESS;
    }
}
