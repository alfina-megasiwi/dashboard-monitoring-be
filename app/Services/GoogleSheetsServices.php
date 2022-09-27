<?php

namespace App\Services;

use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;

class GoogleSheetsServices
{
    public $client, $service, $documentId, $range;

    public function __construct()
    {
        $this->client = $this->getClient();
        $this->service = new Sheets($this->client);
        $this->documentId = "1Bmt7FaHbC0TIxy8JcSkoCir8REvUqRSwhT-gNhmzbgg";
        $this->range = "A2:H";
    }

    public function getClient()
    {
        $tmpclient = new Client();
        $tmpclient->setApplicationName("Dashboard Monitoring");
        $tmpclient->setRedirectUri("http://localhost:8000/googlesheet");
        $tmpclient->setScopes(Sheets::SPREADSHEETS);
        $tmpclient->setAuthConfig("credentials.json");
        $tmpclient->setAccessType("offline");

        return $tmpclient;
    }

    public function readSheet()
    {
        $doc = $this->service->spreadsheets_values->get($this->documentId, $this->range);
        return $doc;
    }
}
