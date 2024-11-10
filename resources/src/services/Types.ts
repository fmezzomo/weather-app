export interface City {
    id: number;
    name: string;
    coord: {
      lat: number;
      lon: number;
    };
    sys: {
      country: string;
    };
  }
  
  export interface WeatherData {
    city: {
      name: string;
      country: string;
    };
    list: Array<{
      dt: number;
      main: {
        temp: number;
      };
      weather: Array<{
        description: string;
      }>;
    }>;
  }

  export interface FavoriteCity {
    userID: number;
    city: City;
  }
  