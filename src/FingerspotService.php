<?php

namespace Fingerspot\SDK;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class FingerspotService
{
    protected $ipAddress;
    protected $serialNumber;
    protected $port;
    protected $client;

    public function __construct($ipAddress, $serialNumber, $port)
    {
        $this->ipAddress = $ipAddress;
        $this->serialNumber = $serialNumber;
        $this->port = $port;
        $this->client = new Client([
            'timeout' => 30,
            'verify' => false,
        ]);
    }

    protected function post(string $endpoint, array $params = []): array
    {
        $url = "{$this->ipAddress}:{$this->port}/{$endpoint}";
        $parsed = parse_url($url);

        $scheme = $parsed['scheme'] ?? 'http';
        $host = $parsed['host'] ?? 'localhost';
        $path = $parsed['path'] ?? '';

        $url = "{$scheme}://{$host}:{$this->port}{$path}";

        try {
            $response = $this->client->post($url, [
                'headers' => [
                    'cache-control' => 'no-cache',
                    'content-type' => 'application/x-www-form-urlencoded',
                ],
                'form_params' => $params
            ]);

            $body = $response->getBody()->getContents();
            $json = json_decode($body, true);

            return is_array($json) ? $json : [
                'status' => false,
                'message' => 'Invalid JSON response',
                'raw' => $body
            ];
        } catch (RequestException $e) {
            return [
                'status' => false,
                'message' => 'HTTP Exception: ' . $e->getMessage()
            ];
        }
    }

    public function getDeviceInfo() {
        return $this->post('dev/info', ['sn' => $this->serialNumber]);
    }

    public function downloadAllUsersPaginated() {
        return $this->post('user/all/paging', ['sn' => $this->serialNumber]);
    }

    public function uploadUser(array $params) {
        return $this->post('user/set', array_merge(['sn' => $this->serialNumber], $params));
    }

    public function deleteAllUsers() {
        return $this->post('user/delall', ['sn' => $this->serialNumber]);
    }

    public function deleteUserByPin(string $pin) {
        return $this->post('user/del', ['sn' => $this->serialNumber, 'pin' => $pin]);
    }

    public function downloadAllScanlogPaginated() {
        return $this->post('scanlog/all/paging', ['sn' => $this->serialNumber]);
    }

    public function downloadNewScanlog() {
        return $this->post('scanlog/new', ['sn' => $this->serialNumber]);
    }

    public function deleteScanlog() {
        return $this->post('scanlog/del', ['sn' => $this->serialNumber]);
    }

    public function syncDateTime() {
        return $this->post('dev/settime', ['sn' => $this->serialNumber]);
    }

    public function deleteAdminAccess() {
        return $this->post('dev/deladmin', ['sn' => $this->serialNumber]);
    }

    public function deleteDeviceOperationalLog() {
        return $this->post('log/del', ['sn' => $this->serialNumber]);
    }

    public function initializeDevice() {
        return $this->post('dev/init', ['sn' => $this->serialNumber]);
    }

    public function downloadScanGPS(string $date) {
        return $this->post('scanlog/GPS', [
            'sn' => 0,
            'by_date' => $date
        ]);
    }

    public function getUserCheckLog(array $params = []) {
        $params = array_merge(['sn' => $this->serialNumber], $params);
        return $this->post('user/checklog', $params);
    }
}
