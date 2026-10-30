import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  RefreshControl,
  ActivityIndicator,
  TextInput,
} from 'react-native';
import {
  Bell,
  Pin,
  Calendar,
  User,
  Search,
  Filter,
  Megaphone,
  BookOpen,
  Award,
  AlertTriangle,
  Info,
} from 'lucide-react-native';
import { Colors } from '../../theme/colors';
import { apiService } from '../../services/api';

const CATEGORIES = [
  { id: 'all', label: 'All Notices' },
  { id: 'urgent', label: 'Urgent' },
  { id: 'academic', label: 'Academic' },
  { id: 'exam', label: 'Exams' },
  { id: 'event', label: 'Events' },
  { id: 'general', label: 'General' },
];

export const NoticesScreen: React.FC = () => {
  const [notices, setNotices] = useState<any[]>([]);
  const [loading, setLoading] = useState<boolean>(true);
  const [refreshing, setRefreshing] = useState<boolean>(false);
  const [selectedCategory, setSelectedCategory] = useState<string>('all');
  const [searchQuery, setSearchQuery] = useState<string>('');

  const fetchNotices = async () => {
    try {
      const data = await apiService.getAnnouncements(
        selectedCategory === 'all' ? undefined : selectedCategory
      );
      setNotices(data || []);
    } catch (e) {
      console.log('Error fetching announcements', e);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useEffect(() => {
    fetchNotices();
  }, [selectedCategory]);

  const onRefresh = () => {
    setRefreshing(true);
    fetchNotices();
  };

  const getCategoryColor = (category?: string) => {
    const cat = category?.toLowerCase();
    switch (cat) {
      case 'urgent':
        return { bg: '#fee2e2', text: '#b91c1c', border: '#fca5a5' };
      case 'academic':
      case 'exam':
        return { bg: '#dbeafe', text: '#1d4ed8', border: '#93c5fd' };
      case 'event':
      case 'sports':
        return { bg: '#fef3c7', text: '#b45309', border: '#fde68a' };
      default:
        return { bg: '#ecfdf5', text: '#047857', border: '#a7f3d0' };
    }
  };

  const filteredNotices = notices.filter((n) => {
    if (!searchQuery.trim()) return true;
    const q = searchQuery.toLowerCase();
    return (
      n.title?.toLowerCase().includes(q) ||
      n.content?.toLowerCase().includes(q) ||
      n.category?.toLowerCase().includes(q)
    );
  });

  const pinnedNotices = filteredNotices.filter((n) => n.is_pinned);
  const regularNotices = filteredNotices.filter((n) => !n.is_pinned);

  if (loading && notices.length === 0) {
    return (
      <View style={styles.centerContainer}>
        <ActivityIndicator size="large" color={Colors.primary} />
        <Text style={styles.loadingText}>Fetching Official Circulars...</Text>
      </View>
    );
  }

  return (
    <View style={styles.screen}>
      {/* Search Header */}
      <View style={styles.searchContainer}>
        <View style={styles.searchBar}>
          <Search size={18} color={Colors.textMuted} />
          <TextInput
            style={styles.searchInput}
            placeholder="Search circulars, exams, holidays..."
            placeholderTextColor={Colors.textMuted}
            value={searchQuery}
            onChangeText={setSearchQuery}
          />
        </View>
      </View>

      {/* Categories Filter Pills */}
      <View style={styles.categoryScrollWrapper}>
        <ScrollView
          horizontal
          showsHorizontalScrollIndicator={false}
          contentContainerStyle={styles.categoryScroll}
        >
          {CATEGORIES.map((cat) => {
            const isActive = selectedCategory === cat.id;
            return (
              <TouchableOpacity
                key={cat.id}
                style={[styles.categoryPill, isActive && styles.categoryPillActive]}
                onPress={() => setSelectedCategory(cat.id)}
                activeOpacity={0.8}
              >
                <Text style={[styles.categoryPillText, isActive && styles.categoryPillTextActive]}>
                  {cat.label}
                </Text>
              </TouchableOpacity>
            );
          })}
        </ScrollView>
      </View>

      {/* Main Notice List */}
      <ScrollView
        contentContainerStyle={styles.scrollContent}
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={[Colors.primary]} />}
      >
        {filteredNotices.length === 0 ? (
          <View style={styles.emptyCard}>
            <Megaphone size={40} color={Colors.textMuted} />
            <Text style={styles.emptyTitle}>No Circulars Found</Text>
            <Text style={styles.emptySubtitle}>
              There are no announcements matching your current category filter.
            </Text>
          </View>
        ) : (
          <>
            {/* Pinned Section */}
            {pinnedNotices.length > 0 && (
              <View style={styles.sectionBlock}>
                <View style={styles.sectionHeader}>
                  <Pin size={16} color={Colors.accent} />
                  <Text style={styles.sectionTitle}>Priority & Pinned Circulars</Text>
                </View>

                {pinnedNotices.map((n, idx) => {
                  const colors = getCategoryColor(n.category);
                  return (
                    <View key={n.id || `pinned-${idx}`} style={styles.pinnedCard}>
                      <View style={styles.pinnedBadge}>
                        <Pin size={12} color="#ffffff" />
                        <Text style={styles.pinnedBadgeText}>PINNED NOTICE</Text>
                      </View>

                      <View style={styles.cardHeader}>
                        <View
                          style={[
                            styles.categoryBadge,
                            { backgroundColor: colors.bg, borderColor: colors.border },
                          ]}
                        >
                          <Text style={[styles.categoryText, { color: colors.text }]}>
                            {n.category?.toUpperCase() || 'GENERAL'}
                          </Text>
                        </View>
                        <View style={styles.dateBadge}>
                          <Calendar size={12} color={Colors.textMuted} />
                          <Text style={styles.dateText}>{n.publish_date || 'Current Session'}</Text>
                        </View>
                      </View>

                      <Text style={styles.noticeTitle}>{n.title}</Text>
                      <Text style={styles.noticeContent}>{n.content}</Text>

                      <View style={styles.cardFooter}>
                        <View style={styles.authorBox}>
                          <User size={13} color={Colors.textMuted} />
                          <Text style={styles.authorText}>{n.author || 'Office of the Principal'}</Text>
                        </View>
                      </View>
                    </View>
                  );
                })}
              </View>
            )}

            {/* Regular Circulars */}
            {regularNotices.length > 0 && (
              <View style={styles.sectionBlock}>
                {pinnedNotices.length > 0 && (
                  <View style={styles.sectionHeader}>
                    <Bell size={16} color={Colors.primary} />
                    <Text style={styles.sectionTitle}>Institutional Announcements</Text>
                  </View>
                )}

                {regularNotices.map((n, idx) => {
                  const colors = getCategoryColor(n.category);
                  return (
                    <View key={n.id || `notice-${idx}`} style={styles.noticeCard}>
                      <View style={styles.cardHeader}>
                        <View
                          style={[
                            styles.categoryBadge,
                            { backgroundColor: colors.bg, borderColor: colors.border },
                          ]}
                        >
                          <Text style={[styles.categoryText, { color: colors.text }]}>
                            {n.category?.toUpperCase() || 'GENERAL'}
                          </Text>
                        </View>
                        <View style={styles.dateBadge}>
                          <Calendar size={12} color={Colors.textMuted} />
                          <Text style={styles.dateText}>{n.publish_date || 'Today'}</Text>
                        </View>
                      </View>

                      <Text style={styles.noticeTitle}>{n.title}</Text>
                      <Text style={styles.noticeContent}>{n.content}</Text>

                      <View style={styles.cardFooter}>
                        <View style={styles.authorBox}>
                          <User size={13} color={Colors.textMuted} />
                          <Text style={styles.authorText}>{n.author || 'Office of the Principal'}</Text>
                        </View>
                      </View>
                    </View>
                  );
                })}
              </View>
            )}
          </>
        )}
      </ScrollView>
    </View>
  );
};

const styles = StyleSheet.create({
  screen: {
    flex: 1,
    backgroundColor: Colors.background,
  },
  searchContainer: {
    paddingHorizontal: 16,
    paddingTop: 12,
    paddingBottom: 8,
    backgroundColor: Colors.cardBg,
    borderBottomWidth: 1,
    borderBottomColor: Colors.border,
  },
  searchBar: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#f1f5f9',
    borderRadius: 10,
    paddingHorizontal: 12,
    height: 42,
    gap: 8,
  },
  searchInput: {
    flex: 1,
    fontSize: 14,
    color: Colors.textPrimary,
  },
  categoryScrollWrapper: {
    backgroundColor: Colors.cardBg,
    paddingVertical: 10,
    borderBottomWidth: 1,
    borderBottomColor: Colors.border,
  },
  categoryScroll: {
    paddingHorizontal: 16,
    gap: 8,
  },
  categoryPill: {
    paddingHorizontal: 14,
    paddingVertical: 7,
    borderRadius: 20,
    backgroundColor: '#f1f5f9',
  },
  categoryPillActive: {
    backgroundColor: Colors.primary,
  },
  categoryPillText: {
    fontSize: 12,
    fontWeight: '600',
    color: Colors.textSecondary,
  },
  categoryPillTextActive: {
    color: '#ffffff',
  },
  scrollContent: {
    padding: 16,
    paddingBottom: 40,
    gap: 16,
  },
  centerContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: Colors.background,
  },
  loadingText: {
    marginTop: 12,
    fontSize: 14,
    color: Colors.textSecondary,
    fontWeight: '500',
  },
  sectionBlock: {
    gap: 12,
  },
  sectionHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
    marginBottom: 4,
  },
  sectionTitle: {
    fontSize: 14,
    fontWeight: '700',
    color: Colors.textPrimary,
    textTransform: 'uppercase',
    letterSpacing: 0.5,
  },
  pinnedCard: {
    backgroundColor: Colors.cardBg,
    borderRadius: 14,
    padding: 16,
    borderWidth: 1.5,
    borderColor: '#fcd34d',
    shadowColor: Colors.accent,
    shadowOpacity: 0.08,
    shadowOffset: { width: 0, height: 3 },
    shadowRadius: 8,
    elevation: 3,
    position: 'relative',
  },
  pinnedBadge: {
    position: 'absolute',
    top: -10,
    right: 16,
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
    backgroundColor: Colors.accent,
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: 6,
  },
  pinnedBadgeText: {
    fontSize: 9,
    fontWeight: '800',
    color: '#ffffff',
    letterSpacing: 0.5,
  },
  noticeCard: {
    backgroundColor: Colors.cardBg,
    borderRadius: 14,
    padding: 16,
    borderWidth: 1,
    borderColor: Colors.border,
    shadowColor: '#000',
    shadowOpacity: 0.03,
    shadowOffset: { width: 0, height: 2 },
    shadowRadius: 4,
    elevation: 1,
  },
  cardHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 8,
  },
  categoryBadge: {
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: 6,
    borderWidth: 1,
  },
  categoryText: {
    fontSize: 10,
    fontWeight: '700',
    letterSpacing: 0.4,
  },
  dateBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
  },
  dateText: {
    fontSize: 12,
    color: Colors.textMuted,
  },
  noticeTitle: {
    fontSize: 16,
    fontWeight: '700',
    color: Colors.textPrimary,
    lineHeight: 22,
    marginBottom: 6,
  },
  noticeContent: {
    fontSize: 13,
    color: Colors.textSecondary,
    lineHeight: 20,
    marginBottom: 12,
  },
  cardFooter: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingTop: 8,
    borderTopWidth: 1,
    borderTopColor: '#f1f5f9',
  },
  authorBox: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
  },
  authorText: {
    fontSize: 12,
    color: Colors.textMuted,
    fontStyle: 'italic',
  },
  emptyCard: {
    backgroundColor: Colors.cardBg,
    borderRadius: 14,
    padding: 36,
    alignItems: 'center',
    justifyContent: 'center',
    borderWidth: 1,
    borderColor: Colors.border,
  },
  emptyTitle: {
    fontSize: 16,
    fontWeight: '700',
    color: Colors.textPrimary,
    marginTop: 12,
  },
  emptySubtitle: {
    fontSize: 13,
    color: Colors.textMuted,
    textAlign: 'center',
    marginTop: 6,
  },
});
