import AsyncStorage from '@react-native-async-storage/async-storage';

// Local Wi-Fi endpoint directly connects computer & phone with zero internet latency
export const WIFI_API_BASE_URL = 'http://192.168.10.34:8000/api/v1';

// Public Cloudflare Tunnel endpoint
export const CLOUDFLARE_API_BASE_URL = 'https://com-kim-agrees-discovery.trycloudflare.com/api/v1';

export const DEFAULT_API_BASE_URL = WIFI_API_BASE_URL;

const API_STORAGE_KEY = '@school_erp_api_url';

export const getApiBaseUrl = async (): Promise<string> => {
  try {
    const saved = await AsyncStorage.getItem(API_STORAGE_KEY);
    // If the saved URL is the dead old tunnel, default back to active WiFi or fresh tunnel
    if (saved && saved.includes('calgary-dental')) {
      await AsyncStorage.removeItem(API_STORAGE_KEY);
      return DEFAULT_API_BASE_URL;
    }
    return saved || DEFAULT_API_BASE_URL;
  } catch {
    return DEFAULT_API_BASE_URL;
  }
};

export const setApiBaseUrl = async (url: string): Promise<void> => {
  try {
    await AsyncStorage.setItem(API_STORAGE_KEY, url);
  } catch (error) {
    console.error('Failed to save API base URL', error);
  }
};
