import React from 'react';
import { View, Platform } from 'react-native';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import {
  Home,
  Calendar,
  CheckCircle2,
  CreditCard,
  Bell,
  User,
} from 'lucide-react-native';
import { Colors } from '../theme/colors';

import { HomeScreen } from '../screens/home/HomeScreen';
import { TimetableScreen } from '../screens/academics/TimetableScreen';
import { AttendanceScreen } from '../screens/attendance/AttendanceScreen';
import { FeesScreen } from '../screens/fees/FeesScreen';
import { NoticesScreen } from '../screens/notices/NoticesScreen';
import { ProfileScreen } from '../screens/profile/ProfileScreen';

export type BottomTabParamList = {
  Home: undefined;
  Timetable: undefined;
  Attendance: undefined;
  Fees: undefined;
  Notices: undefined;
  Profile: undefined;
};

const Tab = createBottomTabNavigator<BottomTabParamList>();

export const TabNavigator: React.FC = () => {
  const insets = useSafeAreaInsets();
  const bottomInset = insets.bottom > 0 ? insets.bottom : (Platform.OS === 'android' ? 14 : 8);

  return (
    <Tab.Navigator
      screenOptions={{
        headerStyle: {
          backgroundColor: Colors.primary,
        },
        headerTintColor: '#ffffff',
        headerTitleStyle: {
          fontWeight: '700',
          fontSize: 17,
        },
        tabBarActiveTintColor: Colors.primary,
        tabBarInactiveTintColor: '#475569', // High-contrast Slate-600
        tabBarStyle: {
          backgroundColor: '#ffffff',
          borderTopColor: '#e2e8f0',
          borderTopWidth: 1.5,
          height: 58 + bottomInset,
          paddingBottom: bottomInset,
          paddingTop: 8,
          elevation: 12,
          shadowColor: '#000',
          shadowOffset: { width: 0, height: -2 },
          shadowOpacity: 0.08,
          shadowRadius: 6,
        },
        tabBarLabelStyle: {
          fontSize: 11,
          fontWeight: '700',
          marginTop: 2,
        },
      }}
    >
      <Tab.Screen
        name="Home"
        component={HomeScreen}
        options={{
          headerTitle: 'Test Subject HSS Portal',
          tabBarLabel: 'Home',
          tabBarIcon: ({ color, focused }) => (
            <Home size={22} color={color} strokeWidth={focused ? 2.6 : 2} />
          ),
        }}
      />

      <Tab.Screen
        name="Timetable"
        component={TimetableScreen}
        options={{
          headerTitle: 'Class Timetable',
          tabBarLabel: 'Timetable',
          tabBarIcon: ({ color, focused }) => (
            <Calendar size={22} color={color} strokeWidth={focused ? 2.6 : 2} />
          ),
        }}
      />

      <Tab.Screen
        name="Attendance"
        component={AttendanceScreen}
        options={{
          headerTitle: 'Turnout & Roll-Call',
          tabBarLabel: 'Attendance',
          tabBarIcon: ({ color, focused }) => (
            <CheckCircle2 size={22} color={color} strokeWidth={focused ? 2.6 : 2} />
          ),
        }}
      />

      <Tab.Screen
        name="Fees"
        component={FeesScreen}
        options={{
          headerTitle: 'Fee Installments & Receipts',
          tabBarLabel: 'Fees',
          tabBarIcon: ({ color, focused }) => (
            <CreditCard size={22} color={color} strokeWidth={focused ? 2.6 : 2} />
          ),
        }}
      />

      <Tab.Screen
        name="Notices"
        component={NoticesScreen}
        options={{
          headerTitle: 'Campus Circulars',
          tabBarLabel: 'Notices',
          tabBarIcon: ({ color, focused }) => (
            <Bell size={22} color={color} strokeWidth={focused ? 2.6 : 2} />
          ),
        }}
      />

      <Tab.Screen
        name="Profile"
        component={ProfileScreen}
        options={{
          headerTitle: 'Digital Student ID',
          tabBarLabel: 'ID Card',
          tabBarIcon: ({ color, focused }) => (
            <User size={22} color={color} strokeWidth={focused ? 2.6 : 2} />
          ),
        }}
      />
    </Tab.Navigator>
  );
};
