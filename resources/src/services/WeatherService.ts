import axios from 'axios';

export const getWeather = async (lat: number, lon: number) => {
    try {
        const response = await axios.get(`/weather/${lat}/${lon}`);
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