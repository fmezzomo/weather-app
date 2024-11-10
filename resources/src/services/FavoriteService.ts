import axios from 'axios';
import { City, FavoriteCity } from './Types';

export async function getFavorites(): Promise<FavoriteCity[]> {
  const response = await axios.get('/api/favorites');
  return response.data;
}

export async function addFavorite(city: City) {
  const response = await axios.post('/api/favorites', { city });
  return response.data;
}

export async function removeFavorite(cityId: number): Promise<void> {
  const response = await axios.delete(`/api/favorites/${cityId}`);
  return response.data;
}
