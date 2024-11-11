<template>
    <div class="fav-cities">
      <h2>
        MY CITIES
        <span class="toggle-link" @click="toggleCities">
          ({{ isExpanded ? 'Hide' : 'Show' }})
        </span>
      </h2>
      <ul v-show="isExpanded">
        <li v-for="(favorite, index) in favoriteCities" :key="index">
          <span>{{ favorite.city.name }} ({{ favorite.city.sys.country }})</span>
          <div class="action-buttons">
            <button class="view-button" @click="handleClick(favorite.city)">View</button>
            <button class="delete-button" @click="removeCity(favorite.city)">Delete</button>
          </div>
        </li>
      </ul>
    </div>
  </template>
  
  <script lang="ts">
    import { defineComponent, PropType, ref } from 'vue';
    import { FavoriteCity } from '@/services/Types';
    import { City } from '../services/Types';
  
    export default defineComponent({
      name: 'FavoritesCities',
      props: {
        favoriteCities: {
          type: Array as PropType<FavoriteCity[]>,
          required: true,
        },
      },
      setup(props, { emit }) {
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
  
    .fav-cities ul {
      list-style-type: none;
      padding: 0;
      margin: 10px 0;
    }
  
    .fav-cities li {
      background-color: #ecf0f1;
      border: 1px solid #bdc3c7;
      padding: 12px;
      margin: 8px 0;
      border-radius: 6px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      transition: background-color 0.3s;
      position: relative;
    }
  
    .fav-cities li:hover {
      background-color: #3498db;
      color: white;
    }
  
    .action-buttons {
      display: none;
    }
  
    .fav-cities li:hover .action-buttons {
      display: flex;
      gap: 8px;
    }
  
    .view-button,
    .delete-button {
      background: none;
      border: none;
      color: white;
      cursor: pointer;
      padding: 4px 8px;
      border-radius: 4px;
      font-size: 14px;
    }
  
    .view-button {
      background-color: #2ecc71;
    }
  
    .view-button:hover {
      background-color: #27ae60;
    }
  
    .delete-button {
      background-color: #e74c3c;
    }
  
    .delete-button:hover {
      background-color: #c0392b;
    }
  </style>
  