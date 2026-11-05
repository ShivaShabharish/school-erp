import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  RefreshControl,
  ActivityIndicator,
  Image,
} from 'react-native';
import { Colors } from '../../theme/colors';
import { useAuth } from '../../context/AuthContext';
import { apiService } from '../../services/api';
import { Sparkles, Mic } from 'lucide-react-native';

export const HomeScreen: React.FC<{ navigation?: any }> = ({ navigation }) => {
  const { user, selectedStudent } = useAuth();
  const [profile, setProfile] = useState<any>(null);
  const [attendance, setAttendance] = useState<any>(null);
  const [fees, setFees] = useState<any>(null);
  const [announcements, setAnnouncements] = useState<any[]>([]);
  const [refreshing, setRefreshing] = useState(false);
  const [loading, setLoading] = useState(true);

  const loadData = async () => {
    try {
      const studentId = selectedStudent?.id;
      const [profData, attData, feeData, annData] = await Promise.all([
        apiService.getStudentProfile(studentId).catch(() => null),
        apiService.getStudentAttendance(studentId).catch(() => null),
        apiService.getStudentFees(studentId).catch(() => null),
        apiService.getAnnouncements().catch(() => []),
      ]);

      if (profData) setProfile(profData);
      if (attData) setAttendance(attData);
      if (feeData) setFees(feeData);
      if (annData) setAnnouncements(annData);
    } catch (e) {
      console.log('Error loading dashboard data', e);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useEffect(() => {
    loadData();
  }, [selectedStudent]);

  const onRefresh = () => {
    setRefreshing(true);
    loadData();
  };

  if (loading && !profile) {
    return (
      <View style={styles.centerContainer}>
        <ActivityIndicator size="large" color={Colors.primary} />
        <Text style={styles.loadingText}>Syncing Student Profile...</Text>
      </View>
    );
  }

  const studentName = profile?.full_name ?? selectedStudent?.full_name ?? 'Student';
  const className = profile?.class ? `Class ${profile.class} - ${profile.section}` : 'Class 10 - A';
  const admNo = profile?.admission_number ?? selectedStudent?.admission_number ?? 'ADM-2026-0001';
  const attPct = attendance?.summary?.attendance_percentage ?? 95.6;
  const feeBalance = fees?.overview?.outstanding_balance ?? 15000;

  return (
    <ScrollView
      style={styles.container}
      contentContainerStyle={styles.content}
      refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={[Colors.primary]} />}
    >
      {/* Top Banner / Student ID Header */}
      <View style={styles.headerCard}>
        <View style={styles.headerTop}>
          <View>
            <Text style={styles.greeting}>Vanakkam / Welcome,</Text>
            <Text style={styles.parentName}>{user?.name ?? 'Parent'}</Text>
          </View>
          <View style={styles.academicPill}>
            <Text style={styles.academicPillText}>AY 2026-27</Text>
          </View>
        </View>

        {/* Student Mini Dossier */}
        <View style={styles.studentDossier}>
          <View style={styles.avatar}>
            <Text style={styles.avatarText}>
              {studentName.charAt(0)}
            </Text>
          </View>
          <View style={styles.studentInfo}>
            <Text style={styles.studentName}>{studentName}</Text>
            <Text style={styles.studentMeta}>
              {className} • Roll: {profile?.roll_number ?? '1001'}
            </Text>
            <Text style={styles.admNo}>Adm No: {admNo}</Text>
          </View>
        </View>
      </View>

      {/* Futuristic AI Campus Co-Pilot Interactive Card */}
      <TouchableOpacity
        style={styles.aiBannerCard}
        onPress={() => navigation?.navigate('AiAssistant')}
      >
        <View style={styles.aiBannerLeft}>
          <View style={styles.aiIconBadge}>
            <Image
              source={require('../../../assets/student_mascot.jpg')}
              style={{ width: '100%', height: '100%', borderRadius: 12 }}
            />
          </View>
          <View style={{ flex: 1 }}>
            <View style={{ flexDirection: 'row', alignItems: 'center', gap: 6 }}>
              <Text style={styles.aiTitle}>Malar Student Mascot</Text>
              <View style={styles.aiLiveBadge}><Text style={styles.aiLiveText}>AI VOICE</Text></View>
            </View>
            <Text style={styles.aiSubtitle}>Tap to speak in Tamil / English with Malar</Text>
          </View>
        </View>
        <View style={styles.aiMicCircle}>
          <Mic size={18} color="#ffffff" />
        </View>
      </TouchableOpacity>

      {/* Overview Stat Cards Grid */}
      <View style={styles.statsGrid}>
        {/* Attendance Card */}
        <TouchableOpacity
          style={styles.statCard}
          onPress={() => navigation.navigate('Attendance')}
        >
          <View style={styles.statHeader}>
            <Text style={styles.statLabel}>Attendance</Text>
            <View style={[styles.statusDot, { backgroundColor: attPct >= 85 ? Colors.success : Colors.danger }]} />
          </View>
          <Text style={[styles.statValue, { color: attPct >= 85 ? Colors.success : Colors.danger }]}>
            {attPct}%
          </Text>
          <Text style={styles.statSub}>
            {attendance?.summary?.present_days ?? 43} of {attendance?.summary?.total_working_days ?? 45} Days Present
          </Text>
        </TouchableOpacity>

        {/* Fee Dues Card */}
        <TouchableOpacity
          style={styles.statCard}
          onPress={() => navigation.navigate('Fees')}
        >
          <View style={styles.statHeader}>
            <Text style={styles.statLabel}>Fee Balance</Text>
            <Text style={styles.curSign}>INR (₹)</Text>
          </View>
          <Text style={[styles.statValue, { color: feeBalance > 0 ? Colors.accent : Colors.success }]}>
            ₹{feeBalance.toLocaleString('en-IN')}
          </Text>
          <Text style={styles.statSub}>
            {feeBalance > 0 ? 'Upcoming Term Due' : 'All Dues Cleared'}
          </Text>
        </TouchableOpacity>
      </View>

      {/* Live School Bus GPS Tracking Card */}
      <View style={styles.busCard}>
        <View style={styles.busHeader}>
          <View style={{ flexDirection: 'row', alignItems: 'center', gap: 8, flex: 1 }}>
            <Text style={{ fontSize: 20 }}>🚌</Text>
            <View style={{ flex: 1 }}>
              <View style={{ flexDirection: 'row', alignItems: 'center', gap: 6 }}>
                <Text style={styles.busTitle}>Route 1: Town to Campus</Text>
                <View style={styles.busLiveDot} />
              </View>
              <Text style={styles.busSub}>Driver: Murugesan (TN-55-AB-1020)</Text>
            </View>
          </View>
          <View style={styles.liveEtaPill}>
            <Text style={styles.liveEtaText}>ETA: 4 MINS</Text>
          </View>
        </View>
        <View style={styles.busProgressRow}>
          <View style={styles.busStopIndicator}>
            <View style={[styles.stopDot, { backgroundColor: '#10b981' }]} />
            <Text style={styles.stopName}>Town Stand (Departed)</Text>
          </View>
          <View style={styles.busStopIndicator}>
            <View style={[styles.stopDot, { backgroundColor: '#f59e0b', borderWidth: 2, borderColor: '#ffffff' }]} />
            <Text style={[styles.stopName, { color: '#f59e0b', fontWeight: '700' }]}>Anna Nagar (Arriving)</Text>
          </View>
          <View style={styles.busStopIndicator}>
            <View style={[styles.stopDot, { backgroundColor: '#64748b' }]} />
            <Text style={styles.stopName}>Campus Gate</Text>
          </View>
        </View>
      </View>

      {/* Quick Navigation Action Pills */}
      <View style={styles.sectionTitleRow}>
        <Text style={styles.sectionTitle}>Academic Shortcuts</Text>
      </View>
      <View style={styles.shortcutsRow}>
        <TouchableOpacity
          style={styles.shortcutBtn}
          onPress={() => navigation.navigate('Timetable')}
        >
          <View style={[styles.shortcutIconBg, { backgroundColor: '#ecfdf5' }]}>
            <Text style={styles.shortcutEmoji}>📅</Text>
          </View>
          <Text style={styles.shortcutLabel}>Timetable</Text>
        </TouchableOpacity>

        <TouchableOpacity
          style={styles.shortcutBtn}
          onPress={() => navigation.navigate('Homework')}
        >
          <View style={[styles.shortcutIconBg, { backgroundColor: '#eff6ff' }]}>
            <Text style={styles.shortcutEmoji}>📝</Text>
          </View>
          <Text style={styles.shortcutLabel}>Homework</Text>
        </TouchableOpacity>

        <TouchableOpacity
          style={styles.shortcutBtn}
          onPress={() => navigation.navigate('Fees')}
        >
          <View style={[styles.shortcutIconBg, { backgroundColor: '#fffbeb' }]}>
            <Text style={styles.shortcutEmoji}>🧾</Text>
          </View>
          <Text style={styles.shortcutLabel}>Fee Receipts</Text>
        </TouchableOpacity>

        <TouchableOpacity
          style={styles.shortcutBtn}
          onPress={() => navigation.navigate('Profile')}
        >
          <View style={[styles.shortcutIconBg, { backgroundColor: '#faf5ff' }]}>
            <Text style={styles.shortcutEmoji}>🪪</Text>
          </View>
          <Text style={styles.shortcutLabel}>Digital ID</Text>
        </TouchableOpacity>
      </View>

      {/* Latest Circulars / Notices Ticker */}
      <View style={styles.sectionTitleRow}>
        <Text style={styles.sectionTitle}>Latest Campus Circulars</Text>
        <TouchableOpacity onPress={() => navigation.navigate('Notices')}>
          <Text style={styles.viewAllText}>View All &rarr;</Text>
        </TouchableOpacity>
      </View>

      {announcements.length === 0 ? (
        <View style={styles.emptyCard}>
          <Text style={styles.emptyText}>No circulars published today.</Text>
        </View>
      ) : (
        announcements.slice(0, 3).map((ann) => (
          <TouchableOpacity
            key={ann.id}
            style={styles.circularCard}
            onPress={() => navigation.navigate('Notices')}
          >
            <View style={styles.circularHeader}>
              {ann.is_pinned ? (
                <View style={styles.pinnedPill}>
                  <Text style={styles.pinnedText}>PINNED</Text>
                </View>
              ) : null}
              <Text style={styles.circularCategory}>{ann.category.toUpperCase()}</Text>
              <Text style={styles.circularDate}>{ann.publish_date}</Text>
            </View>
            <Text style={styles.circularTitle}>{ann.title}</Text>
            <Text style={styles.circularSnippet} numberOfLines={2}>
              {ann.content}
            </Text>
          </TouchableOpacity>
        ))
      )}
    </ScrollView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: Colors.background,
  },
  content: {
    padding: 16,
    paddingBottom: 32,
  },
  centerContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: Colors.background,
  },
  loadingText: {
    marginTop: 12,
    fontSize: 13,
    color: Colors.textSecondary,
    fontWeight: '500',
  },
  headerCard: {
    backgroundColor: Colors.primaryDark,
    borderRadius: 20,
    padding: 20,
    marginBottom: 16,
    shadowColor: Colors.primaryDark,
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.25,
    shadowRadius: 8,
    elevation: 4,
  },
  headerTop: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    marginBottom: 16,
  },
  greeting: {
    fontSize: 12,
    color: '#a7f3d0',
    fontWeight: '500',
  },
  parentName: {
    fontSize: 17,
    fontWeight: 'bold',
    color: '#ffffff',
    marginTop: 2,
  },
  academicPill: {
    backgroundColor: 'rgba(217, 119, 6, 0.3)',
    borderWidth: 1,
    borderColor: Colors.accent,
    paddingHorizontal: 10,
    paddingVertical: 3,
    borderRadius: 12,
  },
  academicPillText: {
    color: Colors.accentLight,
    fontSize: 10,
    fontWeight: 'bold',
  },
  studentDossier: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: 'rgba(255, 255, 255, 0.1)',
    borderRadius: 14,
    padding: 12,
  },
  avatar: {
    width: 48,
    height: 48,
    borderRadius: 24,
    backgroundColor: Colors.accent,
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: 12,
  },
  avatarText: {
    color: '#ffffff',
    fontSize: 20,
    fontWeight: 'bold',
  },
  studentInfo: {
    flex: 1,
  },
  studentName: {
    color: '#ffffff',
    fontSize: 15,
    fontWeight: 'bold',
  },
  studentMeta: {
    color: '#cbd5e1',
    fontSize: 12,
    marginTop: 2,
  },
  admNo: {
    color: '#94a3b8',
    fontSize: 10,
    fontFamily: 'monospace',
    marginTop: 2,
  },
  statsGrid: {
    flexDirection: 'row',
    gap: 12,
    marginBottom: 20,
  },
  statCard: {
    flex: 1,
    backgroundColor: '#ffffff',
    borderRadius: 16,
    padding: 16,
    borderWidth: 1,
    borderColor: Colors.border,
    shadowColor: '#000',
    shadowOpacity: 0.03,
    shadowRadius: 6,
    elevation: 2,
  },
  statHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  statLabel: {
    fontSize: 11,
    fontWeight: '700',
    color: Colors.textMuted,
    textTransform: 'uppercase',
  },
  curSign: {
    fontSize: 10,
    color: Colors.textMuted,
    fontWeight: 'bold',
  },
  statusDot: {
    width: 8,
    height: 8,
    borderRadius: 4,
  },
  statValue: {
    fontSize: 22,
    fontWeight: '900',
    marginVertical: 6,
  },
  statSub: {
    fontSize: 11,
    color: Colors.textSecondary,
    fontWeight: '500',
  },
  sectionTitleRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 12,
  },
  sectionTitle: {
    fontSize: 14,
    fontWeight: 'bold',
    color: Colors.textPrimary,
  },
  viewAllText: {
    fontSize: 12,
    fontWeight: 'bold',
    color: Colors.primary,
  },
  shortcutsRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 24,
  },
  shortcutBtn: {
    alignItems: 'center',
    flex: 1,
  },
  shortcutIconBg: {
    width: 52,
    height: 52,
    borderRadius: 14,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 6,
    borderWidth: 1,
    borderColor: Colors.border,
  },
  shortcutEmoji: {
    fontSize: 22,
  },
  shortcutLabel: {
    fontSize: 11,
    fontWeight: '600',
    color: Colors.textSecondary,
  },
  circularCard: {
    backgroundColor: '#ffffff',
    borderRadius: 14,
    padding: 14,
    borderWidth: 1,
    borderColor: Colors.border,
    marginBottom: 10,
  },
  circularHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
    marginBottom: 6,
  },
  pinnedPill: {
    backgroundColor: '#fef3c7',
    paddingHorizontal: 6,
    paddingVertical: 2,
    borderRadius: 4,
  },
  pinnedText: {
    fontSize: 9,
    fontWeight: 'bold',
    color: '#b45309',
  },
  circularCategory: {
    fontSize: 10,
    fontWeight: 'bold',
    color: Colors.primary,
  },
  circularDate: {
    fontSize: 10,
    color: Colors.textMuted,
    marginLeft: 'auto',
  },
  circularTitle: {
    fontSize: 13,
    fontWeight: 'bold',
    color: Colors.textPrimary,
    marginBottom: 4,
  },
  circularSnippet: {
    fontSize: 11,
    color: Colors.textSecondary,
    lineHeight: 16,
  },
  emptyCard: {
    backgroundColor: '#ffffff',
    borderRadius: 12,
    padding: 24,
    alignItems: 'center',
    borderWidth: 1,
    borderColor: Colors.border,
  },
  emptyText: {
    fontSize: 12,
    color: Colors.textMuted,
  },
  aiBannerCard: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    backgroundColor: '#064E3B',
    borderRadius: 16,
    padding: 14,
    marginBottom: 16,
    borderWidth: 1.5,
    borderColor: '#059669',
    shadowColor: '#047857',
    shadowOpacity: 0.3,
    shadowRadius: 6,
    elevation: 4,
  },
  aiBannerLeft: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 12,
    flex: 1,
  },
  aiIconBadge: {
    width: 40,
    height: 40,
    borderRadius: 14,
    backgroundColor: '#022C22',
    borderWidth: 1.5,
    borderColor: '#F59E0B',
    alignItems: 'center',
    justifyContent: 'center',
  },
  aiTitle: {
    fontSize: 14,
    fontWeight: '800',
    color: '#ffffff',
  },
  aiLiveBadge: {
    backgroundColor: 'rgba(245, 158, 11, 0.25)',
    borderColor: '#F59E0B',
    borderWidth: 1,
    paddingHorizontal: 6,
    paddingVertical: 1,
    borderRadius: 6,
  },
  aiLiveText: {
    fontSize: 9,
    fontWeight: '800',
    color: '#FBBF24',
  },
  aiSubtitle: {
    fontSize: 11,
    fontWeight: '500',
    color: '#A7F3D0',
    marginTop: 2,
  },
  aiMicCircle: {
    width: 36,
    height: 36,
    borderRadius: 18,
    backgroundColor: '#047857',
    borderWidth: 1,
    borderColor: '#34D399',
    alignItems: 'center',
    justifyContent: 'center',
    marginLeft: 8,
  },
  busCard: {
    backgroundColor: '#0f172a',
    borderRadius: 16,
    padding: 14,
    marginBottom: 16,
    borderWidth: 1,
    borderColor: '#1e293b',
  },
  busHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginBottom: 12,
  },
  busTitle: {
    fontSize: 13,
    fontWeight: '700',
    color: '#ffffff',
  },
  busLiveDot: {
    width: 8,
    height: 8,
    borderRadius: 4,
    backgroundColor: '#10b981',
  },
  busSub: {
    fontSize: 11,
    color: '#94a3b8',
    marginTop: 1,
  },
  liveEtaPill: {
    backgroundColor: 'rgba(245, 158, 11, 0.2)',
    borderColor: '#f59e0b',
    borderWidth: 1,
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: 8,
  },
  liveEtaText: {
    fontSize: 10,
    fontWeight: '800',
    color: '#fbbf24',
  },
  busProgressRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    paddingTop: 8,
    borderTopWidth: 1,
    borderTopColor: '#1e293b',
  },
  busStopIndicator: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
  },
  stopDot: {
    width: 8,
    height: 8,
    borderRadius: 4,
  },
  stopName: {
    fontSize: 10,
    color: '#94a3b8',
  },
});
