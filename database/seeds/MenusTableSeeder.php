<?php

use Illuminate\Database\Seeder;
use App\Menu;

class MenusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $position = 0;

        $files = File::files(base_path('database/seeds/menus'));

        foreach (collect($files)->sort() as $file) {
            $menus = require $file;

            foreach ($menus as $menu) {
                $frags = explode('.', $menu['name']);

                $parent = null;

                if ($parentName = $this->getParentName($menu['name'])) {
                    $parent = Menu::where('name', $parentName)->first();
                }

                $menu = new Menu($menu);

                if ($parent) {
                    $menu->parent_id = $parent->id;
                    $menu->level     = $parent->level + 1;
                    $menu->position  = $parent->children()->count() + 1;
                } else {
                    $menu->position = ++$position;
                }

                $menu->save();
            }
        }
    }


    /**
     * Get the parent name
     *
     * @param string $name Name delimited by period (.)
     * @return null|string
     */
    protected function getParentName($name)
    {
        if (substr_count($name, '.') === 0) {
            return null;
        }

        return substr($name, 0, strripos($name, '.'));
    }
}
