<script lang="ts">
    import { defineComponent, PropType, computed } from 'vue';
    import { formatTime, getWeatherIconUrl } from '@/utils/weatherUtils';
    import { WeatherData } from '../services/Types';
    import { roundedTemp } from '@/utils/weatherUtils';

    export default defineComponent({
        name: 'Weather Data',
        props: {
            weatherData: {
                type: Array as PropType<WeatherData[]>,
                required: true
            },
            showData: {
                type: Boolean,
                required: false,
                default: true
            }
        },

        setup(props, { emit }) {

            const groupedForecasts = computed(() => {
                if (!props.weatherData || !props.weatherData.list) {
                    return {};
                }

                const forecastsByDay: { [date: string]: Array<typeof props.weatherData.list[0]> } = {};

                props.weatherData.list.forEach(forecast => {
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

            return {
                formatTime,
                getWeatherIconUrl,
                groupedForecasts,
                roundedTemp,
            };
        },
    });
</script>

<template>
    <div v-if="showData" class="weather-data">
        <h2>Weather for {{ weatherData.city.name }}, {{ weatherData.city.country }}</h2>
        <ul class="daily-forecast-list">
            <li v-for="(forecasts, date) in groupedForecasts" :key="date" class="daily-forecast-item">
                <h3>{{ date }}</h3>
                <ul class="hourly-forecast-list">
                    <li v-for="forecast in forecasts" :key="forecast.dt" class="hourly-forecast-item">
                        <span class="forecast-time">{{ formatTime(forecast.dt) }}</span>
                        <span class="forecast-temp">{{ roundedTemp(forecast.main.temp) }}°C</span>
                        <img :src="getWeatherIconUrl(forecast.weather[0].icon)" :alt="forecast.weather[0].description" class="forecast-icon" />
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</template>

<style scoped>
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