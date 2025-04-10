<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Activer l'affichage des erreurs pour le debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuration
$steamAPIKey = 'AC5D04FA890C7B417618838B8034C312'; // Remplace par ta vraie clé API Steam
$staffConfig = [
    [
        'steamid' => '76561199511612568',
        'role' => 'Fondateur'
    ]
];

try {
    // Récupérer les SteamIDs
    $steamids = array_column($staffConfig, 'steamid');
    $steamids_string = implode(',', $steamids);

    // Appel API Steam
    $url = "https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v2/?key={$steamAPIKey}&steamids={$steamids_string}";
    
    // Utiliser cURL au lieu de file_get_contents
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    
    if(curl_errno($ch)) {
        throw new Exception('Curl error: ' . curl_error($ch));
    }
    
    curl_close($ch);

    $data = json_decode($response, true);

    if (!$data) {
        throw new Exception('Failed to decode JSON response');
    }

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

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}
?>
