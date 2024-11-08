import axios from 'axios';

const apiKey = import.meta.env.VITE_API_KEY;
const apiUrl = import.meta.env.VITE_API_URL;

export const getWeather = async (lat: number, lon: number) => {
    try {
        const response = await axios.get(`/weather`, {
            params: { lat, lon }
        });
        return response.data;
    } catch (error) {
        console.error('Error fetching weather data:', error);
        throw error;
    }
};

export const findCity = async (city: string) => {
    try {
        const encodedCity = encodeURIComponent(city);
        const response = await axios.get(`/find/${encodedCity}`);
        return response.data;
    } catch (error) {
        console.error("Error finding city:", error);
        throw error;
    }
};