<?php

 namespace App\Services;

use Illuminate\Support\Facades\Http;

class EmbeddingClient
{
    public function __construct(
        private readonly string $host,
        private readonly string $model,
    ) {}

    public static function make(): self
    {
        return new self(
            host: config('services.ollama.host', env('OLLAMA_HOST','http://127.0.0.1:11434')),
            model: config('services.ollama.embed_model', env('OLLAMA_EMBED_MODEL','mxbai-embed-large')),
        );
    }

    /** @return float[] */
    public function embed(string $text): array
    {
        $res = Http::post(rtrim($this->host,'/').'/api/embeddings', [
            'model' => $this->model,
            'input' => $text,
        ])->throw()->json();

        return $res['embedding'] ?? [];
    }

    /** cosine similarity */
    public static function cosine(array $a, array $b): float
    {
        if (!$a || !$b || count($a) !== count($b)) return 0.0;
        $dot = 0.0; $na = 0.0; $nb = 0.0;
        $n = count($a);
        for ($i=0; $i<$n; $i++) { $dot += $a[$i]*$b[$i]; $na += $a[$i]**2; $nb += $b[$i]**2; }
        if ($na == 0.0 || $nb == 0.0) return 0.0;
        return $dot / (sqrt($na)*sqrt($nb));
    }
}
