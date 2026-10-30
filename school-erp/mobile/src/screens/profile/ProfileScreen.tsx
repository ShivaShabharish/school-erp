import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  Alert,
  Linking,
  ActivityIndicator,
  Share,
} from 'react-native';
import {
  User,
  Shield,
  Phone,
  Mail,
  MapPin,
  Calendar,
  Heart,
  QrCode,
  LogOut,
  ExternalLink,
  ChevronRight,
  School,
  Settings,
  HelpCircle,
} from 'lucide-react-native';
import { Colors } from '../../theme/colors';
import { useAuth } from '../../context/AuthContext';
import { apiService } from '../../services/api';

export const ProfileScreen: React.FC<{ navigation?: any }> = ({ navigation }) => {
  const { user, selectedStudent, logout } = useAuth();
  const [profile, setProfile] = useState<any>(null);
  const [loading, setLoading] = useState<boolean>(true);

  useEffect(() => {
    const loadProfile = async () => {
      try {
        const data = await apiService.getStudentProfile(selectedStudent?.id);
        if (data) setProfile(data);
      } catch (e) {
        console.log('Error loading student profile', e);
      } finally {
        setLoading(false);
      }
    };
    loadProfile();
  }, [selectedStudent]);

  const handleLogout = () => {
    Alert.alert(
      'Sign Out',
      'Are you sure you want to end this active session and sign out of the School ERP?',
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Sign Out',
          style: 'destructive',
          onPress: async () => {
            await logout();
          },
        },
      ]
    );
  };

  const handleCallParent = (phoneNumber?: string) => {
    if (!phoneNumber) {
      Alert.alert('No Phone', 'No contact telephone number registered for guardian.');
      return;
    }
    Linking.openURL(`tel:${phoneNumber}`);
  };

  const handleShareId = async () => {
    try {
      await Share.share({
        title: 'Student Institutional ID Card',
        message: `Test Subject Higher Secondary School (Pudukkottai)\nStudent: ${studentName}\nAdmission No: ${admNo}\nClass: ${className}\nBlood Group: ${bloodGroup}\nEmergency Contact: ${parentPhone}`,
      });
    } catch {
      // ignore
    }
  };

  const studentName = profile?.full_name || selectedStudent?.full_name || user?.name || 'Aadhiran Selvam';
  const admNo = profile?.admission_number || selectedStudent?.admission_number || 'ADM-2026-0001';
  const className = profile?.class ? `Class ${profile.class} - ${profile.section}` : 'Class 10 - A';
  const rollNo = profile?.roll_number || '14';
  const dob = profile?.date_of_birth || '15 May 2011';
  const bloodGroup = profile?.blood_group || 'O+ve';
  const guardianName = profile?.guardian?.name || user?.name || 'Selvam Sundaram';
  const parentPhone = profile?.guardian?.phone || user?.phone || '+91 98421 55678';
  const email = profile?.guardian?.email || user?.email || 'parent@school.test';
  const address = profile?.guardian?.address || '14, Gandhi Road, Pudukkottai, Tamil Nadu - 622001';

  return (
    <ScrollView style={styles.screen} contentContainerStyle={styles.scrollContent}>
      {/* Institutional ID Card (CR-80 Form Factor) */}
      <View style={styles.idCard}>
        {/* Card Header Strip */}
        <View style={styles.idCardHeader}>
          <View style={styles.idCrestBox}>
            <School size={20} color="#ffffff" />
          </View>
          <View style={{ flex: 1 }}>
            <Text style={styles.idSchoolTitle}>TEST SUBJECT HIGHER SECONDARY SCHOOL</Text>
            <Text style={styles.idSchoolSubtitle}>Tamil Nadu State Board • Pudukkottai</Text>
          </View>
        </View>

        {/* Card Body */}
        <View style={styles.idCardBody}>
          <View style={styles.photoContainer}>
            <View style={styles.avatarPlaceholder}>
              <Text style={styles.avatarInitial}>
                {studentName.split(' ').map((n: string) => n[0]).join('').substring(0, 2).toUpperCase()}
              </Text>
            </View>
            <View style={styles.verifiedBadge}>
              <Shield size={12} color="#ffffff" />
            </View>
          </View>

          <View style={styles.studentDetails}>
            <Text style={styles.idStudentName}>{studentName}</Text>
            <View style={styles.classBadge}>
              <Text style={styles.classBadgeText}>{className}</Text>
            </View>

            <View style={styles.metaRow}>
              <Text style={styles.metaLabel}>Admission No:</Text>
              <Text style={styles.metaValue}>{admNo}</Text>
            </View>

            <View style={styles.metaRow}>
              <Text style={styles.metaLabel}>Roll Number:</Text>
              <Text style={styles.metaValue}>{rollNo}</Text>
            </View>

            <View style={styles.metaRow}>
              <Text style={styles.metaLabel}>Blood Group:</Text>
              <Text style={[styles.metaValue, { color: Colors.danger, fontWeight: '700' }]}>
                {bloodGroup}
              </Text>
            </View>

            <View style={styles.metaRow}>
              <Text style={styles.metaLabel}>Date of Birth:</Text>
              <Text style={styles.metaValue}>{dob}</Text>
            </View>
          </View>
        </View>

        {/* Card Barcode Footer */}
        <View style={styles.idCardFooter}>
          <View style={styles.barcodeVisual}>
            <View style={[styles.bar, { width: 3 }]} />
            <View style={[styles.bar, { width: 1 }]} />
            <View style={[styles.bar, { width: 4 }]} />
            <View style={[styles.bar, { width: 2 }]} />
            <View style={[styles.bar, { width: 1 }]} />
            <View style={[styles.bar, { width: 5 }]} />
            <View style={[styles.bar, { width: 2 }]} />
            <View style={[styles.bar, { width: 3 }]} />
            <View style={[styles.bar, { width: 1 }]} />
            <View style={[styles.bar, { width: 4 }]} />
            <View style={[styles.bar, { width: 2 }]} />
            <View style={[styles.bar, { width: 6 }]} />
            <View style={[styles.bar, { width: 1 }]} />
            <View style={[styles.bar, { width: 3 }]} />
            <View style={[styles.bar, { width: 2 }]} />
            <View style={[styles.bar, { width: 5 }]} />
          </View>
          <Text style={styles.barcodeText}>{admNo} • SESSION 2026-27</Text>
        </View>
      </View>

      {/* Share / Digital Pass Button */}
      <TouchableOpacity style={styles.shareIdButton} onPress={handleShareId} activeOpacity={0.8}>
        <QrCode size={18} color={Colors.primary} />
        <Text style={styles.shareIdButtonText}>Share Digital ID Verification Card</Text>
      </TouchableOpacity>

      {/* Guardian & Emergency Dossier */}
      <View style={styles.sectionCard}>
        <View style={styles.sectionCardHeader}>
          <Shield size={18} color={Colors.primary} />
          <Text style={styles.sectionCardTitle}>Guardian & Emergency Contact</Text>
        </View>

        <View style={styles.contactItem}>
          <User size={18} color={Colors.textMuted} />
          <View style={{ flex: 1 }}>
            <Text style={styles.contactLabel}>Primary Guardian / Parent</Text>
            <Text style={styles.contactValue}>{guardianName}</Text>
          </View>
        </View>

        <View style={styles.contactItem}>
          <Phone size={18} color={Colors.textMuted} />
          <View style={{ flex: 1 }}>
            <Text style={styles.contactLabel}>Emergency Phone</Text>
            <Text style={styles.contactValue}>{parentPhone}</Text>
          </View>
          <TouchableOpacity
            style={styles.callBadge}
            onPress={() => handleCallParent(parentPhone)}
            activeOpacity={0.7}
          >
            <Text style={styles.callBadgeText}>Call Now</Text>
          </TouchableOpacity>
        </View>

        <View style={styles.contactItem}>
          <Mail size={18} color={Colors.textMuted} />
          <View style={{ flex: 1 }}>
            <Text style={styles.contactLabel}>Registered Email Address</Text>
            <Text style={styles.contactValue}>{email}</Text>
          </View>
        </View>

        <View style={[styles.contactItem, { borderBottomWidth: 0 }]}>
          <MapPin size={18} color={Colors.textMuted} />
          <View style={{ flex: 1 }}>
            <Text style={styles.contactLabel}>Residential Address</Text>
            <Text style={styles.contactValue}>{address}</Text>
          </View>
        </View>
      </View>

      {/* Institutional Metadata & Portal Settings */}
      <View style={styles.sectionCard}>
        <View style={styles.sectionCardHeader}>
          <Settings size={18} color={Colors.primary} />
          <Text style={styles.sectionCardTitle}>Application & Session</Text>
        </View>

        <View style={styles.settingItem}>
          <Text style={styles.settingLabel}>Logged In User Role</Text>
          <View style={styles.roleBadge}>
            <Text style={styles.roleBadgeText}>{user?.role?.toUpperCase() || 'PARENT'}</Text>
          </View>
        </View>

        <View style={styles.settingItem}>
          <Text style={styles.settingLabel}>Academic Session</Text>
          <Text style={styles.settingVal}>2026-2027 (Tamil Nadu CCE)</Text>
        </View>

        <View style={styles.settingItem}>
          <Text style={styles.settingLabel}>App Version</Text>
          <Text style={styles.settingVal}>v1.0.0 (Production Build)</Text>
        </View>

        <View style={[styles.settingItem, { borderBottomWidth: 0 }]}>
          <Text style={styles.settingLabel}>Server Sync Gateway</Text>
          <Text style={[styles.settingVal, { color: Colors.primary, fontWeight: '700' }]}>
            ONLINE (Active)
          </Text>
        </View>
      </View>

      {/* Logout Action */}
      <TouchableOpacity style={styles.logoutButton} onPress={handleLogout} activeOpacity={0.85}>
        <LogOut size={20} color="#dc2626" />
        <Text style={styles.logoutButtonText}>Sign Out from School ERP</Text>
      </TouchableOpacity>

      <Text style={styles.footerNote}>
        Test Subject Higher Secondary School • Pudukkottai, Tamil Nadu{'\n'}
        Powered by School ERP 3.0 Enterprise Mobile Gateway
      </Text>
    </ScrollView>
  );
};

