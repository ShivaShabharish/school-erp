import React, { createContext, useContext, useState, useEffect } from 'react';
import { apiService } from '../services/api';

export interface User {
  id: number;
  name: string;
  email: string;
  role: string;
  phone?: string;
  student_profile?: any;
  staff_profile?: any;
}

interface AuthContextType {
  user: User | null;
  token: string | null;
  selectedStudent: any | null;
  isLoading: boolean;
  login: (email: string, pass: string) => Promise<void>;
  logout: () => Promise<void>;
  setSelectedStudent: (student: any) => void;
  refreshUser: () => Promise<void>;
}

const AuthContext = createContext<AuthContextType>({} as AuthContextType);

export const AuthProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const [user, setUser] = useState<User | null>(null);
  const [token, setToken] = useState<string | null>(null);
  const [selectedStudent, setSelectedStudent] = useState<any | null>(null);
  const [isLoading, setIsLoading] = useState<boolean>(true);

  const checkAuth = async () => {
    try {
      const storedToken = await apiService.getStoredToken();
      if (storedToken) {
        setToken(storedToken);
        const me = await apiService.getMe();
        if (me && me.data) {
          setUser(me.data);
          if (me.data.student_profile) {
            setSelectedStudent(me.data.student_profile);
          }
        }
      }
    } catch {
      await apiService.logout();
      setUser(null);
      setToken(null);
    } finally {
      setIsLoading(false);
    }
  };

  useEffect(() => {
    checkAuth();
  }, []);

  const login = async (email: string, pass: string) => {
    const data = await apiService.login(email, pass);
    setToken(data.token);
    setUser(data.user);

    // Fetch full profile and student linkage
    try {
      const me = await apiService.getMe();
      if (me && me.data) {
        setUser(me.data);
        if (me.data.student_profile) {
          setSelectedStudent(me.data.student_profile);
        }
      }
    } catch (e) {
      console.log('Error loading initial profile', e);
    }
  };

  const logout = async () => {
    await apiService.logout();
    setUser(null);
    setToken(null);
    setSelectedStudent(null);
  };

  const refreshUser = async () => {
    try {
      const me = await apiService.getMe();
      if (me && me.data) {
        setUser(me.data);
        if (me.data.student_profile && !selectedStudent) {
          setSelectedStudent(me.data.student_profile);
        }
      }
    } catch (e) {
      console.log('Failed refreshing user data', e);
    }
  };

  return (
    <AuthContext.Provider
      value={{
        user,
        token,
        selectedStudent,
        isLoading,
        login,
        logout,
        setSelectedStudent,
        refreshUser,
      }}
    >
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => useContext(AuthContext);
