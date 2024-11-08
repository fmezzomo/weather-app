import axios from 'axios';

const apiKey = import.meta.env.VITE_API_KEY;
const apiUrl = import.meta.env.VITE_API_URL;

export const getWeather = async (city: string) => {
    try {
        const response = await fetch('http://localhost:8000/weather/${city}');
        const data = await response.json();

        return data;
    } catch (error) {
        console.error('Error fetching weather data:', error);
    }
};
