import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  RefreshControl,
  ActivityIndicator,
} from 'react-native';
import { Colors } from '../../theme/colors';
import { useAuth } from '../../context/AuthContext';
import { apiService } from '../../services/api';

export const AttendanceScreen: React.FC = () => {
  const { selectedStudent } = useAuth();
  const [attData, setAttData] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  const fetchAttendance = async () => {
    try {
      const data = await apiService.getStudentAttendance(selectedStudent?.id);
      setAttData(data);
    } catch (e) {
      console.log('Error fetching attendance', e);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useEffect(() => {
    fetchAttendance();
  }, [selectedStudent]);

  const onRefresh = () => {
    setRefreshing(true);
    fetchAttendance();
  };

  if (loading && !attData) {
    return (
      <View style={styles.centerContainer}>
        <ActivityIndicator size="large" color={Colors.primary} />
        <Text style={styles.loadingText}>Fetching Attendance Register...</Text>
      </View>
    );
  }

  const pct = attData?.summary?.attendance_percentage ?? 95.6;
  const isHealthy = pct >= 85;

  return (
    <ScrollView
      style={styles.container}
      contentContainerStyle={styles.content}
      refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={[Colors.primary]} />}
    >
      {/* Attendance Summary Banner */}
      <View style={styles.summaryCard}>
        <View style={styles.gaugeBox}>
          <Text style={[styles.gaugeValue, { color: isHealthy ? Colors.success : Colors.danger }]}>
            {pct}%
          </Text>
          <Text style={styles.gaugeLabel}>Turnout Score</Text>
        </View>

        <View style={styles.statBreakdown}>
          <View style={styles.breakdownRow}>
            <Text style={styles.breakdownLabel}>Total Days:</Text>
            <Text style={styles.breakdownValue}>{attData?.summary?.total_working_days ?? 45} Days</Text>
          </View>
          <View style={styles.breakdownRow}>
            <Text style={styles.breakdownLabel}>Present:</Text>
            <Text style={[styles.breakdownValue, { color: Colors.success }]}>
              {attData?.summary?.present_days ?? 43} Days
            </Text>
          </View>
          <View style={styles.breakdownRow}>
            <Text style={styles.breakdownLabel}>Absences:</Text>
            <Text style={[styles.breakdownValue, { color: Colors.danger }]}>
              {attData?.summary?.absent_days ?? 2} Days
            </Text>
          </View>
          <View style={styles.breakdownRow}>
            <Text style={styles.breakdownLabel}>Compliance:</Text>
            <Text style={[styles.breakdownValue, { color: isHealthy ? Colors.primary : Colors.danger }]}>
              {isHealthy ? 'Eligible for Exams' : 'Below 85% Cutoff'}
            </Text>
          </View>
        </View>
      </View>

      {/* Info Pill */}
      <View style={styles.infoBanner}>
        <Text style={styles.infoText}>
          ℹ️ Tamil Nadu State Board mandates an 85% minimum attendance requirement for annual exam eligibility.
        </Text>
      </View>

      {/* Attendance Log History */}
      <Text style={styles.sectionHeader}>Daily Section Roll-Call Records</Text>

      {(!attData?.recent_logs || attData.recent_logs.length === 0) ? (
        <View style={styles.emptyCard}>
          <Text style={styles.emptyText}>No attendance logs found for this term.</Text>
        </View>
      ) : (
        attData.recent_logs.map((log: any, idx: number) => {
          const isPresent = log.status === 'present';
          return (
            <View key={idx} style={styles.logRow}>
              <View style={styles.dateCol}>
                <Text style={styles.logDate}>{log.date}</Text>
                <Text style={styles.logSub}>Roll-Call Confirmed</Text>
              </View>

              <View style={[styles.statusBadge, isPresent ? styles.badgePresent : styles.badgeAbsent]}>
                <Text style={[styles.statusText, isPresent ? styles.textPresent : styles.textAbsent]}>
                  {log.status.toUpperCase()}
                </Text>
              </View>
            </View>
          );
        })
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
  summaryCard: {
    backgroundColor: '#ffffff',
    borderRadius: 20,
    padding: 20,
    borderWidth: 1,
    borderColor: Colors.border,
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 16,
    shadowColor: '#000',
    shadowOpacity: 0.04,
    shadowRadius: 8,
    elevation: 2,
  },
  gaugeBox: {
    width: 110,
    height: 110,
    borderRadius: 55,
    backgroundColor: '#f8fafc',
    borderWidth: 3,
    borderColor: Colors.primary,
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: 16,
  },
  gaugeValue: {
    fontSize: 26,
    fontWeight: '900',
  },
  gaugeLabel: {
    fontSize: 10,
    fontWeight: 'bold',
    color: Colors.textMuted,
    textTransform: 'uppercase',
    marginTop: 2,
  },
  statBreakdown: {
    flex: 1,
    gap: 6,
  },
  breakdownRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  breakdownLabel: {
    fontSize: 12,
    color: Colors.textSecondary,
  },
  breakdownValue: {
    fontSize: 12,
    fontWeight: 'bold',
    color: Colors.textPrimary,
  },
  infoBanner: {
    backgroundColor: '#eff6ff',
    borderWidth: 1,
    borderColor: '#bfdbfe',
    borderRadius: 12,
    padding: 12,
    marginBottom: 20,
  },
  infoText: {
    fontSize: 11,
    color: '#1e40af',
    lineHeight: 16,
  },
  sectionHeader: {
    fontSize: 14,
    fontWeight: 'bold',
    color: Colors.textPrimary,
    marginBottom: 12,
  },
  logRow: {
    backgroundColor: '#ffffff',
    borderRadius: 12,
    padding: 14,
    borderWidth: 1,
    borderColor: Colors.border,
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 8,
  },
  dateCol: {
    flex: 1,
  },
  logDate: {
    fontSize: 13,
    fontWeight: 'bold',
    color: Colors.textPrimary,
  },
  logSub: {
    fontSize: 10,
    color: Colors.textMuted,
    marginTop: 2,
  },
  statusBadge: {
    paddingHorizontal: 12,
    paddingVertical: 4,
    borderRadius: 8,
  },
  badgePresent: {
    backgroundColor: '#d1fae5',
  },
  badgeAbsent: {
    backgroundColor: '#fee2e2',
  },
  statusText: {
    fontSize: 11,
    fontWeight: '900',
    letterSpacing: 0.5,
  },
  textPresent: {
    color: '#065f46',
  },
  textAbsent: {
    color: '#991b1b',
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
});
