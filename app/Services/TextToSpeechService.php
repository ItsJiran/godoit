<?php

namespace App\Services;

use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;
use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;

class TextToSpeechService
{
    private $client;
    
    public function __construct()
    {
        $this->client = new TextToSpeechClient([
            'keyFilePath' => storage_path('app/google-credentials.json')
        ]);
    }
    
    public function synthesize($text, $voiceType = 'id-ID-Standard-A')
    {
        $input = new SynthesisInput(['text' => $text]);
        
        $voice = new VoiceSelectionParams([
            'language_code' => 'id-ID',
            'name' => $voiceType,
        ]);
        
        $audioConfig = new AudioConfig([
            'audio_encoding' => AudioEncoding::MP3,
            'speaking_rate' => 1.0,
            'pitch' => 0.0,
        ]);
        
        $response = $this->client->synthesizeSpeech($input, $voice, $audioConfig);
        
        return $response->getAudioContent();
    }
}