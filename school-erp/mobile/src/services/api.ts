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
    timeout: 15000,
  });

  return client;
};

export const apiService = {
  // Auth
  async login(email: string, password: string, deviceName = 'ExpoMobileApp') {
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
  },

  async getMe() {
    const client = await getApiClient();
    const res = await client.get('/auth/user');
    return res.data;
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

  // Student & Ward Portal
  async getStudentProfile(studentId?: number) {
    const client = await getApiClient();
    const res = await client.get('/student/profile', {
      params: studentId ? { student_id: studentId } : {},
    });
    return res.data.data;
  },

  async getStudentAttendance(studentId?: number) {
    const client = await getApiClient();
    const res = await client.get('/student/attendance', {
      params: studentId ? { student_id: studentId } : {},
    });
    return res.data.data;
  },

  async getStudentFees(studentId?: number) {
    const client = await getApiClient();
    const res = await client.get('/student/fees', {
      params: studentId ? { student_id: studentId } : {},
    });
    return res.data.data;
  },

  async getStudentTimetable(studentId?: number) {
    const client = await getApiClient();
    const res = await client.get('/student/timetable', {
      params: studentId ? { student_id: studentId } : {},
    });
    return res.data.data;
  },

  async getStudentHomework(studentId?: number) {
    const client = await getApiClient();
    const res = await client.get('/student/homework', {
      params: studentId ? { student_id: studentId } : {},
    });
    return res.data.data;
  },

  async getAnnouncements(category?: string) {
    const client = await getApiClient();
    const res = await client.get('/announcements', {
      params: category ? { category } : {},
    });
    return res.data.data;
  },
};
