<template>
    <div>
      <h2>MY CITIES</h2>
        <ul>
            <li 
                v-for="(favorite, index) in favoriteCities" 
                :key="index" 
                @click="handleClick(favorite.city)">
                {{ favorite.city.name }} ({{ favorite.city.sys.country }})
            </li>
        </ul>
    </div>
  </template>
  
<script lang="ts">
    import { defineComponent, PropType } from 'vue';
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
        methods: {
            handleClick(city: City) {
            this.$emit('city-clicked', city);
            },
        },
    });
</script>