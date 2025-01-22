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
        let sessionId = null;
        let threadId = null;
        let assistenteId = null;
        let tipoTeste = '';
        let mensagensEnviadas = 0;

        function buildRoute(template, params) {
            return template.replace(/:([a-zA-Z_]+)/g, (_, key) => {
                if (params[key] !== undefined) {
                    return encodeURIComponent(params[key]);
                }
                throw new Error(`Missing parameter "${key}" for route`);
            });
        }

        startChat();
        // Iniciar a sessão de chat
        async function recuperarAssistente() {
            await salvarParametrosNoLocalStorage();
            const vagaId = sessionStorage.getItem('vagaId')
            let tipoTeste = sessionStorage.getItem('tipoTeste')

            if (tipoTeste == 'pre_selection') {
                tipoTeste = 'pre-selection';

            } else if (tipoTeste == 'behavioral_test') {
                tipoTeste = 'behavioral-test';

            } else {
                console.error('Tipo de teste não encontrado na URL');
                return;
            }
            const url = buildRoute(routes.getAssistant, {
                vagaId,
                tipoTeste
            });
           
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                },
            });

            if (!response.ok) {
                throw new Error('Erro na resposta');
            }
            const data = await response.json();
            assistenteId = data.assistenteId;

            return assistenteId
        }
        async function startChat() {
            await recuperarAssistente();
            await salvarParametrosNoLocalStorage();

            const id_candidato = sessionStorage.getItem('id_candidato')
            const nome = sessionStorage.getItem('nome')
            const tipoTeste = sessionStorage.getItem('tipoTeste')          

            if (tipoTeste === 'pre_selection') {
                addMensagem(`Olá ${nome}, tudo bem?`, false);
                await sleep(2000);
                addMensagem(`Iremos iniciar seu processo de seleção.`, false);
                await sleep(3000);
                addMensagem(
                    `Logo a seguir iremos enviar algumas perguntas para você responder. Seja o mais verdadeiro possível.`,
                    false);
                await sleep(2000);
                addMensagem(`Está pronto?`, false);
            } else if (tipoTeste === 'behavioral_test') {
                addMensagem(`Olá ${nome}, bem-vindo ao teste comportamental.`, false);
                await sleep(2000);
                addMensagem(`Este teste tem como objetivo entender melhor o seu perfil comportamental.`, false);
                await sleep(3000);
                addMensagem(
                    `Por favor, responda às perguntas que enviaremos a seguir. Seja o mais verdadeiro possível.`,
                    false);
                await sleep(2000);
                addMensagem(`Vamos começar?`, false);
            }
            const url = buildRoute(routes.createThread);
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
            });
            if (!response.ok) {
                throw new Error('Erro na resposta');
            }
            const data = await response.json();
            threadId = data.threadId;

            sessionStorage.setItem('threadId', threadId);
        }
        // Enviar mensagem para o assistente
        async function enviarMensagem() {
            const messageInput = document.getElementById('messageInput');
            const message = messageInput.value.trim();
            const vagaId = sessionStorage.getItem('vagaId');
            const threadId = sessionStorage.getItem('threadId');
            const id_candidato = sessionStorage.getItem('id_candidato');
            const tipoTeste = sessionStorage.getItem('tipoTeste');

            if (message === '') {
                console.warn('Mensagem vazia. Não será enviada.');
                return;
            }         

            try {
                const url = buildRoute(routes.sendMessage);
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        message: message,
                        threadId: threadId,
                        candidateId: id_candidato,
                        testType: tipoTeste
                    })
                });
                if (!response.ok) {
                    console.error('Erro na resposta', response.statusText);
                    return;
                }
                addMensagem(`você: ${message}`, true);
                messageInput.value = '';

                const runId = await executarAssistente(threadId);             

                await verificarStatusResposta(threadId, runId);
            } catch (error) {
                console.error('Erro ao enviar mensagem:', error);
                return;
            }
        }

        async function executarAssistente(threadId) { 
            const assistenteId = sessionStorage.getItem('assistantId');
            const url = buildRoute(routes.runAssistant, {
                threadId
            });
            try {
                const responseAssistente = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        assistantId: assistenteId
                    })
                });
              
                if (!responseAssistente.ok) {
                    console.error('Erro ao executar assistente:', responseAssistente.statusText);
                    return;
                }

                const data = await responseAssistente.json();
              
                return data.runId;
            } catch (error) {
                console.error('Erro ao executar assistente', error);
            }
        }
        //obtem resposta da IA
        async function obtemRespostaIA() {
            const url = buildRoute(routes.getResponse, {
                threadId
            });            
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            console.log('Response:', response);            
            if (!response.ok) {
                console.error('Erro ao obter resposta:', response.statusText);
                return;
            }
            const mensagemIA = await response.json();
            console.log('Resposta da IA (obterResposta()):', mensagemIA);            
            return mensagemIA.response;
        }
        // Adicionar mensagens no chatbox
        function addMensagem(text, isUser) {
            const chatMensagens = document.getElementById('chat-mensagens');
            const messageClass = isUser ? 'chat-box-body-send' : 'chat-box-body-receive';
            chatMensagens.innerHTML += `<p class="${messageClass}">${text}</p>`;
            chatMensagens.scrollTop = chatMensagens.scrollHeight;
        }
        // Eventos
        document.getElementById('sendBtn').addEventListener('click', enviarMensagem);
        // Função para extrair texto dos richText
        function extractTextFromRichText(richTextArray) {
            let text = '';
            richTextArray.forEach(item => {
                if (item.children) {
                    text += extractTextFromRichText(item.children);
                } else if (item.text) {
                    text += item.text;
                }
            });
            return text;

        }
        // verifica status da resposta IA
        async function verificarStatusResposta(threadId, runId) {

            if (!runId) {
                console.error('Run ID está indefinido. Abortando verificação.');
                return;
            }

            while (true) {
                try {
                    const url = buildRoute(routes.recoverThread, {
                        threadId,
                        runId
                    });
                    const responseIA = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                    });

                    if (!responseIA.ok) {
                        console.error('Erro ao executar assistente:', responseIA.statusText);
                        return;
                    }

                    const resposta = await responseIA.json();
                    console.log('Resposta da IA:', resposta);
                    if (resposta.thread.status === 'completed') {
                        try {
                            const mensagemIA = await obtemRespostaIA();

                            if (isFinalEvaluation(mensagemIA)) {
                                salvarResumo(mensagemIA);
                                addMensagem(
                                    `IA: Obrigado, você finalizou seu teste. Fique atento pois retornaremos o contato`,
                                    false);

                                //desabilita envio de msg apos completar teste
                                document.getElementById('messageInput').disabled = true;
                                document.getElementById('sendBtn').disabled = true;
                            } else {
                                salvarMensagemAssistente(mensagemIA);
                                addMensagem(`IA: ${mensagemIA}`, false);
                            }
                        } catch (error) {
                            console.error('Erro ao obter resposta:', error);
                        }
                        break;
                    }
                } catch (error) {
                    console.error('Erro ao executar assistente:', error);
                    break;
                }
                await new Promise(resolve => setTimeout(resolve, 2000));
            }
        }

        //verifica se esta na avaliação final
        function isFinalEvaluation(message) {
            return message.includes("nota", "resumo");
        }

        function sleep(ms) {
            return new Promise(resolve => setTimeout(resolve, ms));
        }
        //salva resumo no bd
        async function salvarResumo(mensagemIA) {
            const candidateId = sessionStorage.getItem('id_candidato')
            const testType = sessionStorage.getItem('tipoTeste')
            try {
                const url = buildRoute(routes.saveSummary, {
                    candidateId,
                    testType
                });
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        mensagemIA: mensagemIA
                    })
                });
                if (!response.ok) {
                    const errorMessage = await response.text();
                    console.error('Erro ao salvar resumo:', errorMessage);
                    return;
                }
                console.log('Resumo salvo com sucesso!');
            } catch (error) {
                console.error('Erro na requisição:', error);
            }
        }
        // Função para capturar os parâmetros da URL
        function salvarParametrosNoLocalStorage() {

            const urlParams = new URLSearchParams(window.location.search);
            const candidatoId = urlParams.get('jobApplicationId');
            const vagaId = urlParams.get('jobId');
            const nomeUsuario = urlParams.get('name');
            const tipoTeste = urlParams.get('testType');
            const assistantId = urlParams.get('assistantId');

            if (!candidatoId || !vagaId || !nomeUsuario || !tipoTeste) {
                console.error('Parâmetros ausentes na URL.');
                return;
            }

            sessionStorage.setItem('id_candidato', candidatoId);
            sessionStorage.setItem('vagaId', vagaId);
            sessionStorage.setItem('nome', decodeURIComponent(nomeUsuario));
            sessionStorage.setItem('tipoTeste', tipoTeste);
            sessionStorage.setItem('assistantId', assistantId)
        }
        //salva mensagens do assistente
        function salvarMensagemAssistente(mensagem, isUltima = false) {
            const id_candidato = sessionStorage.getItem('id_candidato')
            const tipoTeste = sessionStorage.getItem('tipoTeste')
            const sender = 'candidato'
            console.log('ID do candidato:', id_candidato, tipoTeste, sender, mensagem);
            
            if (!id_candidato) {
                console.error('ID do candidato não encontrado no sessionStorage.');
                return;
            }
            const url = buildRoute(routes.saveHistory);
            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        job_application_id: id_candidato,
                        sender: 'candidato',
                        content: mensagem,
                        test_type: tipoTeste
                    })
                })
                .then(response => response.json())
                .catch(error => {
                    console.error('Erro ao salvar mensagem:', error);
                })
                .catch(error => {
                    console.error('Erro ao salvar mensagem do assistente:', error);
                });
        }
    </script> 
</body>

</html>
