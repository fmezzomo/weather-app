<template>
  <div>
    <form @submit.prevent="fetchCityOptions">
      <label for="city">City:</label>
      <input
        type="text"
        id="city"
        v-model="city"
        placeholder="Enter city"
        required
      />
      <button type="submit">Get Weather</button>
    </form>

    <div v-if="cityOptions.length > 1">
      <h3>Select a City</h3>
      <ul>
        <li v-for="option in cityOptions" :key="option.id">
          <button @click="selectCity(option)">
            {{ option.name }}, {{ option.sys.country }} (Lat: {{ option.coord.lat }}, Lon: {{ option.coord.lon }})
          </button>
        </li>
      </ul>
    </div>

    <div v-else-if="weatherData">
      <h2>Weather for {{ weatherData.city.name }}, {{ weatherData.city.country }}</h2>
      <ul>
        <li v-for="forecast in weatherData.list" :key="forecast.dt">
          <p>{{ new Date(forecast.dt * 1000).toLocaleString() }}: {{ forecast.main.temp }}°C, {{ forecast.weather[0].description }}</p>
        </li>
      </ul>
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

    const fetchCityOptions = async () => {
      try {
        const data = await findCity(city.value);
        if (data.count > 1) {
          cityOptions.value = data.list;
          weatherData.value = null;
        } else if (data.count === 1) {
          await fetchWeatherForecast(data.list[0].coord.lat, data.list[0].coord.lon);
        } else {
          alert('City not found');
        }
      } catch (error) {
        console.error("Error fetching city options:", error);
      }
    };

    const selectCity = async (selectedCity: City) => {
      await fetchWeatherForecast(selectedCity.coord.lat, selectedCity.coord.lon);
    };

    const fetchWeatherForecast = async (lat: number, lon: number) => {
      try {
        weatherData.value = await getWeather(lat, lon);
      } catch (error) {
        console.error("Failed to fetch weather forecast", error);
      }
    };

    return { city, cityOptions, weatherData, fetchCityOptions, selectCity };
  }
});
</script>
