<?php
// XAMPP defaults: user "root", empty password. Change if yours differs.
const DB_HOST = 'localhost';
const DB_NAME = 'zanp_db';
const DB_USER = 'root';
const DB_PASS = '';
// The folder name inside C:\xampp\htdocs\ (keep the trailing slash).
const BASE_URL = '/zanp/';

$pdo = new PDO(
    'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
    DB_USER, DB_PASS,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES => false]
);
