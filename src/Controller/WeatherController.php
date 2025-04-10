<?php 

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Service\WeatherService;

class WeatherController extends AbstractController
{
    private $weatherService;

    // Inject WeatherService through the constructor
    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    #[Route("/weather/weather_info", name: "weather_info", methods: ["GET"])]
    public function getWeatherInfo(Request $request, ValidatorInterface $validator): Response
    {
        // Step 1: Get the 'city' parameter from the request
        $city = $request->query->get('city');

        // Step 2: Define the validation constraints
        $constraints = new Assert\Collection([
            'city' => [
                new Assert\NotBlank(), // Ensure city is not empty
                new Assert\Type('string'), // Ensure city is a string
                new Assert\Length(['max' => 40]), // Ensure city name has max 40 characters
            ],
        ]);

        // Step 3: Validate the 'city' parameter
        $violations = $validator->validate(['city' => $city], $constraints);

        // Step 4: Check if there are any validation violations
        if (count($violations) > 0) {
            $response = $this->render('weather/error.html.twig', [
                'error' => 'Invalid city.', 
            ]);
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            return $response;
        }

        // Step 5: Get the weather data from the WeatherService
        $weatherData = $this->weatherService->getWeatherData($city);

        // Step 6: Check if there was an error with the weather data
        if (isset($weatherData['error'])) {
            $response = $this->render('weather/error.html.twig', [
                'error' => $weatherData['error'], 
            ]);
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            return $response;
        }

        return $this->render('weather/index.html.twig', [
            'weather' => $weatherData, 
        ]);
    }
}
