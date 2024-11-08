<template>
    <div>
      <input v-model="city" placeholder="Enter city name" @keyup.enter="fetchWeather" />
      <button @click="fetchWeather">Get Weather</button>
  
      <div v-if="weather">
        <h2>Weather Forecast for {{ weather.city.name }}</h2>
        <ul>
          <li v-for="item in weather.list" :key="item.dt">
            {{ new Date(item.dt * 1000).toLocaleString() }} - {{ item.main.temp }}°C
          </li>
        </ul>
      </div>
    </div>
  </template>
  
  <script lang="ts">
  import { defineComponent, ref } from 'vue';
  import { getWeather } from '../services/WeatherService';
  
  export default defineComponent({
    name: 'Weather',
    setup() {
      const city = ref('London');
      const weather = ref(null);
  
      const fetchWeather = async () => {
        try {
          weather.value = await getWeather(city.value);
        } catch (error) {
          console.error("Failed to fetch weather", error);
        }
      };
  
      return { city, weather, fetchWeather };
    }
  });
  </script>
  




<!--

  <template>
    <div class="dashboard">
      <h1>Dashboard</h1>
      <div v-if="weatherData">
        <h2>Weather Information</h2>
        <p><strong>City:</strong> {{ weatherData.city.name }}</p>
        <p><strong>Temperature:</strong> {{ weatherData.list[0].main.temp }} °C</p>
        <p><strong>Weather:</strong> {{ weatherData.list[0].weather[0].description }}</p>
      </div>
      <div v-else>
        <p>Loading weather data...</p>
      </div>
    </div>
  </template>
  
  <script lang="ts">
  import { defineComponent } from 'vue';
  import WeatherService from '../services/WeatherService';
  
  export default defineComponent({
    name: 'Dashboard',
    data() {
      return {
        weatherData: null,
      };
    },
    mounted() {
      this.fetchWeatherData();
    },
    methods: {
      async fetchWeatherData() {
        try {
          this.weatherData = await WeatherService.getWeather();
        } catch (error) {
          console.error("Error fetching weather data:", error);
        }
      },
    },
  });
  </script>
  
  <style scoped>
  .dashboard {
    padding: 20px;
  }
  </style>
-->