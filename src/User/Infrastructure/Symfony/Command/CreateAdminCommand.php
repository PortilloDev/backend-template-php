<?php

namespace App\User\Infrastructure\Symfony\Command;

use App\Shared\Application\Service\UuidGeneratorInterface;
use App\User\Domain\Contracts\PasswordHasherInterface;
use App\User\Domain\Contracts\UserRepositoryInterface;
use App\User\Domain\Model\User;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:user:create_admin')]
class CreateAdminCommand extends Command
{
    public function __construct(
        private readonly UuidGeneratorInterface $uuidGenerator,
        private readonly UserRepositoryInterface $userRepository,
        private readonly PasswordHasherInterface $passwordHasher
    ) {
        parent::__construct(null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $identifier = $this->uuidGenerator->generate();
        $user = new User($identifier, 'admin@mo2o.com', 'admin');
        $this->passwordHasher->hashPassword($user);

        $this->userRepository->save($user);

        return Command::SUCCESS;
    }
}
