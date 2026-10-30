import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  ActivityIndicator,
  RefreshControl,
} from 'react-native';
import { Colors } from '../../theme/colors';
import { useAuth } from '../../context/AuthContext';
import { apiService } from '../../services/api';

const DAYS = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

export const TimetableScreen: React.FC = () => {
  const { selectedStudent } = useAuth();
  const [selectedDay, setSelectedDay] = useState('Monday');
  const [scheduleData, setScheduleData] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  const fetchTimetable = async () => {
    try {
      const data = await apiService.getStudentTimetable(selectedStudent?.id);
      setScheduleData(data);
    } catch (e) {
      console.log('Error loading timetable', e);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useEffect(() => {
    fetchTimetable();
  }, [selectedStudent]);

  const onRefresh = () => {
    setRefreshing(true);
    fetchTimetable();
  };

  if (loading && !scheduleData) {
    return (
      <View style={styles.centerContainer}>
        <ActivityIndicator size="large" color={Colors.primary} />
        <Text style={styles.loadingText}>Loading Weekly Schedule...</Text>
      </View>
    );
  }

  const currentPeriods = scheduleData?.schedule?.[selectedDay] ?? [];

  return (
    <View style={styles.container}>
      {/* Day Selector Ribbon */}
      <View style={styles.daySelectorWrapper}>
        <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={styles.dayScroll}>
          {DAYS.map((day) => {
            const isSelected = selectedDay === day;
            return (
              <TouchableOpacity
                key={day}
                style={[styles.dayPill, isSelected && styles.dayPillActive]}
                onPress={() => setSelectedDay(day)}
              >
                <Text style={[styles.dayText, isSelected && styles.dayTextActive]}>
                  {day.slice(0, 3).toUpperCase()}
                </Text>
                <Text style={[styles.dayFullText, isSelected && styles.dayFullTextActive]}>
                  {day}
                </Text>
              </TouchableOpacity>
            );
          })}
        </ScrollView>
      </View>

      {/* Class Meta Header */}
      <View style={styles.metaRow}>
        <Text style={styles.classMeta}>
          Class: <Text style={styles.metaHighlight}>{scheduleData?.class ?? 'Class 10'} - {scheduleData?.section ?? 'A'}</Text>
        </Text>
        <Text style={styles.periodCount}>{currentPeriods.length} Periods Scheduled</Text>
      </View>

      {/* Period Timeline List */}
      <ScrollView
        contentContainerStyle={styles.timelineContent}
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={[Colors.primary]} />}
      >
        {currentPeriods.length === 0 ? (
          <View style={styles.emptyCard}>
            <Text style={styles.emptyEmoji}>🏖️</Text>
            <Text style={styles.emptyTitle}>No Classes Scheduled</Text>
            <Text style={styles.emptySubtitle}>Enjoy your holiday or weekend rest!</Text>
          </View>
        ) : (
          currentPeriods.map((period: any, idx: number) => (
            <View key={idx} style={styles.periodCard}>
              <View style={styles.periodBadge}>
                <Text style={styles.periodNumText}>P{period.period}</Text>
                <Text style={styles.periodTimeText}>{period.start_time}</Text>
              </View>

              <View style={styles.periodDetails}>
                <View style={styles.subjectRow}>
                  <Text style={styles.subjectName}>{period.subject}</Text>
                  <View style={styles.roomPill}>
                    <Text style={styles.roomText}>{period.room || 'Room 101'}</Text>
                  </View>
                </View>
                <Text style={styles.teacherName}>Faculty: {period.teacher}</Text>
                <Text style={styles.timeRange}>
                  {period.start_time} - {period.end_time} (45 mins)
                </Text>
              </View>
            </View>
          ))
        )}
      </ScrollView>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: Colors.background,
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
  daySelectorWrapper: {
    backgroundColor: '#ffffff',
    borderBottomWidth: 1,
    borderBottomColor: Colors.border,
    paddingVertical: 12,
  },
  dayScroll: {
    paddingHorizontal: 16,
    gap: 8,
  },
  dayPill: {
    paddingHorizontal: 16,
    paddingVertical: 8,
    borderRadius: 12,
    backgroundColor: '#f1f5f9',
    alignItems: 'center',
    minWidth: 72,
  },
  dayPillActive: {
    backgroundColor: Colors.primary,
  },
  dayText: {
    fontSize: 12,
    fontWeight: '900',
    color: Colors.textSecondary,
  },
  dayTextActive: {
    color: '#ffffff',
  },
  dayFullText: {
    fontSize: 9,
    color: Colors.textMuted,
    marginTop: 2,
  },
  dayFullTextActive: {
    color: '#a7f3d0',
  },
  metaRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    paddingHorizontal: 16,
    paddingVertical: 12,
  },
  classMeta: {
    fontSize: 12,
    color: Colors.textSecondary,
  },
  metaHighlight: {
    fontWeight: 'bold',
    color: Colors.primaryDark,
  },
  periodCount: {
    fontSize: 12,
    color: Colors.textMuted,
    fontWeight: '500',
  },
  timelineContent: {
    padding: 16,
    paddingTop: 0,
    gap: 10,
    paddingBottom: 30,
  },
  periodCard: {
    backgroundColor: '#ffffff',
    borderRadius: 14,
    borderWidth: 1,
    borderColor: Colors.border,
    flexDirection: 'row',
    overflow: 'hidden',
  },
  periodBadge: {
    backgroundColor: Colors.primaryTint,
    width: 65,
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 14,
    borderRightWidth: 1,
    borderRightColor: '#a7f3d0',
  },
  periodNumText: {
    fontSize: 16,
    fontWeight: '900',
    color: Colors.primaryDark,
  },
  periodTimeText: {
    fontSize: 10,
    fontWeight: '600',
    color: Colors.primary,
    marginTop: 4,
  },
  periodDetails: {
    flex: 1,
    padding: 12,
  },
  subjectRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 4,
  },
  subjectName: {
    fontSize: 14,
    fontWeight: 'bold',
    color: Colors.textPrimary,
  },
  roomPill: {
    backgroundColor: '#f1f5f9',
    paddingHorizontal: 6,
    paddingVertical: 2,
    borderRadius: 6,
  },
  roomText: {
    fontSize: 10,
    color: Colors.textSecondary,
    fontWeight: '600',
  },
  teacherName: {
    fontSize: 12,
    color: Colors.textSecondary,
    marginBottom: 4,
  },
  timeRange: {
    fontSize: 10,
    color: Colors.textMuted,
    fontFamily: 'monospace',
  },
  emptyCard: {
    backgroundColor: '#ffffff',
    borderRadius: 16,
    padding: 36,
    alignItems: 'center',
    borderWidth: 1,
    borderColor: Colors.border,
    marginTop: 40,
  },
  emptyEmoji: {
    fontSize: 40,
    marginBottom: 12,
  },
  emptyTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    color: Colors.textPrimary,
  },
  emptySubtitle: {
    fontSize: 12,
    color: Colors.textMuted,
    marginTop: 4,
  },
});
