<?php


namespace App;


use Illuminate\Support\Facades\Http;

class UptimeRobot
{
    /**
     * UptimeRobot constructor.

     */
    public function __construct()
    {
        $this->api_address = env('UPTIMEROBOT_API_ADDRESS');
        $this->api_key = env('UPTIMEROBOT_API_KEY');
    }

    /**
     * Make get request to UptimeRobot RPI.
     *
     * @param string $action
     * @param array $data
     * @return array
     */
    protected function getRequest(string $action, array $data = [])
    {
        return Http::get($this->api_address . $action . '?api_key=' . $this->api_key, $data);
    }

    /**
     * Make post request to UptimeRobot RPI.
     *
     * @param string $action
     * @param array $data
     * @return array
     */
    public function postRequest(string $action, array $data = [])
    {
        $data['api_key'] = $this->api_key;
        return Http::asForm()->post($this->api_address . $action, $data);
    }

    public function getAlertContacts(array $data = []): array
    {
        $response =  $this->postRequest('getAlertContacts', $data);

        if (!empty($response['alert_contacts'])) {
            return $response['alert_contacts'];
        } else {
            return [];
        }
    }

    public function getMonitors(array $data = []): array
    {
        $response = $this->postRequest('getMonitors', $data);

        if (!empty($response['monitors'])) {
            return $response['monitors'];
        } else {
            return [];
        }
    }

    public function deleteMonitor($id): bool
    {
        $response = $this->postRequest('deleteMonitor', ['id' => $id]);

        if (!empty($response['stat']) && $response['stat'] == 'ok') {
            return true;
        } else {
            return false;
        }
    }

    public function createMonitor(string $host, string $alert_contacts): int
    {
        $data = [
            'friendly_name' => $host,
            'url' => $host,
            'type' => '4',
            'sub_type' => '99',
            'port' => '30003',
            'alert_contacts' => $alert_contacts,
        ];

        $response = $this->postRequest('newMonitor', $data);

        if (!empty($response['stat']) && $response['stat'] == 'ok') {
            return $response['monitor']['id'];
        } else {
            return 0;
        }
    }
}
