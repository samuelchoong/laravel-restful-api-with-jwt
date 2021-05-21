<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Recipe;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class RecipeTest extends TestCase
{
    // use RefreshDatabase;
    use DatabaseMigrations;

    protected $user;
    protected $token;

    protected function authenticate()
    {
        $user = User::whereEmail('test@gmail.com')->first();
        if(!$user){
            $user = User::create([
                'name' => 'test',
                'email' => 'test@gmail.com',
                'password' => Hash::make('secret1234'),
            ]);
        }
        $this->user = $user;
        $token = JWTAuth::fromUser($user);
        $this->token = $token;
        return $token;
    }

    public function testCreate()
    {
        $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
        ])->json('POST',route('recipe.create'),[
            'title' => 'Jollof Rice',
            'procedure' => ' This is the procedure of making Jollof Rice'
        ]);

        $response->assertStatus(200);
        //get count and assert
        $count = User::where('email','test@gmail.com')->first()->recipes()->count();
        $this->assertNotEquals(0,$count);
    }

    //Test the display all routes
    public function testAll(){
        $this->authenticate();
        //Authenticate and attach recipe to user
        $recipe = Recipe::create([
            'title' => 'Jollof Rice',
            'procedure' => 'Parboil rice, get pepper and mix, and some spice and serve!'
        ]);
        $this->user->recipes()->save($recipe);

        //call route and assert response
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
        ])->json('GET',route('recipe.all'));
        $response->assertStatus(200);

        //Assert the count is 1 and the title of the first item correlates
        $this->assertNotEquals(0,count($response->json()));
        $this->assertEquals('Jollof Rice',$response->json()[0]['title']);
    }
    //Test the update route
    public function testUpdate(){
        $this->authenticate();
        $recipe = Recipe::create([
            'title' => 'Jollof Rice',
            'procedure' => 'Parboil rice, get pepper and mix, and some spice and serve!'
        ]);
        $this->user->recipes()->save($recipe);

        //call route and assert response
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
        ])->json('POST',route('recipe.update',['recipe' => $recipe->id]),[
            'title' => 'Rice',
        ]);
        $response->assertStatus(200);

        //Assert title is the new title
        $this->assertEquals('Rice',$this->user->recipes()->first()->title);
    }
    //Test the single show route
    public function testShow(){
        $this->authenticate();
        $recipe = Recipe::create([
            'title' => 'Jollof Rice',
            'procedure' => 'Parboil rice, get pepper and mix, and some spice and serve!'
        ]);
        $this->user->recipes()->save($recipe);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
        ])->json('GET',route('recipe.show',['recipe' => $recipe->id]));
        $response->assertStatus(200);

        //Assert title is correct
        $this->assertEquals('Jollof Rice',$response->json()['title']);
    }
    //Test the delete route
    public function testDelete(){
        $this->authenticate();
        $recipe = Recipe::create([
            'title' => 'Jollof Rice',
            'procedure' => 'Parboil rice, get pepper and mix, and some spice and serve!'
        ]);
        $this->user->recipes()->save($recipe);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
        ])->json('POST',route('recipe.delete',['recipe' => $recipe->id]));
        $response->assertStatus(201 );

        //Assert there are no recipes
        $this->assertEquals(0,$this->user->recipes()->count());
    }


}
