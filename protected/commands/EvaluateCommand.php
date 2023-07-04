<?php

use MongoDB\BSON\ObjectId;
require_once('/data/live/protected/components/config.php');

class EvaluateCommand extends CConsoleCommand
{

    public function run($args)
    {
        echo "Running\n";
        // $_id = '6480ac78f3ede36d5e0ef412';
        $_id = $args[0];
        $this->getFilesLinks($_id);
    }

    private function getFilesLinks($_id) 
    {
        $_id = new ObjectId($_id);
        $submission = Submissions::model()->findByPk($_id);
        if (!$submission) {
            return;
        }
        $links = $submission->attachments;
        $summary = $this->callBard($links);
        if (!$summary) {
            return;
        }
        $submission->summary = $summary;
        $submission->save();
    }

    private function callBard($links)
    {
        if (count($links) == 0) {
            return false;
        }
        echo "Starting\n";
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "http://" . CURL_IP . ":3000/evaluate",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode([
                'links' => $links,
            ]),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ]);
        $response = curl_exec($curl);
        curl_close($curl);
        echo "got curl response\n";
        $response = str_replace('\n', "\n", $response);
        $response = str_replace('\"', "\"", $response);
        $response = str_replace('`', "'", $response);
        $response = substr($response, 1, -1);
        $filePath = '/data/live/protected/utils/bard_responce.txt';
        file_put_contents($filePath, $response);
        echo "Over\n";
        return $response;
    }
}
