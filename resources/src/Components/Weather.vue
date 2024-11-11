<script lang="ts">
  import { defineComponent, ref, computed, onMounted } from 'vue';
  import { findCity, getWeather } from '@/services/WeatherService';
  import { getFavorites, addFavorite, removeFavorite } from '@/services/FavoriteService';
  import { City, WeatherData, FavoriteCity } from '@/services/Types';
  import FavoritesCities from '@/Components/FavoritesCities.vue';

  export default defineComponent({
    components: {
      FavoritesCities,
    },
    name: 'Weather',
    setup() {
      const city = ref<string>('');
      const cityOptions = ref<City[]>([]);
      const weatherData = ref<WeatherData | null>(null);
      const favorites = ref<FavoriteCity[]>([]);

      const message = ref('');
      const isVisible = ref(false);

      const showMessage = (msg: string, success: boolean) => {
        message.value = msg;
        isVisible.value = true;
        
        setTimeout(() => {
          isVisible.value = false;
        }, 3000);
      };

      // Get favorites
      onMounted(() => {
        fetchFavorites();
      });

      const canAddFavorite = computed(() => favorites.value.length < 3);
      const favoriteButtonMessage = computed(() => {
        return (city: City) => {
          
          if (isFavorite(city)) {
            return "Remove from favorites";
          } else {
            if (!canAddFavorite.value) {
              return "Cannot add more than 3 cities";
            }
            return "Add to favorites";
          }
        };
      });

      const groupedForecasts = computed(() => {
        if (!weatherData.value || !weatherData.value.list) {
          return {};
        }

        const forecastsByDay: { [date: string]: Array<typeof weatherData.value.list[0]> } = {};

        weatherData.value.list.forEach(forecast => {
          const date = new Date(forecast.dt * 1000).toLocaleDateString('en-US', {
            weekday: 'long', 
            month: 'short', 
            day: 'numeric'
          });

          if (!forecastsByDay[date]) {
            forecastsByDay[date] = [];
          }

          forecastsByDay[date].push(forecast);
        });

        return forecastsByDay;
      });

      const isFavorite = (city: City) => {
        return favorites.value.some(favorite => favorite.city.id === city.id);
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
        if (favorites.value.length >= 3) {
          showMessage("You can't add more than 3 favorite cities.", false);
          return;
        }

        try {
          const response = await addFavorite(city);

          if (response && response.success) {
            fetchFavorites();
            showMessage("City added to favorites!", true);
          } else {
            showMessage("Failed to add city to favorites.", false);
          }
        } catch (error) {
          showMessage("Fail to add city to favorites: " + error, false);
        }
      };

      const removeFavoriteCity = async (city: City) => {
        if(confirm('Are you sure you want to delete this city from your favorites? This action cannot be undone.?')) {
          try {
            const response = await removeFavorite(city.id);

            if (response && response.success) {
              fetchFavorites();
              showMessage("City removed from favorites!", true);
            } else {
              showMessage("Failed to remove city from favorites.", false);
            }
          } catch (error) {
            showMessage("An error occurred while removing the city: " + error, false);
          }
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

      const resetWeatherData = (): void => {
        weatherData.value = null;
      };

      const roundedTemp = (temp: number): number => Math.round(temp);

      const formatTime = (timestamp: number): string => {
          return new Date(timestamp * 1000).toLocaleTimeString('en-US', {
              hour: '2-digit', 
              minute: '2-digit'
          });
      };

      const getWeatherIconUrl = (iconCode: string): string =>
        `https://openweathermap.org/img/wn/${iconCode}@2x.png`;

      return {
        groupedForecasts,
        favorites,
        favoriteButtonMessage,
        message,
        isVisible,
        city,
        cityOptions,
        weatherData,
        canAddFavorite,
        isFavorite,
        addFavoriteCity,
        removeFavoriteCity,
        fetchCityOptions,
        selectCity,
        roundedTemp,
        formatTime,
        getWeatherIconUrl
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

    <div v-if="!weatherData && cityOptions.length >= 1" class="city-options">
      <h3>Select a City</h3>
      <ul>
        <li v-for="option in cityOptions" :key="option.id" @click="selectCity(option)">
          <div class="city-option-button">
            <div class="city-option-info">
              <span style="width: 250px;">
                {{ option.name }}, {{ option.sys.country }}
                <img :src="'https://openweathermap.org/images/flags/' + option.sys.country.toLowerCase() + '.png'" class="flag">
              </span>
              <span>{{ roundedTemp(option.main.temp) }}°C</span>
              <span>
                <img :src="getWeatherIconUrl(option.weather[0].icon)" :title="option.weather[0].description" width="50" height="50">
              </span>
              <span class="sub" style="width: 150px; text-align: right;">
                {{ option.coord.lat }}, {{ option.coord.lon }}
              </span>
              <span>
                <button 
                  @click.stop="isFavorite(option) ? removeFavoriteCity(option) : addFavoriteCity(option)" 
                  :class="['favorite-btn', { favorite: isFavorite(option) }]"
                  :disabled="!isFavorite(option) && !canAddFavorite"
                  :title="favoriteButtonMessage(option)">
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
        <ul class="daily-forecast-list">
            <li v-for="(forecasts, date) in groupedForecasts" :key="date" class="daily-forecast-item">
                <h3>{{ date }}</h3>
                <ul class="hourly-forecast-list">
                    <li v-for="forecast in forecasts" :key="forecast.dt" class="hourly-forecast-item">
                        <span class="forecast-time">{{ formatTime(forecast.dt) }}</span>
                        <span class="forecast-temp">{{ forecast.main.temp }}°C</span>
                        <img :src="getWeatherIconUrl(forecast.weather[0].icon)" :alt="forecast.weather[0].description" class="forecast-icon" />
                    </li>
                </ul>
            </li>
        </ul>
    </div>
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
    opacity: 0;
    animation: fadeIn 0.5s forwards;
    z-index: 1000;
  }

  .success {
    background-color: green;
  }

  .error {
    background-color: red;
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
    }
    to {
      opacity: 1;
    }
  }

  .daily-forecast-list {
      list-style: none;
      padding: 0;
  }
  .daily-forecast-item {
      margin-bottom: 15px;
      padding-bottom: 10px;
  }
  .hourly-forecast-list {
      list-style: none;
      padding: 0;
  }
  .hourly-forecast-item {
      display: flex;
      align-items: center;
      gap: 20px;
      padding: 8px 0;
      font-size: 1rem;
      color: #333;
      border-bottom: 1px solid #eee;
  }

  .forecast-time {
      flex: 1;
      font-weight: bold;
      color: #007BFF;
  }
  .forecast-temp {
      flex: 1;
      font-size: 1.1rem;
      color: #ff5722;
  }
  .forecast-icon {
      width: 40px;
      height: 40px;
  }
</style>
