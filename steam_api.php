<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Configuration
$steamAPIKey = 'AC5D04FA890C7B417618838B8034C312';
$staffConfig = [
    [
        'steamid' => '76561199511612568',
        'role' => 'Fondateur'
    ],
    [
        'steamid' => '76561198xxxxxxxxx',
        'role' => 'Admin'
    ],
    [
        'steamid' => '76561198xxxxxxxxx',
        'role' => 'Modérateur'
    ]
];

// Récupérer les SteamIDs
$steamids = array_column($staffConfig, 'steamid');
$steamids_string = implode(',', $steamids);

// Appel API Steam
$url = "https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v2/?key={$steamAPIKey}&steamids={$steamids_string}";
$response = file_get_contents($url);
$data = json_decode($response, true);

// Combiner les données Steam avec les rôles
$staff_data = [];
if(isset($data['response']['players'])) {
    foreach($data['response']['players'] as $player) {
        foreach($staffConfig as $staff) {
            if($staff['steamid'] === $player['steamid']) {
                $staff_data[] = [
                    'name' => $player['personaname'],
                    'avatar' => $player['avatarmedium'],
                    'role' => $staff['role']
                ];
                break;
            }
        }
    }
}

echo json_encode($staff_data);
?>
