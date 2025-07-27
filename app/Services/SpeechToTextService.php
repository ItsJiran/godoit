<?php

namespace App\Services;

use Google\Cloud\Speech\V1\SpeechClient;
use Google\Cloud\Speech\V1\AudioEncoding;
use Google\Cloud\Speech\V1\RecognitionConfig;
use Google\Cloud\Speech\V1\RecognitionAudio;

class SpeechToTextService
{
    private $client;
    
    public function __construct()
    {
        $this->client = new SpeechClient([
            'keyFilePath' => storage_path('app/google-credentials.json')
        ]);
    }
    
    public function transcribe($audioData)
    {
        $config = new RecognitionConfig([
            'encoding' => AudioEncoding::WEBM_OPUS,
            'sample_rate_hertz' => 48000,
            'language_code' => 'id-ID', // Indonesian
        ]);
        
        $audio = new RecognitionAudio([
            'content' => $audioData,
        ]);
        
        $response = $this->client->recognize($config, $audio);
        
        foreach ($response->getResults() as $result) {
            return $result->getAlternatives()[0]->getTranscript();
        }
        
        return null;
    }
}