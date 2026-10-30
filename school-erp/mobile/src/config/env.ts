import AsyncStorage from '@react-native-async-storage/async-storage';

export const DEFAULT_API_BASE_URL = 'https://calgary-dental-increase-promotions.trycloudflare.com/api/v1';
export const LOCAL_API_BASE_URL = 'http://127.0.0.1:8000/api/v1';

const API_STORAGE_KEY = '@school_erp_api_url';

export const getApiBaseUrl = async (): Promise<string> => {
  try {
    const saved = await AsyncStorage.getItem(API_STORAGE_KEY);
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
