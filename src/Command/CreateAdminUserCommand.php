<?php

declare(strict_types=1);

namespace ADS\UCCIA\Command;

use ADS\UCCIA\Entity\User;
use ADS\UCCIA\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'uccia:create-admin-user',
    description: 'Creates a new admin user',
)]
final class CreateAdminUserCommand extends Command
{
    use WithAskArgumentFeature;

    private SymfonyStyle $io;

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'The email of the user')
            ->addArgument('password', InputArgument::REQUIRED, 'The password of the user');
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        if (
            $input->getArgument('email') !== null &&
            $input->getArgument('password') !== null
        ) {
            return;
        }

        $this->askArgument($input, 'email');
        $this->askArgument($input, 'password', true);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = (string) $input->getArgument('email');
        $password = (string) $input->getArgument('password');

        $user = new User();
        $user->setEmail($email);
        $user->setRoles(['ROLE_SUPER_ADMIN']);
        $user->setPassword(
            $this->passwordHasher->hashPassword(
                $user,
                $password,
            ),
        );

        $this->userRepository->save($user, true);

        $this->io->success(\sprintf('Admin user %s created successfully.', $email));

        return Command::SUCCESS;
    }
}
