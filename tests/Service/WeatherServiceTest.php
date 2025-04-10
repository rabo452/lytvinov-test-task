<?php 

namespace App\Tests\Service;

use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Service\WeatherService;

class WeatherServiceTest extends TestCase
{
    public function testGetWeatherDataInvalidCity()
    {
        // Mock the HttpClientInterface and ensure it is cast to the right type
        $httpClientMock = $this->createMock(HttpClientInterface::class);
        $parameterBagMock = $this->createMock(ParameterBagInterface::class);
        
        // Mock the 'WEATHER_API_KEY' environment variable
        $parameterBagMock->method('get')->willReturn('fake_api_key');
        
        // Mock the response to simulate an error when the city is invalid
        $responseMock = $this->createMock(ResponseInterface::class);
        
        // Simulate a failure in the response (for example, a 404 or empty data)
        $responseMock->method('toArray')->willThrowException(new \Exception('City not found'));
        
        // Mock the client to return the mocked response when the request is made
        $httpClientMock->method('request')->willReturn($responseMock);
        
        // Create the WeatherService with the mocked HttpClient and ParameterBag
        $weatherService = new WeatherService($httpClientMock, $parameterBagMock);
        
        // Call the method with an invalid city name
        $invalidCityName = 'abcdf';
        $result = $weatherService->getWeatherData($invalidCityName);
        
        // Assert that the returned result contains an error message
        $this->assertArrayHasKey('error', $result);
        $this->assertEquals('Unable to fetch weather data.', $result['error']);
    }
}