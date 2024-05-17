<?php
function kelvinToCelsius(float $temperature): float
{
    return $temperature - 273.15;
}
echo "Search for weather situation by:\n1 - City\n2 - Country\n";
$userInput = (int) readline("Enter your choice (1 or 2): ");
if ($userInput < 1 || $userInput > 2) {
    exit("Invalid choice!\n");
}
if ($userInput === 1) {
    $city = readline("Enter the name of the city: ");
} else {
    $city = readline("Enter the name of the country: ");
    $location = "";
    for ($letter = 0; $letter < strlen($city); $letter++) {
        if ($letter === 0) {
            $location = $location . strtoupper($city[$letter]);
        } elseif ($city[$letter] === " " && $letter !== strlen($city) - 1) {
            $location = $location . " " . strtoupper($city[$letter + 1]);
            $letter++;
        } else {
            $location = $location . strtolower($city[$letter]);
        }
    }
    $city = $location;
    $allCountries = file_get_contents(
        "https://countriesnow.space/api/v0.1/countries/capital"
    );
    $allCountries = json_decode($allCountries, false);
    foreach ($allCountries->data as $country) {
        if ($city === $country->name) {
            $city = $country->iso2;
            break;
        }
    }
}

$APIkey = "74ef34f4b6d76fd2c08491dbdedbcf3a";
$coordinatesByLocationName = file_get_contents(
    "http://api.openweathermap.org/geo/1.0/direct?q=$city&limit=1&appid=$APIkey"
);
$coordinatesByLocationName = json_decode($coordinatesByLocationName);
if (empty($coordinatesByLocationName)) {
    exit("\nInvalid input!\n");
}
$lat = $coordinatesByLocationName[0]->lat;
$lon = $coordinatesByLocationName[0]->lon;
$weatherInfo = file_get_contents(
    "https://api.openweathermap.org/data/2.5/weather?lat=$lat&lon=$lon&appid=$APIkey"
);
$weatherInfo = json_decode($weatherInfo);

echo "\n$weatherInfo->name, {$weatherInfo->sys->country}\n";
echo number_format(kelvinToCelsius($weatherInfo->main->temp)) . "°C\n";
echo "Weather: {$weatherInfo->weather[0]->main}\n";
echo "\nFeels like " .
    number_format(kelvinToCelsius($weatherInfo->main->feels_like)) .
    "°C. ";
echo $weatherInfo->weather[0]->description . ".\n";
echo "Pressure: {$weatherInfo->main->pressure}hPa\n";
echo "Humidity: {$weatherInfo->main->humidity}%\n";
echo "Visibility: " .
    number_format($weatherInfo->visibility / 1000, 1) .
    "km\n";
echo "Wind: {$weatherInfo->wind->deg}° {$weatherInfo->wind->speed}m/s N\n";