<?php

namespace App\Jobs;

use App\Models\Analysis;
use App\Models\File;
use App\Models\Parameter;
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
use OpenAI\Laravel\Facades\OpenAI;

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

            if (Parameter::val('OPENAI_API') === 'true') {
                $this->openai($code);
            }

            if (Parameter::val('GEMINI_API') === 'true') {
                $this->gemini($code);
            }
        }
    }

    private function gemini($code): void
    {
        $result = "";
        if (!empty($code) && $prompt = Parameter::val('PROMPT')) {
            $client = \Gemini::client(Parameter::val('GEMINI_API_KEY'));
            $input_tokens = $client->geminiPro()->countTokens($code)->totalTokens;
            $response = $client
                ->geminiPro()
                ->withSafetySetting(
                    new SafetySetting(
                        category: HarmCategory::HARM_CATEGORY_DANGEROUS_CONTENT,
                        threshold: HarmBlockThreshold::BLOCK_NONE
                    )
                )
                ->generateContent(str_replace(":code", $code, $prompt));
            $response = str_replace(["```json", "```"], "", $response->text());
            Log::info("Gemini\n{$response}");

            $output_tokens = $client->geminiPro()->countTokens($response)->totalTokens;
            $this->analysis->gemini_tokens = ($this->analysis->gemini_tokens ?? 0) + ($input_tokens ?? 0) + ($output_tokens ?? 0);
            $this->analysis->save();

            $json = json_decode($response, true);
            $this->processResponse('GEMINI API', $json);

        }
    }

    private function openai($code): void
    {
        $result = "";
        if (!empty($code) && $prompt = Parameter::val('PROMPT')) {
            $result = OpenAI::chat()->create([
                'model' => Parameter::val('OPENAI_MODEL'),
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => str_replace(":code", $code, $prompt)
                    ],
                ],
            ]);
            $response = str_replace(["```json", "```"], "", $result->choices[0]->message->content);
            Log::info("OpenAI\n{$response}");

            $this->analysis->openai_tokens = ($this->analysis->openai_tokens ?? 0) + $result->usage->totalTokens;
            $this->analysis->save();

            $json = json_decode($response, true);
            $this->processResponse('OPENAI API', $json);
        }
    }

    private function processResponse($method, $json): void
    {
        if ($json) {
            foreach ($json as $item) {
                if ($item['Severidad'] != 'N/A') {
                    $vulnerability = new Vulnerability([
                        'analysis_id' => $this->analysis->id,
                        'file_id' => $this->file->id,
                        'method' => $method,
                        'name' => $item['Nombre de vulnerabilidad'],
                        'description' => $item['Descripción'],
                        'lines' => $item['Línea de código'],
                        'severity' => ucfirst($item['Severidad']),
                        'impact' => $item['Impacto'],
                        'cwe' => $item['CWE'],
                        'cve' => $item['CVE'],
                        'solution' => $item['Solución'],
                        'mitigation' => $item['Mitigación']
                    ]);
                    $vulnerability->save();
                }
            }
        }

        Log::info('', ['file' => $this->file->path]);
        $this->file->analyzed_at = now();
        $this->file->save();
    }
}
