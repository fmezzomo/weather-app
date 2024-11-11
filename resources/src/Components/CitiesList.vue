<script lang="ts">
  import { defineComponent, computed, PropType } from 'vue';
  import { addFavorite, removeFavorite } from '@/services/FavoriteService';
  import { City, FavoriteCity } from '@/services/Types';
  import { roundedTemp, getWeatherIconUrl } from '@/utils/weatherUtils';

  export default defineComponent({
    name: 'Cities List',
    
    props: {
        showCities: {
            type: Boolean,
            required: false,
            default: true
        },
        favorites: {
            type: Array as PropType<FavoriteCity[]>,
            required: true
        },
        cityOptions: {
            type: Array as PropType<City[]>,
            required: true
        },
        showFavorite: {
            type: Boolean,
            required: false,
            default: true
        }
    },
    setup(props, { emit }) {
        const addFavoriteCity = async (city: City) => {
        if (props.favorites.length >= 3) {
            emit('show-message', "You can't add more than 3 favorite cities.", false );
            return;
        }

        try {
          const response = await addFavorite(city);

          if (response && response.success) {
            emit('fetch-favorites');
            emit('show-message', "City added to favorites!", true );
          } else {
            emit('show-message', "Failed to add city to favorites.", false );
          }
        } catch (error) {
          emit('show-message', "Fail to add city to favorites: " + error, false );
        }
      };

      const removeFavoriteCity = async (city: City) => {
        if(confirm('Are you sure you want to delete this city from your favorites? This action cannot be undone.?')) {
          try {
            const response = await removeFavorite(city.id);

            if (response && response.success) {
                emit('fetch-favorites');
                emit('show-message', "City removed from favorites!", true );
            } else {
                emit('show-message', "Failed to remove city from favorites.", false );
            }
          } catch (error) {
            emit('show-message', "An error occurred while removing the city: " + error, false );
          }
        }
      };
        const canAddFavorite = computed(() => props.favorites.length < 3);
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

        const isFavorite = (city: City) => {
            return props.favorites.some(favorite => favorite.city.id === city.id);
        };

        const selectCity = (city: City) => {
            emit('select-city', city);
        }

        return {
            favoriteButtonMessage,
            canAddFavorite,
            isFavorite,
            addFavoriteCity,
            removeFavoriteCity,
            roundedTemp,
            getWeatherIconUrl,
            selectCity,
        }
    },
  });
</script>

<template>
    <div class="city-options" v-if="showCities && cityOptions.length >= 1">
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
              <span v-if="showFavorite">
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
</template>

<style scoped>
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