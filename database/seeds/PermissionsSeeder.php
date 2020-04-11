<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class PermissionsSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $roles = [
      [
        'name' => 'admin',
        'permissions' => [
          'bills' => ['view', 'edit', 'delete'],
          'courses' => ['view', 'edit', 'delete'],
          'users' => ['view', 'edit', 'delete'],
          'registrations' => ['view', 'edit', 'delete'],
          'tenants' => ['view', 'edit'],
        ],
      ],
    	[
        'name' => 'alumni',
        'permissions' => [
          'bills' => ['view'],
          'users' => ['view'],
          'registrations' => ['view'],
        ],
      ],
    	[
        'name' => 'other',
        'permissions' => [],
      ],
    	[
        'name' => 'student',
        'permissions' => [
          'courses' => ['view'],
          'registrations' => ['view'],
        ]
      ],
    	[
        'name' => 'superadmin',
        'permissions' => [
          'bills' => ['view', 'edit', 'delete'],
          'courses' => ['view', 'edit', 'delete'],
          'users' => ['view', 'edit', 'delete'],
          'registrations' => ['view', 'edit', 'delete'],
          'tenants' => ['view', 'edit', 'delete'],
        ],
      ],
      [
        'name' => 'instructor',
        'permissions' => [
          'courses' => ['view', 'edit'],
          'users' => ['view'],
          'registrations' => ['view'],
        ],
      ],
    	[
        'name' => 'parent',
        'permissions' => [
          'bills' => ['view'],
          'courses' => ['view'],
          'registrations' => ['view'],
        ],
      ],
    ];

    $this->create_roles($roles);

    $permissions = [
    	'bills' => ['create','view','edit','delete'],
    	'courses' => ['create','view','edit','delete'],
    	'users' => ['create','view','edit','delete'],
      'registrations' => ['create','view','edit','delete'],
    	'tenants' => ['create','view','edit','delete'],
		];

    $this->create_permissions($permissions);

    $permission_mappings = [
      'student' => [
        'courses' => ['view'],
        'registrations' => ['view'],
      ],
      'instructor' => [
        'courses' => ['view', 'edit'],
        'users' => ['view'],
        'registrations' => ['view'],
      ],
      'parent' => [
        'bills' => ['view'],
        'courses' => ['view'],
        'registrations' => ['view'],
      ],
      'admin' => [
        'bills' => ['view', 'edit', 'delete'],
        'courses' => ['view', 'edit', 'delete'],
        'users' => ['view', 'edit', 'delete'],
        'registrations' => ['view', 'edit', 'delete'],
        'tenants' => ['view', 'edit'],
      ],
      'superadmin' => [
        'bills' => ['view', 'edit', 'delete'],
        'courses' => ['view', 'edit', 'delete'],
        'users' => ['view', 'edit', 'delete'],
        'registrations' => ['view', 'edit', 'delete'],
        'tenants' => ['view', 'edit', 'delete'],
      ],
      'alumni' => [
        'bills' => ['view'],
        'users' => ['view'],
        'registrations' => ['view'],
      ],
    ];

    $this->map_roles($roles);
  }

  private function create_roles($roles) {
    foreach ($roles as $role) {
      $role = Bouncer::role()->firstOrCreate(
        Arr::only($role, ['name'])
      );
    }
  }

  private function map_roles($roles) {
    foreach ($roles as $role) {
      if (isset($role['permissions'])) {
         foreach ($role['permissions'] as $resource_key => $permissions) {
          foreach ($permissions as $permission) {
            Bouncer::allow($role['name'])->to("{$permission}-{$resource_key}");         
          }
        }
      }
    }
  }

  private function create_permissions($permissions) {
    foreach ($permissions as $permission_key => $permissions_value) {
      foreach ($permissions_value as $permission) {
        $permission = Bouncer::ability()->firstOrCreate([
          'name' => "{$permission}-{$permission_key}"
        ]);
      }
    }
  }
}
