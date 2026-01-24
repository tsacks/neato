<?php

namespace App\Controller;

use App\Entity\Location;
use App\Entity\WMOCode;
use App\Enum\WeatherTimeEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class WeatherController extends AbstractController
{
    private function convertDegToDir(int $degrees)
    {
        $dirs = ["N", "NNE", "NE", "ENE", "E", "ESE", "SE", "SSE","S", "SSW", "SW", "WSW", "W", "WNW", "NW", "NNW"];
        $i = round(($degrees + 11.25)/22.5);
        return $dirs[$i % 16];
    }

    #[Route('/weather', name: 'app_weather')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $location = json_decode($request->cookies->get('weatherLocation'));
        if($location == null)
        {
            return $this->redirectToRoute('app_weather_search');
        }

        $repository = $entityManager->getRepository(WMOCode::class);
        $forecast_params = [
            'latitude' => $location->lat,
            'longitude' => $location->lon,
            'current' => implode(',', [
                'temperature_2m',
                'is_day',
                'precipitation_probability',
                'precipitation',
                'rain',
                'showers',
                'snowfall',
                'weather_code',
                'cloud_cover',
                'wind_speed_10m',
                'wind_direction_10m',
                'wind_gusts_10m',
                'relative_humidity_2m',
                
            ]),
            'daily' => implode(',', [
                'temperature_2m_max',
                'temperature_2m_min',
                'weather_code',
            ]),
            'timezone' => 'America/New_York',
            'wind_speed_unit' => 'mph',
            'temperature_unit' => 'fahrenheit',
            'precipitation_unit' => 'inch',
            'forecast_hours' => 24,
        ];
        $temp_url = sprintf('https://api.open-meteo.com/v1/forecast?%s', http_build_query($forecast_params, PHP_QUERY_RFC3986));
        $client = HttpClient::create();
        $response = $client->request('GET', $temp_url);
        $forecast = json_decode($response->getContent());

        $forecast->current->wind_direction_10m_cardinal = $this->convertDegToDir($forecast->current->wind_direction_10m);

        $wmocodes[$forecast->current->weather_code] = $repository->findOneBy([
            'code' => $forecast->current->weather_code,
            'time' => WeatherTimeEnum::DEFAULT,
        ]);
        foreach($forecast->daily->weather_code as $code)
        {
            if( !isset($wmocodes[$code]) )
            {
                $wmocodes[$code] = $repository->findOneBy([
                    'code' => $code,
                    'time' => WeatherTimeEnum::DEFAULT,
                ]);
            }
        }

        return $this->render('weather/index.html.twig', [
            'items' => [],
            'location' => $location,
            'forecast' => $forecast,
            'wmocodes' => $wmocodes,
            'controller_name' => 'WeatherController',
        ]);
    }

    #[Route('/weather/search', name: 'app_weather_search')]
    public function search(Request $request): Response
    {
        $location = new Location();
        $form = $this->createFormBuilder($location)
            ->add('city', TextType::class)
            ->add('country', CountryType::class, [
                'required' => false,
                'placeholder' => '',
                ])
            ->add('search', SubmitType::class, [
                'label' => 'Search',
                'block_prefix' => 'input'
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $location->getData() holds the submitted values
            // but, the original `$location` variable has also been updated
            $location = $form->getData();

            $search_params = [
                'name' => $location->getCity(),
                'countryCode' => $location->getCountry(),
            ];
            $temp_url = sprintf('https://geocoding-api.open-meteo.com/v1/search?%s', http_build_query($search_params, PHP_QUERY_RFC3986));
            $client = HttpClient::create();
            $response = $client->request('GET', $temp_url);
            $results = json_decode($response->getContent());
        }

        return $this->render('weather/search.html.twig', [
            'form' => $form,
            'results' => $results ?? null,
        ], new Response(null, $form->isSubmitted() ? 422 : 200));
    }

    #[Route('/weather/set/{name}/{lat}/{lon}', name: 'app_weather_set')]
    public function set_cookie(string $name, string $lat, string $lon, Request $request): Response
    {
        $cookie = Cookie::create( 'weatherLocation', json_encode([
            'name' => $name,
            'lat' => $lat,
            'lon' => $lon,
        ]))->withExpires(new \DateTime('+1 week'));

        $response = new RedirectResponse($this->generateUrl('app_weather'));
        $response->headers->setCookie($cookie);

        return $response;
    }
}
