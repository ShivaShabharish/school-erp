import React, { useEffect } from 'react';
import { View, Text, StyleSheet, ActivityIndicator } from 'react-native';
import { Colors } from '../../theme/colors';
import { useAuth } from '../../context/AuthContext';

export const SplashScreen: React.FC<{ navigation?: any }> = ({ navigation }) => {
  const { isLoading, token } = useAuth();

  useEffect(() => {
    if (!isLoading && navigation) {
      if (token) {
        navigation.replace('Main');
      } else {
        navigation.replace('Login');
      }
    }
  }, [isLoading, token, navigation]);

  return (
    <View style={styles.container}>
      <View style={styles.crestWrapper}>
        <View style={styles.crest}>
          <Text style={styles.crestText}>TS</Text>
        </View>
        <Text style={styles.schoolName}>TEST SUBJECT</Text>
        <Text style={styles.schoolSub}>HIGHER SECONDARY SCHOOL</Text>
        <View style={styles.badgePill}>
          <Text style={styles.badgeText}>Tamil Nadu State Board • CCE</Text>
        </View>
        <Text style={styles.location}>Pudukkottai, Tamil Nadu</Text>
      </View>

      <View style={styles.footer}>
        <ActivityIndicator size="small" color={Colors.accentLight} />
        <Text style={styles.loadingText}>Connecting to School ERP Network...</Text>
      </View>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: Colors.primaryDark,
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingVertical: 80,
    paddingHorizontal: 24,
  },
  crestWrapper: {
    alignItems: 'center',
    marginTop: 100,
  },
  crest: {
    width: 90,
    height: 90,
    borderRadius: 24,
    backgroundColor: Colors.primary,
    borderWidth: 3,
    borderColor: Colors.accent,
    alignItems: 'center',
    justifyContent: 'center',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.3,
    shadowRadius: 10,
    elevation: 8,
    marginBottom: 24,
  },
  crestText: {
    fontSize: 42,
    fontWeight: '900',
    color: '#ffffff',
    letterSpacing: 2,
  },
  schoolName: {
    fontSize: 24,
    fontWeight: '900',
    color: '#ffffff',
    letterSpacing: 1.5,
    textAlign: 'center',
  },
  schoolSub: {
    fontSize: 13,
    fontWeight: '700',
    color: '#a7f3d0',
    letterSpacing: 2,
    marginTop: 4,
    textAlign: 'center',
  },
  badgePill: {
    marginTop: 16,
    paddingHorizontal: 14,
    paddingVertical: 5,
    borderRadius: 20,
    backgroundColor: 'rgba(217, 119, 6, 0.2)',
    borderWidth: 1,
    borderColor: Colors.accent,
  },
  badgeText: {
    fontSize: 11,
    fontWeight: 'bold',
    color: Colors.accentLight,
    textTransform: 'uppercase',
    letterSpacing: 0.5,
  },
  location: {
    fontSize: 11,
    color: '#94a3b8',
    marginTop: 8,
  },
  footer: {
    alignItems: 'center',
    gap: 12,
  },
  loadingText: {
    fontSize: 12,
    color: '#cbd5e1',
    fontWeight: '500',
  },
});
