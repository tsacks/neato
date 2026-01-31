<?php

namespace App\Command;

use App\Entity\Category;
use App\Entity\User;
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
    name: 'app:create-user',
    description: 'Create a user to access the Guide backend',
)]
class CreateUserCommand extends Command
{
    private EntityManagerInterface $entityManager;
    
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    )
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'The username of the user')
            ->addArgument('email', InputArgument::REQUIRED, 'The email of the user')
            ->addArgument('password', InputArgument::REQUIRED, 'Password in plain text (will be encrypted automatically)')
            ->addOption('category', 'c', InputOption::VALUE_NONE, 'Suppress creating a category for the user under "entertainment/cool-links/guides-picks"')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $name = $input->getArgument('name');
        $email = $input->getArgument('email');
        $plaintextPassword = $input->getArgument('password');
        $createCategory = $input->getOption('category');

        if (!$name) {
            $io->error('You need to provide a username.');
            return Command::INVALID;
        }
        if (!$email) {
            $io->error('You need to provide an email.');
            return Command::INVALID;
        }
        if (!$plaintextPassword) {
            $io->error('You need to provide a password.');
            return Command::INVALID;
        }

        $user = new User();
        $user->setUsername($name);
        $user->setEmail($email);
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);
        $this->entityManager->persist($user);

        $guides_cat = $this->entityManager->getRepository(Category::class)->findBySlug('entertainment/cool-links/guides-picks');
        if($guides_cat && $createCategory)
        {
            $category = new Category();
            $category->setTitle($user->getUsername());
            $category->setSlug('entertainment/cool-links/guides-picks/'.strtolower($user->getUsername()));
            $category->setParent($guides_cat);
            $this->entityManager->persist($category);
        }

        $this->entityManager->flush();

        $io->success('User created.');
        return Command::SUCCESS;
    }
}
