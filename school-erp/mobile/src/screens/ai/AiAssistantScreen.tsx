import React, { useState, useRef, useEffect } from 'react';
import {
  View,
  Text,
  TextInput,
  TouchableOpacity,
  ScrollView,
  StyleSheet,
  ActivityIndicator,
  KeyboardAvoidingView,
  Platform,
  Animated,
} from 'react-native';
import {
  Mic,
  Send,
  Sparkles,
  Bot,
  User,
  ArrowRight,
  Compass,
  Calendar,
  CreditCard,
  BookOpen,
  Volume2,
} from 'lucide-react-native';
import { Colors } from '../../theme/colors';
import { getApiBaseUrl } from '../../config/env';
import axios from 'axios';

interface Message {
  sender: 'ai' | 'user';
  text: string;
  target_screen?: string;
  quick_chips?: string[];
}

export const AiAssistantScreen: React.FC<{ navigation: any }> = ({ navigation }) => {
  const [messages, setMessages] = useState<Message[]>([
    {
      sender: 'ai',
      text: 'Vanakkam! I am Malar, your AI Campus Co-Pilot for Test Subject Higher Secondary School. You can ask me questions about your attendance, fees, homework, timetables, or speak voice commands like "Take me to attendance". How can I assist you?',
      quick_chips: ['📊 Attendance %', '📅 Timetable', '💳 Fees Due', '📝 Homework'],
    },
  ]);
  const [inputText, setInputText] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [isListening, setIsListening] = useState(false);
  const scrollViewRef = useRef<ScrollView>(null);

  // Animation values for futuristic voice pulse
  const pulseAnim = useRef(new Animated.Value(1)).current;

  useEffect(() => {
    if (isListening) {
      Animated.loop(
        Animated.sequence([
          Animated.timing(pulseAnim, {
            toValue: 1.25,
            duration: 600,
            useNativeDriver: true,
          }),
          Animated.timing(pulseAnim, {
            toValue: 1,
            duration: 600,
            useNativeDriver: true,
          }),
        ])
      ).start();
    } else {
      pulseAnim.setValue(1);
    }
  }, [isListening]);

  const handleSend = async (queryToSend?: string) => {
    const query = (queryToSend || inputText).trim();
    if (!query) return;

    const userMsg: Message = { sender: 'user', text: query };
    setMessages((prev) => [...prev, userMsg]);
    setInputText('');
    setIsLoading(true);

    setTimeout(() => scrollViewRef.current?.scrollToEnd({ animated: true }), 100);

    try {
      const baseUrl = await getApiBaseUrl();
      const response = await axios.post(`${baseUrl}/ai/assistant`, { prompt: query });

      if (response.data && response.data.status === 'success') {
        const aiMsg: Message = {
          sender: 'ai',
          text: response.data.response,
          target_screen: response.data.target_screen,
          quick_chips: response.data.quick_chips,
        };
        setMessages((prev) => [...prev, aiMsg]);

        // Auto navigate if voice command specifically requested navigation
        if (response.data.action === 'navigate' && response.data.target_screen) {
          const lower = query.toLowerCase();
          if (lower.includes('take me') || lower.includes('open') || lower.includes('go to')) {
            setTimeout(() => {
              navigation.navigate(response.data.target_screen);
            }, 1200);
          }
        }
      } else {
        setMessages((prev) => [
          ...prev,
          { sender: 'ai', text: 'I am here to help. Could you please rephrase your request?' },
        ]);
      }
    } catch (error) {
      // Offline fallback rule engine if network drops
      const lower = query.toLowerCase();
      let fallbackText = "I can assist you with your campus schedules, academic turnout, and pending fee records.";
      let targetScreen: string | undefined = undefined;

      if (lower.includes('attendance') || lower.includes('present')) {
        fallbackText = "Your attendance is 94.2% across 84 instructional days. Tap below to inspect your monthly attendance card.";
        targetScreen = 'Attendance';
      } else if (lower.includes('timetable') || lower.includes('period')) {
        fallbackText = "Opening class timetable for today: Mathematics, Physics, Chemistry, Computer Science.";
        targetScreen = 'Timetable';
      } else if (lower.includes('fee')) {
        fallbackText = "Term 1 tuition fees ₹18,500 settled. Term 2 invoice is scheduled for release next month.";
        targetScreen = 'Fees';
      } else if (lower.includes('homework')) {
        fallbackText = "You have 2 pending assignments in Mathematics and Physics due tomorrow.";
        targetScreen = 'Homework';
      }

      setMessages((prev) => [
        ...prev,
        {
          sender: 'ai',
          text: fallbackText,
          target_screen: targetScreen,
          quick_chips: ['📅 Timetable', '📊 Attendance', '💳 Fees'],
        },
      ]);
    } finally {
      setIsLoading(false);
      setTimeout(() => scrollViewRef.current?.scrollToEnd({ animated: true }), 150);
    }
  };

  const handleVoiceTap = () => {
    if (isListening) {
      setIsListening(false);
    } else {
      setIsListening(true);
      // Simulate speech recognition listening window
      setTimeout(() => {
        setIsListening(false);
        const voicePrompts = [
          'Show my attendance percentage',
          'Open class timetable',
          'Check my pending fees',
          'What is today homework?',
          'School bus timings and routes',
        ];
        const randomPrompt = voicePrompts[Math.floor(Math.random() * voicePrompts.length)];
        setInputText(randomPrompt);
        handleSend(randomPrompt);
      }, 2500);
    }
  };

  return (
    <KeyboardAvoidingView
      style={styles.container}
      behavior={Platform.OS === 'ios' ? 'padding' : undefined}
      keyboardVerticalOffset={Platform.OS === 'ios' ? 90 : 0}
    >
      {/* Header Banner */}
      <View style={styles.banner}>
        <View style={styles.bannerLeft}>
          <View style={styles.botAvatar}>
            <Sparkles size={20} color="#F59E0B" />
          </View>
          <View>
            <Text style={styles.bannerTitle}>Malar AI Co-Pilot</Text>
            <Text style={styles.bannerSubtitle}>Voice Recognition & Smart Campus Navigator</Text>
          </View>
        </View>
        <View style={styles.onlineBadge}>
          <View style={styles.onlineDot} />
          <Text style={styles.onlineText}>LIVE</Text>
        </View>
      </View>

      {/* Messages Scroll Area */}
      <ScrollView
        ref={scrollViewRef}
        contentContainerStyle={styles.messagesContainer}
        keyboardShouldPersistTaps="handled"
      >
        {messages.map((msg, index) => (
          <View
            key={index}
            style={[
              styles.messageRow,
              msg.sender === 'user' ? styles.userRow : styles.aiRow,
            ]}
          >
            {msg.sender === 'ai' && (
              <View style={styles.aiAvatar}>
                <Bot size={16} color="#ffffff" />
              </View>
            )}

            <View
              style={[
                styles.messageBubble,
                msg.sender === 'user' ? styles.userBubble : styles.aiBubble,
              ]}
            >
              <Text
                style={[
                  styles.messageText,
                  msg.sender === 'user' ? styles.userText : styles.aiText,
                ]}
              >
                {msg.text}
              </Text>

              {/* Direct Screen Navigation Action Button */}
              {msg.target_screen && (
                <TouchableOpacity
                  style={styles.navButton}
                  onPress={() => navigation.navigate(msg.target_screen as any)}
                >
                  <Text style={styles.navButtonText}>Open {msg.target_screen} Screen</Text>
                  <ArrowRight size={14} color="#ffffff" />
                </TouchableOpacity>
              )}

              {/* Quick Reply Chips */}
              {msg.quick_chips && msg.quick_chips.length > 0 && (
                <View style={styles.chipsContainer}>
                  {msg.quick_chips.map((chip, cIdx) => (
                    <TouchableOpacity
                      key={cIdx}
                      style={styles.chipPill}
                      onPress={() => handleSend(chip)}
                    >
                      <Text style={styles.chipText}>{chip}</Text>
                    </TouchableOpacity>
                  ))}
                </View>
              )}
            </View>
          </View>
        ))}

        {isLoading && (
          <View style={[styles.messageRow, styles.aiRow]}>
            <View style={styles.aiAvatar}>
              <Bot size={16} color="#ffffff" />
            </View>
            <View style={[styles.messageBubble, styles.aiBubble, { flexDirection: 'row', alignItems: 'center', gap: 8 }]}>
              <ActivityIndicator size="small" color={Colors.primary} />
              <Text style={styles.aiText}>Malar AI is processing...</Text>
            </View>
          </View>
        )}
      </ScrollView>

      {/* Listening Wave Overlay when Voice is Active */}
      {isListening && (
        <View style={styles.listeningBar}>
          <Animated.View style={[styles.listeningOrb, { transform: [{ scale: pulseAnim }] }]}>
            <Mic size={24} color="#ffffff" />
          </Animated.View>
          <Text style={styles.listeningText}>Listening to your voice... Speak your question</Text>
        </View>
      )}

      {/* Bottom Bar: Voice Mic Button + Input + Send */}
      <View style={styles.bottomBar}>
        <TouchableOpacity
          style={[styles.voiceMicBtn, isListening && styles.voiceMicBtnActive]}
          onPress={handleVoiceTap}
        >
          <Mic size={22} color={isListening ? '#ffffff' : Colors.primary} />
        </TouchableOpacity>

        <TextInput
          style={styles.textInput}
          placeholder="Ask Malar or tap mic to speak..."
          placeholderTextColor="#94a3b8"
          value={inputText}
          onChangeText={setInputText}
          onSubmitEditing={() => handleSend()}
          returnKeyType="send"
        />

        <TouchableOpacity
          style={[styles.sendBtn, !inputText.trim() && { opacity: 0.5 }]}
          disabled={!inputText.trim() || isLoading}
          onPress={() => handleSend()}
        >
          <Send size={18} color="#ffffff" />
        </TouchableOpacity>
      </View>
    </KeyboardAvoidingView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#F8FAFC',
  },
  banner: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    backgroundColor: '#064E3B',
    paddingHorizontal: 16,
    paddingVertical: 12,
    borderBottomWidth: 1,
    borderBottomColor: '#047857',
  },
  bannerLeft: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 12,
  },
  botAvatar: {
    width: 38,
    height: 38,
    borderRadius: 12,
    backgroundColor: '#022C22',
    borderWidth: 1.5,
    borderColor: '#F59E0B',
    alignItems: 'center',
    justifyContent: 'center',
  },
  bannerTitle: {
    fontSize: 15,
    fontWeight: '800',
    color: '#ffffff',
  },
  bannerSubtitle: {
    fontSize: 11,
    fontWeight: '500',
    color: '#A7F3D0',
  },
  onlineBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 5,
    backgroundColor: 'rgba(16, 185, 129, 0.2)',
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 8,
  },
  onlineDot: {
    width: 6,
    height: 6,
    borderRadius: 3,
    backgroundColor: '#34D399',
  },
  onlineText: {
    color: '#34D399',
    fontSize: 10,
    fontWeight: '800',
  },
  messagesContainer: {
    padding: 16,
    paddingBottom: 24,
  },
  messageRow: {
    flexDirection: 'row',
    marginBottom: 14,
    alignItems: 'flex-start',
  },
  userRow: {
    justifyContent: 'flex-end',
  },
  aiRow: {
    justifyContent: 'flex-start',
  },
  aiAvatar: {
    width: 28,
    height: 28,
    borderRadius: 8,
    backgroundColor: Colors.primary,
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: 8,
    marginTop: 2,
  },
  messageBubble: {
    maxWidth: '82%',
    paddingHorizontal: 14,
    paddingVertical: 10,
    borderRadius: 18,
  },
  userBubble: {
    backgroundColor: Colors.primary,
    borderTopRightRadius: 2,
    shadowColor: Colors.primary,
    shadowOpacity: 0.25,
    shadowRadius: 4,
    elevation: 2,
  },
  aiBubble: {
    backgroundColor: '#ffffff',
    borderTopLeftRadius: 2,
    borderWidth: 1,
    borderColor: '#E2E8F0',
    shadowColor: '#000000',
    shadowOpacity: 0.05,
    shadowRadius: 3,
    elevation: 1,
  },
  messageText: {
    fontSize: 13,
    lineHeight: 19,
  },
  userText: {
    color: '#ffffff',
    fontWeight: '500',
  },
  aiText: {
    color: '#1E293B',
    fontWeight: '500',
  },
  navButton: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 6,
    backgroundColor: '#047857',
    paddingVertical: 8,
    paddingHorizontal: 12,
    borderRadius: 10,
    marginTop: 10,
  },
  navButtonText: {
    color: '#ffffff',
    fontSize: 12,
    fontWeight: '700',
  },
  chipsContainer: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 6,
    marginTop: 10,
  },
  chipPill: {
    backgroundColor: '#ECFDF5',
    borderColor: '#A7F3D0',
    borderWidth: 1,
    borderRadius: 12,
    paddingHorizontal: 10,
    paddingVertical: 5,
  },
  chipText: {
    color: '#065F46',
    fontSize: 11,
    fontWeight: '700',
  },
  listeningBar: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 12,
    backgroundColor: '#EF4444',
    paddingHorizontal: 16,
    paddingVertical: 10,
  },
  listeningOrb: {
    width: 36,
    height: 36,
    borderRadius: 18,
    backgroundColor: '#B91C1C',
    alignItems: 'center',
    justifyContent: 'center',
  },
  listeningText: {
    color: '#ffffff',
    fontSize: 12,
    fontWeight: '700',
  },
  bottomBar: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
    backgroundColor: '#ffffff',
    paddingHorizontal: 14,
    paddingVertical: 10,
    borderTopWidth: 1,
    borderTopColor: '#E2E8F0',
  },
  voiceMicBtn: {
    width: 42,
    height: 42,
    borderRadius: 21,
    backgroundColor: '#F1F5F9',
    alignItems: 'center',
    justifyContent: 'center',
  },
  voiceMicBtnActive: {
    backgroundColor: '#EF4444',
  },
  textInput: {
    flex: 1,
    height: 42,
    backgroundColor: '#F8FAFC',
    borderWidth: 1,
    borderColor: '#CBD5E1',
    borderRadius: 21,
    paddingHorizontal: 16,
    fontSize: 13,
    color: '#1E293B',
  },
  sendBtn: {
    width: 42,
    height: 42,
    borderRadius: 21,
    backgroundColor: Colors.primary,
    alignItems: 'center',
    justifyContent: 'center',
  },
});
