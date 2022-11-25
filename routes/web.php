<?php

use App\Mail\Bug;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('bug', function () {
    /**
     * First look:
     *
     * - Configure a mailtrap/similar to catch the email.
     * - Load this endpoint.
     * - Open the raw email content you receive in Mailtrap/whatever
     *      and search for "image/png" -- you will should only see 1 embed
     *      however you will find two images embedded in the email.
     *      In gmail the first image will be displayed inline (great) however
     *      the second image will be appended to the end as a downloadable
     *      attachment (not good).
     *
     *
     *
     *
     * Second look:
     *
     * @see Illuminate\Mail\Message
     *      vendor/laravel/framework/src/Illuminate/Mail/Message.php
     *
     * - Add xdebug breakpoint to line 338 of Message class and reload this endpoint.
     * - The code execution should pause only one on embed, however it will
     *      instead pause twice, thus loading the image in twice.
     *
     *
     *
     *
     * Deeper look:
     *
     * Search for `base_path('file.png')` within storage/framework/views and you'll see
     * a file that mentions this twice. This is where the double-handling takes place.
     *
     *
     *
     *
     * Query:
     *
     * Should Blade store and re-use the resolved values from each prop?
     *      Example:
     *              $anoynmousComponent = new AnonymousComponent();
     *              $anoynmousComponent->setProp('src', $message->embed('.../file.png'));
     *              <img src="{{ $anoynmousComponent->getProp('src') }}">
     *
     * Or does that overkill for what is otherwise an anonymous component?
     *
     * Alternatively, would it not make sense for Illuminate\Mail\Message to keep track
     * of every embedded file and return the previously derived embed hash on subsequent
     * calls?
     *      Example:
     *              public function embed($file)
     *              {
     *                  if ($file instanceof Attachable) {
     *                      // ...
     *                  }
     *
     *                  if ($file instanceof Attachment) {
     *                      // ...
     *                  }
     *
     *                  if (isset($this->embedded[$file])) {
     *                      return $this->embedded[$file];
     *                  }
     *
     *                  $cid = Str::random(10);
     *
     *                  $this->message->embedFromPath($file, $cid);
     *
     *                  return $this->embedded[$file] = "cid:$cid";
     *              }
     *
     * Or is there a benefit to being able to embed the same image twice (and return
     * separate hashes)?
     *
     *
     *
     *
     * Workaround:
     *
     *      Change:
     *              <x-logo
     *                  :src="$message->embed(base_path('file.png'))"
     *              />
     *
     *      To:
     *              @php $logo = $message->embed(base_path('file.png')) @endphp
     *              <x-logo
     *                  :src="$logo"
     *              />
     *
     *
     *
     * Thoughts?
     */

    Mail::to('example@example.com')->send(new Bug());

    return new Bug();
});
