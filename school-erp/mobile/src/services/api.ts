import axios, { AxiosInstance } from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { getApiBaseUrl } from '../config/env';

const TOKEN_KEY = '@school_erp_token';

let apiInstance: AxiosInstance | null = null;

export const getApiClient = async (): Promise<AxiosInstance> => {
  const baseURL = await getApiBaseUrl();
  const token = await AsyncStorage.getItem(TOKEN_KEY);

  const client = axios.create({
    baseURL,
    headers: {
      'Content-Type': 'application/json',
      Accept: 'application/json',
      ...(token ? { Authorization: `Bearer ${token}` } : {}),
    },
    timeout: 45000,
  });

  return client;
};

// Offline-first caching helper
const getCachedOrFetch = async (cacheKey: string, fetcher: () => Promise<any>) => {
  try {
    const data = await fetcher();
    if (data !== undefined && data !== null) {
      await AsyncStorage.setItem(cacheKey, JSON.stringify(data));
    }
    return data;
  } catch (err) {
    const cached = await AsyncStorage.getItem(cacheKey);
    if (cached) {
      try {
        return JSON.parse(cached);
      } catch {
        // fallback to err
      }
    }
    throw err;
  }
};

export const apiService = {
  // Auth with intelligent cold-server wake-up retry engine
  async login(
    email: string,
    password: string,
    onStatus?: (status: string) => void,
    deviceName = 'ExpoMobileApp'
  ) {
    const maxRetries = 6;
    let attempt = 0;

    while (attempt < maxRetries) {
      attempt++;
      try {
        if (attempt > 1 && onStatus) {
          onStatus(`⚡ Cloud Server is waking up (Render Free Tier)... Attempt ${attempt}/${maxRetries}`);
        }
        const client = await getApiClient();
        const res = await client.post('/auth/token', {
          email,
          password,
          device_name: deviceName,
        });
        if (res.data.token) {
          await AsyncStorage.setItem(TOKEN_KEY, res.data.token);
        }
        return res.data;
      } catch (err: any) {
        const isNetworkOrCold =
          !err.response ||
          err.response.status >= 500 ||
          err.code === 'ECONNABORTED' ||
          err.message?.includes('Network Error') ||
          (typeof err.response?.data === 'string' && err.response.data.includes('Render'));

        if (isNetworkOrCold && attempt < maxRetries) {
          if (onStatus) {
            onStatus(`⚡ Cloud Server is waking up... Please hold on (${attempt}/${maxRetries})`);
          }
          await new Promise((resolve) => setTimeout(resolve, 3500));
        } else {
          throw err;
        }
      }
    }
  },

  async getMe() {
    return getCachedOrFetch('@school_cache_me', async () => {
      const client = await getApiClient();
      const res = await client.get('/auth/user');
      return res.data;
    });
  },

  async logout() {
    try {
      const client = await getApiClient();
      await client.post('/auth/revoke');
    } catch {
      // ignore network errors on logout
    } finally {
      await AsyncStorage.removeItem(TOKEN_KEY);
    }
  },

  async getStoredToken() {
    return await AsyncStorage.getItem(TOKEN_KEY);
  },

  // Student & Ward Portal with Offline Cache
  async getStudentProfile(studentId?: number) {
    return getCachedOrFetch(`@school_cache_profile_${studentId ?? 'def'}`, async () => {
      const client = await getApiClient();
      const res = await client.get('/student/profile', {
        params: studentId ? { student_id: studentId } : {},
      });
      return res.data.data;
    });
  },

  async getStudentAttendance(studentId?: number) {
    return getCachedOrFetch(`@school_cache_att_${studentId ?? 'def'}`, async () => {
      const client = await getApiClient();
      const res = await client.get('/student/attendance', {
        params: studentId ? { student_id: studentId } : {},
      });
      return res.data.data;
    });
  },

  async getStudentFees(studentId?: number) {
    return getCachedOrFetch(`@school_cache_fees_${studentId ?? 'def'}`, async () => {
      const client = await getApiClient();
      const res = await client.get('/student/fees', {
        params: studentId ? { student_id: studentId } : {},
      });
      return res.data.data;
    });
  },

  async getStudentTimetable(studentId?: number) {
    return getCachedOrFetch(`@school_cache_tt_${studentId ?? 'def'}`, async () => {
      const client = await getApiClient();
      const res = await client.get('/student/timetable', {
        params: studentId ? { student_id: studentId } : {},
      });
      return res.data.data;
    });
  },

  async getStudentHomework(studentId?: number) {
    return getCachedOrFetch(`@school_cache_hw_${studentId ?? 'def'}`, async () => {
      const client = await getApiClient();
      const res = await client.get('/student/homework', {
        params: studentId ? { student_id: studentId } : {},
      });
      return res.data.data;
    });
  },

  async getAnnouncements(category?: string) {
    return getCachedOrFetch(`@school_cache_ann_${category ?? 'all'}`, async () => {
      const client = await getApiClient();
      const res = await client.get('/announcements', {
        params: category ? { category } : {},
      });
      return res.data.data;
    });
  },
};
