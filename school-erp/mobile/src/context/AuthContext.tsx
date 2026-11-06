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
  login: (email: string, pass: string, onStatus?: (status: string) => void) => Promise<void>;
  loginDemo: (role?: 'parent' | 'teacher' | 'principal') => Promise<void>;
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

  const login = async (email: string, pass: string, onStatus?: (status: string) => void) => {
    const data = await apiService.login(email, pass, onStatus);
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

  const loginDemo = async (role: 'parent' | 'teacher' | 'principal' = 'parent') => {
    const mockStudent = {
      id: 1,
      admission_number: 'ADM-2024-001',
      first_name: 'Kavitha',
      last_name: 'Senthilkumar',
      class_name: 'Class 10 - Section A',
      roll_number: '1001',
      gender: 'female',
      blood_group: 'B+',
      guardian_name: 'M. Senthilkumar',
      guardian_phone: '+91 98421 99991',
    };

    const demoUsers = {
      parent: {
        id: 10,
        name: 'M. Senthilkumar',
        email: 'parent@school.test',
        role: 'parent',
        student_profile: mockStudent,
      },
      teacher: {
        id: 5,
        name: 'S. Ramanathan (Maths HOD)',
        email: 'teacher@school.test',
        role: 'teacher',
      },
      principal: {
        id: 1,
        name: 'Dr. K. Annamalai (Principal)',
        email: 'principal@school.test',
        role: 'super_admin',
      },
    };

    const chosen = demoUsers[role] || demoUsers.parent;
    setUser(chosen);
    setToken('demo-evaluator-token');
    if (chosen.student_profile) {
      setSelectedStudent(chosen.student_profile);
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
