<?php

namespace App\Command;

use App\Entity\Feed;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\String\Slugger\AsciiSlugger;

#[AsCommand(
    name: 'app:create-feed',
    description: 'Add a feed that will show up on the news page',
)]
class CreateFeedCommand extends Command
{
    private EntityManagerInterface $entityManager;

    private AsciiSlugger $slugger;

    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->slugger = new AsciiSlugger();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('title', InputArgument::REQUIRED, 'The title of the feed')
            ->addArgument('url', InputArgument::REQUIRED, 'The url for the feed')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $title = $input->getArgument('title');
        $slug = strtolower($this->slugger->slug($title));
        $url = $input->getArgument('url');

        if (!$title) {
            $io->error('You need to provide a title.');
            return Command::INVALID;
        }
        if (!$url) {
            $io->error('You need to provide a url.');
            return Command::INVALID;
        }

        $feed = new Feed();
        $feed->setTitle($title);
        $feed->setSlug($slug);
        $feed->setURL($url);
        $this->entityManager->persist($feed);

        $this->entityManager->flush();

        $io->success('Feed created.');
        return Command::SUCCESS;
    }
}
