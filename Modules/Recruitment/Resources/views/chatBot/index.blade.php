<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot</title>
    <link rel="stylesheet" href="{{ asset('Modules/Recruitment/Resources/assets/css/chatbot.css') }}">
</head>
<body>
    <div id="chatbox" class="chat-container">
        <div id="chatHeader" class="chat-header d-flex justify-content-between mb-3">
            <span id="creationDate" class="chat-date"></span>
            <span id="timer" class="chat-timer">00:00</span>
        </div>

        <div class="chat-title chat-box-header text-center mb-3">
            <h4>Assistente Virtual</h4>
        </div>

        <div id="chat-mensagens" class="chat-messages" style="height: 300px; overflow-y: scroll;">
            <!-- Mensagens serão adicionadas aqui -->
        </div>

        <div class="chat-footer d-flex gap-2" id="chatFooter">
            <input type="text" id="messageInput" class="form-control chat-input" placeholder="Digite sua resposta..."
                style="flex: 1;" />
            <button type="button" class="chat-send-btn" id="sendBtn">Enviar</button>
        </div>
    </div>
    <script src="{{ asset('Modules/Recruitment/Resources/assets/js/chatbot.js') }}"></script>
</body>
</html>
