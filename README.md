
# Weather App

A web application to check the current weather and forecast for a specified location. Built using Vue.js, PHP, Laravel, and MySQL or SQLite.

## Project Overview

This Weather App allows users to log in, add favorite locations, and retrieve weather forecasts from a third-party API. The app saves weather data, including min/max temperatures and conditions (such as sun, rain, etc.), and displays an icon representing the weather.

## Features

1. **User Authentication**: 
   - Two pre-registered users can log in using email and password. 
   - Includes a login page (no registration form).

2. **Weather Forecast Search**:
   - Users can enter a city and state to fetch and display the weather forecast.

3. **Favorite Locations**:
   - Users can save up to three favorite locations.
   - Saved locations are unique to each user.
   - Locations are saved with associated forecast data.

4. **Weather Icons**:
   - The app displays icons to represent weather conditions, like sun, rain, clouds, etc.

5. **Persistent Data**:
   - Forecast data for saved locations is reloaded when users log in again.

6. **Automated Testing**:
   - Feature tests for the forecast endpoint using PHPUnit or Pest.
   - Mocked API calls for testing without real API usage.

## Setup Instructions

1. **Clone the repository**:

   ```bash
   git clone https://github.com/fmezzomo/weather-app.git
   cd weather-app
   ```

2. **Install dependencies**:

   ```bash
   composer install
   ```

3. **Configure environment variables**:

   - Copy the example environment file and update it:

     ```bash
     cp .env.example .env
     ```

   - Update `.env` with your database settings (MySQL or SQLite) and API key:

     ```plaintext
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=weather_app
     DB_USERNAME=root
     DB_PASSWORD=your_password

     API_KEY=your_openweathermap_api_key
     ```

4. **Generate application key**:

   ```bash
   php artisan key:generate
   ```

5. **Run migrations and seed users**:

   ```bash
   php artisan migrate --seed
   ```

6. **Install front-end dependencies** (if using Vue CLI):

   ```bash
   npm install
   ```

7. **Build front-end assets**:

   ```bash
   npm run dev
   ```

8. **Run the application**:

   ```bash
   php artisan serve
   ```

   The application will be accessible at [http://localhost:8000](http://localhost:8000).

## Usage

1. **Login**: Use one of the pre-registered user accounts to log in.
2. **Search for Weather**: Enter a city and state to get the weather forecast.
3. **Save Locations**: Save up to three locations and see weather forecasts for each.
4. **Remove Locations**: Remove saved locations from your favorites list.

## Testing

This project includes automated tests for the backend, focusing on the forecast endpoint.

1. **Run the tests**:

   ```bash
   php artisan test
   ```

2. **Testing details**:
   - The tests mock API responses for performance and to avoid real API usage.
   - Tests check for both valid and invalid inputs to the forecast endpoint.

---

Enjoy using the Weather App! For more details, see the project on [GitHub](https://github.com/fmezzomo/weather-app).
