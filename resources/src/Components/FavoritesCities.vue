<template>
    {{ cityOptions }}
    <div class="fav-cities">
        <h2>
            MY CITIES
            <span class="toggle-link" @click="toggleCities">
                ({{ isExpanded ? 'Hide' : 'Show' }})
            </span>
        </h2>
        <CitiesList
            ref="citiesListRef"
            :showCities="isExpanded"
            :favorites="favoriteCities"
            :cityOptions="cityOptions"
            :showFavorite="true"
            @selectCity="handleClick"
        />
    </div>
</template>
  
  <script lang="ts">
    import { defineComponent, PropType, ref, computed } from 'vue';
    import { FavoriteCity } from '@/services/Types';
    import { City } from '../services/Types';
    import CitiesList from '@/Components/CitiesList.vue';
  
    export default defineComponent({
    components: {
      CitiesList,
    },
      name: 'FavoritesCities',
      props: {
        favoriteCities: {
          type: Array as PropType<FavoriteCity[]>,
          required: true,
        },
      },
      setup(props, { emit }) {
        const cityOptions = computed(() => {
            return props.favoriteCities.map(favorite => {
                // Accessing the forecast list for the city
                if (favorite.city.forecast === '') return;
                const forecasts = favorite.city.forecast.original.list;

                // Get the current time
                const currentTime = new Date();
                const currentHour = currentTime.getHours();

                // Find the forecast closest to the current time (based on the 3-hour intervals)
                const closestForecast = forecasts.reduce((closest, forecast) => {
                    const forecastTime = new Date(forecast.dt * 1000); // Convert timestamp to Date
                    const forecastHour = forecastTime.getHours();

                    // Calculate the absolute difference between current hour and forecast hour
                    const hourDiff = Math.abs(currentHour - forecastHour);

                    // If the current forecast is closer or if it's the first iteration, choose it
                    if (!closest || hourDiff < closest.diff) {
                        return {
                            forecast,
                            diff: hourDiff,
                        };
                    }

                    return closest;
                }, null);

                // If a forecast for the current hour is found, map the necessary data
                if (closestForecast) {
                    return {
                        date: closestForecast.forecast.dt,
                        id: favorite.city.id,
                        name: favorite.city.name,
                        coord: favorite.city.coord,
                        main: closestForecast.forecast.main,
                        sys: {
                            country: favorite.city.sys.country
                        },
                        weather: closestForecast.forecast.weather,
                    };
                }

                // If no forecast is found for the current hour, return only the city name with null values for the forecast
                return {
                    id: favorite.city.id,
                    name: favorite.city.name,
                    coord: favorite.city.coord,
                    main: null,
                    sys: null,
                    weather: null,
                };
            });
        });

        const isExpanded = ref(true);
  
        const toggleCities = () => {
          isExpanded.value = !isExpanded.value;
        };

        const handleClick = (city: City) => {
            emit('city-clicked', city);
        };
  
        const removeCity = (city: City) => {
            emit('remove-favorite', city);
        };
  
        return {
          isExpanded,
          toggleCities,
          handleClick,
          removeCity,
          cityOptions,
        };
      },
    });
  </script>
  
  <style lang="css" scoped>
    .fav-cities {
      margin: 20px 0;
      font-family: 'Arial', sans-serif;
      color: #333;
    }
  
    .fav-cities h2 {
      font-size: 24px;
      color: #2c3e50;
      margin-bottom: 10px;
      text-align: center;
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 8px;
    }
  
    .toggle-link {
      font-size: 14px;
      color: #3498db;
      cursor: pointer;
      transition: color 0.3s;
    }
  
    .toggle-link:hover {
      color: #2980b9;
    }

  </style>
  