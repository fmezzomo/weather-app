<template>
  <div>
    <form @submit.prevent="submitForm">
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

    <div v-if="options.length > 1">
      <h3>Select a City</h3>
      <ul>
        <li v-for="option in options" :key="option.id">
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
import { ref } from 'vue';

export default {
  setup() {
    const city = ref('');
    const options = ref([]);
    const weatherData = ref(null);

    const submitForm = async () => {
      try {
        const response = await fetch(`http://localhost:8000/find/${city.value}`);
        const data = await response.json();

        if (data.count > 1) {
          options.value = data.list; // Múltiplas opções para o usuário escolher
          weatherData.value = null;
        } else if (data.count === 1) {
          // Uma cidade encontrada, buscar a previsão diretamente
          await getForecast(data.list[0].name);
        } else {
          alert('City not found');
        }
      } catch (error) {
        console.error('Error fetching city options:', error);
      }
    };

    const selectCity = async (selectedCity: any) => {
      await getForecast(selectedCity.name);
    };

    const getForecast = async (cityName: string) => {
      try {
        const response = await fetch(`http://localhost:8000/weather/${cityName}`);
        const data = await response.json();
        weatherData.value = data;
        options.value = [];
      } catch (error) {
        console.error('Error fetching forecast data:', error);
      }
    };

    return { city, submitForm, options, selectCity, weatherData };
  }
};
</script>

<style scoped>
/* Adicione o estilo necessário aqui */
</style>
