<template>
  <div class="weather-container">
    <form @submit.prevent="fetchCityOptions" class="city-form">
      <label for="city">City:</label>
      <input
        type="text"
        id="city"
        v-model="city"
        placeholder="Enter city"
        required
        class="city-input"
      />
      <button type="submit" class="submit-button">Search</button>
    </form>

    <div v-if="!weatherData && cityOptions.length > 1" class="city-options">
      <h3>Select a City</h3>
      <ul>
        <li v-for="option in cityOptions" :key="option.id">
          <button @click="selectCity(option as City)" class="city-option-button">
            <div class="city-option-info">
              <span style="width: 140px;">
                {{ option.name }}, {{ option.sys.country }}
                <img :src="'https://openweathermap.org/images/flags/' + option.sys.country.toLowerCase() + '.png'" class="flag">
              </span>
              <span>{{ roundedTemp(option.main.temp) }}°C</span>
              <span v-html="getWeatherIcon(option.weather[0].main)"></span>
              <span class="sub" style="width: 150px; text-align: right;">
                {{ option.coord.lat }}, {{ option.coord.lon }}
              </span>
            </div>
          </button>
        </li>
      </ul>
    </div>

    <div v-if="weatherData" class="weather-data">
      <h2>Weather for {{ weatherData.city.name }}, {{ weatherData.city.country }}</h2>
      <ul class="forecast-list">
        <li v-for="forecast in weatherData.list" :key="forecast.dt" class="forecast-item">
          <p>{{ new Date(forecast.dt * 1000).toLocaleString() }}: {{ forecast.main.temp }}°C, {{ forecast.weather[0].description }}</p>
        </li>
      </ul>
    </div>

    <div v-if="errorMessage" class="error-message">
      <p>{{ errorMessage }}</p>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue';
import { findCity, getWeather } from '@/services/WeatherService';
import { City, WeatherData } from '@/services/Types';

export default defineComponent({
  name: 'Weather',
  setup() {
    const city = ref('');
    const cityOptions = ref<City[]>([]);
    const weatherData = ref<WeatherData | null>(null);
    const errorMessage = ref<string | null>(null);

    const fetchCityOptions = async () => {
      try {
        const data = await findCity(city.value);
        if (data.count > 1) {
          cityOptions.value = data.list;
          weatherData.value = null;
          errorMessage.value = null;
        } else if (data.count === 1) {
          await fetchWeatherForecast(data.list[0].coord.lat, data.list[0].coord.lon);
        } else {
          errorMessage.value = 'City not found';
        }
      } catch (error) {
        errorMessage.value = 'Error fetching city options: ' + error.message;
        console.error("Error fetching city options:", error);
      }
    };

    const selectCity = async (selectedCity: City) => {
      await fetchWeatherForecast(selectedCity.coord.lat, selectedCity.coord.lon);
    };

    const fetchWeatherForecast = async (lat: number, lon: number) => {
      try {
        weatherData.value = await getWeather(lat, lon);
        errorMessage.value = null;
      } catch (error) {
        errorMessage.value = 'Failed to fetch weather forecast: ' + error.message;
        console.error("Failed to fetch weather forecast", error);
      }
    };

    return { city, cityOptions, weatherData, errorMessage, fetchCityOptions, selectCity };
  },


  methods: {
    roundedTemp(temp) {
      return Math.round(temp);
    },
    getWeatherIcon(weatherCondition) {
      const icons = {
        Clear: `
          <svg width="50px" height="50px" viewBox="0 0 148 148" class="owm-weather-icon">
            <path d="M65.03 60.514c.642 0 1.27.057 1.889.143a15.476 15.476 0 01-.344-3.23c0-8.524 6.91-15.437 15.435-15.437 8.294 0 15.042 6.547 15.402 14.752a9.224 9.224 0 016.208-2.404 9.263 9.263 0 019.263 9.263 9.165 9.165 0 01-.619 3.305c.7-.14 1.423-.218 2.161-.218 5.97 0 10.806 4.839 10.806 10.805 0 5.97-4.836 10.806-10.806 10.806H65.031c-7.674 0-13.893-6.219-13.893-13.893 0-7.671 6.219-13.892 13.893-13.892" fill="#3b3c40"></path>
          </svg>`,
        Clouds: `
          <svg width="50px" height="50px" viewBox="0 0 148 148" class="owm-weather-icon">
            <!-- SVG path para o ícone de nuvens -->
            <path d="M40 80.5c0-22 18-40 40-40s40 18 40 40H40z" fill="#b0bec5"></path>
          </svg>`,
      };
      
      return icons[weatherCondition] || '<svg width="50px" height="50px"></svg>';
    }
  }
});
</script>

<style scoped>
  .weather-container {
    padding: 20px;
    margin: 0 auto;
    background-color: #f8f9fa;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  }

  .city-form {
    display: flex;
    flex-direction: column;
    margin-bottom: 20px;
  }

  .city-form label {
    font-size: 1.1rem;
    margin-bottom: 8px;
  }

  .city-input {
    padding: 8px;
    font-size: 1rem;
    border-radius: 4px;
    border: 1px solid #ddd;
    margin-bottom: 12px;
  }

  .submit-button {
    padding: 10px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }

  .submit-button:hover {
    background-color: #0056b3;
  }

  .city-options {
    margin-top: 20px;
  }

  .city-options h3 {
    font-size: 1.2rem;
    margin-bottom: 10px;
  }

  .city-option-item {
    list-style-type: none;
  }

  .city-option-button {
    display: block;
    padding: 10px;
    width: 100%;
    color: #000;
    border: none;
    border-radius: 4px;
    text-align: left;
    cursor: pointer;
  }

  .city-option-button:hover {
    background-color: #eff0f3;
  }

  .city-option-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .flag {
    display: inline-block;
    vertical-align: initial;
  }

  .city-name {
    width: 200px;
    display: flex;
    align-items: center;
  }

  .city-weather {
    width: 120px;
    text-align: center;
  }

  .city-coordinates {
    width: 160px;
    text-align: right;
    font-size: 0.9rem;
  }

  .weather-data {
    margin-top: 20px;
  }

  .weather-data h2 {
    font-size: 1.5rem;
    margin-bottom: 12px;
  }

  .forecast-list {
    list-style-type: none;
    padding: 0;
  }

  .forecast-item {
    background-color: #ffffff;
    padding: 12px;
    margin-bottom: 10px;
    border-radius: 4px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }

  .forecast-item p {
    margin: 0;
    font-size: 1rem;
  }

  .error-message {
    margin-top: 20px;
    padding: 10px;
    background-color: #f8d7da;
    color: #721c24;
    border-radius: 4px;
    border: 1px solid #f5c6cb;
  }
</style>
