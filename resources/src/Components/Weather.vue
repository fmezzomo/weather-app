<script lang="ts">
  import { defineComponent, ref, computed, onMounted } from 'vue';
  import { findCity, getWeather } from '@/services/WeatherService';
  import { getFavorites } from '@/services/FavoriteService';
  import { City, WeatherData, FavoriteCity } from '@/services/Types';
  import FavoritesCities from '@/Components/FavoritesCities.vue';
  import CitiesList from '@/Components/CitiesList.vue';
  import WeatherDataList from './WeatherDataList.vue';

  export default defineComponent({
    components: {
      FavoritesCities,
      CitiesList,
      WeatherDataList,
    },
    name: 'Weather',
    setup() {
      const weatherData = ref<WeatherData | null>(null);
      const favorites = ref<FavoriteCity[]>([]);
      const city = ref<string>('');
      const cityOptions = ref<City[]>([]);

      const message = ref('');
      const isVisible = ref(false);

      // Get favorites
      onMounted(() => {
        fetchFavorites();
      });

      const showMessage = (msg: string, success: boolean) => {
        message.value = msg;
        isVisible.value = true;
        
        setTimeout(() => {
          isVisible.value = false;
        }, 3000);
      };

      const fetchFavorites = async () => {
        try {
          const favoriteCities = await getFavorites();
          favorites.value = favoriteCities;
        } catch (error) {
          console.error('Fail to fetch favorites:', error);
        }
      };

      const selectCity = async (selectedCity: City): Promise<void> => {
        await fetchWeatherForecast(selectedCity.coord.lat, selectedCity.coord.lon);
      };

      const fetchWeatherForecast = async (lat: number, lon: number): Promise<void> => {
        try {
          weatherData.value = await getWeather(lat, lon);
        } catch (error: any) {
          showMessage("Failed to fetch weather forecast", false);
        }
      };

      const fetchCityOptions = async (): Promise<void> => {
          if (!city.value) return;
          try {
              const data = await findCity(city.value);
              if (data.count >= 1) {
                  cityOptions.value = data.list;
                  resetWeatherData();
              } else {
                showMessage("City not found", false);
              }
          } catch (error: any) {
              showMessage("Error fetching city options", false);
          }
      };

      const resetWeatherData = (): void => {
        weatherData.value = null;
      };

      return {
        favorites,
        message,
        isVisible,
        weatherData,
        selectCity,
        resetWeatherData,
        fetchCityOptions,
        fetchFavorites,
        showMessage,
        city,
        cityOptions,
      };
    },
  });
</script>

<template>
  <div class="weather-container">
    <div v-if="isVisible" class="message-container" :class="{'success': message.includes('added'), 'error': message.includes('failed')}">
      {{ message }}
    </div>

    <FavoritesCities
      class="favorities-cities"
      :favoriteCities="favorites"
      @city-clicked="selectCity"
      @remove-favorite="removeFavoriteCity"
    />

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

    <CitiesList
      :showCities="!weatherData"
      :favorites="favorites"
      :cityOptions="cityOptions"
      :showFavorite="true"
      @select-city="selectCity"
      @fetch-favorites="fetchFavorites"
      @show-message="showMessage"
    />

    <WeatherDataList
      :showData="weatherData"
      :weatherData="weatherData"
    />

  </div>
</template>

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

  .message-container {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    padding: 20px;
    background-color: rgba(0, 0, 0, 0.7);
    color: white;
    border-radius: 8px;
    font-size: 16px;
    animation: fadeIn 0.5s forwards;
    z-index: 1000;
  }

  .success {
    background-color: green;
  }

  .error {
    background-color: red;
  }
</style>
