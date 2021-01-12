<?php
return [
    'user' => [
        'label' => 'MENU MANAGEMEN USER',
        'indent' => 0,
        'parent' => ''
    ],
    // =================================================== RULE DATA USER ===================================================
    'userList' => [
        'label' => 'Dapat akses data user',
        'indent' => 1,
        'parent' => 'user'
    ],
    'userAdd' => [
        'label' => 'Dapat tambah data user',
        'indent' => 2,
        'parent' => 'userList'
    ],
    'userEdit' => [
        'label' => 'Dapat edit data user',
        'indent' => 2,
        'parent' => 'userList'
    ],
    'userDelete' => [
        'label' => 'Dapat hapus data user',
        'indent' => 2,
        'parent' => 'userList'
    ],
    // =================================================== RULE LEVEL USER ===================================================
    'levelList' => [
        'label' => 'Dapat akses data level user',
        'indent' => 1,
        'parent' => 'user'
    ],
    'levelAdd' => [
        'label' => 'Dapat tambah data level',
        'indent' => 2,
        'parent' => 'levelList'
    ],
    'levelEdit' => [
        'label' => 'Dapat edit data level',
        'indent' => 2,
        'parent' => 'levelList'
    ],
    'levelDelete' => [
        'label' => 'Dapat hapus data level',
        'indent' => 2,
        'parent' => 'levelList'
    ],
];