const styles = StyleSheet.create({
  screen: {
    flex: 1,
    backgroundColor: Colors.background,
  },
  scrollContent: {
    padding: 16,
    paddingBottom: 48,
    gap: 16,
  },
  idCard: {
    backgroundColor: Colors.cardBg,
    borderRadius: 16,
    borderWidth: 2,
    borderColor: Colors.primary,
    overflow: 'hidden',
    shadowColor: Colors.primaryDark,
    shadowOpacity: 0.15,
    shadowOffset: { width: 0, height: 4 },
    shadowRadius: 10,
    elevation: 4,
  },
  idCardHeader: {
    backgroundColor: Colors.primary,
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 16,
    paddingVertical: 12,
    gap: 12,
  },
  idCrestBox: {
    width: 36,
    height: 36,
    borderRadius: 8,
    backgroundColor: Colors.primaryDark,
    justifyContent: 'center',
    alignItems: 'center',
  },
  idSchoolTitle: {
    fontSize: 12,
    fontWeight: '800',
    color: '#ffffff',
    letterSpacing: 0.5,
  },
  idSchoolSubtitle: {
    fontSize: 10,
    color: '#a7f3d0',
    marginTop: 2,
  },
  idCardBody: {
    flexDirection: 'row',
    padding: 16,
    gap: 16,
  },
  photoContainer: {
    alignItems: 'center',
    position: 'relative',
  },
  avatarPlaceholder: {
    width: 80,
    height: 96,
    borderRadius: 8,
    backgroundColor: Colors.primaryTint,
    borderWidth: 1.5,
    borderColor: Colors.primaryLight,
    justifyContent: 'center',
    alignItems: 'center',
  },
  avatarInitial: {
    fontSize: 26,
    fontWeight: '800',
    color: Colors.primary,
  },
  verifiedBadge: {
    position: 'absolute',
    bottom: -6,
    backgroundColor: Colors.primary,
    width: 20,
    height: 20,
    borderRadius: 10,
    justifyContent: 'center',
    alignItems: 'center',
    borderWidth: 2,
    borderColor: '#ffffff',
  },
  studentDetails: {
    flex: 1,
    justifyContent: 'center',
  },
  idStudentName: {
    fontSize: 17,
    fontWeight: '800',
    color: Colors.textPrimary,
  },
  classBadge: {
    alignSelf: 'flex-start',
    backgroundColor: Colors.accentTint,
    paddingHorizontal: 8,
    paddingVertical: 2,
    borderRadius: 4,
    marginVertical: 4,
  },
  classBadgeText: {
    fontSize: 11,
    fontWeight: '700',
    color: Colors.accent,
  },
  metaRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
    marginTop: 3,
  },
  metaLabel: {
    fontSize: 11,
    color: Colors.textMuted,
    width: 80,
  },
  metaValue: {
    fontSize: 12,
    fontWeight: '600',
    color: Colors.textPrimary,
  },
  idCardFooter: {
    backgroundColor: '#f8fafc',
    borderTopWidth: 1,
    borderTopColor: Colors.border,
    paddingVertical: 10,
    paddingHorizontal: 16,
    alignItems: 'center',
  },
  barcodeVisual: {
    flexDirection: 'row',
    alignItems: 'center',
    height: 26,
    gap: 2,
  },
  bar: {
    height: '100%',
    backgroundColor: '#0f172a',
  },
  barcodeText: {
    fontSize: 10,
    fontWeight: '600',
    color: Colors.textMuted,
    letterSpacing: 2,
    marginTop: 4,
  },
  shareIdButton: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 8,
    backgroundColor: Colors.cardBg,
    borderRadius: 12,
    paddingVertical: 12,
    borderWidth: 1.5,
    borderColor: Colors.primary,
  },
  shareIdButtonText: {
    fontSize: 13,
    fontWeight: '700',
    color: Colors.primary,
  },
  sectionCard: {
    backgroundColor: Colors.cardBg,
    borderRadius: 14,
    padding: 16,
    borderWidth: 1,
    borderColor: Colors.border,
  },
  sectionCardHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
    paddingBottom: 12,
    borderBottomWidth: 1,
    borderBottomColor: '#f1f5f9',
    marginBottom: 8,
  },
  sectionCardTitle: {
    fontSize: 14,
    fontWeight: '700',
    color: Colors.textPrimary,
  },
  contactItem: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 12,
    paddingVertical: 10,
    borderBottomWidth: 1,
    borderBottomColor: '#f8fafc',
  },
  contactLabel: {
    fontSize: 11,
    color: Colors.textMuted,
  },
  contactValue: {
    fontSize: 13,
    fontWeight: '600',
    color: Colors.textPrimary,
    marginTop: 2,
  },
  callBadge: {
    backgroundColor: Colors.primaryTint,
    paddingHorizontal: 10,
    paddingVertical: 6,
    borderRadius: 8,
  },
  callBadgeText: {
    fontSize: 11,
    fontWeight: '700',
    color: Colors.primary,
  },
  settingItem: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingVertical: 10,
    borderBottomWidth: 1,
    borderBottomColor: '#f8fafc',
  },
  settingLabel: {
    fontSize: 13,
    color: Colors.textSecondary,
  },
  settingVal: {
    fontSize: 13,
    fontWeight: '600',
    color: Colors.textPrimary,
  },
  roleBadge: {
    backgroundColor: Colors.primaryTint,
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: 6,
  },
  roleBadgeText: {
    fontSize: 11,
    fontWeight: '700',
    color: Colors.primary,
  },
  logoutButton: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 8,
    backgroundColor: '#fef2f2',
    borderWidth: 1,
    borderColor: '#fecaca',
    borderRadius: 12,
    paddingVertical: 14,
  },
  logoutButtonText: {
    fontSize: 14,
    fontWeight: '700',
    color: '#dc2626',
  },
  footerNote: {
    fontSize: 11,
    color: Colors.textMuted,
    textAlign: 'center',
    lineHeight: 18,
    marginTop: 8,
  },
});
