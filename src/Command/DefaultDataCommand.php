<?php

namespace App\Command;

use App\Entity\Category;
use App\Entity\Feed;
use App\Entity\WMOCode;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:default-data',
    description: 'Add a short description for your command',
)]
class DefaultDataCommand extends Command
{
    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $continue = $io->confirm('Are you sure you\'d like to load default values?', false);
        if(!$continue)
        {
            return Command::FAILURE;
        }

        $categories = [
            ['id' => 1, 'title' => 'Arts', 'slug' => 'arts'],
            ['id' => 2, 'title' => 'Business and Economy', 'slug' => 'business', 'news_slug' => 'business'],
            ['id' => 3, 'title' => 'Computers and Technology', 'slug' => 'computers', 'news_slug' => 'technology'],
            ['id' => 4, 'title' => 'Education', 'slug' => 'education'],
            ['id' => 5, 'title' => 'Entertainment', 'slug' => 'entertainment', 'news_slug' => 'entertainment'],
            ['id' => 6, 'title' => 'Government', 'slug' => 'government'],
            ['id' => 7, 'title' => 'Health', 'slug' => 'health', 'news_slug' => 'health'],
            ['id' => 8, 'title' => 'News', 'slug' => 'news', 'news_slug' => ''],
            ['id' => 9, 'title' => 'Recreation and Sports', 'slug' => 'sports-and-rec'],
            ['id' => 10, 'title' => 'Reference', 'slug' => 'reference'],
            ['id' => 11, 'title' => 'Regional', 'slug' => 'regional'],
            ['id' => 12, 'title' => 'Science', 'slug' => 'science'],
            ['id' => 13, 'title' => 'Social Science', 'slug' => 'social-science'],
            ['id' => 14, 'title' => 'Society and Culture', 'slug' => 'society-culture'],
            ['id' => 15, 'title' => 'Cool Links', 'slug' => 'entertainment/cool-links', 'parent_id' => 5],
            ['id' => 16, 'title' => 'Guides\' Picks', 'slug' => 'entertainment/cool-links/guides-picks', 'parent_id' => 15],
        ];
        $feeds = [
            [
                'id' => 1,
                'title' => 'Top News',
                'slug' => 'top',
                'url' => 'https://feeds.abcnews.com/abcnews/topstories',
            ],
            [
                'id' => 2,
                'title' => 'US News',
                'slug' => 'us',
                'url' => 'https://feeds.abcnews.com/abcnews/usheadlines',
            ],
            [
                'id' => 3,
                'title' => 'International News',
                'slug' => 'international',
                'url' => 'https://feeds.abcnews.com/abcnews/internationalheadlines',
            ],
            [
                'id' => 4,
                'title' => 'Politics',
                'slug' => 'politics',
                'url' => 'https://feeds.abcnews.com/abcnews/politicsheadlines',
            ],
            [
                'id' => 5,
                'title' => 'Business',
                'slug' => 'business',
                'url' => 'https://feeds.abcnews.com/abcnews/moneyheadlines',
            ],
            [
                'id' => 6,
                'title' => 'Technology',
                'slug' => 'technology',
                'url' => 'https://feeds.abcnews.com/abcnews/technologyheadlines',
            ],
            [
                'id' => 7,
                'title' => 'Health',
                'slug' => 'health',
                'url' => 'https://feeds.abcnews.com/abcnews/healthheadlines',
            ],
            [
                'id' => 8,
                'title' => 'Entertainment',
                'slug' => 'entertainment',
                'url' => 'https://feeds.abcnews.com/abcnews/entertainmentheadlines',
            ],
            [
                'id' => 9,
                'title' => 'Travel',
                'slug' => 'travel',
                'url' => 'https://feeds.abcnews.com/abcnews/travelheadlines',
            ],
        ];
        $wmocodes = [
            [
                'id' => 1,
                'code' => 3,
                'time' => 'default',
                'description' => 'Cloudy',
                'image' => 'cloudy-3-day',
            ],
            [
                'id' => 2,
                'code' => 45,
                'time' => 'default',
                'description' => 'Foggy',
                'image' => 'fog',
            ],
            [
                'id' => 3,
                'code' => 65,
                'time' => 'default',
                'description' => 'Heavy Rain',
                'image' => 'rainy-3',
            ],
            [
                'id' => 4,
                'code' => 63,
                'time' => 'default',
                'description' => 'Rain',
                'image' => 'rainy-2',
            ],
            [
                'id' => 5,
                'code' => 85,
                'time' => 'default',
                'description' => 'Light Snow Showers',
                'image' => 'snowy-1',
            ],
            [
                'id' => 6,
                'code' => 0,
                'time' => 'default',
                'description' => 'Sunny',
                'image' => 'clear-day',
            ],
            [
                'id' => 7,
                'code' => 51,
                'time' => 'default',
                'description' => 'Light Drizzle',
                'image' => 'rainy-1',
            ],
            [
                'id' => 8,
                'code' => 2,
                'time' => 'default',
                'description' => 'Partly Cloudy',
                'image' => 'cloudy-2-day',
            ],
            [
                'id' => 9,
                'code' => 61,
                'time' => 'default',
                'description' => 'Light Rain',
                'image' => 'rainy-1',
            ],
            [
                'id' => 10,
                'code' => 1,
                'time' => 'default',
                'description' => 'Mostly Sunny',
                'image' => 'cloudy-1-day',
            ],
            [
                'id' => 11,
                'code' => 48,
                'time' => 'default',
                'description' => 'Rime Fog',
                'image' => 'frost',
            ],
            [
                'id' => 12,
                'code' => 53,
                'time' => 'default',
                'description' => 'Drizzle',
                'image' => 'rainy-2',
            ],
            [
                'id' => 13,
                'code' => 55,
                'time' => 'default',
                'description' => 'Heavy Drizzle',
                'image' => 'rainy-3',
            ],
            [
                'id' => 14,
                'code' => 56,
                'time' => 'default',
                'description' => 'Light Freezing Drizzle',
                'image' => 'rain-and-sleet-mix',
            ],
            [
                'id' => 15,
                'code' => 57,
                'time' => 'default',
                'description' => 'Freezing Drizzle',
                'image' => 'rain-and-sleet-mix',
            ],
            [
                'id' => 16,
                'code' => 66,
                'time' => 'default',
                'description' => 'Light Freezing Rain',
                'image' => 'rain-and-sleet-mix',
            ],
            [
                'id' => 17,
                'code' => 67,
                'time' => 'default',
                'description' => 'Freezing Rain',
                'image' => 'rain-and-sleet-mix',
            ],
            [
                'id' => 18,
                'code' => 71,
                'time' => 'default',
                'description' => 'Light Snow',
                'image' => 'snowy-1',
            ],
            [
                'id' => 19,
                'code' => 73,
                'time' => 'default',
                'description' => 'Snow',
                'image' => 'snowy-2',
            ],
            [
                'id' => 20,
                'code' => 75,
                'time' => 'default',
                'description' => 'Heavy Snow',
                'image' => 'snowy-3',
            ],
            [
                'id' => 21,
                'code' => 77,
                'time' => 'default',
                'description' => 'Snow Grains',
                'image' => 'snow-and-sleet-mix',
            ],
            [
                'id' => 22,
                'code' => 81,
                'time' => 'default',
                'description' => 'Showers',
                'image' => 'rainy-2',
            ],
            [
                'id' => 23,
                'code' => 82,
                'time' => 'default',
                'description' => 'Heavy Showers',
                'image' => 'rainy-3',
            ],
            [
                'id' => 24,
                'code' => 86,
                'time' => 'default',
                'description' => 'Snow Showers',
                'image' => 'snowy-2',
            ],
            [
                'id' => 25,
                'code' => 95,
                'time' => 'default',
                'description' => 'Thunderstorm',
                'image' => 'thunderstorms',
            ],
            [
                'id' => 26,
                'code' => 96,
                'time' => 'default',
                'description' => 'Light Thunderstorms with Hail',
                'image' => 'thunderstorms',
            ],
            [
                'id' => 27,
                'code' => 99,
                'time' => 'default',
                'description' => 'Thunderstorm with Hail',
                'image' => 'thunderstorms',
            ],
        ];
        
        $this->connection->beginTransaction();
        try {
            foreach ($categories as $category) {
                $this->connection->insert('category', $category);
            }
            foreach ($feeds as $feed) {
                $this->connection->insert('feed', $feed);
            }
            foreach ($wmocodes as $code) {
                $this->connection->insert('wmocode', $code);
            }
            $this->connection->commit();
        }
        catch (\Exception $e) {
            $this->connection->rollBack();
            throw $e;
        }

        $io->success('Your database has been populated with default data.');

        return Command::SUCCESS;
    }
}
