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
    <script>
        const routes = {
            getResponse: "{{ route('assistants.getResponse', ['threadId' => ':threadId']) }}",
            getAssistant: "{{ route('assistants.getAssistant', ['vagaId' => ':vagaId', 'tipoTeste' => ':tipoTeste']) }}",
            createThread: "{{ route('assistants.createThread') }}",
            sendMessage: "{{ route('assistants.sendMessage') }}",
            runAssistant: "{{ route('assistants.run', ['threadId' => ':threadId']) }}",
            recoverThread: "{{ route('assistants.recoverThread', ['threadId' => ':threadId', 'runId' => ':runId']) }}",
            saveSummary: "{{ route('assistants.saveSummary', ['candidateId' => ':candidateId', 'testType' => ':testType']) }}",
            saveHistory: "{{ route('assistants.saveHistory') }}",
        };
    </script>
    <script>
        let currentQuestionIndex = 0;
        let perguntas = [];
        let isFinalEvaluation = false;
        let threadId = null;
        let assistenteId = null;

        startChat();

        function buildRoute(template, params) {
            return template.replace(/:([a-zA-Z_]+)/g, (_, key) => {
                if (params[key] !== undefined) {
                    return encodeURIComponent(params[key]);
                }
                throw new Error(`Missing parameter "${key}" for route`);
            });
        }
        // Inicializa o chat e inicia o processo
        async function startChat() {
            try {
                await salvarParametrosNoLocalStorage();
                await recuperarAssistente();
                iniciarConversacaoPadrao();
                await criarThread();
            } catch (error) {
                console.error('Erro ao iniciar o chat:', error);
            }
        }
        // Recupera dados do assistente com base no tipo de teste
        async function recuperarAssistente() {
            const vagaId = sessionStorage.getItem('vagaId');
            const tipoTeste = sessionStorage.getItem('tipoTeste');

            if (!vagaId || !tipoTeste) {
                throw new Error('Parâmetros "vagaId" ou "tipoTeste" ausentes no sessionStorage.');
            }

            const url = buildRoute(routes.getAssistant, {
                vagaId,
                tipoTeste
            });
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('Erro ao recuperar assistente.');
            }

            const data = await response.json();

            assistenteId = data.assistenteId;
        }
        // Salva parâmetros da URL no sessionStorage
        function salvarParametrosNoLocalStorage() {
            const urlParams = new URLSearchParams(window.location.search);
            const candidatoId = urlParams.get('jobApplicationId');
            const vagaId = urlParams.get('jobId');
            const nomeUsuario = urlParams.get('name');
            const tipoTeste = urlParams.get('testType');
            const assistenteId = urlParams.get('assistantId');

            if (!candidatoId || !vagaId || !nomeUsuario || !tipoTeste) {
                throw new Error('Parâmetros ausentes na URL.');
            }
            sessionStorage.setItem('assistantId', assistenteId);
            sessionStorage.setItem('id_candidato', candidatoId);
            sessionStorage.setItem('vagaId', vagaId);
            sessionStorage.setItem('nome', decodeURIComponent(nomeUsuario));
            sessionStorage.setItem('tipoTeste', tipoTeste);
        }
        // Inicia a conversa com mensagens padrão
        function iniciarConversacaoPadrao() {
            const tipoTeste = sessionStorage.getItem('tipoTeste');
            const nome = sessionStorage.getItem('nome');

            if (tipoTeste === 'pre-selection') {
                setTimeout(() => addMensagem(`Olá ${nome}, tudo bem?`, false), 1000);
                setTimeout(() => addMensagem('Iremos iniciar seu processo de seleção.', false), 2000);
                setTimeout(() => addMensagem(
                    'Logo a seguir iremos enviar algumas perguntas para você responder. Seja o mais verdadeiro possível.',
                    false), 2000);
                setTimeout(() => addMensagem('Está pronto?', false), 2000);
            } else if (tipoTeste === 'behavioral-test') {
                setTimeout(() => addMensagem(`Olá ${nome}, bem-vindo ao teste comportamental.`, false), 1000);
                setTimeout(() => addMensagem('Este teste tem como objetivo entender melhor o seu perfil comportamental.',
                    false), 2000);
                setTimeout(() => addMensagem(
                    'Por favor, responda às perguntas que enviaremos a seguir. Seja o mais verdadeiro possível.',
                    false), 2000);
                setTimeout(() => addMensagem('Vamos começar?', false), 2000);
            }
        }
        // Cria uma nova thread no backend
        async function criarThread() {
            const url = routes.createThread;
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('Erro ao criar thread.');
            }

            const data = await response.json();
            threadId = data.threadId;
            console.log('Thread ID:', threadId);
            sessionStorage.setItem('threadId', threadId);
        }
        // Salva mensagem no banco
        async function salvarMensagemNoBanco(autor, mensagem) {
            const id_candidato = sessionStorage.getItem('id_candidato');
            const tipoTeste = sessionStorage.getItem('tipoTeste');
            const url = routes.saveHistory;

            try {
                await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        job_application_id: id_candidato,
                        sender: autor,
                        content: mensagem,
                        test_type: tipoTeste,
                    }),
                });
            } catch (error) {
                console.error('Erro ao salvar mensagem no banco:', error);
            }
        }
        // Envia mensagem do usuário e salva no banco
        async function enviarMensagem() {
            const input = document.getElementById('messageInput');
            const message = input.value.trim();
            const id_candidato = sessionStorage.getItem('id_candidato');
            const tipoTeste = sessionStorage.getItem('tipoTeste');
            const threadId = sessionStorage.getItem('threadId');
            try {
                const url = buildRoute(routes.sendMessage, {
                    threadId
                })
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        message: message,
                        threadId: threadId,
                        id_candidato: id_candidato,
                        tipoTeste: tipoTeste
                    })
                });
                if (!response.ok) {
                    console.error('Erro na resposta', response.statusText)
                    return;
                }

                addMensagem(`Você: ${message}`, true);
                salvarMensagemNoBanco('candidato', message)

                input.value = '';
                await sleep(1000)
                const runId = await executarAssistente(threadId);
                await verificarStatusResposta(runId)
            } catch (error) {
                console.error('Erro ao enviar mensagem:', error)
                return;
            }
        }
        //executa assistente
        async function executarAssistente() {
            const assistantId = sessionStorage.getItem('assistantId');
            const url = buildRoute(routes.runAssistant, {
                threadId
            });
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        assistantId
                    }),
                });
                if (!response.ok) throw new Error('Erro ao executar assistente.');
                const data = await response.json();
                return data.runId;
            } catch (error) {
                console.error('Erro ao executar assistente:', error);
            }
        }
        // Verifica o status da resposta do assistente
        async function verificarStatusResposta(runId) {
            while (true) {
                const url = buildRoute(routes.recoverThread, {
                    threadId,
                    runId
                });
                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    });
                    if (!response.ok) throw new Error('Erro ao recuperar status da thread.');
                    const data = await response.json();

                    if (data.thread.status === 'completed') {
                        const mensagemIA = await obtemRespostaIA();
                        if (mensagemIA.includes('pontuacao') || mensagemIA.includes('resumo') || mensagemIA.includes(
                                'nota')) {
                            finalizarTeste(mensagemIA);
                        } else {
                            addMensagem(`IA: ${mensagemIA}`, false);
                            salvarMensagemNoBanco('assistente', mensagemIA);
                        }
                        break;
                    }
                } catch (error) {
                    console.error('Erro ao verificar status da resposta:', error);
                }
                await sleep(2000);
            }
        }
        // Obtém a resposta do assistente
        async function obtemRespostaIA() {
            const url = buildRoute(routes.getResponse, {
                threadId
            });
            try {
                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });
                if (!response.ok) throw new Error('Erro ao obter resposta da IA.');
                const data = await response.json();
                return data.response;
            } catch (error) {
                console.error('Erro ao obter resposta:', error);
            }
        }
        //adiciona msg no chat
        function addMensagem(text, isUser) {
            const chatMensagens = document.getElementById('chat-mensagens');

            const messageContainer = document.createElement('div');
            messageContainer.classList.add('chat-message', isUser ? 'chat-message-user' : 'chat-message-bot');

            const avatar = document.createElement('div');
            avatar.classList.add('avatar');
            avatar.textContent = isUser ? 'Você' : 'IA';

            const message = document.createElement('div');
            message.classList.add('message');
            message.textContent = text;

            if (isUser) {
                messageContainer.appendChild(message);
                messageContainer.appendChild(avatar);
            } else {
                messageContainer.appendChild(avatar);
                messageContainer.appendChild(message);
            }

            chatMensagens.appendChild(messageContainer);

            chatMensagens.scrollTop = chatMensagens.scrollHeight;
        }
        // Finaliza o teste e salva o resumo
        async function finalizarTeste(mensagemIA) {
            const id_candidato = sessionStorage.getItem('id_candidato');
            const tipoTeste = sessionStorage.getItem('tipoTeste');
            const url = buildRoute(routes.saveSummary, {
                candidateId: id_candidato,
                testType: tipoTeste
            });

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        mensagemIA
                    }),
                });
                if (!response.ok) throw new Error('Erro ao salvar resumo.');
                addMensagem('IA: Teste concluído. Avaliação salva com sucesso.', false);
            } catch (error) {
                console.error('Erro ao finalizar teste:', error);
            }
        }
        // Adiciona eventos
        document.getElementById('sendBtn').addEventListener('click', enviarMensagem);

        function sleep(ms) {
            return new Promise(resolve => setTimeout(resolve, ms));
        }
    </script>

</body>

</html>
