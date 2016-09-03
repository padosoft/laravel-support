<?php

namespace Padosoft\Laravel\Support\Test\Integration;

use Auth;
use Mockery\Mock as m;

class HelpersTest extends TestCase
{
    use \Padosoft\Laravel\Support\Test\Integration\ValidationDataProvider;

    /**
     * @test
     * @param $fields
     * @param $rules
     * @param $expected
     * @dataProvider validateProvider
     */
    public function validate($fields, $rules, $expected)
    {
        if ($this->expectedIsAnException($expected)) {
            $this->expectException($expected);
            validate($fields, $rules);
        } else {
            $this->assertEquals($expected, validate($fields, $rules));
        }
    }

    /**
     * @test
     */
    public function locale()
    {
        $original = $this->app->getLocale('en');

        $this->app->setLocale('en');
        $this->assertEquals('en', locale());
        $this->app->setLocale('it');
        $this->assertEquals('it', locale());

        $this->app->setLocale($original);
    }

    /**
     * @test
     */
    public function userIsLogged()
    {
        Auth::shouldReceive('guest')->once()->andReturn(true);
        $this->assertEquals(false, userIsLogged());
        Auth::shouldReceive('guest')->once()->andReturn(false);
        $this->assertEquals(true, userIsLogged());
    }

    /**
     * @test
     */
    public function query_interpolate()
    {
        /**
         * to generate a parameter queries
         * $this->testUser->remember_token = 'dfsf234wdfsafsdfsdf';
         * \DB::enableQueryLog();
         * $this->testUser->save();
         * $query = queries();
         * $this->refreshTestUser();
         * \DB::disableQueryLog();
         */
        $query = 'update "users" set "remember_token" = ? where "id" = ?';
        $bindings = [
            0 => "dfsf234wdfsafsdfsdf",
            1 => "1"
        ];
        $result = query_interpolate($query, $bindings);
        $this->assertEquals('update "users" set "remember_token" = \'dfsf234wdfsafsdfsdf\' where "id" = 1', $result);

        $query = 'update "users" set "remember_token" = ? where "id" = ?';
        $bindings = [
            0 => null,
            1 => "1"
        ];
        $result = query_interpolate($query, $bindings);
        $this->assertEquals('update "users" set "remember_token" = NULL where "id" = 1', $result);

        $query = 'update "users" set "remember_token" = :one where "id" = :two';
        $bindings = [
            "one" => null,
            "two" => "1"
        ];
        $result = query_interpolate($query, $bindings);
        $this->assertEquals('update "users" set "remember_token" = NULL where "id" = 1', $result);

        $query = 'update "users" set "remember_token" = ? where "id" = ?';
        $bindings = [
            0 => ["1","1","1"],
            1 => "1"
        ];
        $result = query_interpolate($query, $bindings);
        $this->assertEquals('update "users" set "remember_token" = \'1,1,1\' where "id" = 1', $result);
    }

    /**
     * @test
     */
    public function queries()
    {
        \DB::enableQueryLog();
        User::first();
        User::first();
        User::first();
        $query = queries();
        $this->assertInternalType('array', $query);
        $this->assertCount(3, $query);
        for ($i = 0; $i < count($query); $i++) {
            $this->assertArrayHasKey('look', $query[$i]);
            $this->assertArrayHasKey('query', $query[$i]);
            $this->assertArrayHasKey('bindings', $query[$i]);
            $this->assertArrayHasKey('time', $query[$i]);
        }
        \DB::disableQueryLog();
    }

    /**
     * @test
     */
    public function query_table()
    {
        \DB::enableQueryLog();
        User::first();
        User::first();
        User::first();
        $html = query_table();
        $this->assertInternalType('string', $html);
        $this->assertContains('table', $html);
        $this->assertContains('tr', $html);
        $this->assertContains('select', $html);
        \DB::disableQueryLog();
    }

    /**
     * @test
     */
    public function current_user()
    {
        /*
         * TEST WITH REAL USER AND DB
         */
        $this->assertEquals(false, current_user());

        Auth::login($this->testUser);
        $user = current_user();
        $this->assertInternalType('object', $user);
        $this->assertEquals('test@user.com', $user->email);

        $user_id = current_user('id');
        $this->assertInternalType('int', $user_id);
        $this->assertEquals(1, $user_id);

        $user_email = current_user('email');
        $this->assertInternalType('string', $user_email);
        $this->assertEquals('test@user.com', $user_email);

        Auth::logout();
        $this->assertEquals(false, current_user());

        /*
         * TEST WITH MOCK
         */
        Auth::shouldReceive('check')->once()->andReturn(false);
        $this->assertEquals(false, current_user());

        Auth::shouldReceive('check')->once()->andReturn(true);
        Auth::shouldReceive('id')->once()->andReturn(1);
        $this->assertEquals(1, current_user('id'));

        Auth::shouldReceive('check')->once()->andReturn(true);
        Auth::shouldReceive('user')->once()->andReturn('ok');
        $this->assertEquals('ok', current_user());

        //$mock = m::mock();
        //$mock = m::mock('StdClass');
        //$mock = m::mock('Eloquent', '\App\User');
        //$mock = m::mock('Eloquent', '\App\Models\User');
        //$mock = m::mock('Illuminate\Database\Eloquent\Model');
        //$mock = m::mock('\App\User');
        //$mock = m::mock('User');
        //$mock = $this->createMock('\App\User');
        //$mock = $this->createMock('\App\Models\User');
        //$mock = m::mock(TestModel::class);
            //$mock = $this->createMock(User::class);
        //$this->app['\App\Models\User'];
        //$this->app->instance('\App\Models\User', $mock);
        /*
        $mock
            ->shouldReceive('name')
            ->once()
            ->andReturn('mary');
        */
        /*
        $mock
            ->shouldReceive('getAttribute')
            ->with('name')
            ->once()
            ->andReturn('mary');
        */
        //$mock->setAttribute('name', 'mary');
        //$this->app->instance('User', $mock);
        Auth::shouldReceive('check')->once()->andReturn(true);
        //Auth::shouldReceive('user')->once()->andReturn($mock);
        Auth::shouldReceive('user')->once()->andReturn($this->testUser);
        //$this->assertEquals(null, current_user('name'));
        $this->assertEquals('test@user.com', current_user('email'));
    }
}
