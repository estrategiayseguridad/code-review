<?php

namespace App\Jobs;

use App\Models\Analysis;
use App\Models\File;
use App\Models\Vulnerability;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Gemini\Enums\HarmBlockThreshold;
use Gemini\Data\SafetySetting;
use Gemini\Enums\HarmCategory;

class ProcessAnalysis implements ShouldQueue, ShouldBeEncrypted
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private File $file;
    private Analysis $analysis;

    /**
     * Create a new job instance.
     */
    public function __construct($analysisId, $fileId)
    {
        $this->analysis = Analysis::FindOrFail($analysisId);
        $this->file = File::findOrFail($fileId);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->analysis && $this->file) {
            $code = Storage::disk('projects')->get($this->file->path);

            Log::info('', ['time' => now(), 'path' => $this->file->path]);
            $this->file->analyzed_at = now();
            $this->file->save();

            $response = $this->sendPrompt($code);

            $this->analysis->jobs -= 1;
            $this->analysis->save();

            if ($response) {
                $results = json_decode($response, true);
                if ($results) {
                    foreach ($results as $item) {
                        $vulnerability = new Vulnerability([
                            'analysis_id' => $this->analysis->id,
                            'file_id' => $this->file->id,
                            'name' => $item['Nombre de vulnerabilidad'],
                            'description' => $item['Descripción'],
                            'lines' => $item['Línea de código'],
                            'severity' => $item['Severidad'],
                            'impact' => $item['Impacto'],
                            'cwe' => $item['CWE'],
                            'cve' => $item['CVE'],
                            'solution' => $item['Solución']
                        ]);
                        $vulnerability->save();
                    }
                    $this->file->response = $response;
                    $this->file->save();
                }
            }
        }
    }

    private function sendPrompt($code)
    {
        $result = "";
        if (!empty($code)) {
            $client = \Gemini::client(config('gemini.api_key'));
            $count = $client->geminiPro()->countTokens($code);
            $tokens = $count->totalTokens;
            $response = $client
                ->geminiPro()
                ->withSafetySetting(
                    new SafetySetting(
                        category: HarmCategory::HARM_CATEGORY_DANGEROUS_CONTENT,
                        threshold: HarmBlockThreshold::BLOCK_NONE
                    )
                )
                ->generateContent("Identificar vulnerabilidades del OWASP Top 10-2021 en el código siguiente. Responder en español. No dar explicaciones. No dar comentarios. Proporcionar los datos de cada vulnerabilidad detectada en código json con la siguiente estructura: | Nombre de vulnerabilidad | Descripción | Línea de código | Severidad | Impacto | CWE | CVE | Solución |\n\n" . $code . "\n\nLa descripción no debe superar 500 caracteres.\n\nLos valores de la severidad solo pueden ser: alta, media o baja.\n\nDevolver json.");
            $result = str_replace(["```json\n", "```"], '', $response->text());
        }
        return $result;
    }
}
