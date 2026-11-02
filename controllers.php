<?php

require_once __DIR__ . '/services.php';
class ClassificationController
{
    public function classify()
    {
        header("Content-Type: application/json");

        try {

            $input = json_decode(file_get_contents("php://input"), true);

            if (!is_array($input) || !isset($input['text']) || !is_string($input['text'])) {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "message" => "Text field is required"
                ]);

                return;
            }

            $text = trim($input['text']);

            if ($text === '') {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "message" => "Text cannot be empty"
                ]);

                return;
            }

            if (strlen($text) > 5000) {

                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "message" => "Text must not exceed 5000 characters"
                ]);

                return;
            }

            $service = new AIClassificationService();

            $category = $service->classifyText($text);

            http_response_code(200);

            echo json_encode([
                "success" => true,
                "input" => $text,
                "category" => $category,
                "message" => "Text classified successfully"
            ]);

        } catch (Exception $e) {

            http_response_code(500);

            echo json_encode([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }
}