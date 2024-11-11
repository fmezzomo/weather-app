import axios from 'axios';
import { City, FavoriteCity } from './Types';

export async function getFavorites(): Promise<FavoriteCity[]> {
  const response = await axios.get('/api/favorites');

  const favoriteCities: FavoriteCity[] = response.data.map((favorite: any) => ({
    id: favorite.id,
    userID: favorite.user_id,
    city: {
      id: favorite.city_id,
      name: favorite.city_name,
      coord: {
        lat: favorite.lat,
        lon: favorite.lon,
      },
      sys: {
        country: favorite.country
      }
    }
  }));

  return favoriteCities;
}

export async function addFavorite(city: City) {
  const response = await axios.post('/api/favorites', { city });
  return response.data;
}

export async function removeFavorite(cityId: number): Promise<void> {
  const response = await axios.delete(`/api/favorites/${cityId}`);
  return response.data;
}
