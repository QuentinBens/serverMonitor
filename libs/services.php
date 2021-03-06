<?php
require '../autoload.php';
$Config = new Config();


$datas = array();

$available_protocols = array('tcp', 'udp', 'other');

$show_port = $Config->get('services:show_port');

if (count($Config->get('services:list')) > 0)
{
    foreach ($Config->get('services:list') as $service)
    {
        $host     = $service['host'];
        $port     = $service['port'];
        $name     = $service['name'];
        $protocol = isset($service['protocol']) && in_array($service['protocol'], $available_protocols) ? $service['protocol'] : 'tcp';

        if ($protocol === 'other') {
            $pidJdownloader = file_get_contents($service['pid_path']);

            if (Misc::isPidRunning($pidJdownloader)) {
                $datas[] = [
                    'show_port' => false,
                    'port'      => 'N/A',
                    'name'      => $name,
                    'status'    => 1,
                ];
            } else {
                $datas[] = [
                    'show_port' => false,
                    'port'      => 'N/A',
                    'name'      => $name,
                    'status'    => 0,
                ];
            }
        } else {
            if (Misc::scanPort($host, $port, $protocol)) {
                $status = 1;
            } else {
                $status = 0;
            }

            $datas[] = array(
                'port'      => $show_port === true ? $port : '',
                'name'      => $name,
                'status'    => $status,
            );
        }
    }
}

echo json_encode($datas);