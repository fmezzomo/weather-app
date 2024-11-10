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
        <li v-for="option in cityOptions" :key="option.id" @click="selectCity(option)">
          <div class="city-option-button">
            <div class="city-option-info">
              <span style="width: 140px;">
                {{ option.name }}, {{ option.sys.country }}
                <img :src="'https://openweathermap.org/images/flags/' + option.sys.country.toLowerCase() + '.png'" class="flag">
              </span>
              <span>{{ roundedTemp(option.main.temp) }}°C</span>
              <span>
                <img :src="getWeatherIconUrl(option.weather[0].icon)" :alt="option.weather[0].description" width="50" height="50">
              </span>
              <span class="sub" style="width: 150px; text-align: right;">
                {{ option.coord.lat }}, {{ option.coord.lon }}
              </span>
              <span>
                <button 
                  @click.stop="isFavorite(option) ? removeFavoriteCity(option) : addFavoriteCity(option)" 
                  :class="['favorite-btn', { favorite: isFavorite(option) }]"
                  :disabled="!isFavorite(option) && !canAddFavorite">
                  ♥
                </button>
              </span>
            </div>
          </div>
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
  import { defineComponent, ref, computed, onMounted } from 'vue';
  import { findCity, getWeather } from '@/services/WeatherService';
  import { getFavorites, addFavorite, removeFavorite } from '@/services/FavoriteService';
  import { City, WeatherData, FavoriteCity } from '@/services/Types';

  export default defineComponent({
    name: 'Weather',
    setup() {
      const city = ref<string>('');
      const cityOptions = ref<City[]>([]);
      const weatherData = ref<WeatherData | null>(null);
      const errorMessage = ref<string | null>(null);  
      const favorites = ref<FavoriteCity[]>([]);

      // Get favorites
      onMounted(() => {
        fetchFavorites();
      });

      const canAddFavorite = computed(() => favorites.value.length < 3);

      const isFavorite = (city: City) => {
        return favorites.value.some(favorite => favorite.city_id === city.id);
      };

      const fetchFavorites = async () => {
        try {
          const favoriteCities = await getFavorites();
          favorites.value = favoriteCities;
        } catch (error) {
          console.error('Fail to fetch favorites:', error);
        }
      };

      const addFavoriteCity = async (city: City) => {
       try {
          const response = await addFavorite(city);

          if (response && response.success) {
            //props.favorites.push(response.favorite);
            // Success message
            fetchFavorites();
          } else {
            /*const index = props.favorites.findIndex(fav => fav.city_id === city.id);
            if (index !== -1) props.favorites.splice(index, 1);*/
            // Error message
          }
        } catch (error) {
          handleError("Fail to add city:", error);
        }
      }

      const removeFavoriteCity = async (city: City) => {
        const response = await removeFavorite(city.id);

        if (response && response.success) {
          fetchFavorites();
        } else {

        }
      }

      const fetchCityOptions = async (): Promise<void> => {
        if (!city.value) return;
        try {
          const data = await findCity(city.value);
          if (data.count > 1) {
            cityOptions.value = data.list;
            resetWeatherData();
          } else if (data.count === 1) {
            await fetchWeatherForecast(data.list[0].coord.lat, data.list[0].coord.lon);
          } else {
            errorMessage.value = 'City not found';
          }
        } catch (error: any) {
          handleError('Error fetching city options', error);
        }
      };

      const selectCity = async (selectedCity: City): Promise<void> => {
        await fetchWeatherForecast(selectedCity.coord.lat, selectedCity.coord.lon);
      };

      const fetchWeatherForecast = async (lat: number, lon: number): Promise<void> => {
        try {
          weatherData.value = await getWeather(lat, lon);
          errorMessage.value = null;
        } catch (error: any) {
          handleError('Failed to fetch weather forecast', error);
        }
      };

      const resetWeatherData = (): void => {
        weatherData.value = null;
        errorMessage.value = null;
      };

      const handleError = (message: string, error: any): void => {
        errorMessage.value = `${message}: ${error.message}`;
        console.error(message, error);
      };

      const roundedTemp = (temp: number): number => Math.round(temp);

      const getWeatherIconUrl = (iconCode: string): string =>
        `https://openweathermap.org/img/wn/${iconCode}@2x.png`;

      return {
        city,
        cityOptions,
        weatherData,
        errorMessage,
        canAddFavorite,
        isFavorite,
        addFavoriteCity,
        removeFavoriteCity,
        fetchCityOptions,
        selectCity,
        roundedTemp,
        getWeatherIconUrl
      };
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

  .favorite-btn {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 1.5em;
    color: #ccc;
  }

  .favorite-btn.favorite {
    color: red;
  }
</style>
