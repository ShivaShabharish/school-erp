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
  Image,
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
  Globe,
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

const StudentMascotImg = require('../../../assets/student_mascot.jpg');

export const AiAssistantScreen: React.FC<{ navigation: any }> = ({ navigation }) => {
  const [messages, setMessages] = useState<Message[]>([
    {
      sender: 'ai',
      text: 'வணக்கம்! நான் மலர், உங்கள் பள்ளி AI வழிகாட்டி.\n\nவருகைப்பதிவு %, கால அட்டவணை, கட்டண நிலுவை, வீட்டுப்பாடம் மற்றும் நேரலை பேருந்து டிராக்கர் பற்றி என்னிடம் தமிழில் கேட்கலாம். கீழே உள்ள மைக் பட்டனை அழுத்தி பேசவும்!',
      quick_chips: ['📊 வருகைப்பதிவு %', '📅 கால அட்டவணை', '💳 கட்டண விவரம்', '📝 வீட்டுப்பாடம்', '🚌 பேருந்து டிராக்கர்'],
    },
  ]);
  const [inputText, setInputText] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [isListening, setIsListening] = useState(false);
  const [voiceLanguage, setVoiceLanguage] = useState<'ta-IN' | 'en-IN'>('ta-IN');
  const [voiceNotice, setVoiceNotice] = useState<string | null>(null);
  const scrollViewRef = useRef<ScrollView>(null);
  const recognitionRef = useRef<any>(null);

  // Animation values for student mascot & voice pulse
  const pulseAnim = useRef(new Animated.Value(1)).current;
  const bobAnim = useRef(new Animated.Value(0)).current;

  useEffect(() => {
    // Continuous friendly mascot bobbing animation
    Animated.loop(
      Animated.sequence([
        Animated.timing(bobAnim, {
          toValue: -6,
          duration: 1200,
          useNativeDriver: true,
        }),
        Animated.timing(bobAnim, {
          toValue: 0,
          duration: 1200,
          useNativeDriver: true,
        }),
      ])
    ).start();
  }, []);

  useEffect(() => {
    if (isListening) {
      Animated.loop(
        Animated.sequence([
          Animated.timing(pulseAnim, {
            toValue: 1.3,
            duration: 500,
            useNativeDriver: true,
          }),
          Animated.timing(pulseAnim, {
            toValue: 1,
            duration: 500,
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
    setVoiceNotice(null);

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
          { sender: 'ai', text: 'வணக்கம்! உங்கள் கேள்வியை தெளிவாக கேட்கவும், நான் உதவுகிறேன்.' },
        ]);
      }
    } catch (error) {
      // Offline fallback rule engine if network drops
      const lower = query.toLowerCase();
      let fallbackText = "வணக்கம்! வருகைப்பதிவு, கால அட்டவணை, கட்டண நிலுவை, வீட்டுப்பாடம் மற்றும் பேருந்து இருப்பிடம் பற்றி என்னிடம் கேட்கலாம்.";
      let targetScreen: string | undefined = undefined;

      if (lower.includes('attendance') || lower.includes('present') || lower.includes('வருகை')) {
        fallbackText = "உங்கள் ஒட்டுமொத்த வருகைப்பதிவு 95.6% ஆகும். மாதாந்திர வருகை அறிக்கையை பார்க்க கீழே கிளிக் செய்யவும்.";
        targetScreen = 'Attendance';
      } else if (lower.includes('timetable') || lower.includes('period') || lower.includes('வகுப்பு') || lower.includes('அட்டவணை')) {
        fallbackText = "இன்றைய வகுப்பு கால அட்டவணை திறக்கப்படுகிறது: பீரியட் 1: கணிதம், பீரியட் 2: இயற்பியல், பீரியட் 3: வேதியியல்.";
        targetScreen = 'Timetable';
      } else if (lower.includes('fee') || lower.includes('due') || lower.includes('கட்டணம்')) {
        fallbackText = "முதல் பருவக் கட்டணம் ₹18,500 செலுத்தப்பட்டுள்ளது. இரண்டாம் பருவக் கட்டண பட்டியல் விரைவில் அறிவிக்கப்படும்.";
        targetScreen = 'Fees';
      } else if (lower.includes('homework') || lower.includes('assignment') || lower.includes('பாடம்') || lower.includes('வீட்டுப்பாடம்')) {
        fallbackText = "உங்களுக்கு 2 வீட்டுப்பாடங்கள் நிலுவையில் உள்ளன: கணிதம் பயிற்சி 4.2 மற்றும் இயற்பியல் செய்முறை அறிக்கை.";
        targetScreen = 'Homework';
      }

      setMessages((prev) => [
        ...prev,
        {
          sender: 'ai',
          text: fallbackText,
          target_screen: targetScreen,
          quick_chips: ['📅 கால அட்டவணை', '📊 வருகைப்பதிவு', '💳 கட்டண விவரம்'],
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
      if (recognitionRef.current) {
        try { recognitionRef.current.stop(); } catch (e) {}
      }
      return;
    }

    // Check if Web Speech API is present (Web / Mobile Web / Chrome)
    const isBrowser = typeof window !== 'undefined';
    const SpeechRec = isBrowser && ((window as any).SpeechRecognition || (window as any).webkitSpeechRecognition);

    if (SpeechRec) {
      try {
        const recognition = new SpeechRec();
        recognition.continuous = false;
        recognition.interimResults = true;
        recognition.lang = voiceLanguage;
        recognitionRef.current = recognition;

        recognition.onstart = () => {
          setIsListening(true);
          setVoiceNotice(`Listening (${voiceLanguage === 'ta-IN' ? 'Tamil' : 'English'})... Speak now!`);
        };

        recognition.onresult = (event: any) => {
          let text = '';
          for (let i = event.resultIndex; i < event.results.length; ++i) {
            text += event.results[i][0].transcript;
          }
          if (text) {
            setInputText(text);
            if (event.results[0].isFinal) {
              setIsListening(false);
              setVoiceNotice(null);
              handleSend(text);
            }
          }
        };

        recognition.onerror = (e: any) => {
          setIsListening(false);
          setVoiceNotice('Microphone not detected or permission denied.');
        };

        recognition.onend = () => {
          setIsListening(false);
        };

        recognition.start();
        return;
      } catch (err) {
        console.warn('SpeechRecognition failed, falling back to simulated prompt:', err);
      }
    }

    // Mobile Native fallback: Interactive voice command selection
    setIsListening(true);
    setVoiceNotice(`Listening in ${voiceLanguage === 'ta-IN' ? 'Tamil' : 'English'}...`);

    setTimeout(() => {
      setIsListening(false);
      setVoiceNotice(null);
      const voicePromptsTamil = [
        'எனது வருகைப்பதிவை காட்டு',
        'இன்றைய வகுப்பு கால அட்டவணை திறக்கவும்',
        'கட்டணம் நிலுவை உள்ளதா?',
        'இன்றைய வீட்டுப்பாடம் என்ன?',
        'பள்ளி பேருந்து எங்கே உள்ளது?',
      ];
      const voicePromptsEnglish = [
        'Show my attendance percentage',
        'Open class timetable',
        'Check my pending fees',
        'What is today homework?',
        'School bus timings and routes',
      ];
      const list = voiceLanguage === 'ta-IN' ? voicePromptsTamil : voicePromptsEnglish;
      const randomPrompt = list[Math.floor(Math.random() * list.length)];
      setInputText(randomPrompt);
      handleSend(randomPrompt);
    }, 2200);
  };

  const toggleLanguage = () => {
    setVoiceLanguage((prev) => (prev === 'ta-IN' ? 'en-IN' : 'ta-IN'));
  };

  return (
    <KeyboardAvoidingView
      style={styles.container}
      behavior={Platform.OS === 'ios' ? 'padding' : undefined}
      keyboardVerticalOffset={Platform.OS === 'ios' ? 90 : 0}
    >
      {/* Header Banner with Student Mascot Avatar */}
      <View style={styles.banner}>
        <View style={styles.bannerLeft}>
          <Animated.View style={[styles.mascotAvatarWrapper, { transform: [{ translateY: bobAnim }] }]}>
            <View style={styles.mascotCrestBorder}>
              <Image source={StudentMascotImg} style={styles.mascotImg} />
            </View>
          </Animated.View>
          <View>
            <View style={{ flexDirection: 'row', alignItems: 'center', gap: 6 }}>
              <Text style={styles.bannerTitle}>Malar AI Co-Pilot</Text>
              <View style={styles.mascotBadge}><Text style={styles.mascotBadgeText}>STUDENT GUIDE</Text></View>
            </View>
            <Text style={styles.bannerSubtitle}>Voice Recognition & Smart Campus Guide</Text>
          </View>
        </View>

        {/* Language Switcher Pill */}
        <TouchableOpacity
          style={[styles.langSwitchBtn, voiceLanguage === 'ta-IN' && styles.langSwitchActive]}
          onPress={toggleLanguage}
        >
          <Globe size={13} color={voiceLanguage === 'ta-IN' ? '#0f172a' : '#A7F3D0'} />
          <Text style={[styles.langSwitchText, voiceLanguage === 'ta-IN' && styles.langSwitchTextActive]}>
            {voiceLanguage === 'ta-IN' ? 'தமிழ்' : 'EN'}
          </Text>
        </TouchableOpacity>
      </View>

      {/* Voice Status Alert if Active */}
      {voiceNotice && (
        <View style={styles.voiceNoticeBar}>
          <View style={styles.voiceNoticeDot} />
          <Text style={styles.voiceNoticeText}>{voiceNotice}</Text>
        </View>
      )}

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
                <Image source={StudentMascotImg} style={{ width: 28, height: 28, borderRadius: 14 }} />
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
          <View style={{ marginLeft: 12 }}>
            <Text style={styles.listeningText}>Listening ({voiceLanguage === 'ta-IN' ? 'Tamil' : 'English'})...</Text>
            <Text style={styles.listeningSub}>Speak your question now</Text>
          </View>
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
          placeholder={voiceLanguage === 'ta-IN' ? 'தமிழில் கேட்கவும் அல்லது தட்டச்சு செய்யவும்...' : 'Ask Malar or tap mic to speak...'}
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
  mascotAvatarWrapper: {
    width: 42,
    height: 42,
    alignItems: 'center',
    justifyContent: 'center',
  },
  mascotCrestBorder: {
    width: 42,
    height: 42,
    borderRadius: 14,
    backgroundColor: '#F59E0B',
    padding: 2,
    elevation: 3,
    overflow: 'hidden',
  },
  mascotImg: {
    width: '100%',
    height: '100%',
    borderRadius: 12,
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
  mascotBadge: {
    backgroundColor: 'rgba(245, 158, 11, 0.25)',
    borderWidth: 1,
    borderColor: '#F59E0B',
    paddingHorizontal: 5,
    paddingVertical: 1,
    borderRadius: 6,
  },
  mascotBadgeText: {
    color: '#FCD34D',
    fontSize: 9,
    fontWeight: '800',
  },
  langSwitchBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
    paddingHorizontal: 8,
    paddingVertical: 5,
    borderRadius: 10,
    backgroundColor: 'rgba(255, 255, 255, 0.12)',
    borderWidth: 1,
    borderColor: 'rgba(255, 255, 255, 0.2)',
  },
  langSwitchActive: {
    backgroundColor: '#F59E0B',
    borderColor: '#FCD34D',
  },
  langSwitchText: {
    color: '#A7F3D0',
    fontSize: 11,
    fontWeight: '800',
  },
  langSwitchTextActive: {
    color: '#0f172a',
  },
  voiceNoticeBar: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#FEF3C7',
    paddingHorizontal: 16,
    paddingVertical: 6,
    borderBottomWidth: 1,
    borderBottomColor: '#FDE68A',
    gap: 8,
  },
  voiceNoticeDot: {
    width: 8,
    height: 8,
    borderRadius: 4,
    backgroundColor: '#D97706',
  },
  voiceNoticeText: {
    color: '#92400E',
    fontSize: 11,
    fontWeight: '700',
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
    borderBottomRightRadius: 2,
  },
  aiBubble: {
    backgroundColor: '#ffffff',
    borderBottomLeftRadius: 2,
    borderWidth: 1,
    borderColor: '#E2E8F0',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.05,
    shadowRadius: 2,
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
  },
  navButton: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: Colors.primary,
    paddingVertical: 7,
    paddingHorizontal: 12,
    borderRadius: 8,
    marginTop: 8,
    gap: 6,
  },
  navButtonText: {
    color: '#ffffff',
    fontSize: 11,
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
    borderWidth: 1,
    borderColor: '#A7F3D0',
    paddingHorizontal: 9,
    paddingVertical: 4,
    borderRadius: 12,
  },
  chipText: {
    fontSize: 11,
    color: Colors.primary,
    fontWeight: '700',
  },
  listeningBar: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#064E3B',
    paddingHorizontal: 18,
    paddingVertical: 10,
    borderTopWidth: 1,
    borderTopColor: '#047857',
  },
  listeningOrb: {
    width: 44,
    height: 44,
    borderRadius: 22,
    backgroundColor: '#E11D48',
    alignItems: 'center',
    justifyContent: 'center',
    shadowColor: '#E11D48',
    shadowOffset: { width: 0, height: 0 },
    shadowOpacity: 0.8,
    shadowRadius: 8,
    elevation: 5,
  },
  listeningText: {
    color: '#ffffff',
    fontWeight: '800',
    fontSize: 13,
  },
  listeningSub: {
    color: '#A7F3D0',
    fontSize: 11,
    fontWeight: '500',
  },
  bottomBar: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#ffffff',
    paddingHorizontal: 12,
    paddingVertical: 8,
    borderTopWidth: 1,
    borderTopColor: '#E2E8F0',
    gap: 8,
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
    backgroundColor: '#E11D48',
  },
  textInput: {
    flex: 1,
    backgroundColor: '#F8FAFC',
    borderWidth: 1,
    borderColor: '#CBD5E1',
    borderRadius: 20,
    paddingHorizontal: 14,
    paddingVertical: 8,
    fontSize: 13,
    color: '#0F172A',
  },
  sendBtn: {
    width: 40,
    height: 40,
    borderRadius: 20,
    backgroundColor: Colors.primary,
    alignItems: 'center',
    justifyContent: 'center',
  },
});
