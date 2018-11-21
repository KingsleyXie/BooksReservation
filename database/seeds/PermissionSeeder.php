<?php

use App\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
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

        $booksIndex = Permission::create(['name' => 'books.index']);
        $booksImport = Permission::create(['name' => 'books.import']);
        $booksUpdate = Permission::create(['name' => 'books.update']);
        $reservationsIndex = Permission::create(['name' => 'reservations.index']);



        $superadmin->givePermissionTo($booksIndex);
        $superadmin->givePermissionTo($booksImport);
        $superadmin->givePermissionTo($booksUpdate);
        $superadmin->givePermissionTo($reservationsIndex);

        $booksAdmin->givePermissionTo($booksIndex);
        $booksAdmin->givePermissionTo($booksImport);
        $booksAdmin->givePermissionTo($booksUpdate);

        $booksViewer->givePermissionTo($booksIndex);
        $booksImporter->givePermissionTo($booksImport);
        $booksUpdater->givePermissionTo($booksUpdate);

        $reservationsAdmin->givePermissionTo($reservationsIndex);



        User::find(1)->assignRole('superadmin');
        User::find(2)->assignRole('books.admin');
    }
}
