import React from 'react';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { useAuth } from '../context/AuthContext';
import { SplashScreen } from '../screens/auth/SplashScreen';
import { LoginScreen } from '../screens/auth/LoginScreen';
import { TabNavigator } from './TabNavigator';
import { HomeworkScreen } from '../screens/academics/HomeworkScreen';
import { Colors } from '../theme/colors';

export type RootStackParamList = {
  Splash: undefined;
  Login: undefined;
  Main: undefined;
  Homework: undefined;
};

const Stack = createNativeStackNavigator<RootStackParamList>();

export const RootNavigator: React.FC = () => {
  const { token, isLoading } = useAuth();

  if (isLoading) {
    return <SplashScreen />;
  }

  return (
    <Stack.Navigator
      screenOptions={{
        headerShown: false,
      }}
    >
      {!token ? (
        <Stack.Screen name="Login" component={LoginScreen} />
      ) : (
        <>
          <Stack.Screen name="Main" component={TabNavigator} />
          <Stack.Screen
            name="Homework"
            component={HomeworkScreen}
            options={{
              headerShown: true,
              headerTitle: 'Daily Homework Board',
              headerStyle: {
                backgroundColor: Colors.primary,
              },
              headerTintColor: '#ffffff',
              headerTitleStyle: {
                fontWeight: '700',
              },
            }}
          />
        </>
      )}
    </Stack.Navigator>
  );
};
