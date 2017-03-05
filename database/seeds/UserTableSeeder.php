<?php

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      factory(App\Models\User::class, 5)->create()->each(function($u) {
        $u->chats()->save(factory(App\Models\Chat::class)->make());
      });

      $user1 = App\Models\User::all()->first();
      $user2 = App\Models\User::all()->last();
      $chat = App\Models\Chat::all()->first();
      factory(App\Models\Message::class, 5)->create([
        'chat_id' => $chat->id,
        'user_id' => $user1->id
      ]);
      factory(App\Models\Message::class, 5)->create([
        'chat_id' => $chat->id,
        'user_id' => $user2->id
      ]);
    }
}
