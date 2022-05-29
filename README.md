![Laravel](https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg)


# Laravel Passport API authentication with crud application

Hi All!

Here is the example focused on laravel `passport authentication` and `rest api crud` application to handle the rest API CRUD authenticated requests.

**Representational state transfer (REST)** is a software architectural style that defines a set of constraints to be used for creating Web services.

**Laravel Passport** is an OAuth 2.0 server implementation for API authentication using Laravel. Since tokens are generally used in API authentication, Laravel Passport provides an `easy` and `secure` way to implement token authorization on an `OAuth 2.0 server`.

In this example we have focused on Rest API `login`, `register` the user and `create`, `read`, `update`, `delete` the posts using authenticated routes and fetch the posts based on user. and test the API request using postman tool.

For all routes requests must contain in header.
```
'headers' => [
    'Accept' => 'application/json',
]
```

For authenticated route requests must contain in header.
```
'headers' => [
    'Accept' => 'application/json',
    'Authorization' => 'Bearer '. $accessToken,
]
```

### Preview using postman
login
![login](https://github.com/kcsrinivasa/laravel-api-passport-crud/blob/main/output/login.png?raw=true)
register
![register](https://github.com/kcsrinivasa/laravel-api-passport-crud/blob/main/output/register.png?raw=true)
create post
![create](https://github.com/kcsrinivasa/laravel-api-passport-crud/blob/main/output/create.png?raw=true)
create post and pass headers(pass headers I type)
![create_headers](https://github.com/kcsrinivasa/laravel-api-passport-crud/blob/main/output/create_headers.png?raw=true)
read/fetch post(pass headers II type)
![read](https://github.com/kcsrinivasa/laravel-api-passport-crud/blob/main/output/read.png?raw=true)
update post
![update](https://github.com/kcsrinivasa/laravel-api-passport-crud/blob/main/output/update.png?raw=true)
delete delete
![delete](https://github.com/kcsrinivasa/laravel-api-passport-crud/blob/main/output/delete.png?raw=true)
error response
![error](https://github.com/kcsrinivasa/laravel-api-passport-crud/blob/main/output/error.png?raw=true)

Here are the following steps to achive laravel api crud application with passport authentication. 

### Step 1: Install Laravel
```bash
composer create-project laravel/laravel laravel-passport-auth
```

### Step 2: Update database credentials
```bash
DB_DATABASE=laravel_passport_crud
DB_USERNAME=root
DB_PASSWORD=db_password
```

### Step 3: Install passport package
```bash
composer require laravel/passport
```

### Step 4: Create tables
```bash
php artisan migrate
```

### Step 5: Generate token keys
```bash
php artisan passport:install 
```

### Step 6: Configure Passport Module in `app/Models/User.php`
```bash
<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
...
```
Grab complete User model from app/Models/User.php file.

### Step 7: Configure driver for the Passport, update in the `config/auth.php` file guards
```bash
'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
        
        'api' => [
            'driver' => 'passport',
            'provider' => 'users',
        ],
    ],
```


### Step 8: Create controller and model
```bash
php artisan make:controller AuthController
php artisan make:model Post -mcr
```

### Step 9: Add the login, register functions in `App\Http\Controllers\AuthController.php` controller
```bash
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    /**  For registration */

    public function register(Request $request){
        /* validate request*/
        $request->validate([
            'name'=>'required|string|regex:/[a-zA-Z]/|max:255',
            'email'=>'required|string|email|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix|max:255|unique:users',
            'password'=>'required|string|min:8|confirmed',
        ],[
            'name.regex'=>'Please enter valid name',
            'email.regex'=>'Please enter valid email',
            'email.unique'=>'Entered email is already exists in our records',
        ]);

        /* create user */
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        /* genrate auth token*/
        $user->token = $user->createToken('LaravelAuthToken')->accessToken;

        $res = [
            'message' => 'User registered successfully',
            'user' => $user
        ];
        return response($res,200);
    }

    /**  For login */

    public function login(Request $request){
         /* validate request*/
        $request->validate([
            'email'=>'required|string|email',
            'password'=>'required|string',
        ]);
        $request = $request->only('email','password');

        if(auth()->attempt($request)){
            /* find user */
            $user = auth()->user();

            /* genrate auth token*/
            $user->token = $user->createToken('LaravelAuthToken')->accessToken;

            $res = [
                'message' => 'User logged successfully',
                'user' => $user
            ];
            return response($res,200);
        }else{
            return response(['message'=>'Invalid email or password'],422);
        }
    }
}

```

### Step 10: Set up the Posts section
Update Model (Grab the code from `App\Models\Post.php`)
```bash
    protected $fillable=['title','description','user_id'];
```
Update Migration (Grab the code from `database\migrations\..create_posts_table.php`)
```bash
    Schema::create('posts', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->text('description');
        $table->unsignedBigInteger('user_id');
        $table->foreign('user_id')->references('id')->on('users');
        $table->timestamps();
    });
```
Update Controller (Grab the code from `App\Http\Controllers\PostController.php`)
```bash
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $res = [
            'message' => 'Post records',
            'posts' => $posts = auth()->user()->posts
        ];
        return response($res,200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:65535',
        ]);

        $post = Post::create([
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => auth()->id(),
        ]);

        $res = [
            'message' => 'Post added successfully',
            'post' => $post
        ];
        return response($res,201);
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show($post_id)
    {
        $post = auth()->user()->posts()->find($post_id);

        if($post){
            $res = [
                'message' => 'Post fetched successfully',
                'post' => $post
            ];
            return response($res,200);
        }else{
            $res = [ 'message' => 'Post not found'];
            return response($res,404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $post_id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:65535',
        ]);

        $post = auth()->user()->posts()->find($post_id);

        if($post){
            $post->update([
                'title' => $request->title,
                'description' => $request->description,
            ]);
            $res = [
                'message' => 'Post updated successfully',
                'post' => $post
            ];
            return response($res,200);
        }else{
            $res = [ 'message' => 'Post not found'];
            return response($res,404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy($post_id)
    {
        $post = auth()->user()->posts()->find($post_id);

        if($post){
            $post->delete();
            $res = [
                'message' => 'Post deleted successfully',
            ];
            return response($res,200);
        }else{
            $res = [ 'message' => 'Post not found'];
            return response($res,404);
        }
    }
```
### Step 11: Add Routes in `routes\api.php` file
```bash
Route::post('login','App\Http\Controllers\AuthController@login');
Route::post('register','App\Http\Controllers\AuthController@register');

Route::middleware('auth:api')->group(function(){
    Route::resource('posts','App\Http\Controllers\PostController');
});
```

### Step 12: Final run and check
```bash
php artisan migrate
php artisan serve
```
send request with basepath http://localhost:8000/api/*


## Note : Refer the documentation(root dir file) for API requests
[![document-api](https://img.shields.io/badge/Documentation-APIs_(clieck_here)-blue)](https://github.com/kcsrinivasa/laravel-api-passport-crud/blob/main/api-documentation.docx)
