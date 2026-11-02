<?php

class AIClassificationService
{
    public function classifyText($text)
    {
        $url = "https://api.openai.com/v1/chat/completions";

        $data = [
            "model" => OPENAI_MODEL,

            "messages" => [
                [
                    "role" => "system",
                    "content" => "You are a text classification assistant.
                    Classify the given text into exactly one of these
                    categories: Complaint, Query, Feedback, Other.

                    Complaint: Negative experience or problem.
                    Query: Asking a question or requesting information.
                    Feedback: Sharing an opinion or suggestion.
                    Other: Text that does not fit the above categories.

                    Return only the requested JSON structure."
                ],
                [
                    "role" => "user",
                    "content" => $text
                ]
            ],

            "response_format" => [
                "type" => "json_schema",
                "json_schema" => [
                    "name" => "text_classification",
                    "strict" => true,
                    "schema" => [
                        "type" => "object",
                        "properties" => [
                            "category" => [
                                "type" => "string",
                                "enum" => [
                                    "Complaint",
                                    "Query",
                                    "Feedback",
                                    "Other"
                                ]
                            ]
                        ],
                        "required" => ["category"],
                        "additionalProperties" => false
                    ]
                ]
            ]
        ];

        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Authorization: Bearer " . OPENAI_API_KEY
            ],
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_TIMEOUT => 30
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);

            throw new Exception("AI service connection failed: " . $error);
        }

        curl_close($ch);

        $result = json_decode($response, true);
if ($httpCode !== 200) {

    $apiError = $result['error']['message']
        ?? 'Unknown OpenAI API error';

    throw new Exception(
        "OpenAI API Error (HTTP $httpCode): " . $apiError
    );
}

        $content = $result['choices'][0]['message']['content'] ?? null;

        if (!$content) {
            throw new Exception("Invalid AI response");
        }

        $classification = json_decode($content, true);

        if (!isset($classification['category'])) {
            throw new Exception("Classification result missing");
        }

        return $classification['category'];
    }
}