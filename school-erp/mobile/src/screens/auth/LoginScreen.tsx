import React, { useState } from 'react';
import {
  View,
  Text,
  TextInput,
  TouchableOpacity,
  StyleSheet,
  ActivityIndicator,
  KeyboardAvoidingView,
  Platform,
  ScrollView,
  Alert,
} from 'react-native';
import { Colors } from '../../theme/colors';
import { useAuth } from '../../context/AuthContext';
import { getApiBaseUrl, setApiBaseUrl, WIFI_API_BASE_URL, CLOUDFLARE_API_BASE_URL } from '../../config/env';

export const LoginScreen: React.FC<{ navigation: any }> = ({ navigation }) => {
  const { login } = useAuth();
  const [email, setEmail] = useState('parent@school.test');
  const [password, setPassword] = useState('Password@123');
  const [isLoading, setIsLoading] = useState(false);
  const [errorMessage, setErrorMessage] = useState('');
  const [showConfig, setShowConfig] = useState(false);
  const [customApiUrl, setCustomApiUrl] = useState('');

  const handleLogin = async () => {
    if (!email || !password) {
      setErrorMessage('Please enter both email address and password.');
      return;
    }

    setErrorMessage('');
    setIsLoading(true);

    try {
      await login(email.trim(), password);
    } catch (err: any) {
      console.log('Login failed', err);
      if (err.response?.status === 503) {
        setErrorMessage('Mobile App Integration is disabled in Admin Settings.');
      } else if (err.response?.data?.message) {
        setErrorMessage(err.response.data.message);
      } else {
        setErrorMessage('Could not connect to ERP server. Check network connection.');
      }
    } finally {
      setIsLoading(false);
    }
  };

  const handleDemoSelect = (demoEmail: string) => {
    setEmail(demoEmail);
    setPassword('Password@123');
    setErrorMessage('');
  };

  const openConfig = async () => {
    const current = await getApiBaseUrl();
    setCustomApiUrl(current);
    setShowConfig(!showConfig);
  };

  const saveConfig = async () => {
    if (customApiUrl) {
      await setApiBaseUrl(customApiUrl.trim());
      Alert.alert('Saved', 'API Base URL updated successfully.');
      setShowConfig(false);
    }
  };

  return (
    <KeyboardAvoidingView
      style={styles.container}
      behavior={Platform.OS === 'ios' ? 'padding' : undefined}
    >
      <ScrollView contentContainerStyle={styles.scrollContent} keyboardShouldPersistTaps="handled">
        {/* Header */}
        <View style={styles.header}>
          <View style={styles.crest}>
            <Text style={styles.crestText}>TS</Text>
          </View>
          <Text style={styles.title}>Test Subject HSS</Text>
          <Text style={styles.subtitle}>Mobile Campus ERP Portal</Text>
        </View>

        {/* Card Form */}
        <View style={styles.card}>
          <Text style={styles.cardTitle}>Sign In to Your Account</Text>
          <Text style={styles.cardSubtitle}>Students, Parents & Faculty Access</Text>

          {errorMessage ? (
            <View style={styles.errorBox}>
              <Text style={styles.errorText}>{errorMessage}</Text>
            </View>
          ) : null}

          <View style={styles.inputGroup}>
            <Text style={styles.label}>Email Address</Text>
            <TextInput
              style={styles.input}
              value={email}
              onChangeText={setEmail}
              placeholder="yourname@school.test"
              autoCapitalize="none"
              keyboardType="email-address"
            />
          </View>

          <View style={styles.inputGroup}>
            <Text style={styles.label}>Password</Text>
            <TextInput
              style={styles.input}
              value={password}
              onChangeText={setPassword}
              placeholder="••••••••••••"
              secureTextEntry
            />
          </View>

          <TouchableOpacity
            style={[styles.loginBtn, isLoading && styles.btnDisabled]}
            onPress={handleLogin}
            disabled={isLoading}
          >
            {isLoading ? (
              <ActivityIndicator color="#ffffff" size="small" />
            ) : (
              <Text style={styles.loginBtnText}>Sign In</Text>
            )}
          </TouchableOpacity>

          {/* Quick Demo Credentials Fill */}
          <View style={styles.demoSection}>
            <Text style={styles.demoTitle}>Quick Demo Logins (1-Tap):</Text>
            <View style={styles.demoGrid}>
              <TouchableOpacity
                style={styles.demoPill}
                onPress={() => handleDemoSelect('parent@school.test')}
              >
                <Text style={styles.demoPillText}>Parent / Ward</Text>
              </TouchableOpacity>
              <TouchableOpacity
                style={styles.demoPill}
                onPress={() => handleDemoSelect('teacher@school.test')}
              >
                <Text style={styles.demoPillText}>Teacher</Text>
              </TouchableOpacity>
              <TouchableOpacity
                style={styles.demoPill}
                onPress={() => handleDemoSelect('principal@school.test')}
              >
                <Text style={styles.demoPillText}>Principal</Text>
              </TouchableOpacity>
            </View>
          </View>

          {/* Server Config Switcher */}
          <TouchableOpacity style={styles.configToggle} onPress={openConfig}>
            <Text style={styles.configToggleText}>
              {showConfig ? 'Hide Server Config ▲' : 'Configure Server Endpoint ▼'}
            </Text>
          </TouchableOpacity>

          {showConfig ? (
            <View style={styles.configBox}>
              <Text style={styles.configLabel}>Active API Base URL:</Text>
              <TextInput
                style={styles.configInput}
                value={customApiUrl}
                onChangeText={setCustomApiUrl}
                autoCapitalize="none"
              />
              <View style={{ flexDirection: 'row', gap: 6, marginVertical: 8 }}>
                <TouchableOpacity
                  style={[styles.demoPill, { flex: 1, backgroundColor: Colors.primaryTint }]}
                  onPress={() => setCustomApiUrl(WIFI_API_BASE_URL)}
                >
                  <Text style={[styles.demoPillText, { color: Colors.primary, fontSize: 11 }]}>
                    📶 Local Wi-Fi
                  </Text>
                </TouchableOpacity>
                <TouchableOpacity
                  style={[styles.demoPill, { flex: 1, backgroundColor: Colors.accentTint }]}
                  onPress={() => setCustomApiUrl(CLOUDFLARE_API_BASE_URL)}
                >
                  <Text style={[styles.demoPillText, { color: Colors.accent, fontSize: 11 }]}>
                    🌐 Cloudflare
                  </Text>
                </TouchableOpacity>
              </View>
              <TouchableOpacity style={styles.configSaveBtn} onPress={saveConfig}>
                <Text style={styles.configSaveBtnText}>Save Endpoint</Text>
              </TouchableOpacity>
            </View>
          ) : null}
        </View>

        {/* Footer */}
        <Text style={styles.footerNote}>
          Test Subject Higher Secondary School • Pudukkottai, Tamil Nadu
        </Text>
      </ScrollView>
    </KeyboardAvoidingView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: Colors.background,
  },
  scrollContent: {
    padding: 24,
    justifyContent: 'center',
    minHeight: '100%',
  },
  header: {
    alignItems: 'center',
    marginBottom: 24,
    marginTop: 20,
  },
  crest: {
    width: 60,
    height: 60,
    borderRadius: 16,
    backgroundColor: Colors.primary,
    borderWidth: 2,
    borderColor: Colors.accent,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 10,
    shadowColor: Colors.primary,
    shadowOpacity: 0.3,
    shadowRadius: 6,
    elevation: 4,
  },
  crestText: {
    fontSize: 26,
    fontWeight: '900',
    color: '#ffffff',
  },
  title: {
    fontSize: 20,
    fontWeight: '800',
    color: Colors.textPrimary,
  },
  subtitle: {
    fontSize: 12,
    fontWeight: '600',
    color: Colors.primary,
    marginTop: 2,
  },
  card: {
    backgroundColor: '#ffffff',
    borderRadius: 20,
    padding: 24,
    borderWidth: 1,
    borderColor: Colors.border,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05,
    shadowRadius: 10,
    elevation: 2,
  },
  cardTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    color: Colors.textPrimary,
  },
  cardSubtitle: {
    fontSize: 12,
    color: Colors.textSecondary,
    marginBottom: 20,
    marginTop: 2,
  },
  errorBox: {
    backgroundColor: '#fee2e2',
    borderLeftWidth: 4,
    borderLeftColor: Colors.danger,
    padding: 10,
    borderRadius: 8,
    marginBottom: 16,
  },
  errorText: {
    color: '#991b1b',
    fontSize: 12,
    fontWeight: '500',
  },
  inputGroup: {
    marginBottom: 16,
  },
  label: {
    fontSize: 12,
    fontWeight: '600',
    color: Colors.textSecondary,
    marginBottom: 6,
  },
  input: {
    backgroundColor: '#f8fafc',
    borderWidth: 1,
    borderColor: Colors.border,
    borderRadius: 12,
    paddingHorizontal: 14,
    paddingVertical: 12,
    fontSize: 14,
    color: Colors.textPrimary,
  },
  loginBtn: {
    backgroundColor: Colors.primary,
    paddingVertical: 14,
    borderRadius: 12,
    alignItems: 'center',
    marginTop: 6,
    shadowColor: Colors.primary,
    shadowOpacity: 0.3,
    shadowRadius: 6,
    elevation: 3,
  },
  btnDisabled: {
    opacity: 0.7,
  },
  loginBtnText: {
    color: '#ffffff',
    fontSize: 15,
    fontWeight: 'bold',
  },
  demoSection: {
    marginTop: 20,
    paddingTop: 16,
    borderTopWidth: 1,
    borderTopColor: Colors.border,
  },
  demoTitle: {
    fontSize: 11,
    fontWeight: 'bold',
    color: Colors.textMuted,
    textTransform: 'uppercase',
    letterSpacing: 0.5,
    marginBottom: 8,
  },
  demoGrid: {
    flexDirection: 'row',
    gap: 8,
  },
  demoPill: {
    flex: 1,
    backgroundColor: Colors.primaryTint,
    borderWidth: 1,
    borderColor: '#a7f3d0',
    paddingVertical: 8,
    borderRadius: 10,
    alignItems: 'center',
  },
  demoPillText: {
    fontSize: 11,
    fontWeight: '700',
    color: Colors.primaryDark,
  },
  configToggle: {
    marginTop: 18,
    alignItems: 'center',
  },
  configToggleText: {
    fontSize: 11,
    color: Colors.textMuted,
    fontWeight: '600',
  },
  configBox: {
    marginTop: 12,
    backgroundColor: '#f1f5f9',
    padding: 12,
    borderRadius: 10,
  },
  configLabel: {
    fontSize: 10,
    fontWeight: 'bold',
    color: Colors.textSecondary,
    marginBottom: 4,
  },
  configInput: {
    backgroundColor: '#ffffff',
    borderWidth: 1,
    borderColor: '#cbd5e1',
    borderRadius: 8,
    padding: 8,
    fontSize: 11,
    fontFamily: Platform.OS === 'ios' ? 'Courier' : 'monospace',
    marginBottom: 8,
  },
  configSaveBtn: {
    backgroundColor: Colors.accent,
    paddingVertical: 6,
    borderRadius: 6,
    alignItems: 'center',
  },
  configSaveBtnText: {
    color: '#ffffff',
    fontSize: 11,
    fontWeight: 'bold',
  },
  footerNote: {
    textAlign: 'center',
    fontSize: 11,
    color: Colors.textMuted,
    marginTop: 24,
  },
});
