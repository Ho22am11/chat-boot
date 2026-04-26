<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BootChat AI</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-dark: #0f172a;
            --sidebar-bg: #1e293b;
            --primary-accent: #3b82f6;
            --text-main: #f8fafc;
            --text-dim: #94a3b8;
            --msg-user: #3b82f6;
            --msg-ai: #334155;
            --border-color: #334155;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-main);
            height: 100vh;
            display: flex;
            overflow: hidden;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            background-color: var(--sidebar-bg);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
        }

        .new-chat-btn {
            width: 100%;
            padding: 12px;
            background: transparent;
            border: 1px solid var(--border-color);
            color: var(--text-main);
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: background 0.3s;
        }

        .new-chat-btn:hover {
            background: rgba(255,255,255,0.05);
        }

        .chat-list {
            flex-grow: 1;
            overflow-y: auto;
            padding: 10px;
        }

        .chat-item {
            padding: 12px;
            border-radius: 8px;
            cursor: pointer;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9rem;
            color: var(--text-dim);
            transition: all 0.2s;
        }

        .chat-item:hover, .chat-item.active {
            background: rgba(255,255,255,0.1);
            color: var(--text-main);
        }

        /* Main Chat Area */
        .main-chat {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .chat-header {
            padding: 15px 30px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(10px);
            z-index: 10;
        }

        .chat-messages {
            flex-grow: 1;
            overflow-y: auto;
            padding: 40px 10%;
            display: flex;
            flex-direction: column;
            gap: 24px;
            scroll-behavior: smooth;
        }

        .message {
            max-width: 80%;
            display: flex;
            flex-direction: column;
            gap: 8px;
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .message.user {
            align-self: flex-end;
        }

        .message.ai {
            align-self: flex-start;
        }

        .message-content {
            padding: 14px 18px;
            border-radius: 16px;
            line-height: 1.6;
            font-size: 0.95rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .user .message-content {
            background-color: var(--msg-user);
            color: white;
            border-bottom-right-radius: 4px;
        }

        .ai .message-content {
            background-color: var(--msg-ai);
            color: var(--text-main);
            border-bottom-left-radius: 4px;
            border: 1px solid var(--border-color);
        }

        .message-label {
            font-size: 0.75rem;
            color: var(--text-dim);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .user .message-label {
            text-align: right;
        }

        /* Input Area */
        .input-container {
            padding: 30px 10%;
            background: linear-gradient(transparent, var(--bg-dark) 40%);
        }

        .input-wrapper {
            position: relative;
            background: var(--sidebar-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 5px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            transition: border-color 0.3s;
        }

        .input-wrapper:focus-within {
            border-color: var(--primary-accent);
        }

        textarea {
            width: 100%;
            background: transparent;
            border: none;
            color: var(--text-main);
            padding: 12px 50px 12px 12px;
            resize: none;
            outline: none;
            max-height: 200px;
            min-height: 48px;
            font-size: 1rem;
        }

        .send-btn {
            position: absolute;
            right: 10px;
            bottom: 10px;
            background: var(--primary-accent);
            color: white;
            border: none;
            width: 34px;
            height: 34px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s, opacity 0.2s;
        }

        .send-btn:hover {
            transform: scale(1.05);
        }

        .send-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        /* Typing Indicator */
        .typing {
            display: none;
            padding: 14px 18px;
            background: var(--msg-ai);
            border-radius: 16px;
            border-bottom-left-radius: 4px;
            width: fit-content;
            gap: 5px;
            align-items: center;
        }

        .dot {
            width: 6px;
            height: 6px;
            background: var(--text-dim);
            border-radius: 50%;
            animation: bounce 1.4s infinite;
        }

        .dot:nth-child(2) { animation-delay: 0.2s; }
        .dot:nth-child(3) { animation-delay: 0.4s; }

        @keyframes bounce {
            0%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-6px); }
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border-color); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #4b5563; }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header">
            <button class="new-chat-btn">
                <span>+ New Chat</span>
            </button>
        </div>
        <div class="chat-list" id="chatList">
            <div class="chat-item active">
                <span>Current Chat Session</span>
            </div>
        </div>
    </aside>

    <main class="main-chat">
        <header class="chat-header">
            <h3>BootChat AI</h3>
            <span style="color: var(--text-dim); font-size: 0.8rem;">Gemini-2.5 Flash</span>
        </header>

        <div class="chat-messages" id="chatMessages">
            @if(isset($history) && count($history) > 0)
                @foreach($history as $chat)
                    <div class="message {{ $chat->role }}">
                        <span class="message-label">{{ strtoupper($chat->role) }}</span>
                        <div class="message-content">{{ $chat->content }}</div>
                    </div>
                @endforeach
            @else
                <div class="message ai">
                    <span class="message-label">AI</span>
                    <div class="message-content">Hello hossam! I'm your AI assistant. How can I help you today?</div>
                </div>
            @endif
        </div>

        <div class="input-container">
            <div id="typingIndicator" class="typing" style="margin-bottom: 10px;">
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
            </div>
            <div class="input-wrapper">
                <textarea id="userInput" placeholder="Message BootChat..." rows="1"></textarea>
                <button id="sendBtn" class="send-btn" disabled>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                </button>
            </div>
        </div>
    </main>

    <script>
        const chatMessages = document.getElementById('chatMessages');
        const userInput = document.getElementById('userInput');
        const sendBtn = document.getElementById('sendBtn');
        const typingIndicator = document.getElementById('typingIndicator');

        // Manage dynamic textarea height
        userInput.addEventListener('input', () => {
            userInput.style.height = 'auto';
            userInput.style.height = (userInput.scrollHeight) + 'px';
            sendBtn.disabled = !userInput.value.trim();
        });

        userInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        sendBtn.addEventListener('click', sendMessage);

        async function sendMessage() {
            const message = userInput.value.trim();
            if (!message) return;

            // Add user message
            addMessage(message, 'user');
            userInput.value = '';
            userInput.style.height = 'auto';
            sendBtn.disabled = true;
            
            // Show typing indicator
            typingIndicator.style.display = 'flex';
            scrollToBottom();

            try {
                const response = await fetch('/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ message: message })
                });

                const data = await response.json();
                
                typingIndicator.style.display = 'none';
                
                if (data.reply) {
                    addMessage(data.reply, 'ai');
                } else if (data.error) {
                    addMessage('Error: ' + data.error.message, 'ai');
                } else {
                    addMessage('Sorry, something went wrong.', 'ai');
                }
            } catch (error) {
                typingIndicator.style.display = 'none';
                addMessage('Failed to connect to server.', 'ai');
                console.error(error);
            }
            
            scrollToBottom();
        }

        function addMessage(content, role) {
            const msgDiv = document.createElement('div');
            msgDiv.className = `message ${role}`;
            
            const label = document.createElement('span');
            label.className = 'message-label';
            label.textContent = role.toUpperCase();
            
            const contentDiv = document.createElement('div');
            contentDiv.className = 'message-content';
            contentDiv.textContent = content;
            
            msgDiv.appendChild(label);
            msgDiv.appendChild(contentDiv);
            chatMessages.appendChild(msgDiv);
        }

        function scrollToBottom() {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    </script>
</body>
</html>
