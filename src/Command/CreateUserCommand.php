<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[AsCommand(
    name: 'app:create-user',
    description: 'Creates a user for dev/testing purposes.',
)]
class CreateUserCommand extends Command
{
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->userPasswordHasher = $userPasswordHasher;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::OPTIONAL, 'Email used for authenticating the user..')
            ->addArgument('password', InputArgument::OPTIONAL, 'Password of the user.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $plainPassword = $input->getArgument('password');

        if ($email & $plainPassword) {
            try {
                $newUser = new User();
                $newUser->setEmail($email);
                $newUser->setPassword($plainPassword);
                $hashedPassword = $this->userPasswordHasher->hashPassword($newUser, $plainPassword);
                $newUser->setPassword($hashedPassword)->setRoles(['ROLE_DEV']);
                $this->userRepository->add($newUser, true);


            }catch (\Exception $exception){
                $output->writeln('Something went wrong !');

                return Command::FAILURE;
            }
        }

        $output->writeln('User succesfully created.');

        return Command::SUCCESS;
    }
}
