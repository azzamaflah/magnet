{{-- resources/views/components/chatbot-widget.blade.php --}}
{{-- MagBot: Floating AI Chatbot Widget --}}

<style>
    /* ===== MAGBOT WIDGET STYLES ===== */
    #magbot-container {
        position: fixed;
        bottom: 24px;
        right: 24px;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 12px;
        font-family: 'Inter', sans-serif;
        pointer-events: none; /* Jangan blokir klik elemen lain di layar */
    }

    /* Floating Toggle Button */
    #magbot-toggle {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: linear-gradient(135deg, #d97757 0%, #c4623e 100%);
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
        box-shadow: 0 4px 20px rgba(217, 119, 87, 0.5);
        transition: transform 0.2s, box-shadow 0.2s;
        flex-shrink: 0;
        pointer-events: auto; /* Tombol bisa diklik */
        position: relative;
    }

    #magbot-toggle:hover {
        transform: scale(1.08);
        box-shadow: 0 6px 28px rgba(217, 119, 87, 0.65);
    }

    #magbot-toggle .icon-open,
    #magbot-toggle .icon-close {
        transition: opacity 0.2s, transform 0.2s;
        position: absolute;
    }

    /* Chat Window */
    #magbot-window {
        width: 360px;
        max-height: 520px;
        background: #1e1e1e;
        border: 1px solid #3a3a3a;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.7), 0 0 0 1px rgba(217,119,87,0.1);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        transform-origin: bottom right;
        transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.2s ease;
        pointer-events: auto; /* Window bisa diklik saat terbuka */
    }

    #magbot-window.hidden-widget {
        transform: scale(0.8) translateY(20px);
        opacity: 0;
        pointer-events: none !important;
        display: none;
    }

    /* Chat Header */
    #magbot-header {
        background: linear-gradient(135deg, #2a2a2a 0%, #222222 100%);
        border-bottom: 1px solid #3a3a3a;
        padding: 14px 16px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .magbot-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #d97757, #c4623e);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.9rem;
        flex-shrink: 0;
    }

    .magbot-header-info .name {
        font-weight: 600;
        font-size: 0.875rem;
        color: white;
        line-height: 1.2;
    }

    .magbot-header-info .status {
        font-size: 0.7rem;
        color: #6ee7b7;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .magbot-status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #6ee7b7;
        animation: pulse-green 2s infinite;
    }

    @keyframes pulse-green {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.4; }
    }

    #magbot-close-btn {
        margin-left: auto;
        background: none;
        border: none;
        color: #6b7280;
        cursor: pointer;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.85rem;
        transition: color 0.15s, background 0.15s;
    }

    #magbot-close-btn:hover {
        color: white;
        background: rgba(255,255,255,0.05);
    }

    /* Messages Area */
    #magbot-messages {
        flex: 1;
        overflow-y: auto;
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        scroll-behavior: smooth;
        max-height: 320px;
    }

    #magbot-messages::-webkit-scrollbar { width: 4px; }
    #magbot-messages::-webkit-scrollbar-track { background: transparent; }
    #magbot-messages::-webkit-scrollbar-thumb { background: #3a3a3a; border-radius: 4px; }

    /* Message Bubbles */
    .magbot-msg {
        display: flex;
        gap: 8px;
        max-width: 100%;
        animation: msg-fade-in 0.2s ease;
    }

    @keyframes msg-fade-in {
        from { opacity: 0; transform: translateY(6px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .magbot-msg.user {
        flex-direction: row-reverse;
    }

    .magbot-msg-avatar {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        font-weight: 600;
        margin-top: 2px;
    }

    .magbot-msg-avatar.bot { background: linear-gradient(135deg, #d97757, #c4623e); color: white; }
    .magbot-msg-avatar.usr { background: rgba(217, 119, 87, 0.2); color: #e88968; }

    .magbot-msg-bubble {
        max-width: 78%;
        padding: 10px 13px;
        border-radius: 14px;
        font-size: 0.8125rem;
        line-height: 1.55;
        word-break: break-word;
    }

    .magbot-msg.bot .magbot-msg-bubble {
        background: #2a2a2a;
        border: 1px solid #3a3a3a;
        color: #e5e7eb;
        border-bottom-left-radius: 4px;
    }

    .magbot-msg.user .magbot-msg-bubble {
        background: linear-gradient(135deg, #d97757, #c4623e);
        color: white;
        border-bottom-right-radius: 4px;
    }

    /* Typing indicator */
    .magbot-typing {
        display: flex;
        align-items: center;
        gap: 4px;
        padding: 10px 14px;
    }

    .magbot-typing span {
        width: 6px;
        height: 6px;
        background: #6b7280;
        border-radius: 50%;
        animation: typing-bounce 1.2s infinite;
    }

    .magbot-typing span:nth-child(2) { animation-delay: 0.2s; }
    .magbot-typing span:nth-child(3) { animation-delay: 0.4s; }

    @keyframes typing-bounce {
        0%, 60%, 100% { transform: translateY(0); }
        30% { transform: translateY(-6px); }
    }

    /* Quick Replies */
    #magbot-quick-replies {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        padding: 0 16px 12px;
    }

    .magbot-quick-btn {
        padding: 5px 11px;
        background: rgba(217, 119, 87, 0.1);
        border: 1px solid rgba(217, 119, 87, 0.3);
        border-radius: 20px;
        color: #e88968;
        font-size: 0.72rem;
        cursor: pointer;
        transition: background 0.15s, border-color 0.15s;
        white-space: nowrap;
    }

    .magbot-quick-btn:hover {
        background: rgba(217, 119, 87, 0.2);
        border-color: rgba(217, 119, 87, 0.5);
    }

    /* Input Area */
    #magbot-input-area {
        border-top: 1px solid #3a3a3a;
        padding: 12px 14px;
        display: flex;
        gap: 8px;
        align-items: center;
        background: #1a1a1a;
    }

    #magbot-input {
        flex: 1;
        background: #2a2a2a;
        border: 1px solid #3a3a3a;
        border-radius: 10px;
        padding: 9px 13px;
        color: white;
        font-size: 0.8125rem;
        outline: none;
        transition: border-color 0.15s;
        font-family: 'Inter', sans-serif;
        resize: none;
        height: 38px;
        line-height: 1.4;
    }

    #magbot-input::placeholder { color: #6b7280; }
    #magbot-input:focus { border-color: #d97757; }

    #magbot-send-btn {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: linear-gradient(135deg, #d97757, #c4623e);
        border: none;
        color: white;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        transition: opacity 0.15s, transform 0.15s;
        flex-shrink: 0;
    }

    #magbot-send-btn:hover { opacity: 0.9; transform: scale(1.05); }
    #magbot-send-btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

    /* Notification badge */
    #magbot-badge {
        position: absolute;
        top: -4px;
        right: -4px;
        width: 16px;
        height: 16px;
        background: #ef4444;
        border-radius: 50%;
        font-size: 0.55rem;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        border: 2px solid #1a1a1a;
    }

    /* Mobile responsiveness */
    @media (max-width: 480px) {
        #magbot-container {
            bottom: 16px;
            right: 16px;
        }
        #magbot-window {
            width: calc(100vw - 32px);
            max-height: 80vh;
        }
    }

    /* Markdown styling inside messages */
    .magbot-msg-bubble strong { font-weight: 600; color: #fff; }
    .magbot-msg-bubble ul { list-style: disc; padding-left: 1.2rem; margin-top: 4px; }
    .magbot-msg-bubble ol { list-style: decimal; padding-left: 1.2rem; margin-top: 4px; }
    .magbot-msg-bubble li { margin-bottom: 2px; }
    .magbot-msg-bubble p { margin-bottom: 4px; }
    .magbot-msg-bubble code { background: rgba(255,255,255,0.1); padding: 1px 4px; border-radius: 3px; font-family: monospace; font-size: 0.75rem; }
</style>

{{-- Widget HTML --}}
<div id="magbot-container">

    {{-- Chat Window --}}
    <div id="magbot-window" class="hidden-widget">

        {{-- Header --}}
        <div id="magbot-header">
            <div class="magbot-avatar">
                <i class="fas fa-robot"></i>
            </div>
            <div class="magbot-header-info">
                <div class="name">MagBot</div>
                <div class="status">
                    <span class="magbot-status-dot"></span>
                    Asisten Virtual BPS Bantul
                </div>
            </div>
            <button id="magbot-close-btn" title="Tutup">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- Messages --}}
        <div id="magbot-messages">
            {{-- Pesan sambutan awal --}}
            <div class="magbot-msg bot">
                <div class="magbot-msg-avatar bot"><i class="fas fa-robot" style="font-size:0.65rem"></i></div>
                <div class="magbot-msg-bubble">
                    Halo! 👋 Saya <strong>MagBot</strong>, asisten virtual BPS Kabupaten Bantul.<br><br>
                    Saya siap membantu kamu seputar <strong>pendaftaran magang</strong>, <strong>informasi divisi</strong>, dan <strong>panduan aplikasi MagNet</strong>. 😊
                </div>
            </div>
        </div>

        {{-- Quick Replies --}}
        <div id="magbot-quick-replies">
            <button class="magbot-quick-btn" data-msg="Cara mendaftar magang?">📋 Cara Daftar</button>
            <button class="magbot-quick-btn" data-msg="Dokumen apa saja yang perlu disiapkan?">📁 Syarat Dokumen</button>
            <button class="magbot-quick-btn" data-msg="Divisi apa saja yang tersedia?">🏢 Info Divisi</button>
            <button class="magbot-quick-btn" data-msg="Apa maksud status conditional pada pendaftaran?">❓ Status Conditional</button>
        </div>

        {{-- Input --}}
        <div id="magbot-input-area">
            <textarea id="magbot-input" placeholder="Ketik pertanyaanmu..." rows="1"></textarea>
            <button id="magbot-send-btn" title="Kirim">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>

    {{-- Toggle Button --}}
    <button id="magbot-toggle" title="Buka MagBot">
        <span id="magbot-badge" style="display:none;">1</span>
        <i class="fas fa-robot icon-open" id="icon-open"></i>
        <i class="fas fa-times icon-close" id="icon-close" style="display:none;"></i>
    </button>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken   = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const chatRoute   = '{{ route("chatbot.message") }}';
    const userInitial = '{{ substr(auth()->user()->name, 0, 1) }}';

    // DOM refs
    const toggleBtn    = document.getElementById('magbot-toggle');
    const window_      = document.getElementById('magbot-window');
    const closeBtn     = document.getElementById('magbot-close-btn');
    const messages     = document.getElementById('magbot-messages');
    const input        = document.getElementById('magbot-input');
    const sendBtn      = document.getElementById('magbot-send-btn');
    const quickReplies = document.querySelectorAll('.magbot-quick-btn');
    const badge        = document.getElementById('magbot-badge');
    const iconOpen     = document.getElementById('icon-open');
    const iconClose    = document.getElementById('icon-close');

    if (!toggleBtn || !window_) return;

    let isOpen      = false;
    let isTyping    = false;
    let history     = [];

    // === Utility: Simple Markdown Parser ===
    function parseMarkdown(text) {
        return text
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g,     '<em>$1</em>')
            .replace(/`(.*?)`/g,       '<code>$1</code>')
            .replace(/^- (.+)/gm,      '<li>$1</li>')
            .replace(/^(\d+)\. (.+)/gm,'<li>$2</li>')
            .replace(/(<li>.*<\/li>\n?)+/g, m => `<ul>${m}</ul>`)
            .replace(/\n\n/g, '<br><br>')
            .replace(/\n/g,   '<br>');
    }

    // === Toggle Window ===
    function openChat() {
        isOpen = true;
        window_.style.display = 'flex';
        setTimeout(() => {
            window_.classList.remove('hidden-widget');
        }, 10);
        iconOpen.style.display  = 'none';
        iconClose.style.display = 'block';
        badge.style.display     = 'none';
        scrollToBottom();
        input?.focus();
    }

    function closeChat() {
        isOpen = false;
        window_.classList.add('hidden-widget');
        setTimeout(() => {
            if (!isOpen) window_.style.display = 'none';
        }, 250);
        iconOpen.style.display  = 'block';
        iconClose.style.display = 'none';
    }

    toggleBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        isOpen ? closeChat() : openChat();
    });

    closeBtn?.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        closeChat();
    });

    // === Add Message Bubble ===
    function addMessage(role, text) {
        const wrapper = document.createElement('div');
        wrapper.className = `magbot-msg ${role}`;

        const avatar = document.createElement('div');
        avatar.className = `magbot-msg-avatar ${role === 'bot' ? 'bot' : 'usr'}`;
        avatar.innerHTML = role === 'bot'
            ? '<i class="fas fa-robot" style="font-size:0.65rem"></i>'
            : userInitial;

        const bubble = document.createElement('div');
        bubble.className = 'magbot-msg-bubble';
        bubble.innerHTML  = parseMarkdown(text);

        wrapper.appendChild(avatar);
        wrapper.appendChild(bubble);
        messages.appendChild(wrapper);
        scrollToBottom();

        return bubble;
    }

    // === Typing indicator ===
    function showTyping() {
        const wrapper = document.createElement('div');
        wrapper.className = 'magbot-msg bot';
        wrapper.id = 'magbot-typing-indicator';

        const avatar = document.createElement('div');
        avatar.className = 'magbot-msg-avatar bot';
        avatar.innerHTML = '<i class="fas fa-robot" style="font-size:0.65rem"></i>';

        const bubble = document.createElement('div');
        bubble.className = 'magbot-msg-bubble magbot-typing';
        bubble.innerHTML = '<span></span><span></span><span></span>';

        wrapper.appendChild(avatar);
        wrapper.appendChild(bubble);
        messages.appendChild(wrapper);
        scrollToBottom();
    }

    function hideTyping() {
        const indicator = document.getElementById('magbot-typing-indicator');
        if (indicator) indicator.remove();
    }

    function scrollToBottom() {
        if (messages) messages.scrollTop = messages.scrollHeight;
    }

    // === Send Message ===
    async function sendMessage(text) {
        if (!text || !text.trim() || isTyping) return;

        const cleanText = text.trim();

        // Sembunyikan quick replies setelah pertama kali mengirim
        const qrContainer = document.getElementById('magbot-quick-replies');
        if (qrContainer) qrContainer.style.display = 'none';

        // Tambahkan ke UI
        addMessage('user', cleanText);

        // Tambahkan ke history
        history.push({ role: 'user', text: cleanText });

        // Reset input
        if (input) {
            input.value = '';
            input.style.height = '38px';
        }

        // Loading state
        isTyping = true;
        if (sendBtn) sendBtn.disabled = true;
        showTyping();

        try {
            const response = await fetch(chatRoute, {
                method: 'POST',
                headers: {
                    'Content-Type':     'application/json',
                    'X-CSRF-TOKEN':     csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    message: cleanText,
                    history: history.slice(-10),
                }),
            });

            hideTyping();

            const data = await response.json();
            const reply = data.reply ?? 'Maaf, terjadi kesalahan. Silakan coba lagi.';

            addMessage('bot', reply);

            // Simpan ke history
            history.push({ role: 'model', text: reply });
            saveHistory();

            // Jika window tertutup, tampilkan badge
            if (!isOpen && badge) {
                badge.style.display = 'flex';
            }

        } catch (err) {
            hideTyping();
            addMessage('bot', '❌ Gagal terhubung ke server. Periksa koneksi internetmu.');
            console.error('MagBot error:', err);
        } finally {
            isTyping = false;
            if (sendBtn) sendBtn.disabled = false;
            input?.focus();
        }
    }

    // === Event Listeners ===
    sendBtn?.addEventListener('click', () => sendMessage(input?.value));

    input?.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage(input.value);
        }
    });

    // Auto-resize textarea
    input?.addEventListener('input', () => {
        input.style.height = '38px';
        input.style.height = Math.min(input.scrollHeight, 90) + 'px';
    });

    // Quick reply buttons
    quickReplies.forEach(btn => {
        btn.addEventListener('click', function() {
            sendMessage(this.getAttribute('data-msg'));
        });
    });

    // === Session History ===
    function saveHistory() {
        try {
            sessionStorage.setItem('magbot_history', JSON.stringify(history.slice(-20)));
        } catch(e) {}
    }

    function loadHistory() {
        try {
            const saved = sessionStorage.getItem('magbot_history');
            if (!saved) return;
            const parsed = JSON.parse(saved);
            if (!Array.isArray(parsed) || parsed.length === 0) return;

            history = parsed;

            parsed.forEach(item => {
                addMessage(item.role === 'user' ? 'user' : 'bot', item.text);
            });

            const qrContainer = document.getElementById('magbot-quick-replies');
            if (qrContainer) qrContainer.style.display = 'none';
        } catch(e) {}
    }

    // Load history on init
    loadHistory();
});
</script>
