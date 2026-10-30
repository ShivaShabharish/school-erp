import React from 'react';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
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
        tabBarInactiveTintColor: Colors.textMuted,
        tabBarStyle: {
          backgroundColor: '#ffffff',
          borderTopColor: Colors.border,
          borderTopWidth: 1,
          height: 64,
          paddingBottom: 8,
          paddingTop: 6,
        },
        tabBarLabelStyle: {
          fontSize: 11,
          fontWeight: '600',
        },
      }}
    >
      <Tab.Screen
        name="Home"
        component={HomeScreen}
        options={{
          headerTitle: 'Test Subject HSS Portal',
          tabBarLabel: 'Home',
          tabBarIcon: ({ color, size }) => <Home size={size} color={color} />,
        }}
      />

      <Tab.Screen
        name="Timetable"
        component={TimetableScreen}
        options={{
          headerTitle: 'Class Timetable',
          tabBarLabel: 'Timetable',
          tabBarIcon: ({ color, size }) => <Calendar size={size} color={color} />,
        }}
      />

      <Tab.Screen
        name="Attendance"
        component={AttendanceScreen}
        options={{
          headerTitle: 'Turnout & Roll-Call',
          tabBarLabel: 'Attendance',
          tabBarIcon: ({ color, size }) => <CheckCircle2 size={size} color={color} />,
        }}
      />

      <Tab.Screen
        name="Fees"
        component={FeesScreen}
        options={{
          headerTitle: 'Fee Installments & Receipts',
          tabBarLabel: 'Fees',
          tabBarIcon: ({ color, size }) => <CreditCard size={size} color={color} />,
        }}
      />

      <Tab.Screen
        name="Notices"
        component={NoticesScreen}
        options={{
          headerTitle: 'Campus Circulars',
          tabBarLabel: 'Notices',
          tabBarIcon: ({ color, size }) => <Bell size={size} color={color} />,
        }}
      />

      <Tab.Screen
        name="Profile"
        component={ProfileScreen}
        options={{
          headerTitle: 'Digital Student ID',
          tabBarLabel: 'ID Card',
          tabBarIcon: ({ color, size }) => <User size={size} color={color} />,
        }}
      />
    </Tab.Navigator>
  );
};
