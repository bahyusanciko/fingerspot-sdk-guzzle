# Fingerspot SDK for PHP

A PHP SDK for communicating with **Fingerspot attendance devices** using the EasyLink SDK protocol.

...

## 🧪 Usage

use Fingerspot\SDK\FingerspotService;

$fingerspot = new FingerspotService('http://192.168.1.10', 'SN001', 8080);
$info = $fingerspot->getDeviceInfo();
