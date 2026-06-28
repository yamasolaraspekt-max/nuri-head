<?php
namespace App\Services;

use Generator;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\TransferException;

class OllamaClient
{
    public function __construct(
        private readonly string $host,
        private readonly string $model,
    ) {}

    public static function make(): self
    {
        $raw = config('services.ollama.host', env('OLLAMA_HOST','http://127.0.0.1:11434'));
        $normalized = rtrim(preg_replace('#/api/?$#', '', trim($raw)), '/');

        return new self(
            host: $normalized,
            model: config('services.ollama.model', env('OLLAMA_MODEL', 'llama3.2:3b')),
        );
    }

    /**
     * Stream chat tokens as they arrive.
     * @param array $messages Chat messages
     * @param array $meta     Optional ['rid'=> string] for logging correlation
     */
    public function streamChat(array $messages, array $meta = []): Generator
    {
        $rid = $meta['rid'] ?? substr(bin2hex(random_bytes(4)),0,8);
        $log = function(string $msg, array $ctx = []) use ($rid) {
            Log::info('[OLLAMA '.$rid.'] '.$msg, $ctx + ['rid'=>$rid]);
        };

        $payload = [
            'model'    => $this->model,
            'messages' => $messages,
            'stream'   => true,
            'options'  => [
                'temperature' => 0.2,
                'num_ctx'     => (int) env('AI_MAX_CONTEXT_TOKENS', 8000),
            ],
        ];

        $client = new Client([
            'base_uri' => $this->host,
            'http_errors' => false,
            'timeout' => 0,           // no overall timeout
            'headers' => ['Accept' => 'application/x-ndjson'],
        ]);

        // Try /api/chat (NDJSON with message.content)
        try {
            $log('POST /api/chat', ['model'=>$this->model]);
            $res = $client->request('POST', '/api/chat', ['json' => $payload, 'stream' => true]);
            $code = $res->getStatusCode();
            if ($code === 404) {
                $log('/api/chat 404, falling back to /api/generate');
                return $this->streamGenerate($client, $messages, $meta);
            }
            if ($code < 200 || $code >= 300) {
                $body = (string) $res->getBody();
                throw new \RuntimeException("Ollama /api/chat HTTP $code: ".substr($body,0,200));
            }

            $body   = $res->getBody(); // Psr7 Stream
            $buffer = '';
            $first  = true;
            while (!$body->eof()) {
                $chunk = $body->read(16384);
                if ($chunk === '') { usleep(20_000); continue; }
                $buffer .= $chunk;

                // NDJSON -> split by LF
                while (($pos = strpos($buffer, "\n")) !== false) {
                    $line = trim(substr($buffer, 0, $pos));
                    $buffer = substr($buffer, $pos + 1);
                    if ($line === '') continue;

                    $data = json_decode($line, true);
                    if (!is_array($data)) continue;

                    if (isset($data['message']['content'])) {
                        if ($first) { $log('first_token'); $first = false; }
                        yield (string) $data['message']['content'];
                    }
                }
            }
            return;
        } catch (ClientException $e) {
            throw $e;
        } catch (TransferException $e) {
            $log('transfer_exception', ['err'=>$e->getMessage()]);
            throw $e;
        }
    }

    private function streamGenerate(Client $client, array $messages, array $meta = []): Generator
    {
        $rid = $meta['rid'] ?? 'gen';
        $log = function(string $msg, array $ctx = []) use ($rid) {
            Log::info('[OLLAMA '.$rid.'] '.$msg, $ctx + ['rid'=>$rid]);
        };

        // Flatten to prompt
        $out = [];
        foreach ($messages as $m) {
            $role = strtoupper($m['role'] ?? 'USER');
            $out[] = "[$role]\n".($m['content'] ?? '');
        }
        $out[] = "[ASSISTANT]\n";
        $prompt = implode("\n\n", $out);

        $payload = [
            'model'   => $this->model,
            'prompt'  => $prompt,
            'stream'  => true,
            'options' => [
                'temperature' => 0.2,
                'num_ctx'     => (int) env('AI_MAX_CONTEXT_TOKENS', 8000),
            ],
        ];

        $log('POST /api/generate', ['model'=>$this->model]);
        $res = $client->request('POST', '/api/generate', ['json' => $payload, 'stream' => true]);
        $code = $res->getStatusCode();
        if ($code < 200 || $code >= 300) {
            $body = (string) $res->getBody();
            throw new \RuntimeException("Ollama /api/generate HTTP $code: ".substr($body,0,200));
        }

        $body   = $res->getBody();
        $buffer = '';
        $first  = true;
        while (!$body->eof()) {
            $chunk = $body->read(16384);
            if ($chunk === '') { usleep(20_000); continue; }
            $buffer .= $chunk;

            while (($pos = strpos($buffer, "\n")) !== false) {
                $line = trim(substr($buffer, 0, $pos));
                $buffer = substr($buffer, $pos + 1);
                if ($line === '') continue;

                $data = json_decode($line, true);
                if (!is_array($data)) continue;

                if (isset($data['response'])) {
                    if ($first) { $log('first_token'); $first = false; }
                    yield (string) $data['response'];
                }
            }
        }
    }
}
