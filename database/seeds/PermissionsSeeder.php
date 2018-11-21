<?php

use App\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $superadmin = Role::create(['name' => 'superadmin']);
        $booksAdmin = Role::create(['name' => 'books.admin']);
        $booksViewer = Role::create(['name' => 'books.viewer']);
        $booksImporter = Role::create(['name' => 'books.importer']);
        $booksUpdater = Role::create(['name' => 'books.updater']);
        $reservationsAdmin = Role::create(['name' => 'reservations.admin']);

        $booksView = Permission::create(['name' => 'books.view']);
        $booksImport = Permission::create(['name' => 'books.import']);
        $booksUpdate = Permission::create(['name' => 'books.update']);
        $reservationsView = Permission::create(['name' => 'reservations.view']);



        $superadmin->givePermissionTo($booksView);
        $superadmin->givePermissionTo($booksImport);
        $superadmin->givePermissionTo($booksUpdate);
        $superadmin->givePermissionTo($reservationsView);

        $booksAdmin->givePermissionTo($booksView);
        $booksAdmin->givePermissionTo($booksImport);
        $booksAdmin->givePermissionTo($booksUpdate);

        $booksViewer->givePermissionTo($booksView);

        $booksImporter->givePermissionTo($booksView);
        $booksImporter->givePermissionTo($booksImport);

        $booksUpdater->givePermissionTo($booksView);
        $booksUpdater->givePermissionTo($booksUpdate);

        $reservationsAdmin->givePermissionTo($reservationsView);



        User::find(1)->assignRole('superadmin');
        User::find(2)->assignRole('books.admin');
    }
}
