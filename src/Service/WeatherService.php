<?php 

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class WeatherService
{
    private $client;
    private $apiKey;

    /**
     * WeatherService constructor.
     *
     * @param HttpClientInterface $client The HTTP client to send requests
     * @param ParameterBagInterface $params Access to environment parameters like WEATHER_API_KEY
     */
    public function __construct(HttpClientInterface $client, ParameterBagInterface $params)
    {
        $this->client = $client;
        $this->apiKey = $params->get('WEATHER_API_KEY'); // Access WEATHER_API_KEY from environment variables
    }

    /**
     * Get weather data for a given city.
     *
     * Fetches weather data from OpenWeatherMap API based on the city provided.
     * If the city is invalid, it returns an error message.
     *
     * @param string $city The name of the city to get weather data for
     *
     * @return array An associative array with weather data or an error message on failure:
     *                    - 'city' (string): The name of the city
     *                    - 'country' (string): The country code (e.g., 'GB', 'US')
     *                    - 'temperature' (float): Current temperature in Celsius
     *                    - 'condition' (string): Weather condition description (e.g., 'clear sky')
     *                    - 'humidity' (int): Humidity percentage (0-100)
     *                    - 'wind_speed' (float): Wind speed in meters per second (m/s)
     *                    - 'last_updated' (string): Date and time when the data was last updated (e.g., '2025-04-10 15:30:00')
     *                    Returns an array with a single 'error' key (string) in case of failure:
     *                    - 'error' (string): Error message (e.g., 'Unable to fetch weather data.')
     */
    public function getWeatherData(string $city): ?array
    {
        try {
            // URL to fetch weather data from WeatherAPI
            $url = "https://api.openweathermap.org/data/2.5/weather?q={$city}&appid={$this->apiKey}&units=metric";

            // Make the HTTP GET request
            $response = $this->client->request('GET', $url);

            // Parse the response body into an array
            $data = $response->toArray();

            // Check if there's any error in the response
            if (isset($data['error'])) {
                throw new \Exception('Error fetching data: ' . $data['error']['message']);
            }

            $lastUpdated = date("Y-m-d H:i:s", $data['dt']); // Format it as needed (e.g., YYYY-MM-DD HH:MM:SS)

            // Return the result in the desired format
            $result = [
                'city' => $data['name'],  // City name (string)
                'country' => $data['sys']['country'],  // Country code (string)
                'temperature' => $data['main']['temp'],  // Current temperature in Celsius (float)
                'condition' => $data['weather'][0]['description'],  // Weather description (string)
                'humidity' => $data['main']['humidity'],  // Humidity percentage (int)
                'wind_speed' => $data['wind']['speed'],  // Wind speed in m/s (float)
                'last_updated' => $lastUpdated,  // Last updated time (string)
            ];

            return $result;

        } catch (\Exception $e) {
            // Log the error or handle it as needed
            return ['error' => 'Unable to fetch weather data.']; // Error message (string)
        }
    }
}
