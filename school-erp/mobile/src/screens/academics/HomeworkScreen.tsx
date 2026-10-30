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

export const HomeworkScreen: React.FC = () => {
  const { selectedStudent } = useAuth();
  const [homeworkList, setHomeworkList] = useState<any[]>([]);
  const [selectedSubject, setSelectedSubject] = useState('All');
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  const fetchHomework = async () => {
    try {
      const data = await apiService.getStudentHomework(selectedStudent?.id);
      setHomeworkList(data || []);
    } catch (e) {
      console.log('Error fetching homework', e);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useEffect(() => {
    fetchHomework();
  }, [selectedStudent]);

  const onRefresh = () => {
    setRefreshing(true);
    fetchHomework();
  };

  const subjects = ['All', ...Array.from(new Set(homeworkList.map((h) => h.subject)))];
  const filtered = selectedSubject === 'All'
    ? homeworkList
    : homeworkList.filter((h) => h.subject === selectedSubject);

  if (loading && homeworkList.length === 0) {
    return (
      <View style={styles.centerContainer}>
        <ActivityIndicator size="large" color={Colors.primary} />
        <Text style={styles.loadingText}>Fetching Homework Assignments...</Text>
      </View>
    );
  }

  return (
    <View style={styles.container}>
      {/* Subject Filter Ribbon */}
      <View style={styles.filterWrapper}>
        <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={styles.filterScroll}>
          {subjects.map((sub) => {
            const isSelected = selectedSubject === sub;
            return (
              <TouchableOpacity
                key={sub}
                style={[styles.filterPill, isSelected && styles.filterPillActive]}
                onPress={() => setSelectedSubject(sub)}
              >
                <Text style={[styles.filterText, isSelected && styles.filterTextActive]}>
                  {sub}
                </Text>
              </TouchableOpacity>
            );
          })}
        </ScrollView>
      </View>

      {/* Homework Cards List */}
      <ScrollView
        contentContainerStyle={styles.listContent}
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={[Colors.primary]} />}
      >
        {filtered.length === 0 ? (
          <View style={styles.emptyCard}>
            <Text style={styles.emptyEmoji}>🎉</Text>
            <Text style={styles.emptyTitle}>All Homework Complete!</Text>
            <Text style={styles.emptySubtitle}>No pending assignments due for this subject.</Text>
          </View>
        ) : (
          filtered.map((hw) => (
            <View key={hw.id} style={styles.hwCard}>
              <View style={styles.cardHeader}>
                <View style={styles.subjectPill}>
                  <Text style={styles.subjectPillText}>{hw.subject}</Text>
                </View>
                <View style={styles.duePill}>
                  <Text style={styles.duePillText}>Due: {hw.due_date}</Text>
                </View>
              </View>

              <Text style={styles.hwTitle}>{hw.title}</Text>
              <Text style={styles.hwDescription}>{hw.description}</Text>

              <View style={styles.cardFooter}>
                <Text style={styles.assignedBy}>Assigned by: {hw.teacher}</Text>
                <Text style={styles.assignedDate}>Given: {hw.assigned_date}</Text>
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
  filterWrapper: {
    backgroundColor: '#ffffff',
    borderBottomWidth: 1,
    borderBottomColor: Colors.border,
    paddingVertical: 10,
  },
  filterScroll: {
    paddingHorizontal: 16,
    gap: 8,
  },
  filterPill: {
    paddingHorizontal: 14,
    paddingVertical: 6,
    borderRadius: 20,
    backgroundColor: '#f1f5f9',
  },
  filterPillActive: {
    backgroundColor: Colors.primary,
  },
  filterText: {
    fontSize: 12,
    fontWeight: '600',
    color: Colors.textSecondary,
  },
  filterTextActive: {
    color: '#ffffff',
    fontWeight: 'bold',
  },
  listContent: {
    padding: 16,
    gap: 12,
    paddingBottom: 32,
  },
  hwCard: {
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
  cardHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 8,
  },
  subjectPill: {
    backgroundColor: Colors.primaryTint,
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: 6,
    borderWidth: 1,
    borderColor: '#a7f3d0',
  },
  subjectPillText: {
    fontSize: 10,
    fontWeight: 'bold',
    color: Colors.primaryDark,
  },
  duePill: {
    backgroundColor: '#fffbeb',
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: 6,
    borderWidth: 1,
    borderColor: '#fde68a',
  },
  duePillText: {
    fontSize: 10,
    fontWeight: 'bold',
    color: Colors.accent,
  },
  hwTitle: {
    fontSize: 14,
    fontWeight: 'bold',
    color: Colors.textPrimary,
    marginBottom: 6,
  },
  hwDescription: {
    fontSize: 12,
    color: Colors.textSecondary,
    lineHeight: 18,
    marginBottom: 12,
  },
  cardFooter: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    borderTopWidth: 1,
    borderTopColor: '#f1f5f9',
    paddingTop: 8,
  },
  assignedBy: {
    fontSize: 10,
    color: Colors.textMuted,
    fontWeight: '500',
  },
  assignedDate: {
    fontSize: 10,
    color: Colors.textMuted,
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
