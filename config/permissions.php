<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Permission Resources
    |--------------------------------------------------------------------------
    |
    | Resources that can be granted permissions. Each key is the resource slug
    | used in permission checks, and the value is the display label shown
    | in the Role Create/Edit UI.
    |
    */

    'resources' => [
        'users' => 'Users',
        'roles' => 'Roles',
        'provinces' => 'Provinces',
        'regencies' => 'Regencies',
        'districts' => 'Districts',
        'villages' => 'Villages',
    ],

    /*
    |--------------------------------------------------------------------------
    | Permission Actions
    |--------------------------------------------------------------------------
    |
    | Actions that can be granted per resource. Each key is the action slug
    | and the value is the display label shown in the Role Create/Edit UI.
    |
    */

    'actions' => [
        'create' => 'Create',
        'read' => 'Read',
        'update' => 'Update',
        'delete' => 'Delete',
    ],

];
