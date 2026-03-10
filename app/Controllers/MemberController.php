<?php

namespace App\Controllers;

class MemberController extends BaseController
{
    public function index(): string
    {
        return view('members/index', [
            'pageTitle' => 'Data Anggota',
            'activeMenu' => 'members',
            'members' => [
                ['initials' => 'GO', 'name' => 'Grace Okafor', 'phone' => '0801-234-5678', 'email' => 'grace@email.com', 'joined' => '2023-01-15', 'active_loans' => 2],
                ['initials' => 'SA', 'name' => 'Samuel Adeyemi', 'phone' => '0802-345-6789', 'email' => 'samuel@email.com', 'joined' => '2023-03-20', 'active_loans' => 1],
                ['initials' => 'BE', 'name' => 'Blessing Eze', 'phone' => '0803-456-7890', 'email' => 'blessing@email.com', 'joined' => '2023-06-10', 'active_loans' => 0],
                ['initials' => 'DM', 'name' => 'David Mensah', 'phone' => '0804-567-8901', 'email' => 'david@email.com', 'joined' => '2023-08-05', 'active_loans' => 1],
                ['initials' => 'RA', 'name' => 'Ruth Amadi', 'phone' => '0805-678-9012', 'email' => 'ruth@email.com', 'joined' => '2024-01-12', 'active_loans' => 0],
                ['initials' => 'EN', 'name' => 'Emmanuel Nwosu', 'phone' => '0806-789-0123', 'email' => 'emmanuel@email.com', 'joined' => '2024-03-28', 'active_loans' => 3],
            ],
        ]);
    }
}
