<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as TestingTestCase;

class AuthTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testRegister()
    {
       $data = [
           'email' => 'test@gmail.com',
            'name' => 'Test',
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
       ];
       //send post request
       $response = $this->json('POST',route('api.register'),$data);
       //assert it was successful
       $response->assertStatus(200);
       //assert we receive a token
       $this->assertArrayHasKey('token',$response->json());
       //Delete data
       User::where('email','test@gmail.com')->delete();

    }

    public function testLogin()
    {
        User::create(['name'=>'test','email'=>'test@gmail.com','password'=> bcrypt('secret123')]);
        $response = $this->json('POST',route('api.authenticate'),['email'=>'test@gmail.com','password'=>'secret123']);
        $response->assertStatus(200);
        $this->assertArrayHasKey('token',$response->json());
        //Delete user
        User::where('email','test@gmail.com')->delete();
    }
}
