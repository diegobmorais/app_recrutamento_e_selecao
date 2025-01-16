window.onload = startChat;

let sessionId = null;
let threadId = null;
let assistenteId = null;
let tipoTeste = '';
let mensagensEnviadas = 0;

// Iniciar a sessão de chat
async function recuperarAssistente() {
  await salvarParametrosNoLocalStorage();
  const vagaId = sessionStorage.getItem('vagaId')
  const url = window.location.href

  if (url.includes('pre-selecao')) {
    tipoTeste = 'teste_pre_selecao';

  } else if (url.includes('teste-comportamental')) {
    tipoTeste = 'teste_comportamental';

  } else {
    console.error('Tipo de teste não encontrado na URL');
    return;
  }
  const response = await fetch(`<?php echo base_url('assistente/') ?>${vagaId}/${tipoTeste}`, {
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

  if (tipoTeste === 'teste_pre_selecao') {
    // Mensagens para o teste de pré-seleção
    addMensagem(`Olá ${nome}, tudo bem?`, false);
    await sleep(2000);
    addMensagem(`Iremos iniciar seu processo de seleção.`, false);
    await sleep(3000);
    addMensagem(`Logo a seguir iremos enviar algumas perguntas para você responder. Seja o mais verdadeiro possível.`, false);
    await sleep(2000);
    addMensagem(`Está pronto?`, false);
  } else if (tipoTeste === 'teste_comportamental') {
    // Mensagens para o teste comportamental
    addMensagem(`Olá ${nome}, bem-vindo ao teste comportamental.`, false);
    await sleep(2000);
    addMensagem(`Este teste tem como objetivo entender melhor o seu perfil comportamental.`, false);
    await sleep(3000);
    addMensagem(`Por favor, responda às perguntas que enviaremos a seguir. Seja o mais verdadeiro possível.`, false);
    await sleep(2000);
    addMensagem(`Vamos começar?`, false);
  }

  const response = await fetch(`<?php echo base_url('assistente/criar-thread') ?>`, {
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

  if (message === '') {
    console.warn('Mensagem vazia. Não será enviada.');
    return;
  }

  try {
    const response = await fetch(`<?php echo base_url('assistente/enviar-mensagem') ?>`, {
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
      console.error('Erro na resposta', response.statusText);
      return;
    }
    addMensagem(`você: ${message}`, true);
    messageInput.value = '';

    const runId = await executarAssistente(threadId);
    console.log('runId', runId);

    await verificarStatusResposta(threadId, runId);
  } catch (error) {
    console.error('Erro ao enviar mensagem:', error);
    return;
  }
}

async function executarAssistente(threadId) {

  const responseAssistente = await fetch(`<?php echo base_url('assistente/executar-assistente/') ?>${threadId}`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      assistenteId: assistenteId
    })
  });
  console.log('responseAssistente', responseAssistente);

  if (!responseAssistente.ok) {
    console.error('Erro ao executar assistente:', responseAssistente.statusText);
    return;
  }

  const data = await responseAssistente.json();
  console.log('data', data);

  return data.run;
}
//obtem resposta da IA
async function obtemRespostaIA() {
  const response = await fetch(`<?php echo base_url('assistente/obter-resposta/') ?>${threadId}`, {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json'
    }
  });

  if (!response.ok) {
    console.error('Erro ao obter resposta:', response.statusText);
    return;
  }
  const mensagemIA = await response.json();

  return mensagemIA.value
}
// Adicionar mensagens no chatbox
function addMensagem(text, isUser) {
  const chatbox = document.getElementById('chatbox');
  const messageClass = isUser ? 'chat-box-body-send' : 'chat-box-body-receive';
  chatbox.innerHTML += `<p class="${messageClass}">${text}</p>`;
  chatbox.scrollTop = chatbox.scrollHeight;
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
      const responseIA = await fetch(`<?php echo base_url('assistente/recupera-thread/') ?>${threadId}/${runId}`, {
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

      if (resposta.thread.status === 'completed') {
        try {
          const mensagemIA = await obtemRespostaIA();

          if (isFinalEvaluation(mensagemIA)) {
            salvarResumo(mensagemIA);
            addMensagem(`IA: Obrigado, você finalizou seu teste. Fique atento pois retornaremos o contato`, false);

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
  const id_candidato = sessionStorage.getItem('id_candidato')
  try {
    const response = await fetch(`<?php echo base_url('assistente/salvar-resumo/') ?>${id_candidato}/${tipoTeste}`, {
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

  const pathSegments = window.location.pathname.split('/');
  const candidatoId = pathSegments[2];
  const vagaId = pathSegments[3];
  const nomeUsuario = pathSegments[4];

  sessionStorage.setItem('id_candidato', candidatoId);
  sessionStorage.setItem('vagaId', vagaId);
  sessionStorage.setItem('nome', decodeURIComponent(nomeUsuario));
}
//salva mensagens do assistente
function salvarMensagemAssistente(mensagem, isUltima = false) {
  const id_candidato = sessionStorage.getItem('id_candidato')

  if (!id_candidato) {
    console.error('ID do candidato não encontrado no sessionStorage.');
    return;
  }

  fetch(`<?php echo base_url('historico/salvar-mensagem-assistente/') ?>${id_candidato}`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      conteudo: mensagem,
      tipoTeste: tipoTeste
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