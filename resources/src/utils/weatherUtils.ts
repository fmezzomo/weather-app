export const roundedTemp = (temp: number): number => {
    return Math.round(temp);
}

export const getWeatherIconUrl = (iconCode: string): string => {
    return `https://openweathermap.org/img/wn/${iconCode}@2x.png`;
}
  
export const formatTime = (timestamp: number): string => {
    return new Date(timestamp * 1000).toLocaleTimeString('en-US', {
        hour: '2-digit', 
        minute: '2-digit'
    });
};