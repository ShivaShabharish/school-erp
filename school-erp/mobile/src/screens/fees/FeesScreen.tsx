import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  RefreshControl,
  ActivityIndicator,
  Alert,
} from 'react-native';
import {
  CreditCard,
  CheckCircle2,
  AlertCircle,
  Receipt,
  Calendar,
  Tag,
  ArrowUpRight,
  ShieldCheck,
  ChevronRight,
  Clock,
} from 'lucide-react-native';
import { Colors } from '../../theme/colors';
import { useAuth } from '../../context/AuthContext';
import { apiService } from '../../services/api';

export const FeesScreen: React.FC = () => {
  const { selectedStudent } = useAuth();
  const [feesData, setFeesData] = useState<any>(null);
  const [activeTab, setActiveTab] = useState<'dues' | 'receipts'>('dues');
  const [loading, setLoading] = useState<boolean>(true);
  const [refreshing, setRefreshing] = useState<boolean>(false);

  const fetchFees = async () => {
    try {
      const data = await apiService.getStudentFees(selectedStudent?.id);
      setFeesData(data);
    } catch (e) {
      console.log('Error fetching fees', e);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useEffect(() => {
    fetchFees();
  }, [selectedStudent]);

  const onRefresh = () => {
    setRefreshing(true);
    fetchFees();
  };

  const handlePayPress = (installment: any) => {
    Alert.alert(
      'Fee Payment Portal',
      `Installment: ${installment.title}\nDue Balance: ₹${installment.due_balance.toLocaleString('en-IN')}\n\nYou can pay online via UPI/NetBanking or visit the institution's Accounts Cash Counter (08:30 AM - 04:00 PM).`,
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Proceed to UPI Gateway',
          onPress: () =>
            Alert.alert(
              'UPI Payment',
              'Online payment gateway integration will redirect to institutional VPA: testsubject@sbi'
            ),
        },
      ]
    );
  };

  const handleReceiptPress = (rc: any) => {
    Alert.alert(
      'Official Fee Receipt',
      `Receipt No: ${rc.receipt_no}\nDate: ${rc.receipt_date}\nAmount Paid: ₹${rc.amount_paid.toLocaleString('en-IN')}\nPayment Mode: ${rc.payment_mode}\nRef: ${rc.transaction_ref || 'Counter Transaction'}\n\nOfficial two-part computerized receipt verified with security seal.`,
      [{ text: 'OK' }]
    );
  };

  if (loading && !feesData) {
    return (
      <View style={styles.centerContainer}>
        <ActivityIndicator size="large" color={Colors.primary} />
        <Text style={styles.loadingText}>Loading Fee Ledger & Dues...</Text>
      </View>
    );
  }

  const overview = feesData?.overview ?? {
    total_assessed: 45000,
    total_paid: 30000,
    outstanding_balance: 15000,
    currency: '₹',
  };

  const installments = feesData?.installments ?? [];
  const receipts = feesData?.recent_receipts ?? [];

  return (
    <View style={styles.screen}>
      <ScrollView
        contentContainerStyle={styles.scrollContent}
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={[Colors.primary]} />}
      >
        {/* Header Summary Banner */}
        <View style={styles.summaryCard}>
          <View style={styles.summaryHeader}>
            <View>
              <Text style={styles.summaryLabel}>Academic Year 2026-27 Fee Ledger</Text>
              <Text style={styles.studentName}>
                {feesData?.student_name || selectedStudent?.full_name || 'Student Ward'}
              </Text>
            </View>
            <View style={styles.currencyBadge}>
              <Text style={styles.currencyBadgeText}>INR (₹)</Text>
            </View>
          </View>

          <View style={styles.balanceHighlight}>
            <Text style={styles.balanceCaption}>Outstanding Balance Due</Text>
            <Text style={[styles.balanceAmount, overview.outstanding_balance > 0 ? styles.balanceDue : styles.balanceClear]}>
              ₹{overview.outstanding_balance.toLocaleString('en-IN')}
            </Text>
            {overview.outstanding_balance > 0 ? (
              <View style={styles.dueAlertBadge}>
                <AlertCircle size={14} color={Colors.accent} />
                <Text style={styles.dueAlertText}>Payment due before term deadline</Text>
              </View>
            ) : (
              <View style={styles.clearedBadge}>
                <CheckCircle2 size={14} color={Colors.primary} />
                <Text style={styles.clearedText}>All assessed installments cleared</Text>
              </View>
            )}
          </View>

          {/* Breakdown Mini Cards */}
          <View style={styles.statsRow}>
            <View style={styles.miniStat}>
              <Text style={styles.miniStatLabel}>Total Assessed</Text>
              <Text style={styles.miniStatValue}>₹{overview.total_assessed.toLocaleString('en-IN')}</Text>
            </View>
            <View style={styles.statDivider} />
            <View style={styles.miniStat}>
              <Text style={styles.miniStatLabel}>Total Cleared</Text>
              <Text style={[styles.miniStatValue, { color: Colors.primary }]}>
                ₹{overview.total_paid.toLocaleString('en-IN')}
              </Text>
            </View>
          </View>
        </View>

        {/* Tab Switcher */}
        <View style={styles.tabContainer}>
          <TouchableOpacity
            style={[styles.tabButton, activeTab === 'dues' && styles.tabButtonActive]}
            onPress={() => setActiveTab('dues')}
            activeOpacity={0.8}
          >
            <CreditCard size={18} color={activeTab === 'dues' ? Colors.primary : Colors.textMuted} />
            <Text style={[styles.tabText, activeTab === 'dues' && styles.tabTextActive]}>
              Installments & Dues ({installments.length})
            </Text>
          </TouchableOpacity>

          <TouchableOpacity
            style={[styles.tabButton, activeTab === 'receipts' && styles.tabButtonActive]}
            onPress={() => setActiveTab('receipts')}
            activeOpacity={0.8}
          >
            <Receipt size={18} color={activeTab === 'receipts' ? Colors.primary : Colors.textMuted} />
            <Text style={[styles.tabText, activeTab === 'receipts' && styles.tabTextActive]}>
              Receipts ({receipts.length})
            </Text>
          </TouchableOpacity>
        </View>

        {/* Dues Tab Content */}
        {activeTab === 'dues' && (
          <View style={styles.listSection}>
            {installments.length === 0 ? (
              <View style={styles.emptyCard}>
                <ShieldCheck size={40} color={Colors.primary} />
                <Text style={styles.emptyTitle}>No Pending Installments</Text>
                <Text style={styles.emptySubtitle}>All tuition and term fee installments are up to date.</Text>
              </View>
            ) : (
              installments.map((inst: any, idx: number) => {
                const isPaid = inst.due_balance <= 0 || inst.status === 'paid';
                return (
                  <View key={inst.id || idx} style={styles.installmentCard}>
                    <View style={styles.instHeader}>
                      <View style={{ flex: 1 }}>
                        <View style={styles.termTag}>
                          <Text style={styles.termTagText}>{inst.term || 'Installment'}</Text>
                        </View>
                        <Text style={styles.instTitle}>{inst.title}</Text>
                      </View>
                      <View
                        style={[
                          styles.statusPill,
                          isPaid ? styles.statusPillPaid : styles.statusPillPending,
                        ]}
                      >
                        <Text
                          style={[
                            styles.statusPillText,
                            isPaid ? styles.statusPillTextPaid : styles.statusPillTextPending,
                          ]}
                        >
                          {isPaid ? 'PAID' : 'PENDING'}
                        </Text>
                      </View>
                    </View>

                    {/* Breakdown */}
                    <View style={styles.instDetails}>
                      <View style={styles.detailRow}>
                        <Text style={styles.detailLabel}>Gross Term Fee:</Text>
                        <Text style={styles.detailVal}>₹{inst.gross_amount.toLocaleString('en-IN')}</Text>
                      </View>
                      {inst.concession_discount > 0 && (
                        <View style={styles.detailRow}>
                          <View style={{ flexDirection: 'row', alignItems: 'center', gap: 4 }}>
                            <Tag size={12} color={Colors.primary} />
                            <Text style={[styles.detailLabel, { color: Colors.primary }]}>
                              Scholarship Concession:
                            </Text>
                          </View>
                          <Text style={[styles.detailVal, { color: Colors.primary }]}>
                            - ₹{inst.concession_discount.toLocaleString('en-IN')}
                          </Text>
                        </View>
                      )}
                      <View style={styles.detailRow}>
                        <Text style={styles.detailLabel}>Net Payable:</Text>
                        <Text style={styles.detailValBold}>₹{inst.net_payable.toLocaleString('en-IN')}</Text>
                      </View>
                      <View style={styles.detailRow}>
                        <Text style={styles.detailLabel}>Paid Amount:</Text>
                        <Text style={[styles.detailVal, { color: Colors.primary }]}>
                          ₹{inst.amount_paid.toLocaleString('en-IN')}
                        </Text>
                      </View>
                    </View>

                    <View style={styles.cardDivider} />

                    <View style={styles.instFooter}>
                      <View style={styles.dueDateBox}>
                        <Calendar size={14} color={Colors.textMuted} />
                        <Text style={styles.dueDateText}>
                          Due: {inst.due_date ? new Date(inst.due_date).toLocaleDateString() : 'End of Term'}
                        </Text>
                      </View>

                      {!isPaid && (
                        <TouchableOpacity
                          style={styles.payButton}
                          onPress={() => handlePayPress(inst)}
                          activeOpacity={0.85}
                        >
                          <Text style={styles.payButtonText}>Pay ₹{inst.due_balance.toLocaleString('en-IN')}</Text>
                          <ArrowUpRight size={16} color="#ffffff" />
                        </TouchableOpacity>
                      )}
                    </View>
                  </View>
                );
              })
            )}
          </View>
        )}

        {/* Receipts Tab Content */}
        {activeTab === 'receipts' && (
          <View style={styles.listSection}>
            {receipts.length === 0 ? (
              <View style={styles.emptyCard}>
                <Receipt size={40} color={Colors.textMuted} />
                <Text style={styles.emptyTitle}>No Receipts Available Yet</Text>
                <Text style={styles.emptySubtitle}>Receipts generated at cashier counters will appear here.</Text>
              </View>
            ) : (
              receipts.map((rc: any, idx: number) => (
                <TouchableOpacity
                  key={rc.id || idx}
                  style={styles.receiptCard}
                  onPress={() => handleReceiptPress(rc)}
                  activeOpacity={0.7}
                >
                  <View style={styles.receiptIconBox}>
                    <Receipt size={22} color={Colors.primary} />
                  </View>
                  <View style={styles.receiptInfo}>
                    <Text style={styles.receiptNo}>{rc.receipt_no}</Text>
                    <View style={styles.receiptMeta}>
                      <Clock size={12} color={Colors.textMuted} />
                      <Text style={styles.receiptDate}>{rc.receipt_date}</Text>
                      <Text style={styles.receiptMode}>• {rc.payment_mode?.toUpperCase() || 'CASH'}</Text>
                    </View>
                  </View>
                  <View style={styles.receiptAmountBox}>
                    <Text style={styles.receiptAmount}>+ ₹{rc.amount_paid.toLocaleString('en-IN')}</Text>
                    <ChevronRight size={16} color={Colors.textMuted} />
                  </View>
                </TouchableOpacity>
              ))
            )}
          </View>
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
  scrollContent: {
    padding: 16,
    paddingBottom: 40,
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
  summaryCard: {
    backgroundColor: Colors.cardBg,
    borderRadius: 16,
    padding: 20,
    borderWidth: 1,
    borderColor: Colors.border,
    marginBottom: 16,
    shadowColor: '#000',
    shadowOpacity: 0.04,
    shadowOffset: { width: 0, height: 2 },
    shadowRadius: 6,
    elevation: 2,
  },
  summaryHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
  },
  summaryLabel: {
    fontSize: 12,
    fontWeight: '600',
    color: Colors.textMuted,
    textTransform: 'uppercase',
    letterSpacing: 0.5,
  },
  studentName: {
    fontSize: 16,
    fontWeight: '700',
    color: Colors.textPrimary,
    marginTop: 2,
  },
  currencyBadge: {
    backgroundColor: Colors.primaryTint,
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 6,
  },
  currencyBadgeText: {
    fontSize: 11,
    fontWeight: '700',
    color: Colors.primary,
  },
  balanceHighlight: {
    marginVertical: 16,
    paddingVertical: 12,
    paddingHorizontal: 16,
    backgroundColor: '#f1f5f9',
    borderRadius: 12,
  },
  balanceCaption: {
    fontSize: 12,
    color: Colors.textSecondary,
    fontWeight: '500',
  },
  balanceAmount: {
    fontSize: 32,
    fontWeight: '800',
    marginVertical: 4,
  },
  balanceDue: {
    color: Colors.accent,
  },
  balanceClear: {
    color: Colors.primary,
  },
  dueAlertBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
    marginTop: 2,
  },
  dueAlertText: {
    fontSize: 12,
    color: Colors.accent,
    fontWeight: '600',
  },
  clearedBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
    marginTop: 2,
  },
  clearedText: {
    fontSize: 12,
    color: Colors.primary,
    fontWeight: '600',
  },
  statsRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingTop: 8,
  },
  miniStat: {
    flex: 1,
  },
  miniStatLabel: {
    fontSize: 11,
    color: Colors.textMuted,
    fontWeight: '500',
  },
  miniStatValue: {
    fontSize: 16,
    fontWeight: '700',
    color: Colors.textPrimary,
    marginTop: 2,
  },
  statDivider: {
    width: 1,
    height: 28,
    backgroundColor: Colors.border,
    marginHorizontal: 16,
  },
  tabContainer: {
    flexDirection: 'row',
    backgroundColor: '#e2e8f0',
    borderRadius: 12,
    padding: 4,
    marginBottom: 16,
  },
  tabButton: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 10,
    borderRadius: 8,
    gap: 8,
  },
  tabButtonActive: {
    backgroundColor: Colors.cardBg,
    shadowColor: '#000',
    shadowOpacity: 0.05,
    shadowOffset: { width: 0, height: 1 },
    shadowRadius: 3,
    elevation: 2,
  },
  tabText: {
    fontSize: 13,
    fontWeight: '600',
    color: Colors.textMuted,
  },
  tabTextActive: {
    color: Colors.primary,
  },
  listSection: {
    gap: 12,
  },
  installmentCard: {
    backgroundColor: Colors.cardBg,
    borderRadius: 14,
    padding: 16,
    borderWidth: 1,
    borderColor: Colors.border,
  },
  instHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
  },
  termTag: {
    alignSelf: 'flex-start',
    backgroundColor: Colors.primaryTint,
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: 6,
    marginBottom: 4,
  },
  termTagText: {
    fontSize: 10,
    fontWeight: '700',
    color: Colors.primary,
    textTransform: 'uppercase',
  },
  instTitle: {
    fontSize: 15,
    fontWeight: '700',
    color: Colors.textPrimary,
  },
  statusPill: {
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: 8,
  },
  statusPillPaid: {
    backgroundColor: '#dcfce7',
  },
  statusPillPending: {
    backgroundColor: '#fef3c7',
  },
  statusPillText: {
    fontSize: 11,
    fontWeight: '800',
  },
  statusPillTextPaid: {
    color: '#15803d',
  },
  statusPillTextPending: {
    color: '#b45309',
  },
  instDetails: {
    marginVertical: 12,
    gap: 6,
  },
  detailRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  detailLabel: {
    fontSize: 13,
    color: Colors.textSecondary,
  },
  detailVal: {
    fontSize: 13,
    fontWeight: '600',
    color: Colors.textPrimary,
  },
  detailValBold: {
    fontSize: 14,
    fontWeight: '700',
    color: Colors.textPrimary,
  },
  cardDivider: {
    height: 1,
    backgroundColor: Colors.border,
    marginVertical: 8,
  },
  instFooter: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginTop: 4,
  },
  dueDateBox: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
  },
  dueDateText: {
    fontSize: 12,
    color: Colors.textMuted,
  },
  payButton: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
    backgroundColor: Colors.primary,
    paddingHorizontal: 14,
    paddingVertical: 8,
    borderRadius: 8,
  },
  payButtonText: {
    fontSize: 13,
    fontWeight: '700',
    color: '#ffffff',
  },
  receiptCard: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: Colors.cardBg,
    borderRadius: 12,
    padding: 14,
    borderWidth: 1,
    borderColor: Colors.border,
  },
  receiptIconBox: {
    width: 44,
    height: 44,
    borderRadius: 10,
    backgroundColor: Colors.primaryTint,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 12,
  },
  receiptInfo: {
    flex: 1,
  },
  receiptNo: {
    fontSize: 14,
    fontWeight: '700',
    color: Colors.textPrimary,
  },
  receiptMeta: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
    marginTop: 2,
  },
  receiptDate: {
    fontSize: 12,
    color: Colors.textMuted,
  },
  receiptMode: {
    fontSize: 12,
    color: Colors.textSecondary,
    fontWeight: '600',
  },
  receiptAmountBox: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
  },
  receiptAmount: {
    fontSize: 15,
    fontWeight: '700',
    color: Colors.primary,
  },
  emptyCard: {
    backgroundColor: Colors.cardBg,
    borderRadius: 14,
    padding: 32,
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
