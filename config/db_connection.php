<?php
const DB_HOST = 'localhost';
const DB_USER = 'root';
const DB_PASS = '';
const DB_NAME = 'pnp_system';

function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}


// Rank mapping for display
$rank_titles = [
    'Pat' => 'Patrolman/Patrolwoman',
    'PEM' => 'Police Executive Master Sergeant',
    'PMSg' => 'Police Master Sergeant',
    'PCMS' => 'Police Chief Master Sergeant',
    'PSMS' => 'Police Senior Master Sergeant',
    'PEMS' => 'Police Executive Master Sergeant',
    'PCpt' => 'Police Captain',
    'PMaj' => 'Police Major',
    'PLtCol' => 'Police Lieutenant Colonel',
    'PCol' => 'Police Colonel',
    'PBGen' => 'Police Brigadier General',
    'PMGen' => 'Police Major General',
    'PLtGen' => 'Police Lieutenant General',
    'PGen' => 'Police General'
];

function getRankTitle($abbr) {
    global $rank_titles;
    return isset($rank_titles[$abbr]) ? $rank_titles[$abbr] : $abbr;
}
?>