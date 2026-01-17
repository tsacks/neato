<?php

namespace App\Controller;

use App\Entity\Feed;
use App\Entity\WMOCode;
use App\Enum\WeatherTimeEnum;
use Doctrine\ORM\EntityManagerInterface;
use SimplePie\SimplePie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class NewsController extends AbstractController
{
    private function convertDegToDir(int $degrees)
    {
        $dirs = ["N", "NNE", "NE", "ENE", "E", "ESE", "SE", "SSE","S", "SSW", "SW", "WSW", "W", "WNW", "NW", "NNW"];
        $i = round(($degrees + 11.25)/22.5);
        return $dirs[$i % 16];
    }

    #[Route('/news/{slug:feed}', name: 'app_news_feed', requirements: ['slug' => '.+'])]
    public function index(EntityManagerInterface $entityManager, ?Feed $feed): Response
    {
        $simplepie = new SimplePie();
        $simplepie->set_cache_location('../var/cache/');
        $simplepie->set_feed_url($feed->getURL());
        $success = $simplepie->init();

        //dd($simplepie->get_items()[0]->get_item_tags('http://search.yahoo.com/mrss/', 'thumbnail')[0]['attribs']);

        return $this->render('news/feed.html.twig', [
            'feed' => $simplepie,
            'title' => $simplepie->get_title(),
            'link' => $simplepie->get_link(),
            'description' => $simplepie->get_description(),
            'items' => $simplepie->get_items(),
        ]);
    }

    #[Route('/news', name: 'app_news')]
    public function default(Request $request, EntityManagerInterface $entityManager): Response
    {
        $location = json_decode($request->cookies->get('weatherLocation'));

        if($location)
        {
            $repository = $entityManager->getRepository(WMOCode::class);
            $forecast_params = [
                'latitude' => $location->lat,
                'longitude' => $location->lon,
                'current' => implode(',', [
                    'temperature_2m',
                    'is_day',
                    'precipitation_probability',
                    'weather_code',
                    'wind_speed_10m',
                    'wind_direction_10m',
                    'wind_gusts_10m',
                    'relative_humidity_2m',
                    
                ]),
                'timezone' => 'America/New_York',
                'wind_speed_unit' => 'mph',
                'temperature_unit' => 'fahrenheit',
                'precipitation_unit' => 'inch',
                'forecast_hours' => 24,
            ];
            $temp_url = sprintf('https://api.open-meteo.com/v1/forecast?%s', http_build_query($forecast_params, PHP_QUERY_RFC3986));

            try
            {
                $client = HttpClient::create();
                $response = $client->request('GET', $temp_url);
                $forecast = json_decode($response->getContent());

                $forecast->current->wind_direction_10m_cardinal = $this->convertDegToDir($forecast->current->wind_direction_10m);

                $wmocodes[$forecast->current->weather_code] = $repository->findOneBy([
                    'code' => $forecast->current->weather_code,
                    'time' => WeatherTimeEnum::DEFAULT,
                ]);
            }
            catch(TransportException $e)
            {
                $forecast = false;
            }
            
        }

        $repository = $entityManager->getRepository(Feed::class);
        $feeds = $repository->findAll();

        $simplepie = new SimplePie();
        $simplepie->set_cache_location('../var/cache/');
        foreach( $feeds as $feed )
        {
            $simplepie->set_feed_url($feed->getURL());
            $success = $simplepie->init();
            $feed->items = $simplepie->get_items();
        }

        //dd($feeds);

        return $this->render('news/feeds.html.twig', [
            'feeds' => $feeds,
            'location' => $location ?? null,
            'forecast' => $forecast ?? null,
            'wmocodes' => $wmocodes ?? null,
        ]);
    }
}
