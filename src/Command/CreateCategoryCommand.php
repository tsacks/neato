<?php

namespace App\Command;

use App\Entity\Category;
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
    name: 'app:create-category',
    description: 'Add a category for organizing links',
)]
class CreateCategoryCommand extends Command
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
            ->addArgument('title', InputArgument::REQUIRED, 'The title of the category')
            ->addArgument('slug', InputArgument::OPTIONAL, 'The slug for the category')
            ->addOption('parent', 'p', InputOption::VALUE_REQUIRED, 'If category has a parent, the parent slug', null)
            ->addOption('news', null, InputOption::VALUE_REQUIRED, 'If category links to news, the news slug', null)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $title = $input->getArgument('title');
        $slug = strtolower($this->slugger->slug($title));
        $parent = $input->getOption('parent');
        $news = $input->getOption('news');

        if (!$title) {
            $io->error('You need to provide a title.');
            return Command::INVALID;
        }
        if (!$slug) {
            $slug = strtolower($this->slugger->slug($title));
        }

        if (!is_null($parent)) {
            $parent = $this->entityManager->getRepository(Category::class)->findBySlug($input->getOption('parent'));
            $slug = $parent->getSlug() . '/' . $slug;
        }

        $category = new Category();
        $category->setTitle($title);
        $category->setSlug($slug);
        $category->setParent($parent);
        $category->setNewsSlug($news);
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        $io->success('Category created.');
        return Command::SUCCESS;
    }
}
