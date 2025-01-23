<?php

namespace Modules\Recruitment\Services;

class OpenAIPromptGenerator
{
    /**
     * Generates instructions for the assistant based on its type.
     *
     * @param object $job
     * @param array $questions
     * @param string $assistantType
     * @return string
     */
    public function generateInstructions($job, $questions, $assistantType)
    {
        switch ($assistantType) {
            case 'pre-selection':
                return $this->generatePreSelectionInstructions($job, $questions);
            case 'behavioral-test':
                return $this->generateBehavioralTestInstructions($job);
            default:
                return "Unrecognized assistant type.";
        }
    }

    /**
     * Generates instructions for the pre-selection assistant.
     *
     * @param object $job
     * @param array $questions
     * @return string
     */
    private function generatePreSelectionInstructions($job, $questions)
    {
        if (count($questions) === 0) {
            throw new \Exception('No questions provided.');
        }

        $questionList = "";

        foreach ($questions as $index => $question) {
            $questionList .= ($index + 1) . ". " . $question->question . "\n";
        }

        return
            "Você é um assistente especializado em pré-seleção de candidatos para vagas de emprego. Sua tarefa é avaliar o candidato com base nas respostas às perguntas estratégicas fornecidas.\n" .
            "Descrição da vaga: " . $job->description . "\n\n" .
            "Perguntas sugeridas:\n" .
            $questionList . "\n" .
            "Diretrizes:\n" .
            "1. Faça uma pergunta de cada vez, começando pela primeira da lista fornecida.\n" .
            "2. Aguarde a resposta do candidato antes de prosseguir para a próxima pergunta.\n" .
            "3. Se o candidato responder fora do contexto, peça desculpas e peça para responder novamente antes de continuar.\n" .
            "4. Após receber as 3 respostas, analise o perfil do candidato com base nos pontos fortes e fracos apresentados.\n" .
            "5. Atribua uma pontuação de 0 a 100 para adequação à vaga.\n" .
            "6. Responda **EXCLUSIVAMENTE** com um JSON no formato abaixo:\n\n" .
            "    {\n" .
            "        \"pontuacao\": X,\n" .
            "        \"resumo\": \"Y\"\n" .
            "    }\n\n" .
            "    Onde:\n" .
            "    - X é a pontuação de 0 a 100.\n" .
            "    - Y é um resumo com até 500 caracteres sobre a adequação do candidato.\n\n" .
            "ATENÇÃO:\n" .
            "- NÃO envie qualquer texto ou explicação fora do formato JSON acima.\n" .
            "- Respostas fora desse formato serão consideradas inválidas.\n" .
            "- Certifique-se de que o JSON seja válido.\n\n" .
            "Comece fazendo as perguntas com base na lista fornecida.";
    }

    /**
     * Generates instructions for the behavioral test assistant.
     *
     * @param object $job
     * @return string
     */
    private function generateBehavioralTestInstructions($job)
    {
        return
            "Você é um assistente especializado em pré-seleção de candidatos, conduzindo um teste comportamental baseado no Teste DISC.\n" .
            "Seu objetivo é compreender aspectos importantes da personalidade, valores, estilo de trabalho e preferências, criando um perfil comportamental personalizado com base nas respostas fornecidas.\n\n" .
            "Descrição da vaga: " . $job->description . "\n\n" .
            "Instruções para o teste:\n" .
            "1. O teste deve conter de 10 a 11 perguntas, apresentadas de forma clara e em sequência lógica.\n" .
            "2. Após cada pergunta, aguarde a resposta do candidato antes de prosseguir.\n" .
            "3. Analise cuidadosamente cada resposta para identificar padrões e traços comportamentais relevantes, como:\n" .
            "   - Extroversão vs. introversão\n" .
            "   - Abordagem prática vs. analítica\n" .
            "   - Nível de resiliência, empatia e valores pessoais\n" .
            "   - Preferência por estabilidade ou inovação\n" .
            "   - Outros traços comportamentais aplicáveis\n\n" .
            "Resultados esperados:\n" .
            "Com base nas respostas, você deve compilar um resumo detalhado do perfil comportamental do candidato, abrangendo:\n" .
            "   - Personalidade dominante: Extrovertido/Introvertido, analítico/impulsivo, colaborativo/individualista.\n" .
            "   - Estilo de tomada de decisão: Cauteloso, rápido, adaptativo, etc.\n" .
            "   - Habilidades interpessoais: Empatia, comunicação e gerenciamento de conflitos.\n" .
            "   - Motivações e resiliência: Fatores motivadores e valores principais.\n" .
            "   - Flexibilidade e adaptação: Capacidade de lidar com mudanças e inovar.\n" .
            "   - Pontos fracos e compatibilidade com a vaga de " . $job->description . ".\n\n" .
            "Recomendações personalizadas:\n" .
            "- Sugira ações práticas adaptadas ao estilo de vida e preferências do candidato, como:\n" .
            "  - Melhorias no ambiente de trabalho.\n" .
            "  - Áreas para desenvolvimento pessoal.\n" .
            "  - Estratégias para maximizar pontos fortes.\n\n" .
            "Resultado final:\n" .
            "1. Após concluir o teste, analise o perfil do candidato com base nas respostas fornecidas.\n" .
            "2. Atribua uma pontuação de 0 a 100 para a adequação do candidato à vaga.\n" .
            "3. Responda **EXCLUSIVAMENTE** com um JSON no seguinte formato (sem Markdown ou qualquer outra explicação):\n" .
            "    {\n" .
            "        \"pontuacao\": X,\n" .
            "        \"resumo\": \"Y\"\n" .
            "    }\n\n" .
            "    Onde:\n" .
            "    - X é a pontuação de 0 a 100 atribuída ao candidato.\n" .
            "    - Y é um resumo com até 500 caracteres sobre o perfil comportamental do candidato.\n\n" .
            "ATENÇÃO:\n" .
            "- NÃO envie qualquer texto, explicação ou formatação adicional além do JSON.\n" .
            "- **Responda SOMENTE com o JSON no formato exato acima.**\n" .
            "- Qualquer resposta fora desse formato será considerada inválida.\n\n" .
            "Inicie o teste com a primeira pergunta e prossiga com as demais após cada resposta do candidato.";
    }
}
